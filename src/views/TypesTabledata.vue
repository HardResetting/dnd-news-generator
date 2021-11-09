<template>
    <tr>
        <td>{{ type.value }}</td>
        <td>
            <button class="btn btn-primary me-2">Edit</button>
            <button class="btn btn-danger">Delete</button>
        </td>
    </tr>
</template>

<script lang="ts">
    import { defineComponent } from 'vue';

    import { db } from '../Services/FirestoreDb';
    import { collection, query, where, doc, onSnapshot, addDoc } from "firebase/firestore";
    import { IType } from "../../Typings/Globals";


    const TypesTabledata = defineComponent({
        name: "TypesTabledata",

        props: ["type"],

    })
    export default TypesTabledata;
</script>

<style scoped>
</style>