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
      <BasicCard class="add-item-card">
        <template #title>
          <h2>Add Item</h2>
        </template>
        <template #body>
          <input-validate title="Singular" v-model:value="newItem.singular" />
          <input-validate title="Plural" v-model:value="newItem.plural" />
          <input-validate title="Type" v-model:value="newItem.type" />
        </template>
        <template #footer>
          <button class="primary">Add</button>
        </template>
      </BasicCard>
    </form>

    <div style="margin-top: 3rem">
      <BasicCard :bodyPadding="false">
        <template #title>
          <h2>Items</h2>
        </template>
        <template #title-side> Showing X to Y of Z </template>
        <template #body>
          <table style="width: 100%">
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
                  <button class="primary">Edit</button>
                  <button class="danger" @click="deleteItemPrompt(type.key)">
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </template>
      </BasicCard>
    </div>

    <!-- Modals -->
    <yes-no-modal
      :show="showModal"
      @close="toggleModal(false)"
      @no="toggleModal(false)"
      @yes="deleteSelectedKey()"
    >
      <template #title>Delete the selected Item?</template>
      <template #body>
        <p style="margin-bottom: 1rem">
          This action would delete the following item:
        </p>
        <p>{{ elementToText }}</p>
      </template>
    </yes-no-modal>
  </div>
</template>

<script lang="ts">
import { useVuelidate } from "@vuelidate/core";
import { defineComponent } from "vue";
import { ActionTypes, useStore } from "../../store/index";
import InputValidate from "../../components/InputValidate.vue";
import BasicCard from "../../components/BasicCard.vue";
import { FirebaseTemplateItem, TemplateItem } from "../../typings/Globals";
import YesNoModal from "../../components/YesNoModal.vue";

const store = useStore();

export default defineComponent({
  name: "types",

  components: {
    InputValidate,
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
      newItem: new TemplateItem(),
      currentSortDir: "asc",
      isLoading: true,
      showModal: false,
      selectedKey: "",
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

    toggleModal(show: boolean) {
      this.showModal = show;
    },

    deleteItemPrompt(key: string): void {
      this.selectedKey = key;
      this.toggleModal(true);
    },

    deleteSelectedKey(): void {
      store.dispatch(
        ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM,
        this.selectedKey
      );
      this.selectedKey = "";
      this.toggleModal(false);
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
    elementToText(): string {
      const element = store.state.FirebaseTemplateItems.find(e => e.key == this.selectedKey);
      return element != null ? `${element.singular} / ${element.plural} of type "${element.type}"` : "Could not find element!"
    },

    sortedItems(): Array<FirebaseTemplateItem> | undefined {
      return [...store.state.FirebaseTemplateItems].sort(
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
</script>
