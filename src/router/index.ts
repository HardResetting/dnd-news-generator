import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import Home from '../views/Home.vue';
import Items from '../views/ItemView/index.vue';
import Templates from '../views/TemplateView/index.vue';
import CompileTemplates from '../views/CompileTemplateView/index.vue';

const routes: Array<RouteRecordRaw> = [
  {
    path: "/",
    name: "Home",
    component: Home,
  },
  {
    path: "/items",
    name: "Items",
    component: Items,
  },
  {
    path: "/templates",
    name: "Templates",
    component: Templates,
  },  
  {
    path: "/templates/compile",
    name: "Templates",
    component: CompileTemplates,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;
