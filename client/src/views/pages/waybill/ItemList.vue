<script setup>
import { useWaybillStore } from '@/composables/waybill';
import { onMounted, ref, computed } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { formatPrice, formatPhoneNumber, dateFormat, selectedCompany } from '@/composables/utils';
import { useToast } from 'primevue/usetoast';

useHead({
    title: 'Waybill Items'
});

const toast = useToast();
const route = useRoute();
const waybillStore = useWaybillStore();
const loading = ref(false);
const waybillID = parseInt(route.params.waybillID);
const waybill = ref({});
const items = ref([]);
const materials = computed(() => {
    return waybillStore.materials;
});
const waybillItems = ref([]);

onMounted(async () => {
    loading.value = true;
    await waybillStore.getWaybill(waybillID);
    await waybillStore.getMaterials(`&currency_id=${waybillStore.waybill.data.corporation.currency_id}`);
    waybill.value = waybillStore.waybill.data;
    items.value = waybillStore.waybill.data.items;

    if (items.value.length > 0) {
        waybillItems.value = items.value.map((item) => {
            return {
                waybill_id: waybillID,
                material_id: item.material_id,
                quantity: item.quantity,
                price: item.price,
                unit_name: item.unit_name
            };
        });
    }

    loading.value = false;
});

function changeMaterial(event, index) {
    const material = materials.value.find((material) => material.id === event.value);
    waybillItems.value[index].waybill_id = waybillID;
    waybillItems.value[index].material_id = material.id;
    waybillItems.value[index].price = material.price;
    waybillItems.value[index].quantity = 1;
    waybillItems.value[index].unit_name = material.unit.name;
}

function addItem() {
    waybillItems.value.push({
        waybill_id: waybillID,
        material_id: null,
        quantity: 0,
        price: 0,
        unit_name: ''
    });
}

function removeItem(index) {
    waybillItems.value.splice(index, 1);
}

const formData = ref(new FormData());
const submit = async () => {
    loading.value = true;
    waybillItems.value = waybillItems.value.filter((item) => item.material_id !== null);
    waybillItems.value.forEach((item) => {
        delete item.unit_name;
    });
    formData.value.append('items', JSON.stringify(waybillItems.value));
    console.log(formData.value);
    await waybillStore.storeWaybillItems(waybillID, formData.value).then(() => {
        if (waybillStore.respStatus) {
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Waybill items updated successfully!', life: 3000 });
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    });
    loading.value = false;
};
</script>

<template>
    <Toolbar class="col-12 my-3">
        <template #start>
            <p class="text-xl font-bold">
                Waybill Items <small class="text-[var(--surface-400)]">({{ waybill.number }})</small>
            </p>
        </template>

        <template #end>
            <RouterLink to="/waybills">
                <Button label="Go back" icon="pi pi-arrow-left" class="mr-2 p-button-success" />
            </RouterLink>
        </template>
    </Toolbar>

    <div class="card mb-5" ref="printTemplate">
        <ProgressBar v-if="loading" mode="indeterminate" style="height: 0.5em" />
        <div v-else class="grid gap-2">
            <div class="col w-full">
                <div class="text-2xl font-bold tracking-wider uppercase">
                    <div>Waybill</div>
                </div>
            </div>
            <div class="col w-full">
                <div class="flex items-center justify-end gap-2">
                    <div class="relative inline-block">
                        <Button class="cursor-pointer p-button-info font-semibold inline-flex items-center justify-center" icon="pi pi-save" label="Save" @click="submit" />
                    </div>
                </div>
            </div>
            <div class="col-12 w-full">
                <div class="flex mb-8 justify-between">
                    <div class="grid">
                        <div class="col-12 flex items-center">
                            <label class="w-32 text-[var(--text-color)] block font-bold text-sm tracking-wide">Waybill No. :</label>
                            <div class="flex-1">{{ waybill.number }}</div>
                        </div>

                        <div class="col-12 flex items-center">
                            <label class="w-32 text-[var(--text-color)] block font-bold text-sm tracking-wide">Waybill Date :</label>
                            <div class="flex-1">{{ dateFormat(waybill.waybill_date) }}</div>
                        </div>

                        <div class="col-12 flex items-center">
                            <label class="w-32 text-[var(--text-color)] block font-bold text-sm tracking-wide">Due Date :</label>
                            <div class="flex-1">{{ dateFormat(waybill.due_date) }}</div>
                        </div>
                    </div>
                    <div>
                        <img id="image" class="object-cover w-40 h-auto hidden md:block" :src="axios.defaults.baseURL + selectedCompany.logo" />
                    </div>
                </div>
            </div>
            <div class="md:col flex flex-column md:items-start w-full border border-1 border-[var(--surface-border)] rounded p-3">
                <div class="text-md font-bold text-[var(--surface-600)]">{{ waybill.corporation?.name }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ waybill.corporation?.address }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ waybill.corporation?.city }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ formatPhoneNumber(waybill.corporation?.tel_number) }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ waybill.corporation?.email }}</div>
            </div>
            <Divider layout="vertical" />
            <div class="md:col flex flex-column md:items-end w-full border border-1 border-[var(--surface-border)] rounded p-3">
                <div class="text-md font-bold text-[var(--surface-600)]">{{ waybill.company?.name }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ waybill.company?.address }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ waybill.company?.city }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ formatPhoneNumber(waybill.company?.tel_number) }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ waybill.company?.email }}</div>
            </div>
            <div class="w-full">
                <DataTable :value="waybillItems" stripedRows responsiveLayout="scroll">
                    <template #header>
                        <div class="table-header">
                            <div class="flex gap-2 justify-content-end">
                                <Button label="Add" icon="pi pi-plus" class="p-button-success p-button-sm p-button-outlined" @click="addItem" />
                            </div>
                        </div>
                    </template>

                    <Column header="Material">
                        <template #body="slotProps">
                            <Dropdown
                                :virtualScrollerOptions="{ itemSize: 38 }"
                                :filter="true"
                                :options="materials"
                                optionValue="id"
                                v-model="waybillItems[slotProps.index].material_id"
                                optionLabel="name"
                                class="p-inputtext-sm w-72 md:w-96"
                                @change="changeMaterial($event, slotProps.index)"
                            />
                        </template>
                    </Column>
                    <Column header="Unit">
                        <template #body="slotProps">
                            <span>{{ waybillItems[slotProps.index].unit_name }}</span>
                        </template>
                    </Column>
                    <Column header="Quantity">
                        <template #body="slotProps">
                            <InputText v-model="waybillItems[slotProps.index].quantity" class="p-inputtext-sm w-20" />
                        </template>
                    </Column>
                    <Column header="Price">
                        <template #body="slotProps">
                            <InputText v-model="waybillItems[slotProps.index].price" class="p-inputtext-sm w-20" />
                        </template>
                    </Column>
                    <Column header="Total">
                        <template #body="slotProps">
                            <span>{{ formatPrice(waybillItems[slotProps.index].quantity * waybillItems[slotProps.index].price) }}</span>
                        </template>
                    </Column>
                    <Column header="" bodyClass="border-b-0">
                        <template #body="slotProps">
                            <Button icon="pi pi-trash" class="p-button-danger p-button-sm" @click="removeItem(slotProps.index)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- <div class="col-12 w-full border border-1 border-[var(--surface-border)] rounded p-3 min-h-[150px] overflow-auto">
                    <div class="flex -mx-1 border-b py-2 items-start">
                        <div class="flex-1 px-1">
                            <p class="text-[var(--surface-500)] uppercase tracking-wide text-sm font-bold w-72 md:w-96">Material</p>
                        </div>

                        <div class="px-1 w-20">
                            <p class="leading-none">
                                <span class="block uppercase tracking-wide text-sm font-bold text-[var(--surface-500)]">Unit</span>
                            </p>
                        </div>

                        <div class="px-1 w-20">
                            <p class="leading-none">
                                <span class="block uppercase tracking-wide text-sm font-bold text-[var(--surface-500)]">Quantity</span>
                            </p>
                        </div>

                        <div class="px-1 w-24">
                            <p class="leading-none">
                                <span class="block uppercase tracking-wide text-sm font-bold text-[var(--surface-500)]">Price</span>
                            </p>
                        </div>

                        <div class="px-1 text-right w-20">
                            <p class="leading-none">
                                <span class="block uppercase tracking-wide text-sm font-bold text-[var(--surface-500)]">Amount</span>
                            </p>
                        </div>

                        <div class="px-1 w-20 text-right"></div>
                    </div>
                    <template v-for="(item, index) in waybillItems" :key="item.id">
                        <div class="flex -mx-1 py-2 border-b gap-2 items-center">
                            <div class="flex-1 px-1">
                                <Dropdown :options="materials" optionValue="id" v-model="waybillItems[index].material_id" optionLabel="name" class="p-inputtext-sm w-72 md:w-96" @change="changeMaterial($event, index)" />
                            </div>

                            <div class="px-1">
                                <span>{{ waybillItems[index].unit_name }}</span>
                            </div>

                            <div class="px-1">
                                <InputText v-model="waybillItems[index].quantity" class="p-inputtext-sm w-20" />
                            </div>

                            <div class="px-1">
                                <InputText v-model="waybillItems[index].price" class="p-inputtext-sm w-24" />
                            </div>

                            <div class="px-1 text-right">
                                <p class="text-[var(--text-color)] w-20">
                                    {{ formatPrice(waybillItems[index].quantity * waybillItems[index].price) }}
                                </p>
                            </div>

                            <div class="px-1 w-20 text-right">
                                <Button icon="pi pi-trash" class="p-button-danger p-button-sm" v-show="index !== 0" @click="removeItem(index)" />
                            </div>
                        </div>
                    </template>
                </div> -->
            </div>
        </div>
    </div>
</template>
