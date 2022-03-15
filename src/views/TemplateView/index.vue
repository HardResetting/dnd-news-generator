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

    <br /><br /><br />
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
                <button class="primary">Edit</button>
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

<script lang="ts">
import { ActionTypes, MutationTypes, useStore } from "@/store";
import { useVuelidate } from "@vuelidate/core";
import { defineComponent } from "vue";
import CustomTextarea from "./_customTextarea.vue";
import BasicCard from "../../components/BasicCard.vue";
import { FirebaseTemplate } from "../../typings/Globals";
import YesNoModal from "@/components/YesNoModal.vue";

const store = useStore();

export default defineComponent({
  components: {
    CustomTextarea,
    BasicCard,
    YesNoModal,
  },

  setup() {
    // this will collect all nested component’s validation results
    const v$ = useVuelidate();

    return { v$ };
  },

  data() {
    return {
      FirebaseTemplates: new Array<FirebaseTemplate>(),
      newTemplate: "",
      currentSortDir: "asc",
      isLoading: true,
      showModal: false,
      selectedKey: "",
      selectedKeyValue: "",
    };
  },

  methods: {
    addTemplate(): void {
      store.dispatch(
        ActionTypes.DATABASE_ADD_FIREBASE_TEMPLATE,
        new FirebaseTemplate(this.newTemplate, this.newTemplate)
      );

      this.resetForm();
    },

    toggleModal(show: boolean) {
      this.showModal = show;
    },

    deleteTemplatePrompt(key: string) {
      console.log("here");
      this.selectedKey = key;
      this.selectedKeyValue =
        store.state.FirebaseTemplates.find((e) => e.key == this.selectedKey)
          ?.value || "ERROR";
      this.toggleModal(true);
    },

    deleteSelectedTemplate(): void {
      store.dispatch(
        ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE,
        this.selectedKey
      );
      this.toggleModal(false);
    },
    resetForm(): void {
      this.newTemplate = "";
      this.v$.$reset();
    },
    sort() {
      // reverse
      this.currentSortDir = this.currentSortDir === "asc" ? "desc" : "asc";
    },
  },

  computed: {
    sortedTemplates(): Array<FirebaseTemplate> | undefined {
      return [...store.state.FirebaseTemplates].sort(
        (a: FirebaseTemplate, b: FirebaseTemplate) => {
          let modifier = this.currentSortDir === "asc" ? 1 : -1;

          var cur = a.value;
          var next = b.value;

          return (
            cur.localeCompare(next, undefined, { sensitivity: "accent" }) *
            modifier
          );
        }
      );
    },
  },
});
</script>