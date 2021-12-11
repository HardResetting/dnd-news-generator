<style scoped>
.sortable {
  cursor: pointer !important;
}
</style>

<template>
  <div>
    <form @submit.prevent="addType">
      <div class="d-flex flex-row">
        <div class="card m-5">
          <div class="card-body">
            <h3 class="card-title">Add Item</h3>
            <div class="d-flex p-4">
              <div>
                <input-validate
                  title="Singular"
                  v-model:value="newItem.singular"
                />
                <input-validate
                  title="Plural"
                  v-model:value="newItem.plural"
                />
                <input-validate
                  title="Type"
                  v-model:value="newItem.type"
                />
                <button class="btn btn-primary mt-2">Add</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>

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
    log() {
      console.log();
    },
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
          store.getters.firebaseTemplateItems.find((obj) => obj.key == key)
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
      return [...store.getters.firebaseTemplateItems].sort(
        (a: FirebaseTemplateItem, b: FirebaseTemplateItem) => {
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
