<template>
  <NavigationHeader />
  <div class="app-content">
    <router-view />
  </div>
</template>

<script setup lang="ts">
import { ActionTypes, useStore } from "./store/index";
import NavigationHeader from "./NavigationHeader.vue";
import { onMounted } from "@vue/runtime-core";

onMounted(() => {
  const store = useStore();
  store.dispatch(ActionTypes.DATABASE_INIT_DATA_TEMPLATE_ITEMS);
  store.dispatch(ActionTypes.DATABASE_INIT_DATA_TEMPLATES);
});
</script>

<style lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap");
@import "assets/main";

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}
#app {
  background-color: $app-background;
  min-height: 100vh;

  .app-content {
    padding: 2rem;
    flex: 1;
    position: relative;
  }
}
</style>
