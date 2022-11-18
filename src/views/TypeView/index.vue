<style lang="scss" scoped>
.add-item-card {
  display: inline-block;
}
</style>

<template>
  <div>
    <simple-table
      :items="items"
      :headers="headers"
      title="Types"
      :reducedPadding="true"
      :maxCount="state.FirebaseTemplateItems.length"
    />

    <!-- Modals -->
    <yes-no-modal
      :show="showDeleteTypeModal"
      @close="toggleDeleteTypeModal(false)"
      @no="toggleDeleteTypeModal(false)"
      @yes="deleteTypeWithSelectedKey()"
    >
      <template #title
        >Delete all items with the type {{ selectedKey }}?</template
      >
      <template #body>
        <p style="margin-bottom: 1rem">
          This action would delete the following items:
        </p>
        <p v-for="element in itemsWithSelectedKey" v-bind:key="element.key">
          {{ element.singular }} / {{ element.plural }}
        </p>
      </template>
    </yes-no-modal>
    <yes-no-modal
      :show="showEditTemplatesPromptModal"
      @no="
        toggleEditTemplatesPromptModal(false);
        selectedKey = '';
      "
      @yes="editTemplatesWithSelectedKey()"
      cancel-text="No"
      confirm-text="Yes"
    >
      <template #title>Edit templates using type {{ selectedKey }}?</template>
      <template #body>
        <p style="margin-bottom: 1rem">
          Do you want to change the type in all templates it occours?
        </p>
        <p style="margin-bottom: 1rem">
          E.g.: If you change "Type1" to "Type2" then the template "[Type1]
          world!" becomes "[Type2] world!"
        </p>
      </template>
    </yes-no-modal>
    <yes-no-modal
      :show="showDeleteTemplatesPromptModal"
      @no="
        toggleDeleteTemplatesPromptModal(false);
        selectedKey = '';
      "
      @yes="deleteTemplatesWithSelectedKey()"
      cancel-text="No"
      confirm-text="Yes"
    >
      <template #title>Delete templates using type {{ selectedKey }}?</template>
      <template #body>
        <p style="margin-bottom: 1rem">
          Do you want to delete ALL templates where {{ selectedKey }} occours?
        </p>
      </template>
    </yes-no-modal>
    <yes-no-modal
      :show="showEditTypePromptModal"
      @close="toggleEditTypePromptModal(false)"
      @no="toggleEditTypePromptModal(false)"
      @yes="
        toggleEditTypeModal(true);
        toggleEditTypePromptModal(false);
        newType = selectedKey;
      "
    >
      <template #title>Edit name of the type {{ selectedKey }}?</template>
      <template #body>
        <p style="margin-bottom: 1rem">
          This action would affect the following items:
        </p>
        <div style="max-height: 50vh; overflow: auto">
          <p v-for="element in itemsWithSelectedKey" v-bind:key="element.key">
            {{ element.singular }} / {{ element.plural }}
          </p>
        </div>
      </template>
    </yes-no-modal>
    <yes-no-modal
      :show="showEditTypeModal"
      @close="toggleEditTypeModal(false)"
      @no="toggleEditTypeModal(false)"
      @yes="editTypeWithSelectedKey"
      cancelText="Cancel"
      confirmText="Done"
      :confirmDisabled="selectedKey == newType || v$.$invalid"
    >
      <template #title>Edit name of the type {{ selectedKey }}?</template>
      <template #body>
        <form @submit.prevent="editTypeWithSelectedKey">
          <input-validate v-model:value="newType" :title="newTypeLabel" />
        </form>
      </template>
    </yes-no-modal>
  </div>
</template>

<script setup lang="ts">
/* eslint-disable  @typescript-eslint/no-explicit-any */

import { computed, ref } from "vue";
import InputValidate from "@/components/InputValidate.vue";
import YesNoModal from "@/components/YesNoModal.vue";
import { useStore } from "@/stores";
import router from "@/router";
import { FirebaseTemplateItem, PlainObject } from "@/typings/Globals";
import type { Item, Header } from "@/components/SimpleTable.vue";
import SimpleTable from "@/components/SimpleTable.vue";
import useVuelidate from "@vuelidate/core";

const items: Item = {
  data: computed(() => state.getFirebaseTemplateItemTypes),
  onItemClick: {
    event: (item: Record<string, any>) =>
      goToItemsWithFilter((item as PlainObject).value),
    title: (item: Record<string, any>) =>
      `Go to all Items with the Type: '${(item as PlainObject).value}'`,
  },
  onDeleteClick: {
    event: function (item: Record<string, any>): void {
      const key = (item as any as PlainObject).value;

      selectedKey.value = key;
      deleteTypePrompt(key);
    },
    title: (item: Record<string, any>) =>
      `Delete items with type: '${(item as PlainObject).value}'`,
  },
  onEditClick: {
    event: function (item: Record<string, any>): void {
      const key = (item as any as PlainObject).value;

      selectedKey.value = key;
      editTypePrompt(key);
    },
    title: (item: Record<string, any>) =>
      `Edit items with type: '${(item as PlainObject).value}'`,
  },
};

const headers: Array<Header> = [
  {
    name: "value",
    text: "Type",
    searchable: true,
    sortable: true,
  },
];

const state = useStore();
const v$ = useVuelidate();

const showDeleteTypeModal = ref(false);
const showEditTypeModal = ref(false);
const showEditTypePromptModal = ref(false);
const showEditTemplatesPromptModal = ref(false);
const showDeleteTemplatesPromptModal = ref(false);
const selectedKey = ref("");
const newType = ref("");
const newTypeLabel = computed(() => `Change from ${selectedKey.value} to:`);

function goToItemsWithFilter(type: string): void {
  router.push({
    name: "items",
    query: {
      type: type,
    },
  });
}

function toggleDeleteTypeModal(show: boolean) {
  showDeleteTypeModal.value = show;
}

function toggleEditTypeModal(show: boolean) {
  showEditTypeModal.value = show;
}

function toggleEditTypePromptModal(show: boolean) {
  showEditTypePromptModal.value = show;
}

function toggleEditTemplatesPromptModal(show: boolean) {
  showEditTemplatesPromptModal.value = show;
}

function toggleDeleteTemplatesPromptModal(show: boolean) {
  showDeleteTemplatesPromptModal.value = show;
}

function editTypePrompt(key: string): void {
  selectedKey.value = key;
  toggleEditTypePromptModal(true);
}

function editTypeWithSelectedKey(): void {
  itemsWithSelectedKey.value.forEach((e) => {
    const newItem = new FirebaseTemplateItem(
      e.key,
      e.singular,
      e.plural,
      newType.value
    );

    state.DATABASE_UPDATE_FIREBASE_TEMPLATE_ITEM(e.key, newItem);
  });
  if (templatesWithSelectedKey.value.length > 0)
    toggleEditTemplatesPromptModal(true);
  else selectedKey.value = "";

  toggleEditTypeModal(false);
}

function editTemplatesWithSelectedKey(): void {
  templatesWithSelectedKey.value.forEach((e) => {
    e.value = e.value.replace(selectedKey.value, newType.value);
    state.DATABASE_UPDATE_FIREBASE_TEMPLATE(e.key, e.value);
  });
  selectedKey.value = "";
  toggleEditTemplatesPromptModal(false);
}

function deleteTemplatesWithSelectedKey(): void {
  templatesWithSelectedKey.value.forEach((e) => {
    state.DATABASE_DELETE_FIREBASE_TEMPLATE(e.key);
  });
  selectedKey.value = "";
  toggleEditTemplatesPromptModal(false);
}

function deleteTypePrompt(key: string): void {
  selectedKey.value = key;
  toggleDeleteTypeModal(true);
}

function deleteTypeWithSelectedKey(): void {
  itemsWithSelectedKey.value.forEach((e) =>
    state.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM(e.key)
  );

  if (templatesWithSelectedKey.value.length > 0)
    toggleDeleteTemplatesPromptModal(true);
  else selectedKey.value = "";

  toggleDeleteTypeModal(false);
}

const itemsWithSelectedKey = computed(() => {
  return state.getFirebaseTemplateItemsFilteredByType(selectedKey.value);
});

const templatesWithSelectedKey = computed(() => {
  return state.getFirebaseTemplatesFilteredByType(selectedKey.value);
});
</script>
