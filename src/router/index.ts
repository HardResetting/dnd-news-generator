import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue';
import Items from "../views/ItemView/index.vue";
import Types from "../views/TypeView/index.vue";
import Templates from "../views/TemplateView/index.vue";
import CompileTemplates from "../views/CompileTemplateView/index.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [
    {
      path: "/",
      component: Home,
    },
    {
      path: "/items/:type?",
      component: Items,
      name: "items",
      props: true,
    },
    {
      path: "/types",
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
  ],
})

export default router
