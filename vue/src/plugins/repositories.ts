import { App, InjectionKey, Plugin } from 'vue'

import { itemRepository } from "../modules";
import { IItemRepository } from "../domain/repositories/IItemRepository";

export const itemRepositoryKey: InjectionKey<IItemRepository> = Symbol('itemRepository');

export const repositories: Plugin = {
  install(app: App) {
    app.provide(itemRepositoryKey, itemRepository);
  }
}