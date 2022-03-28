<template>
  <div>
    <BasicCard>
      <template #title>
        <h2>Raw Template</h2>
      </template>
      <template #title-side>
        <button @click="toggleEditModal(true)" class="primary" style="margin: 0">Edit</button>
      </template>
      <template #body>{{ template }}</template>
    </BasicCard>

    <CompiledTemplate :template="template" @done="running = false;" />

    <div class="flex flex-row justify-end">
      <button
        id="Recompile"
        class="success"
        @click="recompile"
        style="margin-right: 0; margin-top: 2rem"
        :disabled="running"
      >Redo same Template</button>
    </div>
    <ok-modal @close="toggleEditModal(false)" @ok="toggleEditModal(false)" :show="showEditModal">
      <template #title>Not implemented</template>
      <template #body>This feature isn't implemented yet!</template>
    </ok-modal>
  </div>
</template>

<script setup lang="ts">
import BasicCard from "../../components/BasicCard.vue";
import OkModal from "@/components/OkModal.vue";
import { ref } from "@vue/reactivity";
import { useStore } from "@/store";
import CompiledTemplate from "./compiledTemplate.vue";
import { nextTick, onMounted, watch } from "vue";

const state = useStore();
const template = ref("Loading...");

onMounted(() => {
  if (!state.isLoading) {
    template.value = state.getRandomFirebaseTemplate();
    return;
  }

  const unwatch = watch(() => state.isLoading, (isLoading) => {
    if (isLoading)
      return;
    template.value = state.getRandomFirebaseTemplate();
    unwatch();
  });
})

const running = ref(false);
function recompile() {
  if (running.value)
    return;

  running.value = true;
  const temp = template.value;
  template.value = "";
  nextTick(() => template.value = temp);
}

const showEditModal = ref(false);
function toggleEditModal(show: boolean) {
  showEditModal.value = show;
}
</script>
