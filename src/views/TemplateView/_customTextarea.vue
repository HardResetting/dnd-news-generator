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
    <label for="name">{{ title }}</label>
    <textarea
      :value="value"
      @input="editValue($event.target.value)"
      :class="{ 'is-invalid': v$.value.$error }"
    />
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
import { defineEmits, defineProps, computed } from "@vue/runtime-core";

const emit = defineEmits(["update:value"]);

function editValue(value: string): void {
  v$.value.$touch();
  emit("update:value", value);
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
});

const rules = computed(() => ({
  value: {
    required,
  },
}));

const v$ = useVuelidate(rules, props);
</script>
