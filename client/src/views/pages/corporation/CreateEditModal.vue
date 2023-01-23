<script setup>
import { ref, onMounted } from 'vue';
import { useCorporationStore } from '@/composables/corporation';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    corporation: {
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

const options = ref([
    {
        label: 'Customer',
        value: 'customer'
    },
    {
        label: 'Client',
        value: 'client'
    }
]);

const corporation = useCorporationStore();
const toast = useToast();
const formData = ref(new FormData());
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

onMounted(async () => {
    await corporation.getCurrencies();
});

const form = ref({
    name: props.isEdit && props.corporation.name !== null ? props.corporation.name : '',
    owner: props.isEdit && props.corporation.owner !== null ? props.corporation.owner : '',
    tel_number: props.isEdit && props.corporation.tel_number !== null ? props.corporation.tel_number : '',
    gsm_number: props.isEdit && props.corporation.gsm_number !== null ? props.corporation.gsm_number : '',
    fax_number: props.isEdit && props.corporation.fax_number !== null ? props.corporation.fax_number : '',
    email: props.isEdit && props.corporation.email !== null ? props.corporation.email : '',
    address: props.isEdit && props.corporation.address !== null ? props.corporation.address : '',
    tax_office: props.isEdit && props.corporation.tax_office !== null ? props.corporation.tax_office : '',
    tax_number: props.isEdit && props.corporation.tax_number !== null ? props.corporation.tax_number : '',
    type: props.isEdit && props.corporation.type !== null ? props.corporation.type : '',
    currency_id: props.isEdit && props.corporation.currency_id !== null ? props.corporation.currency_id : ''
});

const submit = async () => {
    formData.value.append('name', form.value.name);
    formData.value.append('owner', form.value.owner);
    formData.value.append('tel_number', form.value.tel_number);
    formData.value.append('gsm_number', form.value.gsm_number);
    formData.value.append('fax_number', form.value.fax_number);
    formData.value.append('email', form.value.email);
    formData.value.append('address', form.value.address);
    formData.value.append('tax_office', form.value.tax_office);
    formData.value.append('tax_number', form.value.tax_number);
    formData.value.append('type', form.value.type);
    formData.value.append('currency_id', form.value.currency_id);
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await corporation.updateCorporation(formData.value, props.corporation.id).then(() => {
            formData.value = new FormData();
            if (corporation.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Corporation updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await corporation.createCorporation(formData.value).then(() => {
            formData.value = new FormData();
            if (corporation.respStatus) {
                toggleModal();
                emit('newCorporation', form.value);
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Corporation created successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="grid">
        <div class="field md:col-6 col-12 m-0">
            <InputText class="w-full" placeholder="Name" v-model="form.name" />
            <span v-if="corporation.errors.name" id="name" class="block p-error">{{ corporation.errors.name[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <InputText class="w-full" placeholder="Owner name" v-model="form.owner" />
            <span v-if="corporation.errors.owner" id="owner" class="block p-error">{{ corporation.errors.owner[0] }}</span>
        </div>
        <div class="field md:col-6 col-12 m-0">
            <InputText class="w-full" placeholder="Tax office" v-model="form.tax_office" />
            <span v-if="corporation.errors.tax_office" id="tax_office" class="block p-error">{{ corporation.errors.tax_office[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <InputText class="w-full" placeholder="ID number/Tax number" v-model="form.tax_number" />
            <span v-if="corporation.errors.tax_number" id="tax_number" class="block p-error">{{ corporation.errors.tax_number[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <InputMask mask="(999) 999 9999" unmask class="w-full" placeholder="Tel number" v-model="form.tel_number" />
            <span v-if="corporation.errors.tel_number" id="tel_number" class="block p-error">{{ corporation.errors.tel_number[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <InputMask mask="(999) 999 9999" unmask class="w-full" placeholder="GSM number" v-model="form.gsm_number" />
            <span v-if="corporation.errors.gsm_number" id="gsm_number" class="block p-error">{{ corporation.errors.gsm_number[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <InputMask mask="(999) 999 9999" unmask class="w-full" placeholder="Fax number" v-model="form.fax_number" />
            <span v-if="corporation.errors.fax_number" id="fax_number" class="block p-error">{{ corporation.errors.fax_number[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <InputText type="email" class="w-full" placeholder="Email address" v-model="form.email" />
            <span v-if="corporation.errors.email" id="email" class="block p-error">{{ corporation.errors.email[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <Textarea class="w-full" placeholder="Address" v-model="form.address" rows="3" />
            <span v-if="corporation.errors.address" id="address" class="block p-error">{{ corporation.errors.address[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Type</label>
            <Dropdown :options="options" option-label="label" option-value="value" class="w-full" placeholder="Select Type" v-model="form.type" />
            <span v-if="corporation.errors.type" id="type" class="block p-error">{{ corporation.errors.type[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Currency</label>
            <Dropdown :options="corporation.currenciesList" option-label="name" option-value="id" class="w-full" placeholder="Select Currency" v-model="form.currency_id" />
            <span v-if="corporation.errors.currency_id" id="currency_id" class="block p-error">{{ corporation.errors.currency_id[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
