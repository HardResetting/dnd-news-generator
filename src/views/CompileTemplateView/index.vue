<template>
  <div>
    <div class="card">
      <div class="card-title flex flex-collumn">
        <h2>Template</h2>
        <div class="table-display">
          <button @click="edit()" class="primary" style="margin: 0">Edit</button>
        </div>
      </div>
      <div class="card-body">
        {{ template }}
      </div>
    </div>

    <div class="card" style="margin-top: 4rem">
      <div class="card-title flex flex-collumn">
        <h2>Template</h2>
        <div class="table-display">Done in 0.0ms</div>
      </div>
      <div class="card-body">
        {{ compiledTemplate }}
      </div>
    </div>

    <div class="flex flex-row justify-end">
      <button
        id="Recompile"
        @click="runCompileScript"
        class="success"
        style="margin-right: 0; margin-top: 2rem"
      >
        Redo same Template
      </button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { compileTemplate } from "./templateCompiler";

export default defineComponent({
  data() {
    return {
      template:
        // "[race] fordert Auswanderung von [@var=[ran(1,3)]] [?[@var],race]!",
        "Alle [?2,race] fordern die Auswanderung von ALLEN [?2,race](n)! [@var=[ran(100,300)]] gingen fort..",
      compiledTemplate: "Loading...",
    };
  },

  async created() {
    this.runCompileScript();
  },

  methods: {
    async runCompileScript() {
      const parseObject = await compileTemplate(this.template);
      this.compiledTemplate = parseObject.errors.length
        ? parseObject.errors.length + " Error(s) occoured during the process.."
        : parseObject.result;
    },
  },
});
</script>
