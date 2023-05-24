<script setup lang="ts">
import { ref } from 'vue';

import { Item } from '../domain/models/Item';

import { useItem } from '../composables/useItem';

const props = defineProps<{
  item: Item
}>();

const { updateItem, deleteItem } = useItem();

const newName = ref(props.item.name);
const update = () => {
  const newItem = new Item(props.item.id, newName.value);
  updateItem(newItem);
};
</script>

<template>
  {{ item.id }}
  <input type="text" v-model="newName"/>
  <button @click="update">update</button>
  <button @click="deleteItem(item)">delete</button>
</template>

<style scoped>
</style>
