import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import Home from "../views/Home.vue";
import Items from "../views/ItemView/index.vue";
import Types from "../views/TypeView/index.vue";
import EditType from "../views/TypeView/edit.vue";
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
    path: "/Types",
    name: "Types",
    component: Types,
  },
  {
    path: "/Types/:oldName",
    name: "EditType",
    component: EditType,
    props: true,
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
console.log(router);

export default router;
