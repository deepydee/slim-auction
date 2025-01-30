init: docker-down-clear \
	api-clear frontend-clear \
	docker-pull docker-build docker-up \
	api-init frontend-init cucumber-init
up: docker-up
down: docker-down
restart: down up
check: lint analyze api-validate-schema test
lint: api-lint frontend-lint
analyze: api-analyze
test: api-test api-fixtures frontend-test
test-unit: api-test-unit
test-unit-coverage: api-test-unit-coverage
test-functional: api-test-functional api-fixtures
test-functional-coverage: api-test-functional-coverage api-fixtures

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

api-init: api-permissions api-composer-install api-wait-db api-migrations api-fixtures

api-permissions:
	docker run --rm -v ${PWD}/api:/app -w /app alpine chmod 777 var/cache var/log

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* car/log/*'

api-composer-install:
	docker compose run --rm api-php-cli composer install

api-wait-db:
	docker compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 30

api-lint:
	docker compose run --rm api-php-cli composer lint
	docker compose run --rm api-php-cli composer php-cs-fixer fix -- --dry-run --diff

api-cs-fix:
	docker compose run --rm api-php-cli composer php-cs-fixer fix

api-analyze:
	docker compose run --rm api-php-cli composer psalm

api-migrations:
	docker compose run --rm api-php-cli composer app migrations:migrate -- --no-interaction

api-fixtures:
	docker compose run --rm api-php-cli composer app fixtures:load

api-validate-schema:
	docker compose run --rm api-php-cli composer app orm:validate-schema -- -v

api-test:
		docker compose run --rm api-php-cli composer test

api-test-unit:
	docker compose run --rm api-php-cli composer test -- --testsuite=unit

api-test-unit-coverage:
	docker compose run --rm api-php-cli composer test-coverage -- --testsuite=unit

api-test-functional:
	docker compose run --rm api-php-cli composer test -- --testsuite=functional

api-test-functional-coverage:
	docker compose run --rm api-php-cli composer test-coverage -- --testsuite=functional

frontend-clear:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine sh -c 'rm -rf .ready build'

frontend-init: frontend-yarn-install frontend-ready

frontend-yarn-install:
	docker compose run --rm frontend-node-cli yarn install

frontend-ready:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine touch .ready

frontend-lint:
	docker compose run --rm frontend-node-cli yarn eslint
	docker compose run --rm frontend-node-cli yarn stylelint

frontend-lint-fix:
	docker compose run --rm frontend-node-cli yarn eslint-fix
	docker compose run --rm frontend-node-cli yarn stylelint-fix

frontend-pretty:
	docker compose run --rm frontend-node-cli yarn prettier

frontend-test:
	docker compose run --rm frontend-node-cli yarn test --watchAll=false

frontend-test-watch:
	docker compose run --rm frontend-node-cli yarn test

cucumber-init: cucumber-yarn-install

cucumber-yarn-install:
	docker compose run --rm cucumber-node-cli yarn install

build: build-gateway build-frontend build-api

build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-gateway:${IMAGE_TAG} gateway/docker

build-frontend:
	docker --log-level=debug build --pull --file=frontend/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-frontend:${IMAGE_TAG} frontend

build-api:
	docker --log-level=debug build --pull --file=api/docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/auction-api-php-fpm:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-cli/Dockerfile --tag=${REGISTRY}/auction-api-php-cli:${IMAGE_TAG} api

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push: push-gateway push-frontend push-api

push-gateway:
	docker push ${REGISTRY}/auction-gateway:${IMAGE_TAG}

push-frontend:
	docker push ${REGISTRY}/auction-frontend:${IMAGE_TAG}

push-api:
	docker push ${REGISTRY}/auction-api-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/auction-api:${IMAGE_TAG}
	docker push ${REGISTRY}/auction-api-php-cli:${IMAGE_TAG}

deploy:
	ssh deploy@${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh deploy@${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'
	scp -P ${PORT} docker-compose-production.yml deploy@${HOST}:site_${BUILD_NUMBER}/docker-compose-production.yml
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=auction" >> .env'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "DB_PASSWORD=${API_DB_PASSWORD}" >> .env'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "MAILER_FROM_EMAIL=${API_MAILER_FROM_EMAIL}" >> .env'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "MAILER_HOST=${API_MAILER_HOST}" >> .env'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "MAILER_PASSWORD=${API_MAILER_PASSWORD}" >> .env'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "MAILER_PORT=${API_MAILER_PORT}" >> .env'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "MAILER_USER=${API_MAILER_USERNAME}" >> .env'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "SENTRY_DSN=${SENTRY_DSN}" >> .env'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose -f docker-compose-production.yml pull'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose up --build -d api-postgres api-php-cli'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose run api-php-cli wait-for-it api-postgres:5432 -t 60'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose run api-php-cli php bin/app.php migrations:migrate --no-interaction'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose -f docker-compose-production.yml up --build --remove-orphans -d'
	ssh deploy@${HOST} -p ${PORT} 'rm -f site'
	ssh deploy@${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'

rollback:
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose -f docker-compose-production.yml pull'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose -f docker-compose-production.yml up --build --remove-orphans -d'
	ssh deploy@${HOST} -p ${PORT} 'rm -f site'
	ssh deploy@${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'