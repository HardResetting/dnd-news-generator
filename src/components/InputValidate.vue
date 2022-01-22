<style lang="scss" scoped>
@import "../assets/colors";

.form-field {
  display: grid;
  grid-template-columns: 1fr 3fr;
  grid-template-rows: auto;
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
    <input
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

<script lang="ts">
import { defineComponent } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";

const Component = defineComponent({
  name: "inputValidate",

  setup() {
    return { v$: useVuelidate() };
  },

  methods: {
    editValue(value: string): void {
      this.v$.value.$touch();
      this.$emit("update:value", value);
    },
  },

  props: {
    title: {
      type: String,
      default: "Input",
    },
    value: {
      type: String,
      default: "",
    },
  },

  validations() {
    return {
      value: { required },
    };
  },
});

export default Component;
</script>
