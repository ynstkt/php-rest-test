<script setup lang="ts">
import { ref, onUnmounted, computed } from 'vue';

import ItemList from './ItemList.vue';

import { useOrderBy } from '../composables/useOrderBy';

import { useItem } from '../composables/useItem';
import { Item } from '../domain/models/Item';
const { items, subscribeAll, createItem } = useItem();
const unsubscribe = subscribeAll();

onUnmounted(() => {
  unsubscribe();
});

const { orderBy } = useOrderBy();
const itemsOrderedByName = computed (() => {
  const orederDesc = true;
  return orderBy(items.value, 'name', orederDesc);
});

const newId = ref('');
const newName = ref('');
const create = () => {
  createItem(new Item(newId.value, newName.value));
};
</script>

<template>
  <label>ID</label>
  <input type="text" v-model="newId"/><br>
  <label>Item</label>
  <input type="text" v-model="newName"/><br>
  <button @click="create">add</button>

  <ItemList :items="itemsOrderedByName" />
</template>

<style scoped>
</style>
