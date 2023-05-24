const playwright = require('playwright');

const DOMAIN = process.env.TEST_TARGET_DOMAIN;

class ItemsOperation {

  constructor(page) {
    this.page = page
  }

  async open() {
    await this.page.goto(`http://${DOMAIN}`);
  }

  async getCount() {
    return await this.page.locator('li input').count();
  }

  async add(id, item) {
    await this.page.locator('input[type="text"]').first().fill(id);
    await this.page.locator('input[type="text"]').nth(1).fill(item);
    await this.page.getByRole('button', { name: 'add' }).click();
    await this.page.waitForTimeout(1000);
  }

  async update(id, item) {
    await this.page.locator(`li:has-text("${id}") input[type="text"]`).fill(item);
    await this.page.locator(`li:has-text("${id}")`).getByRole('button', { name: 'update' }).click();
    await this.page.waitForTimeout(1000);
  }

  async delete(id) {
    await this.page.locator(`li:has-text("${id}")`).getByRole('button', { name: 'delete' }).click();
    await this.page.waitForTimeout(1000);
  }

  async itemExists(id) {
    return await this.page.locator(`li:has-text("${id}") input[type="text"]`).count() === 1;
  }

  async itemValueOf(id) {
    return await this.page.inputValue(`li:has-text("${id}") input[type="text"]`);
  }

  static async create() {
    const browserType = 'chromium';
    const browser = await playwright[browserType].launch();
    const context = await browser.newContext();
    const page = await context.newPage();
    const itemsOperation = new ItemsOperation(page);
    return [
      browser,
      itemsOperation
    ];
  }
}

module.exports = ItemsOperation;