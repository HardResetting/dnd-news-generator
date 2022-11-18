import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Items from "../views/ItemView/index.vue";
import Types from "../views/TypeView/index.vue";
import Templates from "../views/TemplateView/index.vue";
import CompileTemplates from "../views/CompileTemplateView/index.vue";
import Exports from "../views/Exports/index.vue";

const router = createRouter({
  history: createWebHistory(),

  routes: [
    {
      path: "/",
      component: Home,
    },
    {
      path: "/items",
      component: Items,
      name: "items",
      props: (route) => ({ type: route.query.type }),
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
      props: (route) => ({ templateID: route.query.templateID }),
    },
    {
      path: "/exports",
      component: Exports,
      name: "exports",
    },
  ],
});

export default router;
