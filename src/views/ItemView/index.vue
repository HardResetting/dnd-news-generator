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
    <form @submit.prevent="addType">
      <BasicCard class="add-item-card">
        <template #title>
          <h2>Add Item</h2>
        </template>
        <template #body>
          <input-validate title="Singular" v-model:value="newItem.singular" />
          <input-validate title="Plural" v-model:value="newItem.plural" />
          <input-validate title="Type" v-model:value="newItem.type" />
        </template>
        <template #footer>
          <button class="primary">Add</button>
        </template>
      </BasicCard>
    </form>

    <div style="margin-top: 3rem">
      <BasicCard :bodyPadding="false">
        <template #title>
          <h2>Items</h2>
        </template>
        <template #title-side>Showing X to Y of Z</template>
        <template #body>
          <table style="width: 100%">
            <thead>
              <tr>
                <th
                  class="sortable"
                  :class="sortArrowClass('singular')"
                  scope="col"
                  @click="sort('singular')"
                >
                  Singular
                </th>
                <th
                  class="sortable"
                  :class="sortArrowClass('plural')"
                  scope="col"
                  @click="sort('plural')"
                >
                  Plural
                </th>
                <th
                  class="sortable"
                  :class="sortArrowClass('type')"
                  scope="col"
                  @click="sort('type')"
                >
                  Types
                </th>
                <th class="table-action" scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td v-if="!sortedItems.length" colspan="4" class="noElements">
                  No elements in Database!
                </td>
              </tr>
              <tr v-for="item in sortedItems" :key="item.key">
                <td>{{ item.singular }}</td>
                <td>{{ item.plural }}</td>
                <td>{{ item.type }}</td>
                <td>
                  <button class="primary" @click="toggleEditModal(true)">
                    Edit
                  </button>
                  <button class="danger" @click="deleteItemPrompt(item.key)">
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </template>
      </BasicCard>
    </div>

    <!-- Modals -->
    <yes-no-modal
      :show="showModal"
      @close="toggleDeleteModal(false)"
      @no="toggleDeleteModal(false)"
      @yes="deleteSelectedKey()"
    >
      <template #title>Delete the selected Item?</template>
      <template #body>
        <p style="margin-bottom: 1rem">
          This action would delete the following item:
        </p>
        <p>{{ elementToText }}</p>
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
import { useVuelidate } from "@vuelidate/core";
import { computed, Ref, ref } from "vue";
import { FirebaseTemplateItem, TemplateItem } from "../../typings/Globals";
import InputValidate from "../../components/InputValidate.vue";
import BasicCard from "../../components/BasicCard.vue";
import OkModal from "../../components/OkModal.vue";
import YesNoModal from "../../components/YesNoModal.vue";
import { useStore } from "@/store";

const v$ = useVuelidate();
const state = useStore();

const newItem = ref(new TemplateItem());
const currentSortDir: Ref<"asc" | "desc"> = ref("asc");
const currentSortValue: Ref<"singular" | "plural" | "type"> = ref("singular");
const showModal = ref(false);
const showEditModal = ref(false);
const selectedKey = ref("");

async function addType(): Promise<void> {
  const isValid: boolean = await v$.value.$validate();
  if (!isValid) {
    v$.value.$touch();
    return;
  }

  resetForm();
}

function toggleDeleteModal(show: boolean) {
  showModal.value = show;
}

function toggleEditModal(show: boolean) {
  showEditModal.value = show;
}

function deleteItemPrompt(key: string): void {
  selectedKey.value = key;
  toggleDeleteModal(true);
}

function deleteSelectedKey(): void {
  selectedKey.value = "";
  toggleDeleteModal(false);
}
function resetForm(): void {
  newItem.value = new TemplateItem();
  v$.value.$reset();
}
function sort(s: "singular" | "plural" | "type") {
  if (currentSortValue.value === s) {
    currentSortDir.value = currentSortDir.value === "asc" ? "desc" : "asc";
  } else {
    currentSortValue.value = s;
    currentSortDir.value = "asc";
  }
}

const elementToText = computed(() => {
  const item: FirebaseTemplateItem | undefined =
    state.FirebaseTemplateItems.find((e) => e.key == selectedKey.value);
  return item != null
    ? `${item.singular} / ${item.plural} of type "${item.type}"`
    : "Could not find element!";
});

const sortedItems = computed(
  (): Array<FirebaseTemplateItem> =>
    state.FirebaseTemplateItems != null
      ? [...state.FirebaseTemplateItems].sort(
          (a: FirebaseTemplateItem, b: FirebaseTemplateItem) => {
            let modifier = currentSortDir.value === "asc" ? 1 : -1;

            var cur = a[currentSortValue.value];
            var next = b[currentSortValue.value];

            return (
              cur.localeCompare(next, undefined, { sensitivity: "accent" }) *
              modifier
            );
          }
        )
      : new Array<FirebaseTemplateItem>()
);

function sortArrowClass(sortValue: "singular" | "plural" | "type"): string {
  return currentSortValue.value === sortValue
    ? currentSortDir.value === "asc"
      ? "sort-arrow-asc"
      : "sort-arrow-desc"
    : "";
}
</script>
