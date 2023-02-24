<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useMaterialStore } from '@/composables/material';

const props = defineProps({
    material: {
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

const material = useMaterialStore();
const loading = ref(false);
const toast = useToast();
const formData = ref(new FormData());
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);
const selectedCurrency = ref({
    name: '',
    code: 'TRY',
    symbol: '₺',
    position: 'after'
});

const types = ref([
    {
        label: 'Service',
        value: 'service'
    },
    {
        label: 'Procurement',
        value: 'procurement'
    },
    {
        label: 'Service & Procurement',
        value: 'service_procurement'
    }
]);

const categories = ref([
    {
        label: 'Construction',
        value: 'construction'
    },
    {
        label: 'Electricity',
        value: 'electricity'
    },
    {
        label: 'Plumbing',
        value: 'plumbing'
    },
    {
        label: 'Electronics',
        value: 'electronics'
    },
    {
        label: 'Other',
        value: 'other'
    }
]);

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

onMounted(async () => {
    loading.value = true;
    await material.getCurrencies();
    await material.getUnits();
    await material.getTaxes();
    if (props.isEdit) {
        await material.getMaterial(props.material.id);
        selectedCurrency.value = material.currenciesList.find((currency) => currency.id === material.material.data.currency_id);
    }
    loading.value = false;
});

const form = ref({
    name: props.isEdit && props.material.name !== null ? props.material.name : '',
    description: props.isEdit && props.material.description !== null ? props.material.description : '',
    price: props.isEdit && props.material.price !== null ? props.material.price : '',
    category: props.isEdit && props.material.category !== null ? props.material.category : '',
    type: props.isEdit && props.material.type !== null ? props.material.type : '',
    code: props.isEdit && props.material.code !== null ? props.material.code : '',
    unit_id: props.isEdit && props.material.unit_id !== null ? props.material.unit_id : '',
    tax_id: props.isEdit && props.material.tax_id !== null ? props.material.tax_id : '',
    currency_id: props.isEdit && props.material.currency_id !== null ? props.material.currency_id : ''
});

const submit = async () => {
    formData.value.append('name', form.value.name);
    formData.value.append('description', form.value.description);
    formData.value.append('price', form.value.price);
    formData.value.append('category', form.value.category);
    formData.value.append('type', form.value.type);
    formData.value.append('code', form.value.code);
    formData.value.append('unit_id', form.value.unit_id);
    formData.value.append('tax_id', form.value.tax_id);
    formData.value.append('currency_id', form.value.currency_id);
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await material.updateMaterial(props.material.id, formData.value).then(() => {
            formData.value = new FormData();
            if (material.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Material updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await material.createMaterial(formData.value).then(() => {
            formData.value = new FormData();
            if (material.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Material created successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    }
};

function changeCurrency(e) {
    selectedCurrency.value = material.currenciesList.find((currency) => currency.id === e.value);
}
</script>

<template>
    <form class="grid" @submit.prevent="submit">
        <div class="field md:col-6 col-12 m-0">
            <label>Name</label>
            <InputText class="w-full" placeholder="Name" v-model="form.name" />
            <span v-if="material.errors.name" id="name" class="block p-error">{{ material.errors.name[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Code</label>
            <InputText class="w-full" placeholder="Code" v-model="form.code" />
            <span v-if="material.errors.code" id="code" class="block p-error">{{ material.errors.code[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Type</label>
            <Dropdown :options="types" option-label="label" option-value="value" class="w-full" placeholder="Select Status" v-model="form.type" />
            <span v-if="material.errors.type" id="type" class="block p-error">{{ material.errors.type[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Category</label>
            <Dropdown :options="categories" option-label="label" option-value="value" class="w-full" placeholder="Select Status" v-model="form.category" />
            <span v-if="material.errors.category" id="category" class="block p-error">{{ material.errors.category[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <Textarea class="w-full" placeholder="Description" v-model="form.description" rows="3" />
            <span v-if="material.errors.description" id="description" class="block p-error">{{ material.errors.description[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Currency</label>
            <Dropdown :disabled="loading" :filter="true" :options="material.currenciesList" option-label="name" option-value="id" class="w-full" placeholder="Select Currency" v-model="form.currency_id" @change="changeCurrency($event)" />
            <span v-if="material.errors.currency_id" id="currency_id" class="block p-error">{{ material.errors.currency_id[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Price</label>
            <InputNumber
                :disabled="loading"
                mode="decimal"
                :minFractionDigits="2"
                :maxFractionDigits="2"
                class="w-full"
                placeholder="Balance"
                v-model="form.price"
                :currency="selectedCurrency.code"
                :prefix="selectedCurrency.position === 'before' ? selectedCurrency.symbol + ' ' : ''"
                :suffix="selectedCurrency.position === 'after' ? ' ' + selectedCurrency.symbol : ''"
            />
            <span v-if="material.errors.price" id="price" class="block p-error">{{ material.errors.price[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Tax</label>
            <Dropdown :filter="true" :options="material.taxesList" option-label="name" option-value="id" class="w-full" placeholder="Select Tax" v-model="form.tax_id" />
            <span v-if="material.errors.tax_id" id="tax_id" class="block p-error">{{ material.errors.tax_id[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Unit</label>
            <Dropdown :filter="true" :options="material.unitsList" option-label="name" option-value="id" class="w-full" placeholder="Select Unit" v-model="form.unit_id" />
            <span v-if="material.errors.unit_id" id="unit_id" class="block p-error">{{ material.errors.unit_id[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
