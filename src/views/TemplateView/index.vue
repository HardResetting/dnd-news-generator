<template>
  <div>
    <div class="d-flex flex-row">
      <div class="card m-5">
        <div class="card-body">
          <h3 class="card-title">Add type</h3>
          <div class="d-flex p-4">
            <form method="post">
              <div class="form-group">
                <label for="name">template</label>
                <input class="form-control" id="name" name="name" />
              </div>
              <button type="submit" class="btn btn-primary mt-2">Add</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="card m-5">
      <div class="card-body">
        <h3 class="card-title">Templates</h3>
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Template</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <TemplateTabledata
              v-for="type in types"
              :key="type.key"
              :type="type"
            ></TemplateTabledata>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { db } from "../../services/FirestoreDb";
import { IPlainObject } from "../../typings/Globals";
import TemplateTabledata from "./TemplateTabledata.vue";
import { collection, getDocs } from "firebase/firestore";

const Component = defineComponent({
  name: "types",

  components: {
    TemplateTabledata,
  },

  data() {
    return {
      types: new Array<IPlainObject>(),
    };
  },

  created() {
    getDocs(collection(db, "templates")).then((querySnapshot: any) => {
      querySnapshot.forEach((doc: any) => {
        console.log(doc.data());

        this.types.push({
          key: doc.id,
          value: doc.data().value,
        });
      });
    });
  },
});

export default Component;
</script>
