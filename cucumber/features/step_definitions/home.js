import { Then } from '@cucumber/cucumber'
import { expect } from 'chai'

Then('I see welcome block', async function () {
  await this.page.waitForSelector('[data-testid=welcome]')
  const text = await this.page.$eval(
    '[data-testid=welcome] h1',
    (el) => el.textContent,
  )
  expect(text).to.eql('Auction')
})
