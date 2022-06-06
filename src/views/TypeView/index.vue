<style lang="scss" scoped>
.add-item-card {
  display: inline-block;
}
</style>

<template>
  <div>
    <BasicCard :bodyPadding="false">
      <template #title>
        <h2>Types</h2>
      </template>
      <template #title-side>
        <div class="searchbar">
          <label for="searchInput">Search:</label>
          <input id="searchInput" v-model="searchbarValue" placeholder="Type here..." type="text" autocomplete="off" />
          <div class="counter" style="flex-shrink: 0">
            ({{ sortedFilteredTypes.length }} of {{ state.getFirebaseTemplateItemTypes.length }})
          </div>
        </div>
      </template>
      <template #body>
        <table style="width: 100%">
          <thead>
            <tr>
              <th class="clickable" :class="sortArrowClass()" scope="col" @click="sort()">
                Type
              </th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td v-if="!sortedFilteredTypes.length" colspan="2" class="noElements">
                No elements in Database!
              </td>
            </tr>
            <tr v-for="itemType in sortedFilteredTypes" :key="itemType">
              <td class="clickable" :title="getTitle(itemType)" @click="goToItemsWithFilter(itemType)">
                {{ itemType }}
              </td>
              <td class="table-action">
                <button class="primary" @click="editTypePrompt(itemType)">
                  Edit
                </button>
                <button class="danger" @click="deleteTypePrompt(itemType)">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </template>
    </BasicCard>

    <!-- Modals -->
    <yes-no-modal :show="showModal" @close="toggleModal(false)" @no="toggleModal(false)" @yes="deleteSelectedKey()">
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
import BasicCard from "@/components/BasicCard.vue";
import YesNoModal from "@/components/YesNoModal.vue";
import { useStore } from "@/stores";
import router from "@/router";
import { FirebaseTemplateItem } from "@/typings/Globals";

const state = useStore();
const currentSortDir = ref("asc");
const showModal = ref(false);
const showEditModal = ref(false);
const showEditPromptModal = ref(false);
const selectedKey = ref("");
const searchbarValue = ref("");
const newType = ref("");
const newTypeLabel = computed(() => `Change from ${selectedKey.value} to:`);

function goToItemsWithFilter(s: string): void {
  router.push({
    name: "items",
    query: {
      type: s,
    },
  });
}


function toggleModal(show: boolean) {
  showModal.value = show;
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
  toggleModal(true);
}

function deleteSelectedKey(): void {
  itemsWithSelectedKey.value.forEach(e => state.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM(e.key));
  selectedKey.value = "";
  toggleModal(false);
}

function sort() {
  // reverse
  currentSortDir.value = currentSortDir.value === "asc" ? "desc" : "asc";
}

function getTitle(s: string): string {
  return `Go to all Items with the Type: '${s}'`;
}

function sortArrowClass() {
  return {
    "sort-arrow-asc": currentSortDir.value === "asc",
    "sort-arrow-desc": currentSortDir.value === "desc",
  };
}

const itemsWithSelectedKey = computed(() => {
  const items = state.FirebaseTemplateItems;
  return items.filter(
    (item: FirebaseTemplateItem) => item.type === selectedKey.value
  );
});

const sortedFilteredTypes = computed(() =>
  [...state.getFirebaseTemplateItemTypes].filter((item) =>
    // filter by searchbar value and return only items that have properties defined in keys
    item
      .toLowerCase()
      .includes(searchbarValue.value.toLowerCase())
  )
    .sort((a: string, b: string) => {
      if (currentSortDir.value === "asc") {
        return a.localeCompare(b);
      } else {
        return b.localeCompare(a);
      }
    })
);
</script>
