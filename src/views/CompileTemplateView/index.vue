<template>
  <div>
    <BasicCard>
      <template #title>
        <h2>Raw Template</h2>
      </template>
      <template #title-side>
        <button
          @click="toggleEditModal(true)"
          class="primary"
          style="margin: 0"
        >
          Edit
        </button>
      </template>
      <template #body>
        <div style="white-space: pre">
          {{ template?.value }}
        </div>
      </template>
    </BasicCard>
    <div class="flex flex-row justify-end">
      <button
        class="success"
        @click="getNewTemplate"
        style="margin-right: 0; margin-top: 1rem"
        :disabled="running"
      >
        Get new template
      </button>
    </div>

    <compiled-template
      :template="template?.value"
      @done="running = false"
      style="white-space: pre-line"
    />
    <div class="flex flex-row justify-end">
      <button
        class="success"
        @click="recompile"
        style="margin-right: 0; margin-top: 2rem"
        :disabled="running"
      >
        Redo same template
      </button>
    </div>

    <edit-modal
      v-model:showEditModal="showEditModal"
      :selectedKey="template?.key"
      @toggleEditModal="(b: boolean) => toggleEditModal(b)"
      @submit="onEditSubmited"
    />
  </div>
</template>

<script setup lang="ts">
/* eslint-disable @typescript-eslint/no-non-null-assertion */

import BasicCard from "../../components/BasicCard.vue";
import { ref } from "@vue/reactivity";
import { useStore } from "@/stores";
import CompiledTemplate from "./compiledTemplate.vue";
import { nextTick, onMounted, watch } from "vue";
import editModal from "../TemplateView/editModal.vue";
import { FirebaseTemplate } from "@/typings/Globals";

const state = useStore();
const template = ref<FirebaseTemplate>();
const props = defineProps({
  templateID: {
    type: String,
    default: "",
  },
});

onMounted(() => {
  if (!state.isLoading) {
    loadTemplate();
    return;
  }

  const unwatch = watch(
    () => state.isLoading,
    (isLoading) => {
      if (isLoading) return;
      loadTemplate();
      unwatch();
    }
  );
});

function loadTemplate(key?: string) {
  template.value =
    key === undefined || key === ""
      ? props.templateID === ""
        ? state.getRandomFirebaseTemplate()
        : state.getFirebaseTemplate(props.templateID) ??
          new FirebaseTemplate("", "NO SUCH TEMPLATE") // TODO: Rework to throw Error
      : state.getFirebaseTemplate(key);
}

function getNewTemplate() {
  if (state.isLoading) return;

  template.value = state.getRandomFirebaseTemplate();
}

const running = ref(false);
function recompile() {
  if (running.value) return;

  running.value = true;
  const temp = template.value;
  template.value = undefined;
  nextTick(() => (template.value = temp));
}

const showEditModal = ref(false);

function toggleEditModal(show: boolean) {
  showEditModal.value = show;
}

function onEditSubmited(success: boolean, err?: string) {
  toggleEditModal(false);

  if (!success) {
    console.error(err ?? "empty Error");
    return;
  }

  loadTemplate(template.value?.key ?? "");
}
</script>
