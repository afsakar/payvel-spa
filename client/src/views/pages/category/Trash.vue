<script setup>
import { useCategoryStore } from '@/composables/category';
import { onMounted, ref, watch } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { useHead } from '@unhead/vue';
import { Bootstrap5Pagination } from 'laravel-vue-pagination';
import axios from 'axios';

useHead({
    title: 'Category List'
});

const filters = ref({
    search: '',
    sort: 'asc',
    orderBy: 'name'
});

const toast = useToast();
const confirm = useConfirm();
const categoryStore = useCategoryStore();
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
    await axios.get(`/api/v1/categories/trash?page=${page}&sort=${filters.value.sort}&order=${filters.value.orderBy}&search=${filters.value.search}`).then((res) => {
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
            categoryStore.forceDeleteCategory(id).then(() => {
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Category deleted successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
        }
    });
}

function restoreItem(id) {
    categoryStore.restoreCategory(id).then(() => {
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Category restored successfully!', life: 3000 });
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
}
</script>

<template>
    <Toolbar class="col-12 my-3">
        <template #start>
            <p class="text-xl font-bold">Category List</p>
        </template>

        <template #end>
            <RouterLink to="/categories">
                <Button label="Go back" icon="pi pi-arrow-left" class="mr-2 p-button-success" />
            </RouterLink>
        </template>
    </Toolbar>

    <Message severity="warn">
        <span>If you perform a deletion at this stage, the operation cannot be undone!</span>
    </Message>

    <div class="card">
        <DataTable :loading="loading" ref="dt" dataKey="id" :value="paginateList.data" stripedRows removableSort responsiveLayout="scroll" class="p-datatable-sm">
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
            <Column field="type">
                <template #header>
                    <SortIcon @click="sortItem('type')" name="Type" column="type" :sort="filters.sort" :active-column="filters.orderBy" />
                </template>
                <template #body="slotProps">
                    <span v-if="slotProps.data.type === 'income'" class="font-semibold text-green-600"> Income </span>
                    <span v-else class="font-semibold text-red-600"> Expense </span>
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
