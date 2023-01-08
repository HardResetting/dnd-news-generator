<style lang="scss" scoped>
@import "@/assets/colors";

.add-item-card {
  display: inline-block;
}

#deletionQuestion {
  display: flex;
  justify-content: start;

  input {
    margin-left: 1rem;
    flex: 1;
  }
}

.delete-warning {
  color: $danger;
}
</style>

<template>
  <div>
    <div class="flex flex-column" style="max-width: 800px">
      <button class="success" style="margin-top: 1rem" @click="exportAll">
        Export all
      </button>
      <button style="margin-top: 1rem" @click="exportItems">
        Export items
      </button>
      <button style="margin-top: 1rem" @click="exportTemplates">
        Export templates
      </button>
      <hr style="margin: 2rem 0 2rem 0" />
      <button
        class="secondary"
        style="margin-top: 1rem"
        @click="openFile('all')"
      >
        Import all
      </button>
      <button style="margin-top: 1rem" @click="openFile('items')">
        Import items
      </button>
      <button style="margin-top: 1rem" @click="openFile('templates')">
        Import templates
      </button>
      <hr style="margin: 2rem 0 2rem 0" />
      <button
        class="danger"
        style="margin-top: 1rem"
        @click="openDeleteModal('all')"
      >
        Delete all
      </button>
      <button style="margin-top: 1rem" @click="openDeleteModal('items')">
        Delete items
      </button>
      <button style="margin-top: 1rem" @click="openDeleteModal('templates')">
        Delete templates
      </button>
      <input
        type="file"
        ref="importFileInput"
        @change="importMethod"
        style="display: none"
      />
    </div>

    <yes-no-modal
      @close="toggleDeleteModal(false)"
      @no="toggleDeleteModal(false)"
      @yes="deleteMethod"
      :show="showDeleteModal"
      :confirm-disabled="deleteAllInputValue !== confirmationMessage"
    >
      <template #title>Delete ALL {{ deletedKeys }}?</template>
      <template #body>
        <p style="margin-bottom: 1rem">
          This action is <span class="delete-warning">permanent!</span>
        </p>
        <p style="margin-bottom: 1rem">
          Are you definetly sure you want to delete
          <span class="delete-warning">all {{ deletedKeys }}</span
          >?
        </p>
        <div id="deletionQuestion">
          <label for="deleteAllInput">Please write "I am sure"</label>
          <input
            name="deleteAllInput"
            v-model="deleteAllInputValue"
            autocomplete="off"
          />
        </div>
      </template>
    </yes-no-modal>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useStore } from "../../stores";
import YesNoModal from "@/components/YesNoModal.vue";
import {
  FirebaseTemplate,
  FirebaseTemplateItem,
  Template,
  TemplateItem,
} from "@/typings/Globals";

const store = useStore();
const showDeleteModal = ref(false);
const importFileInput = ref<HTMLInputElement | null>(null);
const importMethod = ref<() => void>();
const deletedKeys = ref("");
const deleteMethod = ref<() => void>();
const deleteAllInputValue = ref("");
const confirmationMessage = "I am sure";

function toggleDeleteModal(toggle: boolean) {
  showDeleteModal.value = toggle;
}

function getFormattedTime() {
  var today = new Date();
  var y = today.getFullYear();
  // JavaScript months are 0-based.
  var m = ("0" + (today.getMonth() + 1)).slice(-2);
  var d = today.getDate();

  return y + m + d;
}

function openDeleteModal(deleteType?: "all" | "items" | "templates") {
  switch (deleteType) {
    case "all":
      deletedKeys.value = "items and templates";
      deleteMethod.value = deleteAll;
      break;
    case "items":
      deletedKeys.value = "items";
      deleteMethod.value = deleteItems;
      break;
    case "templates":
      deletedKeys.value = "templates";
      deleteMethod.value = deleteTemplates;
      break;
  }

  toggleDeleteModal(true);
}
function deleteAll() {
  deleteTemplateItemsInStore();
  deleteTemplatesInStore();
}
function deleteItems() {
  deleteTemplateItemsInStore();
}
function deleteTemplates() {
  deleteTemplatesInStore();
}

function deleteTemplateItemsInStore() {
  store.FirebaseTemplateItems.forEach((fti) =>
    store.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM(fti.key)
  );
}

function deleteTemplatesInStore() {
  store.FirebaseTemplates.forEach((ft) =>
    store.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM(ft.key)
  );
}

function openFile(importType?: "all" | "items" | "templates") {
  switch (importType) {
    case "all":
      importMethod.value = importAll;
      break;
    case "items":
      importMethod.value = importItems;
      break;
    case "templates":
      importMethod.value = importTemplates;
      break;
  }

  importFileInput.value?.click();
}

async function importAll() {
  const fileText = (await importFileInput.value?.files?.item(0)?.text()) ?? "";
  const jsonObj = JSON.parse(fileText);

  const firebaseTemplateItems = jsonObj[
    "FirebaseTemplateItems"
  ] as FirebaseTemplateItem[];
  const firebaseTemplates = jsonObj["FirebaseTemplates"] as FirebaseTemplate[];

  addTemplateItemsToStore(firebaseTemplateItems);
  addTemplatesToStore(firebaseTemplates);
}
async function importItems() {
  const fileText = (await importFileInput.value?.files?.item(0)?.text()) ?? "";
  const jsonObj = JSON.parse(fileText);

  const firebaseTemplateItems = jsonObj[
    "FirebaseTemplateItems"
  ] as FirebaseTemplateItem[];

  addTemplateItemsToStore(firebaseTemplateItems);
}
async function importTemplates() {
  const fileText = (await importFileInput.value?.files?.item(0)?.text()) ?? "";
  const jsonObj = JSON.parse(fileText);

  const firebaseTemplates = jsonObj["FirebaseTemplates"] as FirebaseTemplate[];

  addTemplatesToStore(firebaseTemplates);
}

function addTemplateItemsToStore(items: FirebaseTemplateItem[]) {
  items.forEach((item) =>
    store.DATABASE_ADD_FIREBASE_TEMPLATE_ITEM(
      new TemplateItem(item.singular, item.plural, item.type)
    )
  );
}

function addTemplatesToStore(templates: FirebaseTemplate[]) {
  templates.forEach((template) =>
    store.DATABASE_ADD_FIREBASE_TEMPLATE(new Template(template.value))
  );
}

function exportAll() {
  if (!store.FirebaseTemplateItems.length || !store.FirebaseTemplates.length) {
    return;
  }

  const element = document.createElement("a");

  const filename = "exportAll";

  const fullFilename = `${filename}-${getFormattedTime()}.json`;

  const text = JSON.stringify({
    FirebaseTemplateItems: store.FirebaseTemplateItems,
    FirebaseTemplates: store.FirebaseTemplates,
  });
  element.setAttribute(
    "href",
    "data:application/json;charset=utf-8," + encodeURIComponent(text)
  );
  element.setAttribute("download", fullFilename);

  element.click();
}

function exportItems() {
  if (!store.FirebaseTemplateItems.length) {
    return;
  }

  const element = document.createElement("a");

  const filename = "exportItems";

  const fullFilename = `${filename}-${getFormattedTime()}.json`;

  const text = JSON.stringify(store.FirebaseTemplateItems);
  element.setAttribute(
    "href",
    "data:application/json;charset=utf-8," + encodeURIComponent(text)
  );
  element.setAttribute("download", fullFilename);

  element.click();
}

function exportTemplates() {
  if (!store.FirebaseTemplates.length) {
    return;
  }

  const element = document.createElement("a");

  const filename = "exportTemplates";

  const fullFilename = `${filename}-${getFormattedTime()}.json`;

  const text = JSON.stringify(store.FirebaseTemplateItems);
  element.setAttribute(
    "href",
    "data:application/json;charset=utf-8," + encodeURIComponent(text)
  );
  element.setAttribute("download", fullFilename);

  element.click();
}
</script>
