<script setup>
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useTaxStore } from '@/composables/tax';

const props = defineProps({
    tax: {
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

const tax = useTaxStore();
const toast = useToast();
const formData = ref(new FormData());
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

const form = ref({
    name: props.isEdit && props.tax.name !== null ? props.tax.name : '',
    rate: props.isEdit && props.tax.rate !== null ? props.tax.rate : ''
});

const submit = async () => {
    formData.value.append('name', form.value.name);
    formData.value.append('rate', form.value.rate);
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await tax.updateTax(formData.value, props.tax.id).then(() => {
            formData.value = new FormData();
            if (tax.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Tax updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await tax.createTax(formData.value).then(() => {
            formData.value = new FormData();
            if (tax.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Tax created successfully!', life: 3000 });
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
            <span v-if="tax.errors.name" id="name" class="block p-error">{{ tax.errors.name[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Rate</label>
            <InputText class="w-full" placeholder="Rate" v-model="form.rate" />
            <span v-if="tax.errors.rate" id="symbol" class="block p-error">{{ tax.errors.rate[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
