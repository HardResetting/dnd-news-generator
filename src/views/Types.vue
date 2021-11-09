<template>
    <div>
        <div class="d-flex flex-row">
            <div class="card m-5">
                <div class="card-body">
                    <h3 class="card-title">Add type</h3>
                    <div class="d-flex p-4">
                        <form method="post">
                            <div class="form-group">
                                <label for="name">value</label>
                                <input class="form-control" id="name" name="name"/>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card m-5">
            <div class="card-body">
                <h3 class="card-title">DND News Generator</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Value</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <TypesTabledata v-for="type in types" :key="type.key" :type="type"></TypesTabledata>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>


<script lang="ts">
    import { defineComponent } from 'vue';

    import { db } from '../Services/FirestoreDb';

    import { IType } from "../../Typings/Globals";

    import TypesTabledata from './TypesTabledata.vue';



const Component = defineComponent({
        name: "types",

        components: {
            TypesTabledata,
        },

        data() { return {
            types: new Array <IType>(),
        }},

        created(){
            this.types.push({
                key: "doc.id",
                value: "doc.data().value",
            })
            this.types.push({
                key: "doc.id",
                value: "doc.data().value",
            })
            this.types.push({
                key: "doc.id",
                value: "doc.data().value",
            })
        }

        // created() {
        //     const q = query(collection(db, "types"));
        //     const unsubscribe = onSnapshot(q, (querySnapshot) => {
        //         querySnapshot.forEach((doc) => {
        //             this.types.push({
        //                 key: doc.id,
        //                 value: doc.data().value,
        //             })
        //         });
        //     });
        // },
    })

    export default Component;
</script>