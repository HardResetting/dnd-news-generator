<style lang="scss" scoped>
@import "../assets/colors";

header {
  z-index: 99;
  background-color: $navbar-background;
  display: flex;
  width: 100%;
  margin: 0 auto;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;

  .branding {
    border-radius: 0 20px 20px 0;
    padding: 24px 40px;
    width: auto;
    color: $white;

    text-decoration: none;
  }

  .nav-link {
    display: block;
    padding: 1rem 1.5rem;
    color: $white;
    text-decoration: none;
    transition: background-color 0.15s ease-in-out;

    &.unimportant {
      margin-left: auto;
    }

    &:focus,
    &:hover,
    &.router-link-active {
      background-color: #302055;
    }
  }
}
</style>

<template>
  <header class="flex flex-row" style="padding: 0">
    <router-link class="nav-link" to="/">Home</router-link>
    <router-link class="nav-link" to="/items">Items</router-link>
    <router-link class="nav-link" to="/types">Types</router-link>
    <router-link class="nav-link" to="/templates">Templates</router-link>
    <router-link v-if="!routeIsCompile" class="nav-link" to="/templates/compile"
      >Compile random Templates</router-link
    >
    <router-link
      v-else
      class="nav-link"
      to="/templates/compile"
      @click.prevent="reload"
      >Compile random Templates
    </router-link>
    <router-link class="nav-link unimportant" to="/exports"
      >Exports</router-link
    >
  </header>
</template>

<script setup lang="ts">
import router from "@/router";
import { computed } from "vue";

// comfort feature for people who click the link in the navbar
const routeIsCompile = computed(
  () => router.currentRoute.value.name == "compiledTemplate"
);
function reload() {
  router.go(0);
}
</script>
