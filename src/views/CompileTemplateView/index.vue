<template>
  <div>
    <div class="d-flex flex-column mx-5 mt-5">
      <div class="card w-100">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="m-0">Template</h3>
            <a class="btn btn-primary"> Edit </a>
          </div>
        </div>
        <div class="card-body">
          <span class="card-title">{{ template }}</span>
        </div>
      </div>
      <a id="NewTemplate" class="btn btn-primary mt-1"> Get new Template </a>
    </div>

    <div class="d-flex flex-column w-100 text-center mt-4">
      <i class="fas fa-angle-double-down fa-2x"></i>
    </div>

    <div class="d-flex flex-column mx-5 mt-4">
      <div class="card w-100 mt-3">
        <div class="card-header">
          <h3 class="m-0">Result</h3>
        </div>
        <div class="card-body">
          <span id="Result" class="card-title">
            {{ compiledTemplate }}
          </span>
        </div>
      </div>
      <button id="Recompile" class="btn btn-secondary mt-1">
        Redo same Template
      </button>
    </div>
  </div>
</template>


<script lang="ts">
import { defineComponent } from "vue";
import compiler from "./compile"

const Component = defineComponent({
  data() {
    return {
      template:
        "[Rasse] fordert Auswanderung von [@var=ran(100,200)] [@var, Rasse]",
        compiledTemplate: "",
    };
  },

  created() {
    this.compiledTemplate = compiler(this.template);
  },
});

export default Component;
</script>