import puppeteer from 'puppeteer-core'
import { After, Before, setDefaultTimeout, Status } from '@cucumber/cucumber'

setDefaultTimeout(10 * 1000)

Before({ timeout: 30000 }, async function () {
  this.browser = await puppeteer.launch({
    headless: true,
    args: [
      '--disable-dev-shm-usage',
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-gpu',
    ],
    executablePath:
      process.env.PUPPETEER_EXECUTABLE_PATH || '/usr/bin/chromium-browser',
    protocolTimeout: 120000,
  })
  this.page = await this.browser.newPage()
  await this.page.setViewport({ width: 1280, height: 800 })
})

After(async function (testCase) {
  if (this.page) {
    if (testCase.result && testCase.result.status === Status.FAILED) {
      const name =
        testCase.pickle.uri
          .replace(/^\/app\/features\//, '')
          .replace(/\//g, '_') +
        '-' +
        testCase.pickle.name.toLowerCase().replace(/\W/g, '_')
      await this.page.screenshot({
        path: 'var/' + name + '.png',
        fullPage: true,
      })
    }
    await this.page.close()
  }
  if (this.browser) {
    await this.browser.close()
  }
})
