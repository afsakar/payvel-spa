<script setup>
import Create from '@/views/pages/account/CreateEditModal.vue';
import { useAccountStore } from '@/composables/account';
import { onMounted, ref, watch } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { useHead } from '@unhead/vue';
import { Bootstrap5Pagination } from 'laravel-vue-pagination';
import axios from 'axios';

useHead({
    title: 'Account List'
});

const filters = ref({
    search: '',
    sort: 'asc',
    orderBy: 'name'
});

const toast = useToast();
const confirm = useConfirm();
const accountStore = useAccountStore();
const accountList = ref([]);
const editedAccount = ref({});
const isEdit = ref(false);
const loading = ref(false);
const showModal = ref(false);
const checkMeta = ref(false); // check if there is more data to load

onMounted(async () => {
    await getList();
    checkMeta.value = accountList.value.meta.total >= accountList.value.meta.per_page ? true : false; // check if there is more data to load
});

async function getList() {
    loading.value = true;
    await getAccountList();
    loading.value = false;
}

async function getAccountList(page = 1) {
    await axios.get(`/api/v1/accounts?page=${page}&sort=${filters.value.sort}&order=${filters.value.orderBy}&search=${filters.value.search}`).then((res) => {
        accountList.value = res.data;
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
    editedAccount.value = {};
    isEdit.value = false;
    showModal.value = !showModal.value;
}

function toggleEditModal(account) {
    loading.value = true;
    accountStore.getAccount(account).then(() => {
        editedAccount.value = accountStore.account.data;
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
            accountStore.deleteAccount(id).then(() => {
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Account deleted successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
        }
    });
}
</script>

<template>
    <Toolbar class="col-12 my-3">
        <template #start>
            <p class="text-xl font-bold">Accounts</p>
        </template>

        <template #end>
            <Button label="New" icon="pi pi-plus" class="mr-2 p-button-success" @click="toggleModal" />
        </template>
    </Toolbar>

    <div class="card">
        <DataTable :loading="loading" ref="dt" dataKey="id" :value="accountList.data" stripedRows removableSort responsiveLayout="scroll" class="p-datatable-sm">
            <template #header>
                <div class="table-header">
                    <div class="flex gap-2 justify-content-end">
                        <Button icon="pi pi-history" @click="resetFilters" class="mr-2 p-button-text text-blue-600" />
                        <RouterLink to="/accounts/trash">
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

            <Column field="name">
                <template #header>
                    <SortIcon @click="sortItem('name')" name="Name" column="name" :sort="filters.sort" :active-column="filters.orderBy" />
                </template>
            </Column>
            <Column field="currency.code">
                <template #header>
                    <SortIcon @click="sortItem('currency_id')" name="Currency" column="currency_id" :sort="filters.sort" :active-column="filters.orderBy" />
                </template>
            </Column>
            <Column field="account_type">
                <template #header>
                    <SortIcon @click="sortItem('account_type_id')" name="Account Type" column="account_type_id" :sort="filters.sort" :active-column="filters.orderBy" />
                </template>
            </Column>
            <Column field="balance">
                <template #header>
                    <SortIcon @click="sortItem('balance')" name="Balance" column="balance" :sort="filters.sort" :active-column="filters.orderBy" />
                </template>
                <template #body="slotProps">
                    <span>{{ slotProps.data.currency.position === 'after' ? slotProps.data.balance + ' ' + slotProps.data.currency.symbol : slotProps.data.currency.symbol + ' ' + slotProps.data.balance }}</span>
                </template>
            </Column>
            <Column header="" width="100" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center">
                <template #body="slotProps">
                    <div>
                        <Button icon="pi pi-pencil" class="p-button-warning p-button-text" @click="toggleEditModal(slotProps.data.id)" />
                        <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="deleteItem($event, slotProps.data.id)" />
                    </div>
                </template>
            </Column>
            <template #footer>
                <div v-if="checkMeta" class="border border-1 mt-4 rounded border-[#304562] flex items-center justify-center">
                    <Bootstrap5Pagination :data="accountList" @pagination-change-page="getAccountList" />
                </div>
            </template>
        </DataTable>
        <Dialog :modal="true" header="Create Account" v-model:visible="showModal" class="m-3 md:w-5 w-full md:max-w-screen">
            <Create @toggleModal="toggleModal" :account="editedAccount" :is-edit="isEdit" />
        </Dialog>
    </div>
</template>
