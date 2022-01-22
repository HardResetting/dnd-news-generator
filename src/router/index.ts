import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import Home from "../views/Home.vue";
import Items from "../views/ItemView/index.vue";
import Types from "../views/TypeView/index.vue";
import Templates from "../views/TemplateView/index.vue";
import CompileTemplates from "../views/CompileTemplateView/index.vue";

const routes: Array<RouteRecordRaw> = [
  {
    path: "/",
    component: Home,
  },
  {
    path: "/items",
    component: Items,
  },
  {
    path: "/Types",
    component: Types,
  },

  {
    path: "/templates",
    component: Templates,
  },
  {
    path: "/templates/compile",
    component: CompileTemplates,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;
