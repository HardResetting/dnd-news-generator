<style lang="scss" scoped>
@import "../assets/colors";

.form-field {
    display: grid;
    grid-template-columns: 1fr 3fr;
    grid-template-rows: 1fr 1rem;
    grid-template-areas:
        "label input"
        ". validation";

    * {
        align-self: center;
    }

    label {
        grid-area: label;
    }

    input {
        grid-area: input;
        padding: 0.5rem;
        margin: 0.5rem;
    }

    .feedback {
        grid-area: validation;
        display: block;

        p {
            color: $danger;
        }

        margin-left: 0.5rem;
    }
}
</style>

<template>
    <div class="form-field">
        <label for="name">{{ title }}</label>
        <input :tabindex="tabindex" :value="value" @input="setValue" @blur="touchValue()" :class="{ 'is-invalid': v$.value.$error }"
            :disabled="disabled" list="list" />
        <datalist id="list">
            <option v-for="item in datalist" v-bind:value="item.value" />
        </datalist>
        <div class="feedback">
            <p v-for="error of v$.$errors" v-bind:key="error.$uid">
                {{ error.$message }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { defineEmits, computed, type PropType } from "@vue/runtime-core";
import type { PlainObject } from "@/typings/Globals";

const emit = defineEmits(["update:value"]);

const props = defineProps({
    title: {
        type: String,
        default: "Input",
    },
    datalist: {
        type: Array as PropType<PlainObject[]>,
        default: [],
    },
    value: {
        type: String,
        default: "",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    specialCase: {
        type: Boolean,
        default: false,
    },
    tabindex: {
        type: Number,
        default: 0,
    },
});

function touchValue(): void {
    v$.value.$touch();
}

function setValue(e: Event): void {
    v$.value.$touch();
    emit("update:value", (e.target as HTMLTextAreaElement).value);
}

const rules = computed(() => ({
    value: {
        required,
    },
}));

const v$ = useVuelidate(rules, props);
</script>
