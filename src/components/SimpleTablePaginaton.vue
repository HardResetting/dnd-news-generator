<script lang="ts" setup>
import { computed, watch } from "vue";

const pageNumberArrLLCRR = computed(() => [
  props.currentPage - 2,
  props.currentPage - 1,
  props.currentPage,
  props.currentPage + 1,
  props.currentPage + 2,
]);
const pageNumberArrRR = computed(() => [props.maxPage - 1, props.maxPage]);

const props = defineProps({
  maxPage: {
    type: Number,
    required: true,
  },
  currentPage: {
    type: Number,
    required: true,
  },
});

watch(
  () => props.maxPage,
  (number) => {
    if (number < props.currentPage) emit("changePage", number);
  }
);

const emit = defineEmits<{
  (e: "changePage", number: number): void;
}>();
</script>
<template>
  <div class="pagination">
    <button
      class="secondary"
      @click.prevent="emit('changePage', currentPage - 1)"
      :disabled="currentPage === 1"
    >
      <div class="arrow arrow-left"></div>
    </button>

    <div class="number-box" v-if="maxPage <= 10">
      <button
        v-for="index in maxPage"
        v-bind:key="index"
        class="secondary"
        :class="{ selected: index === currentPage }"
        @click="emit('changePage', index)"
      >
        {{ index }}
      </button>
    </div>

    <div class="number-box" v-else-if="currentPage < 7">
      <button
        v-for="index in 7"
        v-bind:key="index"
        class="secondary"
        :class="{ selected: index === currentPage }"
        @click="emit('changePage', index)"
      >
        {{ index }}
      </button>
      <span class="secondary seperator">&hellip;</span>
      <button
        v-for="index in pageNumberArrRR"
        v-bind:key="index"
        class="secondary"
        @click="emit('changePage', index)"
      >
        {{ index }}
      </button>
    </div>

    <div class="number-box" v-else-if="currentPage < maxPage - 5">
      <button class="secondary" @click="emit('changePage', 1)">{{ 1 }}</button>
      <button class="secondary" @click="emit('changePage', 2)">{{ 2 }}</button>

      <span class="secondary seperator">&hellip;</span>

      <button
        v-for="index in pageNumberArrLLCRR"
        v-bind:key="index"
        class="secondary"
        :class="{ selected: index === currentPage }"
        @click="emit('changePage', index)"
      >
        {{ index }}
      </button>

      <span class="secondary seperator">&hellip;</span>
      <button
        v-for="index in 2"
        v-bind:key="index"
        class="secondary"
        :class="{ selected: maxPage - 2 + index === currentPage }"
        @click="emit('changePage', maxPage - 2 + index)"
      >
        {{ maxPage - 2 + index }}
      </button>
    </div>

    <div class="number-box" v-else>
      <button
        v-for="index in 2"
        v-bind:key="index"
        class="secondary"
        @click="emit('changePage', index)"
      >
        {{ index }}
      </button>
      <span class="secondary seperator">&hellip;</span>
      <button
        v-for="index in 7"
        v-bind:key="index"
        class="secondary"
        :class="{ selected: maxPage - 7 + index === currentPage }"
        @click="emit('changePage', maxPage - 7 + index)"
      >
        {{ maxPage - 7 + index }}
      </button>
    </div>

    <button
      class="secondary"
      @click.prevent="emit('changePage', currentPage + 1)"
      :disabled="currentPage === maxPage"
    >
      <div class="arrow arrow-right"></div>
    </button>
  </div>
</template>
<style lang="scss" scoped>
@import "@/assets/colors";
$pagination-background-color: black;
$pagination-label-color: $white;
$pagination-label-background-color: $pagination-background-color;
$pagination-label-active-color: $white;
$pagination-label-active-background-color: darken(
  $pagination-background-color,
  15%
);
$pagination-label-disabled-color: $white;
$pagination-label-disabled-background-color: lighten(
  $pagination-background-color,
  15%
);

.pagination {
  background-color: $pagination-background-color;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: flex-end;
  padding: 5px 0;
  background-color: $table-header-background-color;

  .seperator {
    margin-bottom: 0;
    font-size: 150%;
    display: inline-block;
  }

  button {
    .arrow {
      display: inline-block;
      width: 8px;
      height: 8px;
      border-top: 2px solid $white;
      border-left: 2px solid $white;

      &.arrow-left {
        transform: rotate(-45deg);
      }

      &.arrow-right {
        transform: rotate(135deg);
      }
    }

    margin-right: 0.25rem;
    margin-left: 0.25rem;
  }
}
</style>
