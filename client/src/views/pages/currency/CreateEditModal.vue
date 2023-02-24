<script setup>
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useCurrencyStore } from '@/composables/currency';

const props = defineProps({
    currency: {
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

const currency = useCurrencyStore();
const toast = useToast();
const formData = ref(new FormData());
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);

const options = ref([
    {
        label: 'After',
        value: 'after'
    },
    {
        label: 'Before',
        value: 'before'
    }
]);

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

const form = ref({
    name: props.isEdit && props.currency.name !== null ? props.currency.name : '',
    code: props.isEdit && props.currency.code !== null ? props.currency.code : '',
    symbol: props.isEdit && props.currency.symbol !== null ? props.currency.symbol : '',
    position: props.isEdit && props.currency.position !== null ? props.currency.position : '',
});

const submit = async () => {
    formData.value.append('name', form.value.name);
    formData.value.append('code', form.value.code);
    formData.value.append('position', form.value.position);
    formData.value.append('symbol', form.value.symbol);
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await currency.updateCurrency(formData.value, props.currency.id).then(() => {
            formData.value = new FormData();
            if (currency.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Currency updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await currency.createCurrency(formData.value).then(() => {
            formData.value = new FormData();
            if (currency.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Currency created successfully!', life: 3000 });
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
            <span v-if="currency.errors.name" id="name" class="block p-error">{{ currency.errors.name[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Position</label>
            <Dropdown :options="options" option-label="label" option-value="value" class="w-full" placeholder="Select Position" v-model="form.position" />
            <span v-if="currency.errors.position" id="position" class="block p-error">{{ currency.errors.position[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Code</label>
            <InputText class="w-full" placeholder="Code" v-model="form.code" />
            <span v-if="currency.errors.code" id="code" class="block p-error">{{ currency.errors.code[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Symbol</label>
            <InputText class="w-full" placeholder="Symbol" v-model="form.symbol" />
            <span v-if="currency.errors.symbol" id="symbol" class="block p-error">{{ currency.errors.symbol[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
