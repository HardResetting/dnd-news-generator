<script lang="ts" setup>
export type Header = {
    name: string,
    text: string,
    searchable: boolean,
    sortable: boolean,
}

export type Item = {
    // key value pair array
    data: ComputedRef<Array<Record<string, any>>>,
    onItemClick?: ClickableProps,
    onEditClick?: ClickableProps,
    onDeleteClick?: ClickableProps,
}

export type ClickableProps = {
    event: (item: Record<string, any>) => void,
    title?: (item: Record<string, any>) => string,
}

import { computed, ref, type ComputedRef, type PropType, type Ref } from 'vue';
import BasicCard from './BasicCard.vue';
import SimpleTablePaginaton from './SimpleTablePaginaton.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'NO_TITLE',
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
        default: '',
    },
    reducedPadding: {
        type: Boolean,
        default: false,
    },
});

const anyActionDefined = props.items.onEditClick != undefined || props.items.onDeleteClick != undefined;
const headerLength = anyActionDefined ? props.headers.length : props.headers.length;

const currentSortValue = ref('');
const currentSortDirection: Ref<'asc' | 'desc'> = ref('asc');

const searchbarValue = ref('');

const sortedFilteredItems = computed(() => {
    const sortValue = currentSortValue.value;
    const sortDir = currentSortDirection.value;

    const sortedArray = sortValue === ""
        ? [...props.items.data.value]
        : [...props.items.data.value]
            .sort((a: Record<string, string>, b: Record<string, string>) =>
                a[sortValue].toString().localeCompare(b[sortValue].toString()) * (sortDir === 'asc' ? 1 : -1)
            )

    const filteredArray = sortedArray.filter((item: Record<string, string>) => {

        const ok = props.headers.some((header: Header) => {
            if (!header.searchable) return false;

            return (item[header.name].toString().toLowerCase().includes(searchbarValue.value.toLowerCase()))
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
const paginationBreakPoint = 10
const currentPage = ref(1);


function sort(header: Header) {
    if (!header.sortable)
        return '';

    if (currentSortValue.value == header.name) {
        currentSortDirection.value = currentSortDirection.value == 'asc' ? 'desc' : 'asc';
    } else {
        currentSortValue.value = header.name;
        currentSortDirection.value = 'asc';
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
                <input id="searchInput" placeholder="Type here..." v-model="searchbarValue" type="text"
                    autocomplete="off" />
                <div class="counter" style="flex-shrink: 0">
                    ( {{ sortedFilteredItems.length }} of {{ items.data.value.length }} )
                </div>
            </div>
        </template>
        <template #body>
            <table :class="{ 'reduced-padding': reducedPadding }" style="width: 100%">
                <thead>
                    <tr>
                        <th v-for="header in headers" :key="header.name" class="sortable"
                            :class="sortArrowClass(header)" scope="col" @click="sort(header)">
                            {{ header.text }}
                        </th>
                        <th v-if="anyActionDefined" scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!sortedFilteredItems.length">
                        <td :colspan="anyActionDefined ? headers.length + 1 : headers.length" class="noElements">No
                            elements found!
                        </td>
                    </tr>
                    <tr v-for="item in sortedFilteredPaginatedItems" :key="item.key">
                        <td v-for="header in headers" :class="{ clickable: items.onItemClick != undefined }"
                            @click.prevent.stop="items.onItemClick?.event?.(item)">{{
                                    item[header.name]
                            }}</td>
                        <td v-if="anyActionDefined" class="table-action">
                            <button v-if="items.onEditClick != undefined" class="primary"
                                :title="items.onEditClick?.title?.(item)" @click="items.onEditClick?.event?.(item)">
                                Edit
                            </button>
                            <button v-if="items.onDeleteClick != undefined" class="danger"
                                :title="items.onDeleteClick?.title?.(item)" @click="items.onDeleteClick?.event?.(item)">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <simple-table-paginaton :maxPage="maxPage" :currentPage="currentPage"
                @changePage="(n) => currentPage = n" />
        </template>
    </BasicCard>
</template>

<style scoped lang="scss">
@import "@/assets/colors.scss";


.table-radio {
    display: none;

    &:checked+.card-title-side {
        display: block;
    }

    &:checked+.card-title-side+table {
        width: 100%;
        display: table;
    }

    &:checked+.card-title-side+table+.pagination {
        display: flex;

        &>label.active {
            color: $pagination-label-active-color;
            background-color: $pagination-label-active-background-color;
            cursor: default;
        }

        &>label.disabled {
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