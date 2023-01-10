<template>
  <div>
    <form @submit.prevent="addTemplate">
      <BasicCard>
        <template #title>
          <h2>Add Template</h2>
        </template>
        <template #body>
          <CustomTextarea
            title="Template"
            v-model:value="newTemplate"
            :disabled="false"
          />
        </template>
        <template #footer>
          <button class="primary">Add</button>
        </template>
      </BasicCard>
    </form>

    <div style="margin-top: 3rem">
      <simple-table
        :items="items"
        :headers="headers"
        title="Templates"
        :reducedPadding="true"
        :maxCount="state.FirebaseTemplates.length"
        defaultSortingKey="timestamp"
      />
    </div>

    <yes-no-modal
      @close="toggleDeleteModal(false)"
      @no="toggleDeleteModal(false)"
      @yes="deleteSelectedTemplate()"
      :show="showModal"
    >
      <template #title>Delete this template?</template>
      <template #body>
        <p style="margin-bottom: 1rem">Are you sure you want to delete:</p>
        <p>{{ selectedKeyValue }}</p>
      </template>
    </yes-no-modal>
    <edit-modal
      v-model:showEditModal="showEditModal"
      :selectedKey="selectedKey"
      @toggleEditModal="(b) => toggleEditModal(b)"
      @submit="onEditSubmited"
    />
  </div>
</template>

<script setup lang="ts">
import { useVuelidate } from "@vuelidate/core";
import { computed, ref } from "vue";
import CustomTextarea from "./customTextarea.vue";
import BasicCard from "@/components/BasicCard.vue";
import YesNoModal from "@/components/YesNoModal.vue";
import EditModal from "./editModal.vue";
import { FirebaseTemplate, Template } from "@/typings/Globals";
import { useStore } from "@/stores";
import router from "@/router";
import SimpleTable, {
  type Header,
  type Item,
} from "../../components/SimpleTable.vue";

const v$ = useVuelidate();
const state = useStore();

const items: Item = {
  data: computed(() => state.FirebaseTemplates),
  onItemClick: {
    event: (item: Record<string, unknown>) => {
      if (!FirebaseTemplate.isFirebaseTemplate(item))
        throw new Error("Type conversion failed");

      goToCompiledTemplateWithID(item.key);
    },
    title: () => "Compile this template",
  },
  buttons: [
    {
      innerText: "Edit",
      class: "primary",
      event: function (item: Record<string, unknown>): void {
        if (!FirebaseTemplate.isFirebaseTemplate(item))
          throw new Error("Type conversion failed");

        selectedKey.value = item.key;
        editTemplatePrompt(item.key);
      },
      title: () => "Edit this template",
    },
    {
      innerText: "Delete",
      class: "danger",
      event: function (item: Record<string, unknown>): void {
        if (!FirebaseTemplate.isFirebaseTemplate(item))
          throw new Error("Type conversion failed");

        selectedKey.value = item.key;
        deleteTemplatePrompt(item.key);
      },
      title: () => "Delete this template",
    },
  ],
};

const headers: Array<Header> = [
  {
    name: "value",
    text: "Template",
    searchable: true,
    sortable: true,
  },
  // TODO: fix formatting fist
  // {
  //   name: "timestamp",
  //   text: "Date",
  //   searchable: true,
  //   sortable: true,
  //   format: (timestamp) => {
  //     const date = (timestamp as Timestamp).toDate();
  //     var dateString =
  //       date.getUTCFullYear() +
  //       "/" +
  //       ("0" + (date.getUTCMonth() + 1)).slice(-2) +
  //       "/" +
  //       ("0" + date.getUTCDate()).slice(-2);

  //     return dateString;
  //   },
  // },
];

const newTemplate = ref("");
const showModal = ref(false);
const showEditModal = ref(false);
const selectedKey = ref("");
const selectedKeyValue = ref("");

function goToCompiledTemplateWithID(s: string): void {
  router.push({
    name: "compiledTemplate",
    query: {
      templateID: s,
    },
  });
}

async function addTemplate(): Promise<void> {
  const isValid = await v$.value.$validate();
  if (!isValid) {
    v$.value.$touch();
    return;
  }
  state.DATABASE_ADD_FIREBASE_TEMPLATE(new Template(newTemplate.value));
  resetForm();
}

function toggleDeleteModal(show: boolean) {
  showModal.value = show;
}

function toggleEditModal(show: boolean) {
  showEditModal.value = show;
}

function onEditSubmited(success: boolean, err?: string) {
  toggleEditModal(false);
  selectedKey.value = "";

  if (!success) {
    console.error(err ?? "empty Error");
  }
}

function editTemplatePrompt(key: string) {
  selectedKey.value = key;
  toggleEditModal(true);
}

function deleteTemplatePrompt(key: string) {
  selectedKey.value = key;
  selectedKeyValue.value =
    state.FirebaseTemplates.find(
      (template: FirebaseTemplate) => template.key === key
    )?.value ?? "ERROR: Not found!";
  toggleDeleteModal(true);
}

function deleteSelectedTemplate(): void {
  state.DATABASE_DELETE_FIREBASE_TEMPLATE(selectedKey.value);
  toggleDeleteModal(false);
}

function resetForm(): void {
  newTemplate.value = "";
  v$.value.$reset();
}
</script>
