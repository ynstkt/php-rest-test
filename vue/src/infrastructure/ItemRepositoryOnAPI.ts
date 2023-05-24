import { Item } from '../domain/models/Item';

export class ItemRepositoryOnAPI<T extends Item> {

  protected URL = `http://${import.meta.env.VITE_API_HOST}/api/items/`;

  private effectMap = new Map<string, () => void>();
  private effectCount = 0;
  
  protected getAll() {
    return fetch(this.URL)
      .then(response => response.json())
      .then(json => json.items.map((item: any) => new Item(item.id, item.name)))
      .catch(error => {
        console.error('通信に失敗しました', error);
        return [];
      });
  }

  protected async postData(method: string, url = '', data = {}) {
    const response = await fetch(url, {
      method: method, // *GET, POST, PUT, DELETE, etc.
      mode: 'cors', // no-cors, *cors, same-origin
      cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
      // credentials: 'same-origin', // include, *same-origin, omit
      headers: {
        'Content-Type': 'application/json'
      },
      // redirect: 'follow', // manual, *follow, error
      // referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
      body: JSON.stringify(data),
    })
    return response;
  }

  public create(item: Item): void {
    const data = {
      id: item.id,
      name: item.name,
    };
    this.postData('POST', this.URL, data)
      .then(data => {
        console.log(data);
        this.effectAll();
      });
  };

  public update(item: Item): void {
    const data = {
      id: item.id,
      name: item.name,
    };
    this.postData('PUT', `${this.URL}${item.id}`, data)
    .then(data => {
      console.log(data);
      this.effectAll();
    });
  }
  
  public delete(item: Item): void {
    this.postData('DELETE', `${this.URL}${item.id}`)
    .then(data => {
      console.log(data);
      this.effectAll();
    });
  }

  protected subscribeWithQuery(items: T[], filterFn?: (item: T) => boolean): () => void {
    const mapKey = `subscribe${++this.effectCount}`;
    this.effectMap.set(mapKey, () => {
      if (filterFn) {
        this.getAll()
          .then(fetchedItems => items.splice(0, items.length, ...fetchedItems.filter(filterFn)));
      } else {
        this.getAll()
          .then(fetchedItems => items.splice(0, items.length, ...fetchedItems));
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
