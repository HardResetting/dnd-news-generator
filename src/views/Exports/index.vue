<style lang="scss" scoped>
.add-item-card {
  display: inline-block;
}
</style>

<template>
  <div class="flex flex-column">
    <button class="success" style="margin-top: 1rem;" @click="exportAll">Export all</button>
    <button style="margin-top: 1rem;" @click="exportItems">Export items</button>
    <button style="margin-top: 1rem;" @click="exportTemplates">Export templates</button>
  </div>
</template>

<script setup lang="ts">
import { useStore } from "../../stores";

const store = useStore();

function getFormattedTime() {
  var today = new Date();
  var y = today.getFullYear();
  // JavaScript months are 0-based.
  var m = ("0" + (today.getMonth() + 1)).slice(-2);
  var d = today.getDate();

  return y + m + d;
}

function exportAll() {
  if (!store.FirebaseTemplateItems.length || !store.FirebaseTemplates.length) {
    return;
  }

  const element = document.createElement("a");

  const filename = "exportAll";

  const fullFilename = `${filename}-${getFormattedTime()}.json`;

  const text = JSON.stringify(
    {
      FirebaseTemplateItems: store.FirebaseTemplateItems,
      FirebaseTemplates: store.FirebaseTemplates,
    }
  );
  element.setAttribute("href", "data:application/json;charset=utf-8," + encodeURIComponent(text));
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
  element.setAttribute("href", "data:application/json;charset=utf-8," + encodeURIComponent(text));
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
  element.setAttribute("href", "data:application/json;charset=utf-8," + encodeURIComponent(text));
  element.setAttribute("download", fullFilename);

  element.click();
}


</script>
