<template>
  <div id="app">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="/">DnD-News-Generator</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav" ref="collapsable">
          <ul class="navbar-nav">
            <li class="nav-item">
              <router-link class="nav-link" to="/">Home</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/Items">Items</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/Templates"
                >Templates</router-link
              >
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/Types">Types</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/Templates/Compile"
                >Compile random Templates</router-link
              >
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <router-view />
  </div>
</template>

<script lang="ts">
import { Collapse } from "bootstrap";
import { defineComponent } from "vue";
import { ActionTypes, useStore } from "./store/index";
import { RouteLocation } from "vue-router";

let collapsable: Collapse;

export default defineComponent({
  created() {
    const store = useStore();
    store.dispatch(ActionTypes.INIT_DATA, undefined);
  },

  mounted() {
    collapsable = new Collapse(this.$refs.collapsable as Element, {
      toggle: false,
    });
  },

  watch: {
    $route(from: RouteLocation, to: RouteLocation) {
      if (from.path != to.path) {
        collapsable.hide();
      }
    },
  },
});
</script>
