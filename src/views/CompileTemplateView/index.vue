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
      <template #body>{{ template }}</template>
    </BasicCard>

    <BasicCard style="margin-top: 4rem">
      <template #title>
        <h2>Compiled Template</h2>
      </template>
      <template #title-side>Done in {{ timetaken }}ms</template>
      <template #body>{{ compiledTemplate }}</template>
    </BasicCard>

    <div class="flex flex-row justify-end">
      <button
        id="Recompile"
        @click="runCompileScript"
        class="success"
        style="margin-right: 0; margin-top: 2rem"
      >
        Redo same Template
      </button>
    </div>
    <ok-modal
      @close="toggleEditModal(false)"
      @ok="toggleEditModal(false)"
      :show="showEditModal"
    >
      <template #title>Not implemented</template>
      <template #body>This feature isn't implemented yet!</template>
    </ok-modal>
  </div>
</template>

<script setup lang="ts">
import { compileTemplate } from "./templateCompiler";
import BasicCard from "../../components/BasicCard.vue";
import OkModal from "@/components/OkModal.vue";
import { store } from "@/store";
import { ref } from "@vue/reactivity";

const template = ref("");
const compiledTemplate = ref("Loading...");
const timetaken = ref(0);
const showEditModal = ref(false);

runCompileScript();

function toggleEditModal(show: boolean) {
  showEditModal.value = show;
}

async function runCompileScript() {
  template.value = store.getters.randomFirebaseTemplate;

  const parseObject = await compileTemplate(template.value);
  compiledTemplate.value = parseObject.errors.length
    ? parseObject.errors.length + " Error(s) occoured during the process.."
    : parseObject.result;
  timetaken.value = parseObject.performance;
}
</script>
