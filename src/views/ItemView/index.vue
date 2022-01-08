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
    <form @submit.prevent="addType">
      <div class="card add-item-card">
        <h2 class="card-title">Add Item</h2>
          <div class="card-body">
            <input-validate title="Singular" v-model:value="newItem.singular" />
            <input-validate title="Plural" v-model:value="newItem.plural" />
            <input-validate title="Type" v-model:value="newItem.type" />
            <button class="btn btn-primary mt-2">Add</button>
          </div>
      </div>
    </form>

    <br /><br /><br />
    <div class="card">
      <div class="card-title flex flex-collumn">
        <h2>Items</h2>
        <div class="table-display">Showing 1 to 5 of 5</div>
      </div>

      <table>
        <thead>
          <tr>
            <th class="sortable" scope="col" @click="sort('singular')">
              Singular
            </th>
            <th class="sortable" scope="col" @click="sort()">Plural</th>
            <th class="sortable" scope="col" @click="sort()">Types</th>
            <th class="table-action" scope="col"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td v-if="!sortedItems.length" colspan="4" class="noElements">
              No elements in Database!
            </td>
          </tr>
          <tr v-for="type in sortedItems" :key="type">
            <td>{{ type.singular }}</td>
            <td>{{ type.plural }}</td>
            <td>{{ type.type }}</td>
            <td>
              <button>Edit</button>
              <button @click="deleteType(type.key)">Delete</button>
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
import InputValidate from "../../components/InputValidate.vue";
import { FirebaseTemplateItem, TemplateItem } from "../../typings/Globals";

const store = useStore();

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
      newItem: new TemplateItem(),
      currentSortDir: "asc",
      isLoading: true,
    };
  },

  methods: {
    addType(): void {
      this.v$.$validate().then((value) => {
        if (value) {
          store.dispatch(
            ActionTypes.DATABASE_ADD_FIREBASE_TEMPLATE_ITEM,
            this.newItem
          );
          this.resetForm();
        }
      });
    },
    deleteType(key: string): void {
      var confirm = window.confirm(
        `Delete "${
          store.state.FirebaseTemplateItems.find((obj) => obj.key == key)
            ?.singular
        }"?`
      );
      if (confirm)
        store.dispatch(ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM, key);
    },
    resetForm(): void {
      this.newItem = new TemplateItem();
      this.v$.$reset();
    },
    sort(s: "singular" | "plural" | "type") {
      console.log(s);

      // reverse
      this.currentSortDir = this.currentSortDir === "asc" ? "desc" : "asc";
    },
  },

  computed: {
    sortedItems(): Array<FirebaseTemplateItem> | undefined {
      var x = new FirebaseTemplateItem("ss", "test1", "test1", "testType");
      var y = new FirebaseTemplateItem("ss", "test2", "test2", "testType");
      var z = new FirebaseTemplateItem("ss", "test3", "test3", "testType");
      var a = new FirebaseTemplateItem("ss", "test4", "test5", "otherTestType");
      return [x, y, z, a];
      // return [...store.state.FirebaseTemplateItems].sort(
      //   (a: FirebaseTemplateItem, b: FirebaseTemplateItem) => {
      //     let modifier = this.currentSortDir === "asc" ? 1 : -1;

      //     var cur = a.singular;
      //     var next = b.singular;

      //     return (
      //       cur.localeCompare(next, undefined, { sensitivity: "accent" }) *
      //       modifier
      //     );
      //   }
      // );
    },
  },
});

export default Component;
</script>