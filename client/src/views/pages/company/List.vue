<script setup>
import Create from '@/views/pages/company/CreateEditModal.vue';
import { useCompanyStore } from '@/composables/company';
import { onMounted, ref, watch } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { useHead } from '@unhead/vue';
import { Bootstrap5Pagination } from 'laravel-vue-pagination';
import axios from 'axios';
import { selectedCompany } from '@/composables/utils';

useHead({
    title: 'Company List'
});

const filters = ref({
    search: '',
    sort: 'asc',
    orderBy: 'name'
});

const toast = useToast();
const confirm = useConfirm();
const companyStore = useCompanyStore();
const paginateList = ref([]);
const editedItem = ref({});
const isEdit = ref(false);
const loading = ref(false);
const showModal = ref(false);
const checkMeta = ref(false); // check if there is more data to load

onMounted(async () => {
    await getList();
    checkMeta.value = paginateList.value.meta.total >= paginateList.value.meta.per_page ? true : false; // check if there is more data to load
});

async function getList() {
    loading.value = true;
    await getPaginateData();
    loading.value = false;
}

async function getPaginateData(page = 1) {
    await axios.get(`/api/v1/companies?page=${page}&sort=${filters.value.sort}&order=${filters.value.orderBy}&search=${filters.value.search}`).then((res) => {
        paginateList.value = res.data;
    });
}

function sortItem(orderBy) {
    filters.value.orderBy = orderBy;
    filters.value.sort = filters.value.sort == 'asc' ? 'desc' : 'asc';
}

watch(
    filters.value,
    async () => {
        await getList();
    },
    { immediate: true }
);

async function resetFilters() {
    filters.value.search = '';
    filters.value.sort = 'asc';
    filters.value.orderBy = 'name';
}

function toggleModal() {
    editedItem.value = {};
    isEdit.value = false;
    showModal.value = !showModal.value;
}

function toggleEditModal(company) {
    loading.value = true;
    companyStore.getCompany(company).then(() => {
        editedItem.value = companyStore.company.data;
        isEdit.value = true;
        showModal.value = !showModal.value;
        loading.value = false;
    });
}

function deleteItem(event, id) {
    confirm.require({
        message: 'Are you sure you want to proceed?',
        icon: 'pi pi-exclamation-triangle',
        header: 'Confirmation',
        accept: () => {
            companyStore.deleteCompany(id).then(() => {
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Company deleted successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
        }
    });
}

function separateNumber(val) {
    return val.toString().replace(/(\d\d\d)(?=(\d\d))/g, '$1 ');
}
</script>

<template>
    <Toolbar class="col-12 my-3">
        <template #start>
            <p class="text-xl font-bold">Company List</p>
        </template>

        <template #end>
            <Button label="New" icon="pi pi-plus" class="mr-2 p-button-success" @click="toggleModal" />
        </template>
    </Toolbar>

    <div class="card">
        <DataTable :loading="loading" ref="dt" dataKey="id" :value="paginateList.data" stripedRows removableSort responsiveLayout="scroll" class="p-datatable-sm">
            <template #header>
                <div class="table-header">
                    <div class="flex gap-2 justify-content-end">
                        <Button icon="pi pi-history" @click="resetFilters" class="mr-2 p-button-text text-blue-600" />
                        <RouterLink to="/companies/trash">
                            <Button icon="pi pi-trash" class="mr-2 p-button-text text-red-600" />
                        </RouterLink>
                        <span class="p-input-icon-left">
                            <i class="pi pi-search" />
                            <InputText v-model.lazy="filters.search" placeholder="Search..." />
                        </span>
                    </div>
                </div>
            </template>

            <template #empty>
                <div class="text-lg text-primary-400 py-3 flex align-items-center justify-content-center">
                    <div class="flex align-items-center between gap-2">
                        <i class="pi pi-search"></i>
                        <p class="text-center">{{ filters.search ? 'No results found' : 'No data found' }}</p>
                    </div>
                </div>
            </template>

            <Column header="Logo">
                <template #body="slotProps">
                    <img v-if="slotProps.data.logo" :src="axios.defaults.baseURL + slotProps.data.logo" :alt="slotProps.data.name" class="w-6rem" />
                </template>
            </Column>
            <Column field="name">
                <template #header>
                    <SortIcon @click="sortItem('name')" name="Name" column="name" :sort="filters.sort" :active-column="filters.orderBy" />
                </template>
            </Column>
            <Column field="tel_number" header="Tel Number">
                <template #body="slotProps">
                    <span>{{ separateNumber(slotProps.data.tel_number) }}</span>
                </template>
            </Column>
            <Column field="email">
                <template #header>
                    <SortIcon @click="sortItem('email')" name="Email" column="email" :sort="filters.sort" :active-column="filters.orderBy" />
                </template>
            </Column>
            <Column header="" width="100" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center">
                <template #body="slotProps">
                    <div>
                        <Button icon="pi pi-pencil" class="p-button-warning p-button-text" @click="toggleEditModal(slotProps.data.id)" />
                        <Button :disabled="slotProps.data.id === selectedCompany.id" icon="pi pi-trash" class="p-button-danger p-button-text" @click="deleteItem($event, slotProps.data.id)" />
                    </div>
                </template>
            </Column>
            <template #footer>
                <div v-if="checkMeta" class="border border-1 mt-4 rounded border-[#304562] flex items-center justify-center">
                    <Bootstrap5Pagination :data="paginateList" @pagination-change-page="getPaginateData" />
                </div>
            </template>
        </DataTable>
        <Dialog :modal="true" header="Create Company" v-model:visible="showModal" class="m-3 md:w-5 w-full md:max-w-screen">
            <Create @toggleModal="toggleModal" :company="editedItem" :is-edit="isEdit" />
        </Dialog>
    </div>
</template>
