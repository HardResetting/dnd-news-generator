<template>
  <div>
    <div class="d-flex flex-row">
      <div class="card m-5">
        <div class="card-body">
          <h3 class="card-title">Add type</h3>
          <div class="d-flex p-4">
            <div>
              <div class="form-group">
                <label for="name">value</label>
                <input class="form-control" v-bind:class="{ 'is-invalid': isInvalid }" v-model.trim="newType" />
                <div class="invalid-feedback">Only letters and underscores are allowed!</div>
              </div>
              <button class="btn btn-primary mt-2" v-bind:class="{ 'disabled': isInvalid }" @click="addType">Add</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card m-5">
      <div class="card-body">
        <h3 class="card-title">Values</h3>
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Value</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <TypesTabledata
              v-for="type in types"
              :key="type.key"
              :type="type"
            ></TypesTabledata>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>


<script lang="ts">
import { defineComponent } from "vue";
import { db } from "../../services/FirestoreDb";
import { IPlainObject } from "../../../typings/Globals";
import TypesTabledata from "./TypesTabledata.vue";
import { doc, collection, getDocs, setDoc } from "firebase/firestore";

const Component = defineComponent({
  name: "types",

  components: {
    TypesTabledata,
  },

  data() {
    return {
      types: new Array<IPlainObject>(),
      newType: "",
    };
  },

  methods: {
    addType() {
      console.log(this.newType);

      // setDoc(doc(db, "types"), {
      //   value: this.newType,
      //   items: [],
      //   });
    },
  },

  created() {
    getDocs(collection(db, "types")).then((querySnapshot: any) => {
      console.log(querySnapshot);

      querySnapshot.forEach((doc: any) => {
        console.log(doc);
        console.log(doc.data());

        this.types.push({
          key: doc.id,
          value: doc.data().value,
        });
      });
    });
  },

  computed: {
    isInvalid(): boolean {
      return /[^A-Za-z_]/.test(this.newType);
    },
  },
});

export default Component;
</script>