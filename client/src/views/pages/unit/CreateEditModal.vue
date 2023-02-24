<script setup>
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useUnitStore } from '@/composables/unit';

const props = defineProps({
    unit: {
        type: Object,
        required: true,
        default: () => ({})
    },
    isEdit: {
        type: Boolean,
        required: true,
        default: false
    }
});

const unit = useUnitStore();
const toast = useToast();
const formData = ref(new FormData());
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

const form = ref({
    name: props.isEdit && props.unit.name !== null ? props.unit.name : ''
});

const submit = async () => {
    formData.value.append('name', form.value.name);
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await unit.updateUnit(formData.value, props.unit.id).then(() => {
            formData.value = new FormData();
            if (unit.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Unit updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await unit.createUnit(formData.value).then(() => {
            formData.value = new FormData();
            if (unit.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Unit created successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    }
};
</script>

<template>
    <form class="grid" @submit.prevent="submit">
        <div class="field col-12 m-0">
            <label>Name</label>
            <InputText class="w-full" placeholder="Name" v-model="form.name" />
            <span v-if="unit.errors.name" id="name" class="block p-error">{{ unit.errors.name[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
