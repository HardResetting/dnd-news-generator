<template>
  <div v-if="!showModal">
    <NavigationHeader />
    <div class="app-content">
      <!-- show modal if visiting on mobile  -->

      <router-view />
    </div>
  </div>
  <ok-modal
    :show="showModal"
    @ok="showModal = false"
    @close="showModal = false"
    :fullscreen="true"
  >
    <template #title>
      <h1>Unsuported device!</h1>
    </template>
    <template #body>
      <p style="margin-bottom: 2rem">
        Sorry, this app is currently not supported on Mobile Devices
      </p>
      <p>
        s I recommend you use a computer or Tablet. If you still want to
        proceed, however, you can simply close this window.
      </p>
    </template>
  </ok-modal>
</template>

<script setup lang="ts">
import NavigationHeader from "./components/NavigationHeader.vue";
import { onMounted } from "@vue/runtime-core";
import { useStore } from "./stores/index";
import OkModal from "./components/OkModal.vue";
import { ref } from "vue";

const store = useStore();
const mobile = window.innerWidth < 768;

const showModal = ref(mobile);

onMounted(async () => {
  store.DATABASE_INIT_DATA_TEMPLATES();
  store.DATABASE_INIT_DATA_TEMPLATE_ITEMS();
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

.mobile-message {
  text-align: center;
  justify-content: center;
  align-items: center;
  height: 100vh;
  color: #fff;

  p {
    margin-top: 16px;
  }
}
</style>
