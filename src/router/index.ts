import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
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
      path: "/items",
      component: Items,
      name: "items",
      props: route => ({ type: route.query.type })
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
      name: "compiledTemplate",
      props: route => ({ templateID: route.query.templateID })
    },
  ],
});

export default router;
