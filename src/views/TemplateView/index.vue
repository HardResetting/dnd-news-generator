<style scoped>
.sortable {
  cursor: pointer !important;
}
</style>

<template>
  <div>
    <div class="d-flex flex-row">
      <div class="card m-5">
        <div class="card-body">
          <h3 class="card-title">Add Item</h3>
          <div class="d-flex p-4">
            <div>
              <input-validate
                title="Singular"
                v-model:value="newTemplate.singular"
                @keyup.enter="addType"
              />
              <input-validate
                title="Plural"
                v-model:value="newTemplate.plural"
                @keyup.enter="addType"
              />
              <input-validate
                title="Type"
                v-model:value="newTemplate.type"
                @keyup.enter="addType"
              />
              <button class="btn btn-primary mt-2" @click="addType">Add</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card m-5">
      <div class="card-body">
        <h3 class="card-title">Templates</h3>
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="sortable" scope="col" @click="sort()">
                Template
              </th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="template in sortedTemplates" :key="template.key">
              <td>{{ template.value }}</td>
              <td>
                <button class="btn btn-primary me-2">Edit</button>
                <button class="btn btn-danger" @click="deleteTemplate(template.key)">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { useVuelidate } from "@vuelidate/core";
import {
  addDoc,
  collection,
  deleteDoc,
  doc,
  onSnapshot,
  query,
  terminate,
} from "firebase/firestore";
import { defineComponent } from "vue";
import InputValidate from "../../components/InputValidate.vue";
import { db } from "../../services/FirestoreDb";
import { FirebaseTemplate, Template } from "../../typings/Globals";

const Component = defineComponent({
  name: "types",

  components: {
    InputValidate,
  },

  setup() {
    // this will collect all nested component’s validation results
    const v$ = useVuelidate();

    return { v$ };
  },

  data() {
    return {
      FirebaseTemplates: new Array<FirebaseTemplate>(),
      newTemplate: new Template(),
      currentSortDir: "asc",
      isLoading: true,
    };
  },

  methods: {
    addType(): void {
      this.v$.$validate().then((value) => {
        if (value) {
          const ref = collection(db, "templates").withConverter(
            FirebaseTemplate.converter
          );

          addDoc(ref, this.newTemplate).then(
            (value) => {
              this.resetForm();
            },
            (reason) => {
              console.log(`Write Failed! Reason: ${reason}`);
            }
          );
        }
      });
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
      this.newTemplate = new Template();
      this.v$.$reset();
    },
    sort(s: "singular" | "plural" | "type") {
      console.log(s);

      // reverse
      this.currentSortDir = this.currentSortDir === "asc" ? "desc" : "asc";
    },
  },

  created() {
    const q = query(collection(db, "templates")).withConverter(
      FirebaseTemplate.converter
    );
    const unsubscribe = onSnapshot(q, (querySnapshot) => {
      querySnapshot.docChanges().forEach((changes) => {
        switch (changes.type) {
          case "removed":
            this.FirebaseTemplates = this.FirebaseTemplates.filter(
              (obj) => obj.key != changes.doc.id
            );
            break;

          case "added":
            this.FirebaseTemplates.push(changes.doc.data());
            break;

          case "modified": {
            var index = this.FirebaseTemplates.findIndex(
              (obj) => obj.key == changes.doc.id
            );

            if (~index) {
              this.FirebaseTemplates[index] = changes.doc.data();
            }
          }
        }
      });
    });
    this.isLoading = false;
  },

  computed: {
    sortedTemplates(): Array<FirebaseTemplate> | undefined {
      if (this.isLoading) return;

      return [...this.FirebaseTemplates].sort(
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

export default Component;
</script>
