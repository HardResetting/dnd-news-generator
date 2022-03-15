<template>
  <div>
    <form @submit.prevent="addType">
      <div class="d-flex flex-row">
        <div class="card m-5">
          <div class="card-body">
            <h3 class="card-title">Add Item</h3>
            <div class="d-flex p-4">
              <div>
                <div class="mb-3">
                  <label for="oldName">Old Name</label>
                  <input
                    id="oldName"
                    name="oldName"
                    :value="oldName"
                    disabled
                  />
                </div>
                <input-validate title="New Name" />
                <button class="btn btn-primary mt-2">Edit</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import InputValidate from "../../components/InputValidate.vue";

export default defineComponent({
  components: {
    InputValidate,
  },

  props: {
    oldName: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      newName: "",
    };
  },
});
</script>
