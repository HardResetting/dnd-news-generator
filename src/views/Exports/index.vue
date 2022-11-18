<style lang="scss" scoped>
.add-item-card {
  display: inline-block;
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
      <button class="secondary" style="margin-top: 1rem" @click="exportAll">
        Import all
      </button>
      <button style="margin-top: 1rem" @click="exportItems">
        Import items
      </button>
      <button style="margin-top: 1rem" @click="exportTemplates">
        Import templates
      </button>
      <hr style="margin: 2rem 0 2rem 0" />
      <button
        class="danger"
        style="margin-top: 1rem"
        @click="toggleDeleteAllModal(true)"
      >
        Delete all
      </button>
      <button style="margin-top: 1rem" @click="exportItems">
        Delete items
      </button>
      <button style="margin-top: 1rem" @click="exportTemplates">
        Delete templates
      </button>
    </div>

    <yes-no-modal
      @close="toggleDeleteAllModal(false)"
      @no="toggleDeleteAllModal(false)"
      @yes="deleteAll()"
      :show="showDeleteAllModal"
      :confirm-disabled="true"
    >
      <template #title>Delete ALL items?</template>
      <template #body>
        <p style="margin-bottom: 1rem">This action is permanent!</p>
        <p style="margin-bottom: 1rem">
          Are you definetly sure you want to delete all items?
        </p>
        <input />
      </template>
    </yes-no-modal>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useStore } from "../../stores";
import YesNoModal from "@/components/YesNoModal.vue";

const store = useStore();
const showDeleteAllModal = ref(false);

function toggleDeleteAllModal(toggle: boolean) {
  showDeleteAllModal.value = toggle;
}

function getFormattedTime() {
  var today = new Date();
  var y = today.getFullYear();
  // JavaScript months are 0-based.
  var m = ("0" + (today.getMonth() + 1)).slice(-2);
  var d = today.getDate();

  return y + m + d;
}

function deleteAll() {
  console.log("called");
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
