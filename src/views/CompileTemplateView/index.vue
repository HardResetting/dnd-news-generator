<template>
  <div>
    <BasicCard>
      <template #title>
        <h2>Raw Template</h2>
      </template>
      <template #title-side>
        <button @click="edit()" class="primary" style="margin: 0">Edit</button>
      </template>
      <template #body>
        {{ template }}
      </template>
    </BasicCard>

    <BasicCard style="margin-top: 4rem">
      <template #title>
        <h2>Compiled Template</h2>
      </template>
      <template #title-side> Done in 0.0ms </template>
      <template #body> {{ compiledTemplate }} </template>
    </BasicCard>

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
import BasicCard from "../../components/BasicCard.vue";
import { store } from "@/store";

export default defineComponent({
  components: {
    BasicCard,
  },

  data() {
    return {
      template:
        // "[race] fordert Auswanderung von [@var=[ran(1,3)]] [?[@var],race]!",
        store.getters.randomFirebaseTemplate,
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
