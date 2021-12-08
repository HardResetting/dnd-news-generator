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
                v-model:value="newItem.singular"
                @keyup.enter="addType"
              />
              <input-validate
                title="Plural"
                v-model:value="newItem.plural"
                @keyup.enter="addType"
              />
              <input-validate
                title="Type"
                v-model:value="newItem.type"
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
        <h3 class="card-title">Items</h3>
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="sortable" scope="col" @click="sort('singular')">
                Singular
              </th>
              <th class="sortable" scope="col" @click="sort()">Plural</th>
              <th class="sortable" scope="col" @click="sort()">Types</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="type in sortedItems" :key="type">
              <td>{{ type.singular }}</td>
              <td>{{ type.plural }}</td>
              <td>{{ type.type }}</td>
              <td>
                <button class="btn btn-primary me-2">Edit</button>
                <button class="btn btn-danger" @click="deleteType(type.key)">
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
} from "firebase/firestore";
import { defineComponent } from "vue";
import InputValidate from "../../components/InputValidate.vue";
import { db } from "../../services/FirestoreDb";
import { FirebaseItem, Item } from "../../typings/Globals";
import * as bootstrap from "bootstrap";

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
      FirebaseItems: new Array<FirebaseItem>(),
      newItem: new Item(),
      currentSortDir: "asc",
      isLoading: true,
    };
  },

  methods: {
    addType(): void {
      this.v$.$validate().then((value) => {
        if (value) {
          const ref = collection(db, "templateItems").withConverter(
            FirebaseItem.converter
          );

          addDoc(ref, this.newItem).then(
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
          this.FirebaseItems.find((obj) => obj.key == key)?.singular
        }"?`
      );
      if (confirm) deleteDoc(doc(db, "templateItems", key));
    },
    resetForm(): void {
      this.newItem = new Item();
      this.v$.$reset();
    },
    sort(s: "singular" | "plural" | "type") {
      console.log(s);

      // reverse
      this.currentSortDir = this.currentSortDir === "asc" ? "desc" : "asc";
    },
  },

  created() {
    const q = query(collection(db, "templateItems")).withConverter(
      FirebaseItem.converter
    );
    const unsubscribe = onSnapshot(q, (querySnapshot) => {
      querySnapshot.docChanges().forEach((changes) => {
        switch (changes.type) {
          case "removed":
            this.FirebaseItems = this.FirebaseItems.filter(
              (obj) => obj.key != changes.doc.id
            );
            break;

          case "added":
            this.FirebaseItems.push(changes.doc.data());
            break;

          case "modified": {
            var index = this.FirebaseItems.findIndex(
              (obj) => obj.key == changes.doc.id
            );

            if (~index) {
              this.FirebaseItems[index] = changes.doc.data();
            }
          }
        }
      });
    });
    this.isLoading = false;
  },

  computed: {
    sortedItems(): Array<FirebaseItem> | undefined {
      if (this.isLoading) return;

      return [...this.FirebaseItems].sort(
        (a: FirebaseItem, b: FirebaseItem) => {
          let modifier = this.currentSortDir === "asc" ? 1 : -1;

          var cur = a.singular;
          var next = b.singular;

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
