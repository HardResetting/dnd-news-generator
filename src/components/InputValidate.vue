<template>
  <div class="form-group mb-3">
    <label for="name">{{ title }}</label>
    <input
      class="form-control"
      :value="value"
      @input="editValue($event.target.value)"
      :class="{ 'is-invalid': v$.value.$error }"
    />
    <div class="invalid-feedback" >
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
    editValue(value: any): void {     
      this.v$.value.$touch();
      this.$emit('update:value', value);
    }
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