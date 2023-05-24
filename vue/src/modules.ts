// import { ItemRepositoryOnFirestore } from './infrastructure/ItemRepositoryOnFirestore';
import { ItemRepository } from './infrastructure/ItemRepository';
import { IItemRepository } from './domain/repositories/IItemRepository';

// const itemRepository: IItemRepository = new ItemRepositoryOnFirestore(firestore);
const itemRepository: IItemRepository = new ItemRepository();

export {
  itemRepository,
}