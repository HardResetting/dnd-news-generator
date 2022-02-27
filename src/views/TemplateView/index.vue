<style scoped>
.sortable {
  cursor: pointer !important;
}
</style>

<template>
<div>
    <form @submit.prevent="addTemplate">
      <div class="card add-item-card">
        <h2 class="card-title">Add Type</h2>
        <div class="card-body">
          <CustomTextarea title="Template" v-model:value="newTemplate" />
          <div class="flex flex-row justify-end">
            <button class="primary">Add</button>
          </div>
        </div>
      </div>
    </form>

    <br /><br /><br />
    <div class="card">
      <div class="card-title flex flex-collumn">
        <h2>Templates</h2>
        <div class="table-display">Showing X to Y of Z</div>
      </div>

      <table>
        <thead>
          <tr>
            <th class="sortable" scope="col" @click="sort()">
              Template
            </th>
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
              <button class="danger" @click="deleteType(Template.key)">Delete</button>
            </td>
          </tr> 
        </tbody>
      </table>
    </div>
  </div>
</template>

<script lang="ts">
import { MutationTypes, useStore } from "@/store";
import { useVuelidate } from "@vuelidate/core";
import { addDoc, collection, deleteDoc, doc } from "firebase/firestore";
import { defineComponent } from "vue";
import CustomTextarea from "./_customTextarea.vue"
import { db } from "../../store/FirestoreDb";
import { FirebaseTemplate, Template } from "../../typings/Globals";

const store = useStore();
      console.log(store);

export default defineComponent({
  name: "types",

  components: {
    CustomTextarea,
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
    };
  },

  methods: {
    addTemplate(): void {
      store.commit(MutationTypes.ADD_FIREBASE_TEMPLATE, new FirebaseTemplate(this.newTemplate, this.newTemplate))
      this.resetForm();
      // this.v$.$validate().then((value) => {
      //   if (value) {
      //     const ref = collection(db, "templates").withConverter(
      //       FirebaseTemplate.converter
      //     );

      //     addDoc(ref, new Template(this.newTemplate)).then(
      //       () => {
      //         this.resetForm();
      //       },
      //       (reason) => {
      //         console.log(`Write Failed! Reason: ${reason}`);
      //       }
      //     );
      //   }
      // });
    },
    deleteType(key: string): void {
      var confirm = window.confirm(
        `Delete "${
          this.FirebaseTemplates.find((obj) => obj.key == key)?.value
        }"?`
      );
      if (confirm) deleteDoc(doc(db, "templateItems", key));
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