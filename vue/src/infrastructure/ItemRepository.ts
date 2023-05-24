import { IItemRepository } from '../domain/repositories/IItemRepository';
import { Item } from '../domain/models/Item';
import { ItemRepositoryOnAPI } from './ItemRepositoryOnAPI';
// import { ItemRepositoryOnMemory } from './ItemRepositoryOnMemory';

// const testData = [
//   {
//     id: "00000000001",
//     name:"ほげほげ",
//     isCompleted: false,
//   },
//   {
//     id: "00000000003",
//     name: "ふがふが",
//     isCompleted: false,
//   },
//   {
//     id: "00000000004",
//     name: "ぴよぴよ",
//     isCompleted: true,
//   }
// ];

export class ItemRepository extends ItemRepositoryOnAPI<Item> implements IItemRepository {

  // public constructor() {
  //   super(testData);
  // }

  public subscribe(items: Item[]): () => void {
    return this.subscribeWithQuery(items);
  }
}
