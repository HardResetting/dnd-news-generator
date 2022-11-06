<style lang="scss" scoped>
.add-item-card {
  display: inline-block;
}
</style>

<template>
  <div>

    <simple-table :items="items" :headers="headers" title="Types" :reducedPadding="true"
      :maxCount="state.FirebaseTemplateItems.length" />

    <!-- Modals -->
    <yes-no-modal :show="showDeleteModal" @close="toggleDeleteModal(false)" @no="toggleDeleteModal(false)"
      @yes="deleteSelectedKey()">
      <template #title>Delete all items with the type {{ selectedKey }}?</template>
      <template #body>
        <p style="margin-bottom: 1rem">
          This action would delete the following items:
        </p>
        <p v-for="element in itemsWithSelectedKey" v-bind:key="element.key">
          {{ element.singular }} / {{ element.plural }}
        </p>
      </template>
    </yes-no-modal>
    <yes-no-modal :show="showEditPromptModal" @close="toggleEditPromptModal(false)" @no="toggleEditPromptModal(false)"
      @yes="toggleEditModal(true); toggleEditPromptModal(false); newType = selectedKey">
      <template #title>Edit name of the type {{ selectedKey }}?</template>
      <template #body>
        <p style="margin-bottom: 1rem">
          This action would affect the following items:
        </p>
        <p v-for="element in itemsWithSelectedKey" v-bind:key="element.key">
          {{ element.singular }} / {{ element.plural }}
        </p>
      </template>
    </yes-no-modal>
    <yes-no-modal :show="showEditModal" @close="toggleEditModal(false)" @no="toggleEditModal(false)"
      @yes="editSelectedKey" cancelText="Cancel" confirmText="Done" :confirmDisabled="selectedKey == newType">
      <template #title>Edit name of the type {{ selectedKey }}?</template>
      <template #body>
        <form @submit.prevent="editSelectedKey">
          <input-validate v-model:value="newType" :title="newTypeLabel" />
        </form>
      </template>
    </yes-no-modal>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import InputValidate from "@/components/InputValidate.vue";
import YesNoModal from "@/components/YesNoModal.vue";
import { useStore } from "@/stores";
import router from "@/router";
import { FirebaseTemplateItem, IPlainObject } from "@/typings/Globals";
import type { Item, Header } from "@/components/SimpleTable.vue";
import SimpleTable from "@/components/SimpleTable.vue";

const items: Item = {
  data: computed(() => state.getFirebaseTemplateItemTypes),
  onItemClick: {
    event: (item: Record<string, any>) => goToItemsWithFilter((item as IPlainObject).value),
    title: (item: Record<string, any>) => `Go to all Items with the Type: '${(item as IPlainObject).value}'`
  },
  onDeleteClick: {
    event: function (item: Record<string, any>): void {
      const key = (item as any as IPlainObject).value;

      selectedKey.value = key;
      deleteTypePrompt(key);
    },
    title: (item: Record<string, any>) => `Delete items with type: '${(item as IPlainObject).value}'`
  },
  onEditClick: {
    event: function (item: Record<string, any>): void {
      const key = (item as any as IPlainObject).value;

      selectedKey.value = key;
      editTypePrompt(key);
    },
    title: (item: Record<string, any>) => `Edit items with type: '${(item as IPlainObject).value}'`
  }
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
const showDeleteModal = ref(false);
const showEditModal = ref(false);
const showEditPromptModal = ref(false);
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


function toggleDeleteModal(show: boolean) {
  showDeleteModal.value = show;
}

function toggleEditModal(show: boolean) {
  showEditModal.value = show;
}

function toggleEditPromptModal(show: boolean) {
  showEditPromptModal.value = show;
}

function editTypePrompt(key: string): void {
  selectedKey.value = key;
  toggleEditPromptModal(true);
}

function editSelectedKey(): void {
  itemsWithSelectedKey.value.forEach(e => {
    const newItem = new FirebaseTemplateItem(e.key, e.singular, e.plural, newType.value);
    console.log(newItem);

    state.DATABASE_UPDATE_FIREBASE_TEMPLATE_ITEM(e.key, newItem);
  })
  selectedKey.value = "";
  toggleEditModal(false);
}

function deleteTypePrompt(key: string): void {
  (selectedKey.value = key);
  toggleDeleteModal(true);
}

function deleteSelectedKey(): void {
  itemsWithSelectedKey.value.forEach(e => state.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM(e.key));
  selectedKey.value = "";
  toggleDeleteModal(false);
}

const itemsWithSelectedKey = computed(() => {
  const items = state.FirebaseTemplateItems;
  return items.filter(
    (item: FirebaseTemplateItem) => item.type === selectedKey.value
  );
});

</script>
