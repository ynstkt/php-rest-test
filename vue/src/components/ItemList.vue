<script setup lang="ts">
import { Item } from '../domain/models/Item';

import ItemLine from './ItemLine.vue';

const props = defineProps<{
  items: Item[]
}>();

</script>

<template>
  <ul>
    <li v-for="item in props.items" :key="item.id">
      <ItemLine :item="item" />
    </li>
  </ul>
</template>

<style scoped>
</style>
