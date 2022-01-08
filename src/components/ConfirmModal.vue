<template>
  <div
    class="modal fade"
    :id="id"
    tabindex="-1"
    :aria-labelledby="labelID"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="labelID">{{ title }}</h5>
          <button
            type="button"
            class="btn-close"
            :data-bs-dismiss="id"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body"><slot></slot></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" :data-bs-dismiss="id">
            {{ CancelText }}
          </button>
          <button type="button" class="btn btn-primary">{{ OKText }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  data() {
    return {
      labelID: this.id + "label",
    };
  },

  props: {
    id: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      default: "Modal",
    },
  },
});
</script>
