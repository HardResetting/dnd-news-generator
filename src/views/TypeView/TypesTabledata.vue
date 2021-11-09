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

    const TypesTabledata = defineComponent({
        name: "TypesTabledata",

        props: ["type"],
    })
    export default TypesTabledata;
</script>

<style scoped>
</style>