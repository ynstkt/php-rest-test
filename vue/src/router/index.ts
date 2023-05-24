import { createRouter, createWebHistory } from 'vue-router';

import ContentsView from '../views/ContentsView.vue';

const routes = [
  {
    path: '/',
    component: ContentsView,
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});


export { router };