import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import Home from "../views/Home.vue";
import Items from "../views/ItemView/index.vue";
import Types from "../views/TypeView/index.vue";
import Templates from "../views/TemplateView/index.vue";
import CompileTemplates from "../views/CompileTemplateView/index.vue";

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
    path: "/types",
    name: "Types",
    component: Types,
  },
  {
    path: "/templates",
    name: "Templates",
    component: Templates,
  },
  {
    path: "/templates/compile",
    name: "TemplatesCompile",
    component: CompileTemplates,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;
