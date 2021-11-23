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
          <h3 class="card-title">Add type</h3>
          <div class="d-flex p-4">
            <div>
              <div class="form-group">
                <label for="name">Value</label>
                <input
                  class="form-control"
                  v-bind:class="{ 'is-invalid': isInvalid }"
                  v-model.trim="newType"
                />
                <div class="invalid-feedback">
                  Only letters and underscores are allowed!
                </div>
              </div>
              <div class="form-group">
                <label for="name">Type</label>
                <input
                  class="form-control"
                  v-bind:class="{ 'is-invalid': isInvalid }"
                  v-model.trim="newType"
                />
                <div class="invalid-feedback">
                  Only letters and underscores are allowed!
                </div>
              </div>
              <button
                class="btn btn-primary mt-2"
                v-bind:class="{ disabled: isInvalid }"
                v-on:keyup.enter="addType"
                v-on:click="addType"
              >
                Add
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card m-5">
      <div class="card-body">
        <h3 class="card-title">TemplateItems</h3>
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="sortable" scope="col" @click="sort()">Singular</th>
              <th class="sortable" scope="col" @click="sort()">Plural</th>
              <th class="sortable" scope="col" @click="sort()">Types</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="type in sortedTypes" :key="type">
              <td>{{ type.singular }}</td>
              <td>{{ type.plural }}</td>
              <td>{{ type.type }}</td>
              <td>
                <button class="btn btn-primary me-2">Edit</button>
                <button class="btn btn-danger">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { db } from "../../services/FirestoreDb";
import {
  doc,
  setDoc,
  collection,
  query,
  where,
  DocumentData,
  onSnapshot,
  QueryDocumentSnapshot,
  SnapshotOptions,
} from "firebase/firestore";

// credit: Typescript documentation, src
// https://www.typescriptlang.org/docs/handbook/advanced-types.html#index-types
function getProperty<T, K extends keyof T>(o: T, propertyName: K): T[K] {
  return o[propertyName]; // o[propertyName] is of type T[K]
}

class TemplateItem {
  key: string;
  singular: string;
  plural: string;
  type: string;

  constructor(key: string, singular: string, plural: string, type: string) {
    this.key = key;
    this.singular = singular;
    this.plural = plural;
    this.type = type;
  }
}

const Component = defineComponent({
  name: "types",

  data() {
    return {
      templateItems: new Array<TemplateItem>(),
      newType: "",
      currentSortDir: "asc",
      isLoading: true,
    };
  },

  methods: {
    addType() {
      // if (!this.isInvalid) {
      //   console.log(this.newType);
      //   setDoc(doc(db, "types", this.newType), {
      //     value: this.newType,
      //   }).then(
      //     (value) => {
      //       this.types.push(this.newType);
      //       this.newType = "";
      //     },
      //     (reason) => {
      //       console.log(`Write Failed! Reason: ${reason}`);
      //     }
      //   );
      // }
    },
    sort(s: string) {
      // reverse
      this.currentSortDir = this.currentSortDir === "asc" ? "desc" : "asc";
    },
  },

  created() {
    const postConverter = {
      toFirestore(TemplateItem: TemplateItem): DocumentData {
        return {
          key: TemplateItem.key,
          singular: TemplateItem.singular,
          plural: TemplateItem.plural,
          type: TemplateItem.type
        };
      },
      fromFirestore(
        snapshot: QueryDocumentSnapshot,
        options: SnapshotOptions
      ): TemplateItem {
        const data = snapshot.data(options)!;
        return new TemplateItem(data.key, data.singular, data.plural, data.type);
      },
    };

    const q = query(collection(db, "templateItems")).withConverter(postConverter);
    const unsubscribe = onSnapshot(q, (querySnapshot) => {
      querySnapshot.forEach((doc) => {
        this.templateItems.push(doc.data());
      });
    });
    this.isLoading = false;
  },

  computed: {
    isInvalid(): boolean {
      return !(this.newType != "" && /[^A-Za-z_]/.test(this.newType));
    },
    sortedTypes(): Array<TemplateItem> | undefined {
      if (this.isLoading) return;

      return [...this.templateItems].sort((a: TemplateItem, b: TemplateItem) => {
        let modifier = this.currentSortDir === "asc" ? 1 : -1;

        var cur = a.singular;
        var next = b.plural;

        return (
          cur.localeCompare(next, undefined, { sensitivity: "accent" }) *
          modifier
        );
      });
    },
  },
});

export default Component;
</script>