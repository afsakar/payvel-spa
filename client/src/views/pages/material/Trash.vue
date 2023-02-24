<script setup>
import { useMaterialStore } from '@/composables/material';
import { onMounted, ref, watch } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { useHead } from '@unhead/vue';
import { Bootstrap5Pagination } from 'laravel-vue-pagination';
import axios from 'axios';

useHead({
    title: 'Deleted Material List'
});

const filters = ref({
    search: '',
    sort: 'asc',
    orderBy: 'name'
});

const types = ref({
    service: 'Service',
    procurement: 'Procurement',
    service_procurement: 'Service & Procurement'
});

const categories = ref({
    construction: 'Construction',
    electricity: 'Electricity',
    plumbing: 'Plumbing',
    electronics: 'Electronics',
    other: 'Other'
});

const toast = useToast();
const confirm = useConfirm();
const materialStore = useMaterialStore();
const paginateList = ref([]);
const loading = ref(false);
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
    await axios.get(`/api/v1/materials/trash?page=${page}&sort=${filters.value.sort}&order=${filters.value.orderBy}&search=${filters.value.search}`).then((res) => {
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

function forceDeleteItem(event, id) {
    confirm.require({
        message: 'Are you sure? You can not undo this action!',
        icon: 'pi pi-exclamation-triangle',
        header: 'Confirmation',
        accept: () => {
            materialStore.forceDeleteMaterial(id).then(() => {
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Material deleted successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
        }
    });
}

function restoreItem(id) {
    materialStore.restoreMaterial(id).then(() => {
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Material restored successfully!', life: 3000 });
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
}
</script>

<template>
    <Toolbar class="col-12 my-3">
        <template #start>
            <p class="text-xl font-bold">Deleted Material List</p>
        </template>

        <template #end>
            <RouterLink to="/materials">
                <Button label="Go back" icon="pi pi-arrow-left" class="mr-2 p-button-success" />
            </RouterLink>
        </template>
    </Toolbar>

    <div class="card">
        <DataTable :loading="loading" ref="dt" dataKey="id" :value="paginateList.data" stripedRows responsiveLayout="scroll" class="p-datatable-sm">
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

            <Column field="code" style="width: 10%">
                <template #header>
                    <SortIcon @click="sortItem('code')" name="Code" column="code" :sort="filters.sort" :active-column="filters.orderBy" />
                </template>
            </Column>
            <Column field="name">
                <template #header>
                    <SortIcon @click="sortItem('name')" name="Name" column="name" :sort="filters.sort" :active-column="filters.orderBy" />
                </template>
            </Column>
            <Column field="type" header="Type">
                <template #body="slotProps">
                    <span class="text-sm">{{ types[slotProps.data.type] }}</span>
                </template>
            </Column>
            <Column field="category" header="Material">
                <template #body="slotProps">
                    <span class="text-sm">{{ categories[slotProps.data.category] }}</span>
                </template>
            </Column>
            <Column field="price" header="Price" style="width: 10%">
                <template #body="slotProps">
                    <span class="text-sm">{{ slotProps.data.currency.position === 'before' ? slotProps.data.currency.symbol + ' ' + slotProps.data.price : slotProps.data.price + ' ' + slotProps.data.currency.symbol }}</span>
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
                    <Bootstrap5Pagination :data="paginateList" @pagination-change-page="getPaginateData" />
                </div>
            </template>
        </DataTable>
    </div>
</template>
