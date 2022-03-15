<template>
  <div class="home">
    <p style="margin-bottom: 2rem">
      Welcome to your new single-page application, built with
      <a href="https://vuejs.org" target="_blank">Vue.js</a> and
      <a href="http://www.typescriptlang.org/" target="_blank">TypeScript</a>.
    </p>

    <!-- Testing Modals -->
    <button @click="showModal = true">Show Modal Basic</button>
    <basic-modal :show="showModal" @close="showModal = false">
      <template #title>Huh! There's no way out it seems.</template>
      <template #body>Maybe try thinking outside the box?</template>
    </basic-modal>

    <button @click="showModal2 = true">Show Modal OK</button>
    <ok-modal :show="showModal2" @close="ignoreClose" @ok="ok">
      <template #title>Wow.. You just did that.</template>
      <template #body
        >You should never just click on random buttons dude. This could have
        unforeseeable consequences</template
      >
    </ok-modal>

    <button @click="showModal3 = true">Show Modal YesNo</button>
    <yes-no-modal :show="showModal3" @close="ignoreClose" @yes="yes" @no="no">
      <template #title>
        <h2>Oh realy?</h2>
      </template>
      <template #body>Do you realy want to do that?</template>
    </yes-no-modal>
  </div>
</template>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>

<script lang="ts">
import { defineComponent } from "vue";
import basicModal from "../components/BasicModal.vue";
import yesNoModal from "../components/YesNoModal.vue";
import okModal from "../components/OkModal.vue";

export default defineComponent({
  components: { basicModal, yesNoModal, okModal },

  methods: {
    ok() {
      console.log("Ok");
      this.showModal2 = false;
    },
    yes() {
      console.log("yeah!");
      this.showModal3 = false;
    },
    no() {
      console.log("nah..");
      this.showModal3 = false;
    },
    ignoreClose() {
      console.log("Use the damn buttons!");
    },
  },

  data() {
    return {
      showModal: false,
      showModal2: false,
      showModal3: false,
    };
  },
});
</script>
