<script lang="ts" setup>
export type Header = {
  name: string;
  text: string;
  searchable: boolean;
  sortable: boolean;
  format?: (item: unknown) => string;
};

export type Item = {
  // key value pair array
  data: ComputedRef<Array<Record<string, unknown>>>;
  onItemClick?: clickableProperties;
  buttons?: simpleTableButton[];
};

export type clickableProperties = {
  event: (item: Record<string, unknown>) => void;
  title?: (item: Record<string, unknown>) => string;
};

export type simpleTableButton = clickableProperties & {
  innerText: string;
  class?: string;
};

import { computed, ref, type ComputedRef, type PropType, type Ref } from "vue";
import BasicCard from "./BasicCard.vue";
import SimpleTablePaginaton from "./SimpleTablePaginaton.vue";

const props = defineProps({
  title: {
    type: String,
    default: "NO_TITLE",
  },
  headers: {
    type: Array as PropType<Array<Header>>,
    required: true,
  },
  items: {
    type: Object as PropType<Item>,
    required: true,
  },
  filterType: {
    type: String,
    default: "",
  },
  reducedPadding: {
    type: Boolean,
    default: false,
  },
  defaultSortingKey: {
    type: String,
    default: "",
  },
});

const anyActionDefined =
  props.items.buttons != undefined && props.items.buttons.length > 0;
const headerLength = anyActionDefined
  ? props.headers.length
  : props.headers.length;

const currentSortValue = ref(props.defaultSortingKey);
const currentSortDirection: Ref<"asc" | "desc"> = ref("desc");

const searchbarValue = ref("");

const sortedFilteredItems = computed(() => {
  const sortValue = currentSortValue.value;
  const sortDir = currentSortDirection.value;

  const sortedArray =
    sortValue === ""
      ? [...props.items.data.value]
      : [...props.items.data.value].sort(
          (a: Record<string, unknown>, b: Record<string, unknown>) =>
            (a[sortValue] as object)
              .toString()
              .localeCompare((b[sortValue] as object).toString()) *
            (sortDir === "asc" ? 1 : -1)
        );

  const filteredArray = sortedArray.filter((item: Record<string, unknown>) => {
    const ok = props.headers.some((header: Header) => {
      if (!header.searchable) return false;

      return (item[header.name] as string)
        .toString()
        .toLowerCase()
        .includes(searchbarValue.value.toLowerCase());
    });

    return ok;
  });

  return filteredArray;
});

const maxPage = computed(() =>
  sortedFilteredItems.value.length > 0
    ? Math.ceil(sortedFilteredItems.value.length / paginationBreakPoint)
    : 1
);
const sortedFilteredPaginatedItems = computed(() => {
  if (sortedFilteredItems.value.length == 0) return sortedFilteredItems.value;

  const start = (currentPage.value - 1) * paginationBreakPoint;
  const end = start + paginationBreakPoint;
  return sortedFilteredItems.value.slice(start, end);
});
const paginationBreakPoint = 10;
const currentPage = ref(1);

function sort(header: Header) {
  if (!header.sortable) return "";

  if (currentSortValue.value == header.name) {
    currentSortDirection.value =
      currentSortDirection.value == "asc" ? "desc" : "asc";
  } else {
    currentSortValue.value = header.name;
    currentSortDirection.value = "asc";
  }
}

function sortArrowClass(header: Header): string {
  return currentSortValue.value === header.name
    ? currentSortDirection.value === "asc"
      ? "sort-arrow-asc"
      : "sort-arrow-desc"
    : "";
}
</script>
<template>
  <BasicCard :bodyPadding="false">
    <template #title>
      <slot name="title">
        <h2>{{ title }}</h2>
      </slot>
    </template>
    <template #title-side>
      <div class="searchbar">
        <label for="searchInput">Search:</label>
        <input
          id="searchInput"
          placeholder="Type here..."
          v-model="searchbarValue"
          type="text"
          autocomplete="off"
        />
        <div class="counter" style="flex-shrink: 0">
          ( {{ sortedFilteredItems.length }} of {{ items.data.value.length }} )
        </div>
      </div>
    </template>
    <template #body>
      <table :class="{ 'reduced-padding': reducedPadding }" style="width: 100%">
        <thead>
          <tr>
            <th
              v-for="header in headers"
              :key="header.name"
              class="sortable"
              :class="sortArrowClass(header)"
              scope="col"
              @click="sort(header)"
            >
              {{ header.text }}
            </th>
            <th v-if="anyActionDefined" scope="col"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!sortedFilteredItems.length">
            <td
              :colspan="anyActionDefined ? headers.length + 1 : headers.length"
              class="noElements"
            >
              No elements found!
            </td>
          </tr>
          <tr
            v-for="item in sortedFilteredPaginatedItems"
            :key="(item.key as string)"
          >
            <td
              v-for="header in headers"
              v-bind:key="header.name"
              :class="{ clickable: items.onItemClick != undefined }"
              @click.prevent.stop="items.onItemClick?.event?.(item)"
              :title="items.onItemClick?.title?.(item)"
            >
              {{
                header.format != undefined
                  ? header.format(item[header.name])
                  : item[header.name]
              }}
            </td>
            <td v-if="anyActionDefined" class="table-action">
              <button
                v-for="button in items.buttons"
                v-bind:key="button.innerText"
                :class="button.class"
                :title="button.title?.(item)"
                @click="button.event(item)"
              >
                {{ button.innerText }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <simple-table-paginaton
        :maxPage="maxPage"
        :currentPage="currentPage"
        @changePage="(n) => (currentPage = n)"
      />
    </template>
  </BasicCard>
</template>

<style scoped lang="scss">
@import "@/assets/colors.scss";

.table-radio {
  display: none;

  &:checked + .card-title-side {
    display: block;
  }

  &:checked + .card-title-side + table {
    width: 100%;
    display: table;
  }

  &:checked + .card-title-side + table + .pagination {
    display: flex;

    & > label.active {
      color: $pagination-label-active-color;
      background-color: $pagination-label-active-background-color;
      cursor: default;
    }

    & > label.disabled {
      color: $pagination-label-disabled-color;
      background-color: $pagination-label-disabled-background-color;
      cursor: default;
    }
  }
}

table {
  &.reduced-padding tr th,
  &.reduced-padding tr td {
    padding: 0.5rem;
  }

  background-color: $table-background-color;
  font-size: 16px;
  border-collapse: collapse;
  display: grid;
  grid-template-columns: repeat(v-bind(headerLength), minmax(200px, 1fr)) auto;

  thead,
  tbody,
  tr {
    display: contents;
  }

  tr {
    &:last-child td {
      border-bottom: 0;
    }

    th,
    td {
      text-align: left;
    }

    th {
      background-color: $table-header-background-color;
      color: $table-header-color;
      border-bottom: solid 2px $table-border-color;
      font-weight: normal;

      &.sortable {
        cursor: pointer !important;
      }

      &.sort-arrow-asc::after {
        content: " ↑";
      }

      &.sort-arrow-desc::after {
        content: " ↓";
      }
    }

    td {
      border-bottom: solid 1px $dark-white;

      &.noElements {
        text-align: center;
      }

      &.table-action {
        text-align: right;
      }
    }
  }

  tbody tr {
    transition: background-color 150ms ease-out;

    &:nth-child(2n) {
      background-color: $table-even-background-color;
    }

    &:hover {
      background-color: $table-hover-background-color;
    }
  }
}

.pagination {
  background-color: $pagination-background-color;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: flex-end;
  padding: 5px 0;
  background-color: $table-header-background-color;

  .number-box {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .seperator {
    margin-bottom: 0;
    align-self: end;
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
