<style lang="scss" scoped>
@import "../assets/main";
.modal-background {
  z-index: 9999;
  display: block;
  padding-top: 100px;
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.5);
  transition: opacity 0.3s ease;
}

.modal-container {
  width: 40vw;
  margin: 0 auto;
}

.modal-body {
  margin: 20px 0;
}

.modal-default-button {
  float: right;
}

/*
 * Animation
 */

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}

.card-bounce-enter-active {
  animation: bounce-in 0.5s;
}
.card-bounce-leave-active {
  animation: bounce-in 0.5s reverse;
}
@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
  }
}
</style>

<template>
  <Teleport to="body">
    <div v-if="show" class="modal-background" @click="$emit('close')">
      <transition name="card-bounce" appear>
        <div class="modal-container" @click.stop>
          <BasicCard style="margin-top: 4rem">
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
              <slot name="footer" />
            </template>
          </BasicCard>
        </div>
      </transition>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import BasicCard from "./BasicCard.vue";
import { defineEmits, defineProps } from "@vue/runtime-core";

defineEmits(["close"]);
defineProps({
  show: Boolean,
});
</script>
