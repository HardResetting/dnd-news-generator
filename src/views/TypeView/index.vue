<style lang="scss" scoped>
.sortable {
  cursor: pointer !important;
}
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
              <th class="sortable" scope="col" @click="sort()">Type</th>
              <th class="table-action" scope="col"></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td v-if="!sortedTypes.length" colspan="2" class="noElements">
                No elements in Database!
              </td>
            </tr>
            <tr v-for="type in sortedTypes" :key="type">
              <td>{{ type }}</td>
              <td>
                <button class="primary" @click="toggleEditModal(true)">
                  Edit
                </button>
                <button class="danger" @click="deleteTypePrompt(type)">
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
import { ActionTypes, useStore } from "../../store/index";
import BasicCard from "../../components/BasicCard.vue";
import YesNoModal from "../../components/YesNoModal.vue";
import OkModal from "../../components/OkModal.vue";
import { FirebaseTemplateItem } from "@/typings/Globals";

const store = useStore();

const currentSortDir = ref("asc");
const showModal = ref(false);
const showEditModal = ref(false);
const selectedKey = ref("");

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
  store.dispatch(
    ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM_WITH_TYPE,
    selectedKey.value
  );
  selectedKey.value = "";
  toggleModal(false);
}

function sort() {
  // reverse
  currentSortDir.value = currentSortDir.value === "asc" ? "desc" : "asc";
}

const itemsWithSelectedKey = computed((): FirebaseTemplateItem[] =>
  store.state.FirebaseTemplateItems.filter((e) => e.type == selectedKey.value)
);

const sortedTypes = computed(
  (): Array<string> =>
    [...store.getters.firebaseTemplateItemTypes].sort(
      (a: string, b: string) => {
        let modifier = currentSortDir.value === "asc" ? 1 : -1;

        return (
          a.localeCompare(b, undefined, { sensitivity: "accent" }) * modifier
        );
      }
    )
);
</script>
