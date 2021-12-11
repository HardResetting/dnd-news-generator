<style scoped>
.sortable {
  cursor: pointer !important;
}
</style>

<template>
  <div>
    <div class="card m-5">
      <div class="card-body">
        <h3 class="card-title">Types</h3>
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="sortable" scope="col" @click="sort()">Type</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="type in sortedTypes" :key="type">
              <td>{{ type }}</td>
              <td>
                <button class="btn btn-primary me-2">Edit</button>
                <button class="btn btn-danger" @click="deleteType(type)">
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
import { FirebaseTemplateItem, TemplateItem } from "@/typings/Globals";
import { defineComponent } from "vue";
import { ActionTypes, MutationTypes, useStore } from "../../store/index";

const store = useStore();

const Component = defineComponent({
  data() {
    return {
      Types: new Array<string>(),
      currentSortDir: "asc",
    };
  },

  methods: {
    deleteType(type: string): void {
      var confirm = window.confirm(`Delete ALL ITEMS with the type "${type}"? \nThis process can NOT be reverted!`);
      if (confirm)
        store.dispatch(
          ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM_WITH_TYPE,
          type
        );
    },
    sort(s: "singular" | "plural" | "type") {
      console.log(s);

      // reverse
      this.currentSortDir = this.currentSortDir === "asc" ? "desc" : "asc";
    },
  },

  computed: {
    types(): Array<string> {
      return store.state.FirebaseTemplateItems.map(function (item) {
        return item.type;
      }).filter((value, index, self) => {
        return self.indexOf(value) === index;
      });
    },
    sortedTypes(): Array<string> | undefined {
      if (!store.state.dataLoaded) return;
      return [...this.types].sort(
        (a: string, b: string) => {
          let modifier = this.currentSortDir === "asc" ? 1 : -1;

          return (
            a.localeCompare(b, undefined, { sensitivity: "accent" }) * modifier
          );
        }
      );
    },
  },
});

export default Component;
</script>
