<style lang="scss" scoped>
#footer {
  display: flex;
  justify-content: end;
}
</style>

<template>
  <basic-modal :show="show" @close="$emit('close')">
    <template #title>
      <slot name="title" />
    </template>
    <template #title-side>
      <slot name="title-side" />
    </template>
    <template #body>
      <slot name="body" />
    </template>
    <template #footer>
      <div id="footer">
        <button class="success" @click="$emit('yes')">Yes</button>
        <button class="danger" @click="$emit('no')">No</button>
      </div>
    </template>
  </basic-modal>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BasicModal from "./BasicModal.vue";

export default defineComponent({
  components: {
    BasicModal,
  },

  emits: ["yes", "no", "close"],

  props: {
    show: Boolean,
    title: String,
    text: String,
  },
});
</script>
