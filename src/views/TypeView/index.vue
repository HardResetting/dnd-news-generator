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
    <BasicCard :bodyPadding="false">
      <template #title>
        <h2>Types</h2>
      </template>
      <template #title-side> Showing X to Y of Z </template>
      <template #body>
        <table style="width: 100%">
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
                <button class="primary" @click="toggleEditModal(true)">Edit</button>
                <button class="danger" @click="deleteTypePrompt(type)">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </template>
    </BasicCard>

    <!-- Modals -->
    <yes-no-modal
      :show="showModal"
      @close="toggleModal(false)"
      @no="toggleModal(false)"
      @yes="deleteSelectedKey()"
    >
      <template #title
        >Delete all items with the type {{ this.selectedKey }}?</template
      >
      <template #body>
        <p style="margin-bottom: 1rem">
          This action would delete the following items:
        </p>
        <p v-for="element in itemsWithSelectedKey" v-bind:key="element.key">
          {{ element.singular }} / {{ element.plural }}
        </p>
      </template>
    </yes-no-modal>
    <ok-modal @close="toggleEditModal(false)" @ok="toggleEditModal(false)" :show="showEditModal">
      <template #title>Not implemented</template>
      <template #body>This feature isn't implemented yet!</template>
    </ok-modal>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { ActionTypes, useStore } from "../../store/index";
import BasicCard from "../../components/BasicCard.vue";
import YesNoModal from "../../components/YesNoModal.vue";
import OkModal from "../../components/OkModal.vue";
import { FirebaseTemplateItem } from "@/typings/Globals";

const store = useStore();

export default defineComponent({
  components: {
    BasicCard,
    YesNoModal,
    OkModal,
  },

  data() {
    return {
      currentSortDir: "asc",
      showModal: false,
      showEditModal: false,
      selectedKey: "",
    };
  },

  methods: {
    toggleModal(show: boolean) {
      this.showModal = show;
    },

    toggleEditModal(show: boolean) {
      this.showEditModal = show;
    },

    deleteTypePrompt(key: string): void {
      this.selectedKey = key;
      this.toggleModal(true);
    },

    deleteSelectedKey(): void {
      store.dispatch(
        ActionTypes.DATABASE_DELETE_FIREBASE_TEMPLATE_ITEM_WITH_TYPE,
        this.selectedKey
      );
      this.selectedKey = "";
      this.toggleModal(false);
    },

    sort() {
      // reverse
      this.currentSortDir = this.currentSortDir === "asc" ? "desc" : "asc";
    },
  },

  computed: {
    itemsWithSelectedKey(): FirebaseTemplateItem[] {
      return store.state.FirebaseTemplateItems.filter(
        (e) => e.type == this.selectedKey
      );
    },

    sortedTypes(): Array<string> | undefined {
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
</script>
