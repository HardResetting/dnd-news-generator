<style lang="scss" scoped>
@import "../../assets/colors";

.form-field {
  display: grid;
  grid-template-columns: min-content auto;
  grid-template-rows: auto;
  grid-template-areas:
    "label textarea"
    ". validation";

  * {
    align-self: center;
  }

  label {
    grid-area: label;
    padding-right: 1.5rem;
  }

  textarea {
    grid-area: textarea;
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
    <label :style="{ width: labelLength }" for="name">{{ title }}</label>
    <textarea :value="value" @input="setValue" @keyup="resizeTextArea" :class="{ 'is-invalid': v$.value.$error }"
      :style="{ 'height': height }" style="resize: none" :disabled="disabled" ref="textarea" />
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
import { defineEmits, computed, ref, onMounted, nextTick } from "@vue/runtime-core";

const emit = defineEmits(["update:value"]);
const textarea = ref<HTMLInputElement | null>()
const height = ref<string>();

const defaultSize: string = "56px";

onMounted(() => resizeTextArea());

function resizeTextArea(): void {
  height.value = "0px";
  nextTick(
    () => height.value =
      props.value !== ""
        ? ((textarea.value?.scrollHeight ?? 0) + 20) + "px"
        : defaultSize
  );
}

const props = defineProps({
  title: {
    type: String,
    default: "Input",
  },
  value: {
    type: String,
    default: "",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  labelLength: {
    type: String,
    default: "auto"
  }
});

function setValue(e: Event) {
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
