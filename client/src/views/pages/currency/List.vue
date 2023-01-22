<script setup>
import { onMounted, ref } from 'vue';
import { useCurrencyStore } from '@/composables/currencies';
import { FilterMatchMode } from 'primevue/api';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Create from '@/views/pages/currency/CreateEditModal.vue';
import { useHead } from '@unhead/vue';

useHead({
    title: 'Currency List'
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const toast = useToast();
const confirm = useConfirm();
const currencyStore = useCurrencyStore();
const currentList = ref(null);
const currencyList = ref([]);
const deletedCurrencyList = ref([]);
const editedCurrency = ref({});
const isEdit = ref(false);
const loading = ref(false);
const isDeletedList = ref(false);
const showModal = ref(false);

onMounted(async () => {
    loading.value = true;
    await currencyStore.getCurrencies();
    currencyList.value = currencyStore.currencyList.data;
    deletedCurrencyList.value = currencyStore.deletedCurrencies;
    currentList.value = currencyList.value;
    loading.value = false;
});

function changeList() {
    isDeletedList.value = !isDeletedList.value;
    if (!isDeletedList.value) {
        currentList.value = currencyList.value;
    } else {
        currentList.value = deletedCurrencyList.value;
    }
}
function toggleModal() {
    editedCurrency.value = {};
    isEdit.value = false;
    showModal.value = !showModal.value;
}
function newCurrency(currency) {
    currencyList.value.push(currency);
}

function toggleEditModal(currency) {
    loading.value = true;
    currencyStore.getCurrency(currency).then(() => {
        editedCurrency.value = currencyStore.currency.data;
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
            currencyStore.deleteCurrency(id).then(() => {
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Currency deleted successfully!', life: 3000 });
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
            currencyStore.forceDeleteCurrency(id).then(() => {
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Currency deleted successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
        }
    });
}

function restoreItem(id) {
    currencyStore.restoreCurrency(id).then(() => {
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Currency restored successfully!', life: 3000 });
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
}
</script>

<template>
    <Toolbar class="col-12 my-3">
        <template #start>
            <p class="text-xl font-bold">Currencies</p>
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

            <Column>
                <template #body="slotProps">
                    <div :class="`currency-flag currency-flag-${slotProps.data.code.toLowerCase()}`"></div>
                </template>
            </Column>
            <Column field="name" header="Name" :sortable="true"></Column>
            <Column field="code" header="Code" :sortable="true"></Column>
            <Column field="symbol" header="Symbol"></Column>
            <Column field="position" header="Position">
                <template #body="slotProps">
                    <span v-if="slotProps.data.position === 'after'">After</span>
                    <span v-else>Before</span>
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
        <Dialog :modal="true" header="Create Currency" v-model:visible="showModal" class="m-3 md:w-5 w-full md:max-w-screen">
            <Create @toggleModal="toggleModal" @newCurrency="newCurrency" :currency="editedCurrency" :is-edit="isEdit" />
        </Dialog>
    </div>
</template>
