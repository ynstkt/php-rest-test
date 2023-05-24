import { Item } from '../domain/models/Item';

export class ItemRepositoryOnMemory<T extends Item> {

  private effectMap = new Map<string, () => void>();
  private effectCount = 0;
  
  private items: T[];
  
  public constructor(initalItems: T[]) {
    this.items = [...initalItems];
  }

  public create(item: Item): void {
    this.items.push(item as T);
    this.effectAll();
  };

  public update(item: Item): void {
    const index = this.items.findIndex((anItem: Item) => anItem.id === item.id);
    this.items[index] = item as T;
    this.effectAll();
  }
  
  public delete(item: Item): void {
    const index = this.items.findIndex((anItem: Item) => anItem.id === item.id);
    this.items.splice(index, 1);
    this.effectAll();
  }

  protected subscribeWithQuery(items: T[], filterFn?: (item: T) => boolean): () => void {
    const mapKey = `subscribe${++this.effectCount}`;
    this.effectMap.set(mapKey, () => {
      if (filterFn) {
        items.splice(0, items.length, ...this.items.filter(filterFn));        
      } else {
        items.splice(0, items.length, ...this.items);        
      }
    });
    this.effectAll();
    return () => {
      this.effectMap.delete(mapKey);
    };
  }

  private effectAll() {
    this.effectMap.forEach(effect => effect());
  }
}
