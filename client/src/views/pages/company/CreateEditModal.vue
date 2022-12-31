<script setup>
import { ref, onMounted } from 'vue';
import { useCompanyStore } from '@/composables/company';
import { useLayout } from '@/layout/composables/layout';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    company: {
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

const company = useCompanyStore();
const toast = useToast();
const formData = ref(new FormData());
const { contextPath } = useLayout();
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

const defaultLogo = ref({
    name: '',
    url: ''
});

onMounted(async () => {
    defaultLogo.value.url = props.isEdit && props.company?.logo !== null ? axios.defaults.baseURL + props.company?.logo : `${contextPath}layout/images/default.png`;
});

const form = ref({
    logo: defaultLogo.value,
    name: props.isEdit && props.company.name !== null ? props.company.name : '',
    owner: props.isEdit && props.company.owner !== null ? props.company.owner : '',
    tel_number: props.isEdit && props.company.tel_number !== null ? props.company.tel_number : '',
    gsm_number: props.isEdit && props.company.gsm_number !== null ? props.company.gsm_number : '',
    fax_number: props.isEdit && props.company.fax_number !== null ? props.company.fax_number : '',
    email: props.isEdit && props.company.email !== null ? props.company.email : '',
    address: props.isEdit && props.company.address !== null ? props.company.address : '',
    tax_office: props.isEdit && props.company.tax_office !== null ? props.company.tax_office : '',
    tax_number: props.isEdit && props.company.tax_number !== null ? props.company.tax_number : ''
});

const changePreview = (event) => {
    const file = event.target.files[0];
    defaultLogo.value.url = URL.createObjectURL(file);
    defaultLogo.value.name = file.name;
};

const clearLogo = () => {
    form.value.logo = null;
    defaultLogo.value.url = `${contextPath}layout/images/default.png`;
    defaultLogo.value.name = '';
};

const submit = async () => {
    if(form.value.logo) {
        formData.value.append('logo', form.value.logo);
    }
    formData.value.append('name', form.value.name);
    formData.value.append('owner', form.value.owner);
    formData.value.append('tel_number', form.value.tel_number);
    formData.value.append('gsm_number', form.value.gsm_number);
    formData.value.append('fax_number', form.value.fax_number);
    formData.value.append('email', form.value.email);
    formData.value.append('address', form.value.address);
    formData.value.append('tax_office', form.value.tax_office);
    formData.value.append('tax_number', form.value.tax_number);
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await company.updateCompany(formData.value, props.company.id).then(() => {
            clearLogo();
            formData.value = new FormData();
            if (company.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Company updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await company.createCompany(formData.value).then(() => {
            clearLogo();
            formData.value = new FormData();
            if (company.respStatus) {
                toggleModal();
                emit('newCompany', form.value);
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Company created successfully!', life: 3000 });
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
        <div class="field col-12 m-0">
            <label for="logo" class="block text-900 text-lg font-medium">Logo</label>
            <div class="w-full h-8rem mb-5" :style="{ background: 'url(' + defaultLogo.url + ') no-repeat center center', 'background-size': 'cover' }"></div>
            <div class="w-full flex align-items-center justify-content-between gap-3">
                <label for="logo" class="w-full">
                    <span class="bg-primary py-3 border-round text-center cursor-pointer font-bold hover:bg-primary-600 block">{{ defaultLogo.name ? defaultLogo.name : 'Select Logo' }}</span>
                    <input type="file" id="logo" class="p-sr-only" @change="changePreview" @input="form.logo = $event.target.files[0]" />
                </label>
                <Button @click="clearLogo" v-if="defaultLogo.url !== `${contextPath}layout/images/default.png`" class="p-button-danger py-3 border-round text-center" icon="pi pi-times" />
            </div>
            <span v-if="company.errors.logo" id="logo" class="block p-error">{{ company.errors.logo[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <InputText class="w-full" placeholder="Tax office" v-model="form.tax_office" />
            <span v-if="company.errors.tax_office" id="tax_office" class="block p-error">{{ company.errors.tax_office[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <InputText class="w-full" placeholder="ID number/Tax number" v-model="form.tax_number" />
            <span v-if="company.errors.tax_number" id="tax_number" class="block p-error">{{ company.errors.tax_number[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <InputText class="w-full" placeholder="Name" v-model="form.name" />
            <span v-if="company.errors.name" id="name" class="block p-error">{{ company.errors.name[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <InputText class="w-full" placeholder="Owner name" v-model="form.owner" />
            <span v-if="company.errors.owner" id="owner" class="block p-error">{{ company.errors.owner[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <InputMask mask="(999) 999 9999" unmask class="w-full" placeholder="Tel number" v-model="form.tel_number" />
            <span v-if="company.errors.tel_number" id="tel_number" class="block p-error">{{ company.errors.tel_number[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <InputMask mask="(999) 999 9999" unmask class="w-full" placeholder="GSM number" v-model="form.gsm_number" />
            <span v-if="company.errors.gsm_number" id="gsm_number" class="block p-error">{{ company.errors.gsm_number[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <InputMask mask="(999) 999 9999" unmask class="w-full" placeholder="Fax number" v-model="form.fax_number" />
            <span v-if="company.errors.fax_number" id="fax_number" class="block p-error">{{ company.errors.fax_number[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <InputText type="email" class="w-full" placeholder="Email address" v-model="form.email" />
            <span v-if="company.errors.email" id="email" class="block p-error">{{ company.errors.email[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <Textarea class="w-full" placeholder="Address" v-model="form.address" rows="3" />
            <span v-if="company.errors.address" id="address" class="block p-error">{{ company.errors.address[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
