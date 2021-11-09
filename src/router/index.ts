import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import Home from '../views/Home.vue';
import Types from '../views/TypeView/index.vue';
import Items from '../views/Items.vue';
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
  {
    path: "/types",
    name: "Types",
    component: Types,
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;
