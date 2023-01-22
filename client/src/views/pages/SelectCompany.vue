<script setup>
import { useLayout } from '@/layout/composables/layout';
import { ref, computed, onMounted } from 'vue';
import { useCompanyStore } from '@/composables/company';
import { useHead } from '@unhead/vue';

useHead({
    title: 'Select Company'
});

const { contextPath } = useLayout();
const selectedCompany = ref(null);

const companies = useCompanyStore();
const companyList = ref([]);

const logoUrl = computed(() => {
    return `${contextPath}layout/images/logo-white.svg`;
});

onMounted(async () => {
    await companies.getCompanies();
    companyList.value = [...companies.companyList.data];
});

const submit = async () => {
    await companies.selectCompany(selectedCompany.value);
};
</script>

<template>
    <div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <div class="flex flex-column align-items-center justify-content-center">
            <img :src="logoUrl" alt="Sakai logo" class="mb-5 w-6rem flex-shrink-0" />
            <Message severity="info" class="w-full" :closable="false"> Please select a company to continue </Message>
            <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
                <form @submit.prevent="submit">
                    <div class="field mb-5">
                        <label for="company" class="block text-900 text-xl font-medium">Company</label>
                        <Dropdown :filter="true" :options="companyList" option-label="name" id="company" type="company" class="w-full md:w-30rem" v-model="selectedCompany" />
                    </div>

                    <Button type="submit" label="Submit" class="w-full p-3 text-xl bg-primary hover:bg-primary-600"></Button>
                </form>
            </div>
        </div>
    </div>
</template>
