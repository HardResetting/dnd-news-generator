<style scoped>
.sortable {
  cursor: pointer !important;
}
</style>

<template>
  <div>
    <form @submit.prevent="addTemplate">
      <BasicCard>
        <template #title>
          <h2>Add Template</h2>
        </template>
        <template #body>
          <CustomTextarea title="Template" v-model:value="newTemplate" />
        </template>
        <template #footer>
          <button class="primary">Add</button>
        </template>
      </BasicCard>
    </form>

    <br />
    <br />
    <br />
    <BasicCard :bodyPadding="false">
      <template #title>
        <h2>Templates</h2>
      </template>
      <template #title-side>Showing X to Y of Z</template>
      <template #body>
        <table style="width: 100%">
          <thead>
            <tr>
              <th class="sortable" scope="col" @click="sort()">Template</th>
              <th class="table-action" scope="col"></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td v-if="!sortedTemplates.length" colspan="4" class="noElements">
                No elements in Database!
              </td>
            </tr>
            <tr v-for="template in sortedTemplates" :key="template.key">
              <td>{{ template.value }}</td>
              <td>
                <!-- <button class="primary">Edit</button> -->
                <button
                  class="danger"
                  @click="deleteTemplatePrompt(template.key)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </template>
    </BasicCard>

    <yes-no-modal
      @close="toggleModal(false)"
      @no="toggleModal(false)"
      @yes="deleteSelectedTemplate()"
      :show="showModal"
    >
      <template #title>Delete this template?</template>
      <template #body>
        <p style="margin-bottom: 1rem">Are you sure you want to delete:</p>
        <p>{{ selectedKeyValue }}</p>
      </template>
    </yes-no-modal>
  </div>
</template>

<script setup lang="ts">
import { ActionTypes, useStore } from "@/store";
import { useVuelidate } from "@vuelidate/core";
import { computed, ref } from "vue";
import CustomTextarea from "./_customTextarea.vue";
import BasicCard from "@/components/BasicCard.vue";
import YesNoModal from "@/components/YesNoModal.vue";
import { FirebaseTemplate } from "@/typings/Globals";

const store = useStore();

const v$ = useVuelidate();

const newTemplate = ref("");
const currentSortDir = ref("asc");
const showModal = ref(false);
const selectedKey = ref("");
const selectedKeyValue = ref("");

async function addTemplate(): Promise<void> {
  const isValid = await v$.value.$validate();
  if (!isValid) {
    v$.value.$touch();
    return;
  }

  store.dispatch(
    ActionTypes.DATABASE_ADD_FIREBASE_TEMPLATE,
    new FirebaseTemplate(newTemplate.value, newTemplate.value)
  );

  resetForm();
}

function toggleModal(show: boolean) {
  showModal.value = show;
}

function deleteTemplatePrompt(key: string) {
  selectedKey.value = key;
  selectedKeyValue.value =
    store.state.FirebaseTemplates.find((e) => e.key == selectedKey.value)
      ?.value || "ERROR";
  toggleModal(true);
}

function deleteSelectedTemplate(): void {
  store.dispatch(
    ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE,
    selectedKey.value
  );
  toggleModal(false);
}

function resetForm(): void {
  newTemplate.value = "";
  v$.value.$reset();
}

function sort() {
  // reverse
  currentSortDir.value = currentSortDir.value === "asc" ? "desc" : "asc";
}

const sortedTemplates = computed(
  (): Array<FirebaseTemplate> =>
    store.state.FirebaseTemplates != null
      ? [...store.state.FirebaseTemplates].sort(
          (a: FirebaseTemplate, b: FirebaseTemplate) => {
            let modifier = currentSortDir.value === "asc" ? 1 : -1;

            var cur = a.value;
            var next = b.value;

            return (
              cur.localeCompare(next, undefined, { sensitivity: "accent" }) *
              modifier
            );
          }
        )
      : new Array<FirebaseTemplate>()
);
</script>
