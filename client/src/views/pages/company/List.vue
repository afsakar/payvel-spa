<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useCompanyStore } from '@/composables/company';
import { FilterMatchMode } from 'primevue/api';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Create from '@/views/pages/company/CreateEditModal.vue';

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const toast = useToast();
const confirm = useConfirm();
const companyStore = useCompanyStore();
const selectedCompany = JSON.parse(localStorage.getItem('selectedCompany'));
const currentList = ref(null);
const companyList = ref([]);
const deletedCompanyList = ref([]);
const editedCompany = ref({});
const isEdit = ref(false);
const loading = ref(false);

onMounted(async () => {
    loading.value = true;
    await companyStore.getCompanies();
    companyList.value = companyStore.companyList.data;
    deletedCompanyList.value = companyStore.deletedCompanies;
    currentList.value = companyList.value;
    loading.value = false;
});

const showModal = ref(false);
function toggleModal() {
    editedCompany.value = {};
    isEdit.value = false;
    showModal.value = !showModal.value;
}

function separateNumber(number) {
    return number.toString().replace(/(\d\d\d)(?=(\d\d))/g, '$1 ');
}

function newCompany(company) {
    companyList.value.push(company);
}

function toggleEditModal(company) {
    loading.value = true;
    companyStore.getCompany(company).then(() => {
        editedCompany.value = companyStore.company.data;
        isEdit.value = true;
        showModal.value = !showModal.value;
        loading.value = false;
    });
}
function deleteItem(event, id) {
    confirm.require({
        message: 'Are you sure you want to proceed?',
        icon: 'pi pi-exclamation-triangle',
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

function forceDeleteItem(event, id) {
    confirm.require({
        message: 'Are you sure? You can not undo this action!',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
            companyStore.forceDeleteCompany(id).then(() => {
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Company deleted successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
        }
    });
}

function restoreItem(id) {
    companyStore.restoreCompany(id).then(() => {
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Company restored successfully!', life: 3000 });
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
}

const isDeletedList = ref(false);
function changeList() {
    isDeletedList.value = !isDeletedList.value;
    if (!isDeletedList.value) {
        currentList.value = companyList.value;
    } else {
        currentList.value = deletedCompanyList.value;
    }
}
</script>

<template>
    <Toolbar class="col-12 my-3">
        <template #start>
            <p class="text-xl font-bold">Companies</p>
        </template>

        <template #end>
            <Button label="New" icon="pi pi-plus" class="mr-2 p-button-success" @click="toggleModal" />
        </template>
    </Toolbar>
    <div class="card">
        <DataTable
            :loading="loading"
            :bodyClass="'mb-3'"
            ref="dt"
            :filters="filters"
            dataKey="id"
            :value="currentList"
            :paginator="true"
            :rows="5"
            stripedRows
            removableSort
            paginatorTemplate="PrevPageLink PageLinks NextPageLink RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 20, 50]"
            responsiveLayout="scroll"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
            class="p-datatable-sm"
        >
            <template #header>
                <div class="table-header">
                    <div class="flex gap-2 justify-content-end">
                        <Button icon="pi pi-list" class="p-button-text" @click="changeList" />
                        <span class="p-input-icon-left">
                            <i class="pi pi-search" />
                            <InputText v-model="filters['global'].value" placeholder="Ara..." />
                        </span>
                    </div>
                </div>
            </template>
            <Column header="Logo">
                <template #body="slotProps">
                    <img v-if="slotProps.data.logo" :src="axios.defaults.baseURL + slotProps.data.logo" :alt="slotProps.data.name" class="w-6rem" />
                </template>
            </Column>
            <Column field="name" header="Name" :sortable="true"></Column>
            <Column field="tel_number" header="Tel Number">
                <template #body="slotProps">
                    <span>{{ separateNumber(slotProps.data.tel_number) }}</span>
                </template>
            </Column>
            <Column field="email" header="Email" :sortable="true"></Column>
            <Column field="tax_number" header="ID number/Tax number" :sortable="true"></Column>
            <Column header="" width="100" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center">
                <template #body="slotProps">
                    <div v-if="slotProps.data.deleted_at === null">
                        <Button icon="pi pi-pencil" class="p-button-warning p-button-text" @click="toggleEditModal(slotProps.data.id)" />
                        <Button :disabled="slotProps.data.id === selectedCompany.id" icon="pi pi-trash" class="p-button-danger p-button-text" @click="deleteItem($event, slotProps.data.id)" />
                    </div>
                    <div v-else>
                        <Button icon="pi pi-refresh" class="p-button-success p-button-text" @click="restoreItem(slotProps.data.id)" />
                        <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="forceDeleteItem($event, slotProps.data.id)" />
                    </div>
                </template>
            </Column>
            <template #empty>
                <div class="text-lg text-primary-400 py-3 flex align-items-center justify-content-center">
                    <div class="flex align-items-center between gap-2">
                        <i class="pi pi-search"></i>
                        <p class="text-center">{{ filters['global'].value ? 'No results found' : 'No data found' }}</p>
                    </div>
                </div>
            </template>
        </DataTable>
    </div>
    <Dialog :modal="true" header="Create Company" v-model:visible="showModal" class="m-3 md:w-5 w-full md:max-w-screen">
        <Create @toggleModal="toggleModal" @newCompany="newCompany" :company="editedCompany" :is-edit="isEdit" />
    </Dialog>
</template>

<style>
.p-paginator-bottom {
    @apply mt-4;
}

.p-datatable-header {
    border: none !important;
}
</style>
