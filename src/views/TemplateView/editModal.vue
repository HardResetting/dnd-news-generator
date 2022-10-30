<script lang="ts" setup>
import YesNoModal from '@/components/YesNoModal.vue';
import CustomTextArea from './customTextarea.vue';
import { useStore } from '@/stores';
import useVuelidate from '@vuelidate/core';
import { FirebaseTemplate } from '@/typings/Globals';
import { type Ref, ref, watch } from 'vue';

const v$ = useVuelidate();
const state = useStore();
const replacementItem: Ref<FirebaseTemplate> = ref(new FirebaseTemplate("", ""));
const selectedItem: Ref<FirebaseTemplate> = ref(new FirebaseTemplate("", ""));

const props = defineProps({
    showEditModal: {
        type: Boolean,
        required: true,
    },
    selectedKey: {
        type: String,
        default: "",
    }
});

watch(() => props.selectedKey, (key) => {
    if (key === undefined || key === "")
        return;

    const item = state.getFirebaseTemplate(key);
    if (item === undefined)
        return;

    selectedItem.value = item;
    replacementItem.value = new FirebaseTemplate(item.key, item.value);
})

const emit = defineEmits<{
    (e: 'toggleEditModal', show: boolean): void
    (e: 'submit', success: boolean, exception?: string): void
}>()

async function editSelectedKey(): Promise<void> {
    const isValid: boolean = await v$.value.$validate();
    if (!isValid)
        return;

    try {
        await state.DATABASE_UPDATE_FIREBASE_TEMPLATE(props.selectedKey, replacementItem.value.value);
        emit("submit", true);
    } catch (err) {
        emit("submit", false, err instanceof Error ? err.message : undefined);
    }
}

</script>
<template>
    <yes-no-modal v-bind:show="showEditModal" @close="emit('toggleEditModal', false)"
        @no="emit('toggleEditModal', false)" @yes="editSelectedKey" cancelText="Cancel" confirmText="Done"
        :confirmDisabled="replacementItem.value === selectedItem.value || v$.$invalid">
        <template #title>Edit template?</template>
        <template #body>
            <custom-text-area :value="selectedItem.value" :disabled="true" title="Old" labelLength="3rem" />
            <custom-text-area v-model:value="replacementItem.value" title ="New" labelLength="3rem"/>
        </template>
    </yes-no-modal>
</template>
<style lang="scss" scoped>

</style>