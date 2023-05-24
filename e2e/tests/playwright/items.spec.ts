import { test, expect } from '@playwright/test';

import ItemsOperation from '../../operations/items.operation';

const randomId = (length: number) => {
  const randNum = Math.random() * (10 ** length);
  const randStr = Math.floor(randNum).toFixed();
  return randStr;
};

let itemsOperation;

test.beforeEach(async ({ page }) => {
  itemsOperation = new ItemsOperation(page);
  await itemsOperation.open();
});

test.describe.serial('Items', () => {
  const id = randomId(8);

  test('Add item', async ({ page }) => {
    console.log(id, 'create');
    await expect(await itemsOperation.itemExists(id)).toBeFalsy();

    await itemsOperation.add(id, 'aaa');

    await expect(await itemsOperation.itemExists(id)).toBeTruthy();
    await expect(await itemsOperation.itemValueOf(id)).toEqual('aaa');
  });

  test('Update item', async ({ page }) => {
    console.log(id, 'update');
    await expect(await itemsOperation.itemValueOf(id)).toEqual('aaa');

    await itemsOperation.update(id, 'bbb');

    await expect(await itemsOperation.itemValueOf(id)).toEqual('bbb');

    await itemsOperation.open();
    await expect(await itemsOperation.itemValueOf(id)).toEqual('bbb');
  });

  test('Delete item', async ({ page }) => {
    console.log(id, 'delete');
    await expect(await itemsOperation.itemValueOf(id)).toEqual('bbb');

    await itemsOperation.delete(id);

    await expect(await itemsOperation.itemExists(id)).toBeFalsy();
  });
});
