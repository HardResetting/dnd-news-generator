<style lang="scss" scoped>
@import "../assets/colors";

// $card-max-width: 960px;
$card-max-width: 100%;
$text-color: $white;
.card {
  background-color: $contrast-background;
  max-width: $card-max-width;
  max-height: 100%;
  display: flex;
  flex-direction: column;
  box-shadow: 0 15px 35px rgba(#000000, 0.5);
  overflow: auto;
  box-sizing: border-box;
  color: $text-color;
}
.card-title {
  background-color: $primary;
  width: 100%;
  display: flex;
  justify-content: space-between;
  padding: 0.5rem;
  margin: 0 auto;
  .title-side,
  .title-main {
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 0;
    margin: 0;
    align-self: center;
  }
  .title-side {
    text-align: right;
  }
}
.card-body {
  background-color: $contrast-background;
  padding: 1rem;
}
.card-footer {
  background-color: $contrast-background;
  padding: 0 1rem 1rem 1rem;
}
.p0 {
  padding: 0;
}
</style>

<template>
  <div class="card">
    <div class="card-title">
      <slot name="title">
        <h2 class="title-main">Example Title</h2>
      </slot>
      <div class="title-side">
        <slot name="title-side"></slot>
      </div>
    </div>
    <div class="card-body" :class="{ p0: !bodyPadding }">
      <slot name="body">Example Text</slot>
    </div>
    <div class="card-footer" :class="{ p0: !bodyPadding }">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps } from "vue";

defineProps({
  bodyPadding: {
    type: Boolean,
    default: true,
  },
});
</script>
