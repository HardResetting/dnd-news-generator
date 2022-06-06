<template>
  <div class="home">
    <p style="margin-bottom: 2rem">
      Welcome to our random news generator built with
      <a href="https://vuejs.org" target="_blank">Vue.js</a> and
      <a href="http://www.typescriptlang.org/" target="_blank">TypeScript</a>.
    </p>
    <p style="margin-bottom: 2rem">
      This is still a work-in-progress and subject to change.
    </p>
    <simple-table :items="items" :headers="headers">

    </simple-table>
  </div>
</template>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>

<script setup lang="ts">
import SimpleTable from "@/components/SimpleTable.vue";
import type { Item, Header } from "@/components/SimpleTable.vue";
import { computed } from "vue";

const dataArr = new Array(45).fill(0).map((_, i) => ({
  id: i,
  name: `Item ${i}`,
  description: `Description ${i}`,
  price: i * 10,
}));

const items: Item = {
  data: computed(() => dataArr),
  onEditClick: function (item: Record<string, any>): void {
    throw new Error("Function not implemented.");
  },
  onDeleteClick: function (item: Record<string, any>): void {
    throw new Error("Function not implemented.");
  }
};

const headers: Array<Header> = [
  {
    name: "name",
    text: "Name",
    searchable: true,
    sortable: true,
  },
  {
    name: "description",
    text: "Description",
    searchable: true,
    sortable: true,
  },
  {
    name: "price",
    text: "Price",
    searchable: true,
    sortable: true,
    onItemClick: (item) => console.log(item)
    
  },
];


</script>
