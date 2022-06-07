<script lang="ts" setup>import { FirebaseTemplateItem } from '@/typings/Globals';
import { computed, watch } from 'vue';

const pageNumberArrLLCRR = computed(() => [props.currentPage - 2, props.currentPage - 1, props.currentPage, props.currentPage + 1, props.currentPage + 2]);
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

watch(() => props.maxPage, (number) => {
    if (number < props.currentPage)
        emit("changePage", number);
})

const emit = defineEmits<{
    (e: 'changePage', number: number): void
}>()
</script>
<template>
    <div class="pagination">
        <button class="secondary" @click.prevent="emit('changePage', currentPage - 1)" :disabled="currentPage === 1">
            <div class="arrow arrow-left"></div>
        </button>

        <div class="number-box" v-if="maxPage <= 10">
            <button v-for="index in maxPage" class="secondary" :class="{ selected: index === currentPage }"
                @click="emit('changePage', index)">{{
                        index
                }}</button>
        </div>

        <div class="number-box" v-else-if="currentPage < 7">
            <button v-for="index in 7" class="secondary" :class="{ selected: index === currentPage }"
                @click="emit('changePage', index)">{{
                        index
                }}</button>
            <span class="secondary seperator">&hellip;</span>
            <button v-for="index in pageNumberArrRR" class="secondary" @click="emit('changePage', index)">{{
                    index
            }}</button>
        </div>

        <div class="number-box" v-else-if="currentPage < (maxPage - 5)">
            <button class="secondary" @click="emit('changePage', 1)">{{ 1 }}</button>
            <button class="secondary" @click="emit('changePage', 2)">{{ 2 }}</button>

            <span class="secondary seperator">&hellip;</span>

            <button v-for="index in pageNumberArrLLCRR" class="secondary" :class="{ selected: index === currentPage }"
                @click="emit('changePage', index)">{{
                        index
                }}</button>

            <span class="secondary seperator">&hellip;</span>
            <button v-for="index in 2" class="secondary" :class="{ selected: (maxPage - 2) + index === currentPage }"
                @click="emit('changePage', (maxPage - 2) + index)">{{
                        (maxPage - 2) + index
                }}</button>
        </div>

        <div class="number-box" v-else>
            <button v-for="index in 2" class="secondary" @click="emit('changePage', index)">{{
                    index
            }}</button>
            <span class="secondary seperator">&hellip;</span>
            <button v-for="index in 7" class="secondary" :class="{ selected: maxPage - 7 + index === currentPage }"
                @click="emit('changePage', maxPage - 7 + index)">{{
                maxPage - 7 + index
                }}</button>
        </div>

        <button class="secondary" @click.prevent="emit('changePage', currentPage+1)"
            :disabled="currentPage === maxPage">
            <div class="arrow arrow-right"></div>
        </button>
    </div>
</template>
<script>
</script>