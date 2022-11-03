<style lang="scss" scoped>
#footer {
  display: flex;
  justify-content: flex-end;
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
        <button class="success" @click="$emit('yes')" :disabled="confirmDisabled">{{ confirmText }}</button>
        <button class="danger" @click="$emit('no')">{{ cancelText }}</button>
      </div>
    </template>
  </basic-modal>
</template>

<script setup lang="ts">
import BasicModal from "./BasicModal.vue";
import { defineEmits } from "@vue/runtime-core";

defineEmits(["yes", "no", "close"]);
defineProps({
  show: {
    type: Boolean,
    required: true
  },
  confirmText: {
    type: String,
    required: false,
    default: "Yes"
  },
  cancelText: {
    type: String,
    required: false,
    default: "No"
  },
  confirmDisabled: {
    type: Boolean,
    default: false,
  },
});
</script>
