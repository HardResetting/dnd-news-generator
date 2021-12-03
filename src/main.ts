import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

router.options.linkActiveClass = "active";

createApp(App).use(router).mount("#app");
