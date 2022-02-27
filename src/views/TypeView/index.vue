<style lang="scss" scoped>
.sortable {
  cursor: pointer !important;
}
.add-item-card {
  display: inline-block;
}
</style>

<template>
  <div>
    <div class="card">
      <div class="card-title flex flex-collumn">
        <h2>Types</h2>
        <div class="table-display">Showing X to Y of Z</div>
      </div>

      <table>
        <thead>
          <tr>
            <th class="sortable" scope="col" @click="sort()">Type</th>
            <th class="table-action" scope="col"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td v-if="!sortedTypes.length" colspan="2" class="noElements">
              No elements in Database!
            </td>
          </tr>
          <tr v-for="type in sortedTypes" :key="type">
            <td>{{ type }}</td>
            <td>
              <button class="primary">Edit</button>
              <button class="danger" @click="deleteType(type)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script lang="ts">
import { useVuelidate } from "@vuelidate/core";
import { defineComponent } from "vue";
import { ActionTypes, useStore } from "../../store/index";

const store = useStore();

const Component = defineComponent({
  setup() {
    // this will collect all nested component’s validation results
    const v$ = useVuelidate();

    return { v$ };
  },

  data() {
    return {
      currentSortDir: "asc",
      isLoading: true,
    };
  },

  methods: {
    deleteType(key: string): void {
      var confirm = window.confirm(
        `Delete all items with the type "${
          key
        }"?`
      );
      if (confirm)
        store.dispatch(ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM_WITH_TYPE, key);
    },

    sort() {
      // reverse
      this.currentSortDir = this.currentSortDir === "asc" ? "desc" : "asc";
    },
  },

  computed: {
    sortedTypes(): Array<string> | undefined{
      return [...store.getters.firebaseTemplateItemTypes].sort(
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
