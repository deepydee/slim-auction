init: docker-down-clear api-clear docker-pull docker-build docker-up api-init
up: docker-up
down: docker-down
restart: down up
check: lint analyze test
lint: api-lint
analyze: api-analyze
test: api-test
test-unit: api-test-unit
test-unit-coverage: api-test-unit-coverage
test-functional: api-test-functional
test-functional-coverage: api-test-functional-coverage

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

api-init: api-composer-install

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/*'

api-composer-install:
	docker compose run --rm api-php-cli composer install

api-lint:
	docker compose run --rm api-php-cli composer lint
	docker compose run --rm api-php-cli composer cs-check

api-analyze:
	docker compose run --rm api-php-cli composer psalm

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
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose -f docker-compose-production.yml pull'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose -f docker-compose-production.yml up --build --remove-orphans -d'
	ssh deploy@${HOST} -p ${PORT} 'rm -f site'
	ssh deploy@${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'

rollback:
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose -f docker-compose-production.yml pull'
	ssh deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose -f docker-compose-production.yml up --build --remove-orphans -d'
	ssh deploy@${HOST} -p ${PORT} 'rm -f site'
	ssh deploy@${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'