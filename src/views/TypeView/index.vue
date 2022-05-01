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
      <template #title-side>Showing X to Y of Z</template>
      <template #body>
        <table style="width: 100%">
          <thead>
            <tr>
              <th
                class="clickable"
                :class="sortArrowClass()"
                scope="col"
                @click="sort()"
              >
                Type
              </th>
              <th class="table-action" scope="col"></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td v-if="!sortedTypes.length" colspan="2" class="noElements">
                No elements in Database!
              </td>
            </tr>
            <tr v-for="itemType in sortedTypes" :key="itemType">
              <td
                class="clickable"
                :title="getTitle(itemType)"
                @click="goToItemsWithFilter(itemType)"
              >
                {{ itemType }}
              </td>
              <td>
                <button class="primary" @click="toggleEditModal(true)">
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
    <yes-no-modal
      :show="showModal"
      @close="toggleModal(false)"
      @no="toggleModal(false)"
      @yes="deleteSelectedKey()"
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
import { computed, ref } from "vue";
import BasicCard from "../../components/BasicCard.vue";
import YesNoModal from "../../components/YesNoModal.vue";
import OkModal from "../../components/OkModal.vue";
import { FirebaseTemplateItem } from "@/typings/Globals";
import { useStore } from "@/store";
import router from "@/router";

const state = useStore();
const currentSortDir = ref("asc");
const showModal = ref(false);
const showEditModal = ref(false);
const selectedKey = ref("");

function goToItemsWithFilter(s: string): void {
  router.push({
    name: "items",
    params: {
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

function deleteTypePrompt(key: string): void {
  (selectedKey.value = key), toggleModal(true);
}

function deleteSelectedKey(): void {
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

const sortedTypes = computed(() =>
  state.FirebaseTemplateItems.map((item: FirebaseTemplateItem) => item.type)
    .filter(
      (item: string, index: number, self: string[]) =>
        self.indexOf(item) === index
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
