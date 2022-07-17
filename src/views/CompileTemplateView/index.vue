<template>
  <div>
    <BasicCard>
      <template #title>
        <h2>Raw Template</h2>
      </template>
      <template #title-side>
        <button @click="toggleEditModal(true)" class="primary" style="margin: 0">
          Edit
        </button>
      </template>
      <template #body>{{ template }}</template>
    </BasicCard>
    <div class="flex flex-row justify-end">
      <button class="success" @click="getNewTemplate" style="margin-right: 0; margin-top: 1rem" :disabled="running">
        Get new template
      </button>
    </div>

    <compiled-template :template="template" @done="running = false" />
    <div class="flex flex-row justify-end">
      <button class="success" @click="recompile" style="margin-right: 0; margin-top: 2rem" :disabled="running">
        Redo same template
      </button>
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
import { useStore } from "@/stores";
import CompiledTemplate from "./compiledTemplate.vue";
import { nextTick, onMounted, watch } from "vue";

const state = useStore();
const template = ref("[@name=[NPC_Name]] hat nun zum wiederholten Male dem Königlichen Befehl widersprochen und erschien nicht zur Anhörung. Somit ist auf [@name] ein Kopfgeld von [@zahl=[ran(1,10000)]] Gold ausgeschrieben! Ich wiederhole: Auf [@name] ist ein unglaubliches Kopfgeld von [@zahl] ausgeschrieben! Meldet euch mit dem Gefangenen bei der königlichen Garde! Für [@name] gibt es nur lebendig die Belohnung!");
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

function loadTemplate() {
  template.value =
    (props.templateID === "")
      ? state.getRandomFirebaseTemplate()
      : state.getFirebaseTemplate(props.templateID)?.value ?? "NO SUCH TEMPLATE";
}

function getNewTemplate() {
  if (state.isLoading)
    return;

  template.value = state.getRandomFirebaseTemplate();
}

const running = ref(false);
function recompile() {
  if (running.value) return;

  running.value = true;
  const temp = template.value;
  template.value = "";
  nextTick(() => (template.value = temp));
}

const showEditModal = ref(false);
function toggleEditModal(show: boolean) {
  showEditModal.value = show;
}
</script>
