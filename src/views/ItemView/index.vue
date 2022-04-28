<style lang="scss" scoped>
@import "@/assets/colors";

.sortable {
  cursor: pointer !important;
}

.searchbar {
  display: grid;
  flex-direction: row;

  label {
    grid-column: 1 / span 2;
    font-size: 1.2rem;
    align-self: center;
  }

  input {
    grid-column: 3 / span 2;
    width: 100%;
    height: 100%;
    padding: 0.5rem;
    border: none;
    outline: none;
    background: transparent;
    color: $white;

    &::placeholder {
      color: $light-gray;
    }
  }
}

.add-item-card {
  display: inline-block;
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
</style>

<template>
  <div>
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
            <div id="singularSameAsPlural" class="flex flex-row align-center ">
              <label class="checkbox">Same as Singular?
                <input type="checkbox" v-model="singularSameAsPlural">
                <div class="checkmark"></div>
              </label>
            </div>

            <input-validate id="type" title="Type" v-model:value="newItem.type" />
            <div id="keepTypeValue" class="flex flex-row align-center ">
              <label class="checkbox">Keep type?
                <input type="checkbox" v-model="keepTypeValue">
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
            <div class="counter" style="flex-shrink: 0;">({{ sortedFilteredItemsLength }} of {{
                state.FirebaseTemplateItems.length
            }})</div>
          </div>
        </template>
        <template #body>
          <table style="width: 100%">
            <thead>
              <tr>
                <th class="sortable" :class="sortArrowClass('singular')" scope="col" @click="sort('singular')">Singular
                </th>
                <th class="sortable" :class="sortArrowClass('plural')" scope="col" @click="sort('plural')">Plural</th>
                <th class="sortable" :class="sortArrowClass('type')" scope="col" @click="sort('type')">Types</th>
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
                <td>{{ item.type }}</td>
                <td class="table-action">
                  <button class="primary" @click="toggleEditModal(true)">Edit</button>
                  <button class="danger" @click="deleteItemPrompt(item.key)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </template>
      </BasicCard>
    </div>

    <!-- Modals -->
    <yes-no-modal :show="showModal" @close="toggleDeleteModal(false)" @no="toggleDeleteModal(false)"
      @yes="deleteSelectedKey()">
      <template #title>Delete the selected Item?</template>
      <template #body>
        <p style="margin-bottom: 1rem">This action would delete the following item:</p>
        <p>{{ elementToText }}</p>
      </template>
    </yes-no-modal>
    <ok-modal @close="toggleEditModal(false)" @ok="toggleEditModal(false)" :show="showEditModal">
      <template #title>Not implemented</template>
      <template #body>This feature isn't implemented yet!</template>
    </ok-modal>
  </div>
</template>

<script setup lang="ts">
import { useVuelidate } from "@vuelidate/core";
import { computed, Ref, ref, defineProps } from "vue";
import { FirebaseTemplateItem, TemplateItem } from "../../typings/Globals";
import InputValidate from "../../components/InputValidate.vue";
import BasicCard from "../../components/BasicCard.vue";
import OkModal from "../../components/OkModal.vue";
import YesNoModal from "../../components/YesNoModal.vue";
import { useStore } from "@/store";

const v$ = useVuelidate();
const state = useStore();
const props = defineProps({
  filterBy: {
    type: String,
    default: "",
  },
})

const newItem: Ref<TemplateItem> = ref(new TemplateItem());
const currentSortDir: Ref<"asc" | "desc"> = ref("asc");
const currentSortValue: Ref<"singular" | "plural" | "type"> = ref("singular");
const keepTypeValue: Ref<boolean> = ref(false);
const singularSameAsPlural: Ref<boolean> = ref(false);
const showModal: Ref<boolean> = ref(false);
const showEditModal: Ref<boolean> = ref(false);
const selectedKey: Ref<string> = ref("");

async function addType(): Promise<void> {
  const isValid: boolean = await v$.value.$validate();
  if (singularSameAsPlural.value)
    newItem.value.plural = newItem.value.singular;

  if (!isValid) {
    v$.value.$touch();
    return;
  }

  const templateItem = new TemplateItem(newItem.value.singular, newItem.value.plural, newItem.value.type);
  state.DATABASE_ADD_FIREBASE_TEMPLATE_ITEM(templateItem);
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
  state.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM(selectedKey.value);
  selectedKey.value = "";
  toggleDeleteModal(false);
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

//filter items by searchbar and arrows
const searchbarValue = ref("");
const sortedFilteredItems = computed(() => {
  const sortValue = currentSortValue.value;
  const sortDir = currentSortDir.value;

  const searchValue = searchbarValue.value.toLocaleLowerCase();
  const filter = props.filterBy.toLocaleLowerCase();

  return [...state.FirebaseTemplateItems]
    .filter(item => filter === "" || item.type.toLocaleLowerCase() == filter)
    .filter(item =>
    (
      item.singular.toLocaleLowerCase().includes(searchValue) ||
      item.plural.toLocaleLowerCase().includes(searchValue) ||
      item.type.toLocaleLowerCase().includes(searchValue)
    )
    )
    .sort((a, b) =>
      a[sortValue] <= b[sortValue]
        ? sortDir === "asc"
          ? -1
          : 1
        : sortDir === "asc"
          ? 1
          : -1
    );
});
const sortedFilteredItemsLength = computed(() => sortedFilteredItems.value.length);

function sortArrowClass(sortValue: "singular" | "plural" | "type"): string {
  return currentSortValue.value === sortValue
    ? currentSortDir.value === "asc"
      ? "sort-arrow-asc"
      : "sort-arrow-desc"
    : "";
}
</script>
