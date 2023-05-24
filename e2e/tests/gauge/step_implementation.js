/* globals gauge*/
"use strict";
const assert = require("assert");

const ItemsOperation = require('../../operations/items.operation.js');

let browser;
let itemsOperation;

beforeSuite(async () => {
    [ browser, itemsOperation ] = await ItemsOperation.create();
});

afterSuite(async () => {
    await browser.close();
});

// Return a screenshot file name
gauge.customScreenshotWriter = async function () {
    const screenshotFilePath = path.join(process.env['gauge_screenshots_dir'],
        `screenshot-${process.hrtime.bigint()}.png`);

    await screenshot({
        path: screenshotFilePath
    });
    return path.basename(screenshotFilePath);
};

step("Open items application", async function () {
    await itemsOperation.open();
});

step("Add item id = <id> , item = <item>", async (id, item) => {
    assert.ok(!(await itemsOperation.itemExists(id)));
    await itemsOperation.add(id, item);
});

step("Must have id = <id> , item = <item>", async function (id, item) {
    assert.ok(await itemsOperation.itemExists(id));
    assert.strictEqual(await itemsOperation.itemValueOf(id), item);    
});

step("Update item id = <id> , item = <newItem>", async (id, newItem) => {
    assert.ok(await itemsOperation.itemExists(id));
    assert.notStrictEqual(await itemsOperation.itemValueOf(id), newItem);    
    await itemsOperation.update(id, newItem);
});

step("Delete item id = <id>", async function (id) {
    await itemsOperation.delete(id);
});

step("Must not have id = <id>", async function (id) {
    assert.ok(!(await itemsOperation.itemExists(id)));
});
