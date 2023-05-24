import { 
  describe, it, expect,
  beforeEach, afterEach,
  vi, SpyInstance
} from 'vitest';
import { Ref, provide } from 'vue';

import { withSetup } from './test-utils';

import { useItem } from '../../composables/useItem';
import { Item } from '../../domain/models/Item';
import { IItemRepository } from '../../domain/repositories/IItemRepository';
import { itemRepositoryKey } from '../../plugins/repositories';


const testItems = [
  new Item('id1', 'name1'),
  new Item('id3', 'name3'),
  new Item('id2', 'name2'),
];

class FakeItemRepository implements IItemRepository {
  subscribe(items: Item[]) {
    items.splice(0, items.length, ...testItems);
    return () => {};
  }

  updatedItem: Item | undefined;
  update(item: Item) {
    this.updatedItem = item;
  };
  create(item: Item) {};
  delete(item: Item) {};
}

let fakeItemRepository: FakeItemRepository;
let spySubscribeAll: SpyInstance;
let spyUpdate: SpyInstance;

let composed: {
  result: {
    items: Ref<Item[]>;
    subscribeAll: () => () => void;
    updateItem: (item: Item) => void
  },
  unmount: () => void
};

describe('useItem', () => {

  beforeEach(() => {
    vi.clearAllMocks();

    fakeItemRepository = new FakeItemRepository();
    spySubscribeAll = vi.spyOn(fakeItemRepository, 'subscribe');
    spyUpdate = vi.spyOn(fakeItemRepository, 'update');

    composed = withSetup(() => useItem(), {
      provider: () => {
        provide(itemRepositoryKey, fakeItemRepository);
      },
    });
  });

  it('subscribeする前はitemsは空', async () => {
    expect(composed.result.items.value).toStrictEqual([]);
  });

  it('subscribeすると、itemsにRepositoryのsubuscribe結果が反映される', async () => {
    composed.result.subscribeAll();
    expect(composed.result.items.value).toStrictEqual([...testItems]);
    expect(spySubscribeAll).toHaveBeenCalledTimes(1);  
    expect(spySubscribeAll).toBeCalledWith(composed.result.items.value);
  });

  it('updateItemすると、Repositoryのupdateに移譲される', async () => {
    const item = new Item('id3', 'name3');
    composed.result.updateItem(item);
    expect(fakeItemRepository.updatedItem).toBe(item);
    expect(spyUpdate).toHaveBeenCalledTimes(1);
    expect(spyUpdate).toBeCalledWith(item);
  });

  afterEach(() => {
    composed.unmount();
  });
});
