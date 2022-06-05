<style lang="scss" scoped>
@import "@/assets/colors";

.sortable {
  cursor: pointer !important;
}

.grid {
  display: grid;
  grid-template-columns: auto auto;
  grid-template-rows: 1fr 1fr 1fr;
  gap: 0px 0px;
  grid-template-areas:
    "singular ."
    "plural singularSameAsPlural"
    "type keepTypeValue";
}

#singular {
  grid-area: singular;
}

#plural {
  grid-area: plural;
}

#type {
  grid-area: type;
}

#singularSameAsPlural {
  grid-area: singularSameAsPlural;
  margin-bottom: 1rem;
}

#keepTypeValue {
  grid-area: keepTypeValue;
  margin-bottom: 1rem;
}

.add-item-card {
  display: block;
  margin-right: 3rem;
  height: fit-content;
}
</style>

<template>
  <div>
    <div class="flex flex-row justify-space-between">
      <form @submit.prevent="addType">
        <BasicCard class="add-item-card">
          <template #title>
            <h2>Add Item</h2>
          </template>
          <template #body>
            <div class="grid">
              <input-validate id="singular" title="Singular" v-model:value="newItem.singular" />
              <input-validate v-if="!singularSameAsPlural" id="plural" title="Plural" v-model:value="newItem.plural" />
              <input-validate v-else id="plural" title="Plural" v-model:value="newItem.singular" :disabled="true" />
              <div id="singularSameAsPlural" class="flex flex-row align-center">
                <label class="checkbox">Same as Singular?
                  <input type="checkbox" v-model="singularSameAsPlural" />
                  <div class="checkmark"></div>
                </label>
              </div>

              <input-validate-with-datalist id="type" title="Type" v-model:value="newItem.type"
                v-model:datalist="state.getFirebaseTemplateItemTypes" />
              <div id="keepTypeValue" class="flex flex-row align-center">
                <label class="checkbox">Keep type?
                  <input type="checkbox" v-model="keepTypeValue" />
                  <div class="checkmark"></div>
                </label>
              </div>
            </div>
          </template>
          <template #footer>
            <button class="primary">Add</button>
          </template>
        </BasicCard>
      </form>
      <BasicCard v-if="type" class="add-item-card">
        <template #title>
          <h2>Filter: On</h2>
        </template>
        <template #body> Items are filtered by Type: "{{ type }}" </template>
        <template #footer>
          <div class="flex row justify-center">
            <button class="primary" @click="goToItemsWithFilter('')">
              Cancel
            </button>
          </div>
        </template>
      </BasicCard>
    </div>

    <div style="margin-top: 3rem">
      <BasicCard :bodyPadding="false">
        <template #title>
          <h2>Items</h2>
        </template>
        <template #title-side>
          <div class="searchbar">
            <label for="searchInput">Search:</label>
            <input id="searchInput" v-model="searchbarValue" placeholder="Type here..." type="text"
              autocomplete="off" />
            <div class="counter" style="flex-shrink: 0">
              ({{ sortedFilteredItems.length }} of {{ state.FirebaseTemplateItems.length }})
            </div>
          </div>
        </template>
        <template #body>
          <table style="width: 100%">
            <thead>
              <tr>
                <th class="sortable" :class="sortArrowClass('singular')" scope="col" @click="sort('singular')">
                  Singular
                </th>
                <th class="sortable" :class="sortArrowClass('plural')" scope="col" @click="sort('plural')">
                  Plural
                </th>
                <th class="sortable" :class="sortArrowClass('type')" scope="col" @click="sort('type')">
                  Types
                </th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!sortedFilteredItems.length">
                <td colspan="4" class="noElements">No elements found!</td>
              </tr>
              <tr v-for="item in sortedFilteredItems" :key="item.key">
                <td>{{ item.singular }}</td>
                <td>{{ item.plural }}</td>
                <td class="clickable">
                  <router-link style="text-decoration: none;" :to="{ name: 'items', query: { type: item.type } }">{{
                      item.type
                  }}</router-link>
                </td>
                <td class="table-action">
                  <button class="primary" @click="editItemPrompt(item.key)">
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
    <yes-no-modal :show="showDeleteModal" @close="toggleDeleteModal(false)" @no="toggleDeleteModal(false)"
      @yes="deleteSelectedKey()">
      <template #title>Delete the selected Item?</template>
      <template #body>
        <p style="margin-bottom: 1rem">
          This action would delete the following item:
        </p>
        <p>{{ elementToText }}</p>
      </template>
    </yes-no-modal>
    <yes-no-modal :show="showEditModal" @close="toggleEditModal(false)" @no="toggleEditModal(false)"
      @yes="editSelectedKey" cancelText="Cancel" confirmText="Done"
      :confirmDisabled="JSON.stringify(selectedItem) === JSON.stringify(replacementItem)">
      <template #title>Edit template-item: "{{ selectedItem!.singular }}"?</template>
      <template #body>
        <form @submit.prevent="editSelectedKey">
          <input-validate v-model:value="replacementItem!.singular" :title="labelMessage(selectedItem!.singular)" />
          <input-validate v-model:value="replacementItem!.plural" :title="labelMessage(selectedItem!.plural)" />
          <input-validate v-model:value="replacementItem!.type" :title="labelMessage(selectedItem!.type)" />
        </form>
      </template>
    </yes-no-modal>
  </div>
</template>

<script setup lang="ts">
import { useVuelidate } from "@vuelidate/core";
import { computed, ref, type Ref } from "vue";
import { FirebaseTemplateItem, TemplateItem } from "../../typings/Globals";
import InputValidate from "../../components/InputValidate.vue";
import InputValidateWithDatalist from "../../components/InputValidateWithDatalist.vue";
import BasicCard from "../../components/BasicCard.vue";
import YesNoModal from "../../components/YesNoModal.vue";
import { useStore } from "@/stores";
import router from "@/router";

const v$ = useVuelidate();
const state = useStore();
const props = defineProps({
  type: {
    type: String,
    default: "",
  },
});


const newItem: Ref<TemplateItem> = ref(new TemplateItem());
const currentSortDir: Ref<"asc" | "desc"> = ref("asc");
const currentSortValue: Ref<"singular" | "plural" | "type"> = ref("singular");
const searchbarValue = ref("");
const keepTypeValue: Ref<boolean> = ref(false);
const singularSameAsPlural: Ref<boolean> = ref(false);
const showDeleteModal: Ref<boolean> = ref(false);
const showEditModal: Ref<boolean> = ref(false);
const selectedKey: Ref<string> = ref("");
const selectedItem: Ref<FirebaseTemplateItem | undefined> = ref(undefined);
const replacementItem: Ref<FirebaseTemplateItem | undefined> = ref(undefined);

function labelMessage(value: string) {
  return `Change from ${value} to:`;
}

function toggleDeleteModal(show: boolean) {
  showDeleteModal.value = show;
}

function toggleEditModal(show: boolean) {
  showEditModal.value = show;
}

function editItemPrompt(key: string): void {
  selectedKey.value = key;
  selectedItem.value = state.getFirebaseTemplateItem(key);
  replacementItem.value = new FirebaseTemplateItem(selectedItem.value!.key, selectedItem.value!.singular, selectedItem.value!.plural, selectedItem.value!.type);
  toggleEditModal(true);
}

async function addType(): Promise<void> {
  const isValid: boolean = await v$.value.$validate();
  if (singularSameAsPlural.value) newItem.value.plural = newItem.value.singular;

  if (!isValid) {
    v$.value.$touch();
    return;
  }

  const templateItem = new TemplateItem(
    newItem.value.singular,
    newItem.value.plural,
    newItem.value.type
  );
  state.DATABASE_ADD_FIREBASE_TEMPLATE_ITEM(templateItem);
  resetForm();
}

function goToItemsWithFilter(s: string): void {
  router.push({
    name: "items",
    params: {
      type: s,
    },
  });
}

function deleteItemPrompt(key: string): void {
  selectedKey.value = key;
  toggleDeleteModal(false);
  showDeleteModal.value = true;
}

function editSelectedKey(): void {
  console.dir(newItem.value);
}

function deleteSelectedKey(): void {
  state.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM(selectedKey.value);
  selectedKey.value = "";
  showDeleteModal.value = false;
}

function resetForm(): void {
  newItem.value.plural = "";
  newItem.value.singular = "";
  if (!keepTypeValue.value) {
    newItem.value.type = "";
  }

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

const sortedFilteredItems = computed(() => {
  const sortValue = currentSortValue.value;
  const sortDir = currentSortDir.value;


  const sortedArray = [...state.FirebaseTemplateItems]
    .filter(item => {
      const searchValue = searchbarValue.value.toLowerCase();
      return (props.type == "" || item.type == props.type) &&
        (
          item.singular
            .toLowerCase()
            .includes(searchValue)
          || item.plural
            .toLowerCase()
            .includes(searchValue)
          || item.key
            .toLowerCase()
            .includes(searchValue)
        )
    })
    .sort((a: FirebaseTemplateItem, b: FirebaseTemplateItem) => {
      switch (sortValue) {
        case "singular":
          return a.singular.localeCompare(b.singular);
        case "plural":
          return a.plural.localeCompare(b.plural);
        case "type":
          return a.type.localeCompare(b.type);
      }
    })
  if (sortDir === "desc") {
    sortedArray.reverse();
  }
  return sortedArray;
});

function sortArrowClass(sortValue: "singular" | "plural" | "type"): string {
  return currentSortValue.value === sortValue
    ? currentSortDir.value === "asc"
      ? "sort-arrow-asc"
      : "sort-arrow-desc"
    : "";
}
</script>
