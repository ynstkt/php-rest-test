import { ref, Ref, inject } from 'vue';

import { Item } from '../domain/models/Item';
import { itemRepositoryKey } from '../plugins/repositories';

export function useItem() {

  const itemRepository = inject(itemRepositoryKey);
  if(!itemRepository) throw new Error('provide missing: itemRepository');

  const items: Ref<Item[]> = ref([]);
  
  const subscribeAll = () => {
    return itemRepository.subscribe(items.value);
  };

  const createItem = (item: Item) => {
    itemRepository.create(item);
  };

  const updateItem = (item: Item) => {
    itemRepository.update(item);
  };

  const deleteItem = (item: Item) => {
    itemRepository.delete(item);
  };

  return {
    items,
    subscribeAll,
    createItem,
    updateItem,
    deleteItem,
  }
}