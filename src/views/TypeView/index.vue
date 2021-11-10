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
                <label for="name">value</label>
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
                @click="addType"
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
        <h3 class="card-title">Values</h3>
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="sortable" scope="col" @click="sort('value')">Value</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <TypesTabledata
              v-for="type in sortedTypes"
              :key="type.key"
              :type="type"
            ></TypesTabledata>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { db } from "../../services/FirestoreDb";
import { IPlainObject } from "../../../typings/Globals";
import TypesTabledata from "./TypesTabledata.vue";
import { doc, collection, getDocs, addDoc } from "firebase/firestore";

// credit: Typescript documentation, src
// https://www.typescriptlang.org/docs/handbook/advanced-types.html#index-types
function getProperty<T, K extends keyof T>(o: T, propertyName: K): T[K] {
  return o[propertyName]; // o[propertyName] is of type T[K]
}

const Component = defineComponent({
  name: "types",

  components: {
    TypesTabledata,
  },

  data() {
    return {
      types: new Array<IPlainObject>(),
      newType: "",
      currentSort: "value",
      currentSortDir: "asc",
      isLoading: true,
    };
  },

  methods: {
    addType() {
      if (!this.isInvalid) {
        addDoc(collection(db, "types"), {
          value: this.newType,
          items: [],
        }).then(
          (value) => {
            this.types.push({ key: value.id, value: this.newType });
            this.newType = "";
          },

          (reason) => {
            console.log(`Write Failed! Reason: ${reason}`);
          }
        );
      }
    },

    sort(s: string) {
      //if s == current sort, reverse
      if (s === this.currentSort) {
        this.currentSortDir = this.currentSortDir === "asc" ? "desc" : "asc";
      } else {
        this.currentSort = s;
        this.currentSortDir = "asc";
      }
    },
  },

  created() {
    getDocs(collection(db, "types")).then((querySnapshot: any) => {
      console.log(querySnapshot);

      querySnapshot.forEach((doc: any) => {
        this.types.push({
          key: doc.id,
          value: doc.data().value,
        });
      });
      this.isLoading = false;
    });
  },

  computed: {
    isInvalid(): boolean {
      return /[^A-Za-z_]/.test(this.newType);
    },
    sortedTypes(): IPlainObject[] {
      if(this.isLoading) return this.types;

      return [...this.types].sort((a: any, b: any) => {
        let modifier = this.currentSortDir === "asc" ? 1 : -1;

        var cur = a[this.currentSort];
        var next = b[this.currentSort];

        console.log(cur);
        
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