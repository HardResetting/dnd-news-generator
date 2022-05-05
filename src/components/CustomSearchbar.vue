<style scoped lang="scss">
@import "@/assets/colors";

.searchbar {
  display: grid;
  flex-direction: row;

  label {
    grid-column: 1 / span 2;
    font-size: 1.2rem;
    align-self: center;
  }

  input {
    grid-column: 3 / span 2;
    width: 100%;
    height: 100%;
    padding: 0.5rem;
    border: none;
    outline: none;
    background: transparent;
    color: $white;

    &::placeholder {
      color: $light-gray;
    }
  }
}

.counter {
  grid-column: 5 / span 2;
  font-size: 1.2rem;
  align-self: center;
}
</style>

<template>
  <div class="searchbar">
    <label for="searchInput">Search:</label>
    <input
      id="searchInput"
      v-model="searchbarValue"
      placeholder="Type here..."
      type="text"
      autocomplete="off"
    />
    <div class="counter" style="flex-shrink: 0">
      ({{ sortedArray }} of {{ dataArray.length }})
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, defineProps, defineEmits, PropType } from "vue";

const emit = defineEmits<{
  (event: "arrayChanged", array: Array<Record<string, unknown>>): void;
}>();

const props = defineProps({
  dataArray: {
    type: Object as PropType<Array<Record<string, unknown>>>,
    required: true,
  },
  keys: {
    type: Array as PropType<Array<string>>,
    required: true,
  },
});

function getProperty<T, K extends keyof T>(o: T, propertyName: K): T[K] {
  return o[propertyName]; // o[propertyName] is of type T[K]
}

const searchbarValue = ref("");
const sortedArray = computed(() => {
  const filteredArray = [...props.dataArray].filter((item) =>
    // filter by searchbar value and return only items that have properties defined in keys
    props.keys.some((key) => {
      const prop = getProperty(item, key) as Record<string, unknown>;
      return (
        prop &&
        prop
          .toString()
          .toLowerCase()
          .includes(searchbarValue.value.toLowerCase())
      );
    })
  );
  emit("arrayChanged", filteredArray);
  return filteredArray.length;
});
</script>
