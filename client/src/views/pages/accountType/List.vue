<script setup>
import Create from '@/views/pages/accountType/CreateEditModal.vue';
import { useAccountTypeStore } from '@/composables/accountType';
import { onMounted, ref } from 'vue';
import { FilterMatchMode } from 'primevue/api';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const toast = useToast();
const confirm = useConfirm();
const accountTypeStore = useAccountTypeStore();
const currentList = ref(null);
const accountTypeList = ref([]);
const deletedAccountTypeList = ref([]);
const editedAccountType = ref({});
const isEdit = ref(false);
const loading = ref(false);
const isDeletedList = ref(false);
const showModal = ref(false);

onMounted(async () => {
    loading.value = true;
    await accountTypeStore.getAccountTypes();
    accountTypeList.value = accountTypeStore.accountTypesList.data;
    deletedAccountTypeList.value = accountTypeStore.deletedAccountTypeList;
    currentList.value = accountTypeList.value;
    loading.value = false;
});

function changeList() {
    isDeletedList.value = !isDeletedList.value;
    if (!isDeletedList.value) {
        currentList.value = accountTypeList.value;
    } else {
        currentList.value = deletedAccountTypeList.value;
    }
}
function toggleModal() {
    editedAccountType.value = {};
    isEdit.value = false;
    showModal.value = !showModal.value;
}
function newAccountType(accountType) {
    accountTypeList.value.push(accountType);
}

function toggleEditModal(accountType) {
    loading.value = true;
    accountTypeStore.getAccountType(accountType).then(() => {
        editedAccountType.value = accountTypeStore.accountType.data;
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
            accountTypeStore.deleteAccountType(id).then(() => {
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Account Type deleted successfully!', life: 3000 });
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
            accountTypeStore.forceDeleteAccountType(id).then(() => {
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Account Type deleted successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
        }
    });
}

function restoreItem(id) {
    accountTypeStore.restoreAccountType(id).then(() => {
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Account Type restored successfully!', life: 3000 });
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
}
</script>

<template>
    <Toolbar class="col-12 my-3">
        <template #start>
            <p class="text-xl font-bold">Account Types</p>
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
                        <Button :class="{ 'text-green-600': isDeletedList, 'text-red-600': !isDeletedList }" :icon="isDeletedList ? 'pi pi-list' : 'pi pi-trash'" class="p-button-text" @click="changeList" />
                        <span class="p-input-icon-left">
                            <i class="pi pi-search" />
                            <InputText v-model="filters['global'].value" placeholder="Search..." />
                        </span>
                    </div>
                </div>
            </template>

            <template #empty>
                <div class="text-lg text-primary-400 py-3 flex align-items-center justify-content-center">
                    <div class="flex align-items-center between gap-2">
                        <i class="pi pi-search"></i>
                        <p class="text-center">{{ filters['global'].value ? 'No results found' : 'No data found' }}</p>
                    </div>
                </div>
            </template>

            <Column field="name" header="Name" :sortable="true"></Column>
            <Column field="status" header="Status" :sortable="true">
                <template #body="slotProps">
                    <span v-if="slotProps.data.status === 'active'">
                        <i class="pi pi-check-circle text-green-600"></i>
                    </span>
                    <span v-else>
                        <i class="pi pi-times-circle text-red-600"></i>
                    </span>
                </template>
            </Column>
            <Column header="" width="100" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center">
                <template #body="slotProps">
                    <div v-if="slotProps.data.deleted_at === null">
                        <Button icon="pi pi-pencil" class="p-button-warning p-button-text" @click="toggleEditModal(slotProps.data.id)" />
                        <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="deleteItem($event, slotProps.data.id)" />
                    </div>
                    <div v-else>
                        <Button icon="pi pi-refresh" class="p-button-success p-button-text" @click="restoreItem(slotProps.data.id)" />
                        <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="forceDeleteItem($event, slotProps.data.id)" />
                    </div>
                </template>
            </Column>
        </DataTable>
        <Dialog :modal="true" header="Create Account Type" v-model:visible="showModal" class="m-3 md:w-5 w-full md:max-w-screen">
            <Create @toggleModal="toggleModal" @newAccountType="newAccountType" :account-type="editedAccountType" :is-edit="isEdit" />
        </Dialog>
    </div>
</template>
