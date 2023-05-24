import { Item } from '../models/Item';

export interface IItemRepository {
  create: (item: Item) => void;
  update: (item: Item) => void;
  delete: (item: Item) => void;
  subscribe: (items: Item[]) => () => void;
}
