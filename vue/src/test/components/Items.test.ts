import { describe, beforeEach, expect, it, vi, Mock } from 'vitest'
import { shallowMount, VueWrapper } from '@vue/test-utils'

import { ref, Ref } from 'vue';

import Items from "../../components/Items.vue";
import ItemList from "../../components/ItemList.vue";

import { Item } from '../../domain/models/Item';

import * as composable from '../../composables/useItem';


const testItems = [
  new Item('id1', 'name1'),
  new Item('id3', 'name3'),
  new Item('id2', 'name2'),
];

let items: Ref<Item[]>;
let subscribeAll: Mock;
let updateItem: Mock;
let createItem: Mock;
let deleteItem: Mock;

let wrapper: VueWrapper;

describe('Items', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    
    items = ref([...testItems]);
    subscribeAll = vi.fn();
    updateItem = vi.fn();
    createItem = vi.fn();
    deleteItem = vi.fn();
    vi.spyOn(composable, 'useItem').mockReturnValue({
      items,
      subscribeAll,
      updateItem,
      createItem,
      deleteItem,
    });
    wrapper = shallowMount(Items);
  });

  it('useItem経由でItemのリストのリスナーを取得する', () => {
    expect(subscribeAll).toBeCalledTimes(1);
  });

  it('ItemListのpropsに、Itemのリストをnameで降順ソートした結果を渡す', () => {
    const child = wrapper.getComponent(ItemList);
    expect(child.props().items).toStrictEqual([
      new Item('id3', 'name3'),
      new Item('id2', 'name2'),
      new Item('id1', 'name1'),
    ]);
  });
});
