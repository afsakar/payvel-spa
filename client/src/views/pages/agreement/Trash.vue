<script setup>
import { useAgreementStore } from '@/composables/agreement';
import { onMounted, ref, watch } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { useHead } from '@unhead/vue';
import { Bootstrap5Pagination } from 'laravel-vue-pagination';
import axios from 'axios';
import { selectedCompany } from '@/composables/utils';

useHead({
    title: 'Deleted Agreements List'
});

const filters = ref({
    search: '',
    sort: 'asc',
    orderBy: 'name'
});

const toast = useToast();
const confirm = useConfirm();
const agreementStore = useAgreementStore();
const deletedList = ref([]);
const loading = ref(false);
const checkMeta = ref(false); // check if there is more data to load

onMounted(async () => {
    await getList();
    checkMeta.value = deletedList.value.meta.total >= deletedList.value.meta.per_page ? true : false; // check if there is more data to load
});

async function getList() {
    loading.value = true;
    await getDeletedList();
    loading.value = false;
}

async function getDeletedList(page = 1) {
    await axios.get(`/api/v1/agreements/${selectedCompany.value.id}/trash?page=${page}&sort=${filters.value.sort}&order=${filters.value.orderBy}&search=${filters.value.search}`).then((res) => {
        deletedList.value = res.data;
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

function forceDeleteItem(event, id) {
    confirm.require({
        message: 'Are you sure? You can not undo this action!',
        icon: 'pi pi-exclamation-triangle',
        header: 'Confirmation',
        accept: () => {
            agreementStore.forceDeleteAgreement(id).then(() => {
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Agreement deleted successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
        }
    });
}

function restoreItem(id) {
    agreementStore.restoreAgreement(id).then(() => {
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Agreement restored successfully!', life: 3000 });
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
}
</script>

<template>
    <Toolbar class="col-12 my-3">
        <template #start>
            <p class="text-xl font-bold">Deleted Agreements</p>
        </template>

        <template #end>
            <RouterLink to="/agreements">
                <Button label="Go back" icon="pi pi-arrow-left" class="mr-2 p-button-success" />
            </RouterLink>
        </template>
    </Toolbar>

    <Message severity="warn">
        <span>If you perform a deletion at this stage, the operation cannot be undone!</span>
    </Message>

    <div class="card">
        <DataTable :loading="loading" ref="dt" dataKey="id" :value="deletedList.data" stripedRows removableSort responsiveLayout="scroll" class="p-datatable-sm">
            <template #header>
                <div class="table-header">
                    <div class="flex gap-2 justify-content-end">
                        <Button icon="pi pi-history" @click="resetFilters" class="mr-2 p-button-text text-blue-600" />
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
            <Column field="corporation.name">
                <template #header>
                    <SortIcon @click="sortItem('corporation_id')" name="Corporation" column="corporation_id" :sort="filters.sort" :active-column="filters.orderBy" />
                </template>
            </Column>
            <Column header="" width="100" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center">
                <template #body="slotProps">
                    <div>
                        <Button icon="pi pi-refresh" class="p-button-success p-button-text" @click="restoreItem(slotProps.data.id)" />
                        <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="forceDeleteItem($event, slotProps.data.id)" />
                    </div>
                </template>
            </Column>
            <template #footer>
                <div v-if="checkMeta" class="border border-1 mt-4 rounded border-[#304562] flex items-center justify-center">
                    <Bootstrap5Pagination :data="deletedList" @pagination-change-page="getDeletedList" />
                </div>
            </template>
        </DataTable>
    </div>
</template>
