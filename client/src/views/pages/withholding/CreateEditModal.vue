<script setup>
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useWithholdingStore } from '@/composables/withholding';

const props = defineProps({
    withholding: {
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

const withholding = useWithholdingStore();
const toast = useToast();
const formData = ref(new FormData());
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

const form = ref({
    name: props.isEdit && props.withholding.name !== null ? props.withholding.name : '',
    rate: props.isEdit && props.withholding.rate !== null ? props.withholding.rate : ''
});

const submit = async () => {
    formData.value.append('name', form.value.name);
    formData.value.append('rate', form.value.rate);
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await withholding.updateWithholding(formData.value, props.withholding.id).then(() => {
            formData.value = new FormData();
            if (withholding.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Withholding updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await withholding.createWithholding(formData.value).then(() => {
            formData.value = new FormData();
            if (withholding.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Withholding created successfully!', life: 3000 });
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
            <span v-if="withholding.errors.name" id="name" class="block p-error">{{ withholding.errors.name[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Rate</label>
            <InputText class="w-full" placeholder="Rate" v-model="form.rate" />
            <span v-if="withholding.errors.rate" id="symbol" class="block p-error">{{ withholding.errors.rate[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
