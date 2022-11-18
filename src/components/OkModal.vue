<style lang="scss" scoped>
#footer {
  display: flex;
  justify-content: center;
}
</style>

<template>
  <div>
    <basic-modal :show="show" @close="$emit('close')" :fullscreen="fullscreen">
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
          <button class="primary" @click="$emit('ok')">OK</button>
        </div>
      </template>
    </basic-modal>
  </div>
</template>

<script setup lang="ts">
import BasicModal from "./BasicModal.vue";
import { defineEmits } from "@vue/runtime-core";

defineEmits(["ok", "close"]);
defineProps({
  show: {
    type: Boolean,
    required: true,
  },
  fullscreen: {
    type: Boolean,
    default: false,
  },
});
</script>
