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
              <input-validate :tabindex="3" ref="firstInput" id="singular" title="Singular"
                v-model:value="newItem.singular" />
              <input-validate :tabindex="4" v-if="!singularSameAsPlural" id="plural" title="Plural"
                v-model:value="newItem.plural" />
              <input-validate :tabindex="5" v-else id="plural" title="Plural" v-model:value="newItem.singular"
                :disabled="true" />
              <div id="singularSameAsPlural" class="flex flex-row align-center">
                <label class="checkbox">Same as Singular?
                  <input tabindex="1" type="checkbox" v-model="singularSameAsPlural" />
                  <div class="checkmark"></div>
                </label>
              </div>

              <input-validate-with-datalist :tabindex="6" id="type" title="Type" v-model:value="newItem.type"
                v-model:datalist="state.getFirebaseTemplateItemTypes" />
              <div id="keepTypeValue" class="flex flex-row align-center">
                <label class="checkbox">Keep type?
                  <input tabindex="2" type="checkbox" v-model="keepTypeValue" />
                  <div class="checkmark"></div>
                </label>
              </div>
            </div>
          </template>
          <template #footer>
            <button tabindex="7" type="submit" class="primary">Add</button>
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
            <button class="primary" @click="goToItemsWithFilter()">
              Cancel
            </button>
          </div>
        </template>
      </BasicCard>
    </div>

    <div style="margin-top: 3rem">
      <simple-table :items="items" :headers="headers" title="Items" :reducedPadding="true"
        :maxCount="state.FirebaseTemplateItems.length">
      </simple-table>
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
    <edit-modal v-model:showEditModal="showEditModal" :selectedKey="selectedKey"
      @toggleEditModal="(b) => toggleEditModal(b)" @submit="onEditSubmited" />
  </div>
</template>

<script setup lang="ts">
import { useVuelidate } from "@vuelidate/core";
import { computed, onMounted, ref, type Ref } from "vue";
import { FirebaseTemplateItem, TemplateItem } from "../../typings/Globals";
import InputValidate from "../../components/InputValidate.vue";
import InputValidateWithDatalist from "../../components/InputValidateWithDatalist.vue";
import SimpleTable from "../../components/SimpleTable.vue";
import BasicCard from "../../components/BasicCard.vue";
import YesNoModal from "../../components/YesNoModal.vue";
import { useStore } from "@/stores";
import router from "@/router";
import type { Item, Header } from "@/components/SimpleTable.vue.__VLS_script";
import EditModal from "./editModal.vue";

const firstInput = ref<InstanceType<typeof InputValidate> | null>(null)
function focus() {
  firstInput.value?.focus();
}

const v$ = useVuelidate();
const state = useStore();

const newItem: Ref<TemplateItem> = ref(new TemplateItem());
const keepTypeValue: Ref<boolean> = ref(false);
const singularSameAsPlural: Ref<boolean> = ref(false);
const showDeleteModal: Ref<boolean> = ref(false);
const showEditModal: Ref<boolean> = ref(false);
const selectedKey: Ref<string> = ref("");

const props = defineProps({
  type: {
    type: String,
    default: "",
  },
});

const items: Item = {
  data: computed(() => props.type == "" ? state.FirebaseTemplateItems : state.FirebaseTemplateItems.filter((item) => item.type == props.type)),
  onEditClick: function (item: Record<string, any>): void {
    const key = (item as any as FirebaseTemplateItem).key;


    selectedKey.value = key;
    toggleEditModal(true);
  },
  onDeleteClick: function (item: Record<string, any>): void {
    const key = (item as any as FirebaseTemplateItem).key;

    selectedKey.value = key;
    toggleDeleteModal(false);
    showDeleteModal.value = true;
  }
};

const headers: Array<Header> = [
  {
    name: "singular",
    text: "Singular",
    searchable: true,
    sortable: true,
  },
  {
    name: "plural",
    text: "Plural",
    searchable: true,
    sortable: true,
  },
  {
    name: "type",
    text: "Type",
    searchable: true,
    sortable: true,
    onItemClick: (item) => goToItemsWithFilter((item as any as FirebaseTemplateItem).type)
  },
];

function toggleDeleteModal(show: boolean) {
  showDeleteModal.value = show;
}

function toggleEditModal(show: boolean) {
  showEditModal.value = show;
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

function goToItemsWithFilter(s?: string): void {
  if (typeof (s) === "undefined") {
    router.push({ name: "items" });
  } else {
    router.push({ name: "items", query: { type: s } });
  }
}

function onEditSubmited(success: boolean, err?: String) {
  toggleEditModal(false);
  if (!success) {
    console.error(err ?? "empty Error");
  }
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
  focus();
}

const elementToText = computed(() => {
  const item: FirebaseTemplateItem | undefined =
    state.FirebaseTemplateItems.find((e) => e.key == selectedKey.value);
  return item != null
    ? `${item.singular} / ${item.plural} of type "${item.type}"`
    : "Could not find element!";
});
</script>
