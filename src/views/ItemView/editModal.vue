<script lang="ts" setup>
import YesNoModal from '@/components/YesNoModal.vue';
import InputValidate from '@/components/InputValidate.vue';
import { useStore } from '@/stores';
import useVuelidate from '@vuelidate/core';
import { FirebaseTemplateItem } from '@/typings/Globals';
import { type Ref, ref, type PropType, watch } from 'vue';

const v$ = useVuelidate();
const state = useStore();
const replacementItem: Ref<FirebaseTemplateItem> = ref(new FirebaseTemplateItem("", "", "", ""));
const selectedItem: Ref<FirebaseTemplateItem> = ref(new FirebaseTemplateItem("", "", "", ""));

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

    const item = state.getFirebaseTemplateItem(key);
    if (item === undefined)
        return;

    selectedItem.value = item;
    replacementItem.value = new FirebaseTemplateItem(item.key, item.singular, item.plural, item.type);
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
        await state.DATABASE_UPDATE_FIREBASE_TEMPLATE_ITEM(props.selectedKey, replacementItem.value);
        emit("submit", true);
    } catch (err) {
        emit("submit", false, err instanceof Error ? err.message : undefined);
    }
}


function labelMessage(value: string) {
    return `Change from ${value} to:`;
}
</script>
<template>
    <yes-no-modal v-bind:show="showEditModal" @close="emit('toggleEditModal', false)"
        @no="emit('toggleEditModal', false)" @yes="editSelectedKey" cancelText="Cancel" confirmText="Done"
        :confirmDisabled="replacementItem.equals(selectedItem) || v$.$invalid">
        <template #title>Edit template-item: "{{ selectedItem!.singular }}"?</template>
        <template #body>
            <form @submit.prevent="editSelectedKey">
                <input-validate v-model:value="replacementItem.singular"
                    :title="labelMessage(selectedItem!.singular)" />
                <input-validate v-model:value="replacementItem.plural" :title="labelMessage(selectedItem!.plural)" />
                <input-validate v-model:value="replacementItem.type" :title="labelMessage(selectedItem!.type)" />
            </form>
        </template>
    </yes-no-modal>
</template>
<style lang="scss" scoped>
</style>