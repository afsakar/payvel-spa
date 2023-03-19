<script setup>
import { useInvoiceStore } from '@/composables/invoice';
import { onMounted, ref, computed } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { formatPrice, formatPhoneNumber, dateFormat, selectedCompany } from '@/composables/utils';
import { useToast } from 'primevue/usetoast';

useHead({
    title: 'Invoice Items'
});

const toast = useToast();
const route = useRoute();
const invoiceStore = useInvoiceStore();
const loading = ref(false);
const invoiceID = parseInt(route.params.invoiceID);
const invoice = ref({});
const items = ref([]);
const materials = ref([]);
const withholding = ref(null);
const invoiceItems = ref([]);

onMounted(async () => {
    loading.value = true;
    await invoiceStore.getInvoice(invoiceID);
    await invoiceStore.getMaterials(`&currency_id=${invoiceStore.invoice.data.corporation.currency_id}`);
    invoice.value = invoiceStore.invoice.data;
    items.value = invoiceStore.invoice.data.waybill.items;
    withholding.value = invoiceStore.invoice.data.withholding;
    materials.value = invoiceStore.materials;

    invoiceItems.value = items.value.map((item) => {
        return {
            invoice_id: invoiceID,
            material_id: item.material_id,
            quantity: item.quantity,
            price: item.price,
            unit_name: item.unit_name,
            tax_rate: item.material.tax.rate
        };
    });

    loading.value = false;
});

function changeMaterial(event, index) {
    const material = materials.value.find((material) => material.id === event.value);
    invoiceItems.value[index].invoice_id = invoiceID;
    invoiceItems.value[index].material_id = material.id;
    invoiceItems.value[index].price = material.price;
    invoiceItems.value[index].quantity = 1;
    invoiceItems.value[index].unit_name = material.unit.name;
    invoiceItems.value[index].tax_rate = material.tax.rate;
}

function addItem() {
    invoiceItems.value.push({
        invoice_id: invoiceID,
        material_id: null,
        quantity: 0,
        price: 0,
        unit_name: '',
        tax_rate: 0
    });
}

function removeItem(index) {
    invoiceItems.value.splice(index, 1);
}

const formData = ref(new FormData());
const submit = async () => {
    loading.value = true;
    invoiceItems.value = invoiceItems.value.filter((item) => item.material_id !== null);
    invoiceItems.value.forEach((item) => {
        delete item.unit_name;
    });
    formData.value.append('items', JSON.stringify(invoiceItems.value));
    formData.value.append('waybill_id', invoice.value.waybill_id);
    formData.value.append('discount', invoice.value.discount ?? 0);
    await invoiceStore.storeInvoiceItems(invoiceID, formData.value).then(() => {
        if (invoiceStore.respStatus) {
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Invoice items updated successfully!', life: 3000 });
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    });
    loading.value = false;
};

const subtotal = computed(() => {
    let subtotal = 0;
    invoiceItems.value.forEach((item) => {
        subtotal += item.quantity * item.price;
    });
    return subtotal;
});

const totalTax = computed(() => {
    let totalTax = 0;
    invoiceItems.value.forEach((item) => {
        totalTax += (item.quantity * item.price * item.tax_rate) / 100;
    });
    return totalTax;
});

const totalWithholding = computed(() => {
    return (totalTax.value * withholding.value?.rate) / 100;
});

// filter tax list by material group by tax
const filteredTaxList = computed(() => {
    let taxList = [];
    invoiceItems.value.forEach((item) => {
        const material = materials.value.find((material) => material.id === item.material_id);
        if (material) {
            taxList.push({
                tax: material.tax.rate
            });
        }
    });
    return taxList.filter((tax, index, self) => self.findIndex((t) => t.tax === tax.tax) === index);
});

const taxSumList = computed(() => {
    let taxList = [];
    filteredTaxList.value.forEach((tax) => {
        let total = 0;
        invoiceItems.value.forEach((item) => {
            if (item.tax_rate === tax.tax) {
                total += (item.quantity * item.price * item.tax_rate) / 100;
            }
        });
        taxList.push({
            tax: tax.tax,
            total: total
        });
    });
    return taxList;
});
</script>

<template>
    <Toolbar class="col-12 my-3">
        <template #start>
            <p class="text-xl font-bold">
                Invoice Items <small class="text-[var(--surface-400)]">({{ invoice.number }})</small>
            </p>
        </template>

        <template #end>
            <RouterLink to="/invoices">
                <Button label="Go back" icon="pi pi-arrow-left" class="mr-2 p-button-success" />
            </RouterLink>
        </template>
    </Toolbar>

    <Message severity="info" :closable="false">
        <span>If you make changes to the items, the relevant waybill will change accordingly!</span>
    </Message>

    <div class="card mb-5" ref="printTemplate">
        <ProgressBar v-if="loading" mode="indeterminate" style="height: 0.5em" />
        <div v-else class="grid gap-2">
            <div class="col w-full">
                <div class="text-2xl font-bold tracking-wider uppercase">
                    <div>Invoice</div>
                </div>
            </div>
            <div class="col w-full">
                <div class="flex items-center justify-end gap-2">
                    <div class="relative inline-block">
                        <Button class="cursor-pointer print:hidden p-button-info font-semibold inline-flex items-center justify-center" icon="pi pi-save" label="Save" @click="submit" />
                    </div>
                </div>
            </div>
            <div class="col-12 w-full">
                <div class="flex mb-8 justify-between">
                    <div class="grid">
                        <div class="col-12 flex items-center">
                            <label class="w-32 text-[var(--text-color)] block font-bold text-sm tracking-wide">Invoice No. :</label>
                            <div class="flex-1">{{ invoice.number }}</div>
                        </div>

                        <div class="col-12 flex items-center">
                            <label class="w-32 text-[var(--text-color)] block font-bold text-sm tracking-wide">Issue Date :</label>
                            <div class="flex-1">{{ dateFormat(invoice.issue_date) }}</div>
                        </div>
                    </div>
                    <div>
                        <img id="image" class="object-cover w-40 h-auto hidden md:block" :src="axios.defaults.baseURL + selectedCompany.logo" />
                    </div>
                </div>
            </div>
            <div class="md:col flex flex-column md:items-start w-full border border-1 border-[var(--surface-border)] rounded p-3">
                <div class="text-md font-bold text-[var(--surface-600)]">{{ invoice.corporation?.name }}</div>
                <div class="text-md font-bold text-[var(--surface-600)]">{{ invoice.corporation?.tax_office }}</div>
                <div class="text-md font-bold text-[var(--surface-600)]">{{ invoice.corporation?.tax_number }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ invoice.corporation?.city }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ formatPhoneNumber(invoice.corporation?.tel_number) }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ invoice.corporation?.email }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ invoice.corporation?.address }}</div>
            </div>
            <Divider layout="vertical" />
            <div class="md:col flex flex-column md:items-end w-full border border-1 border-[var(--surface-border)] rounded p-3">
                <div class="text-md font-bold text-[var(--surface-600)]">{{ invoice.company?.name }}</div>
                <div class="text-md font-bold text-[var(--surface-600)]">{{ invoice.company?.tax_office }}</div>
                <div class="text-md font-bold text-[var(--surface-600)]">{{ invoice.company?.tax_number }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ invoice.company?.city }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ formatPhoneNumber(invoice.company?.tel_number) }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ invoice.company?.email }}</div>
                <div class="text-md text-[var(--surface-600)]">{{ invoice.company?.address }}</div>
            </div>
            <div class="w-full">
                <DataTable :value="invoiceItems" responsiveLayout="scroll">
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
                                v-model="invoiceItems[slotProps.index].material_id"
                                optionLabel="name"
                                class="p-inputtext-sm w-72 md:w-96"
                                @change="changeMaterial($event, slotProps.index)"
                            />
                        </template>
                    </Column>
                    <Column header="Unit">
                        <template #body="slotProps">
                            <span>{{ invoiceItems[slotProps.index].unit_name }}</span>
                        </template>
                    </Column>
                    <Column header="Quantity">
                        <template #body="slotProps">
                            <InputText v-model="invoiceItems[slotProps.index].quantity" class="p-inputtext-sm w-20" />
                        </template>
                    </Column>
                    <Column header="Price">
                        <template #body="slotProps">
                            <InputText v-model="invoiceItems[slotProps.index].price" class="p-inputtext-sm w-20" />
                        </template>
                    </Column>
                    <Column header="Tax Rate" style="width: 5%; min-width: 5rem">
                        <template #body="slotProps">
                            <span>{{ invoiceItems[slotProps.index].tax_rate }} %</span>
                        </template>
                    </Column>
                    <Column header="Tax">
                        <template #body="slotProps">
                            <span>{{ formatPrice((invoiceItems[slotProps.index].quantity * invoiceItems[slotProps.index].price * invoiceItems[slotProps.index].tax_rate) / 100) }}</span>
                        </template>
                    </Column>
                    <Column header="Subtotal">
                        <template #body="slotProps">
                            <span>{{ formatPrice(invoiceItems[slotProps.index].quantity * invoiceItems[slotProps.index].price) }}</span>
                        </template>
                    </Column>
                    <Column header="" bodyClass="border-b-0">
                        <template #body="slotProps">
                            <Button icon="pi pi-trash" class="p-button-danger p-button-sm" @click="removeItem(slotProps.index)" />
                        </template>
                    </Column>

                    <template #footer>
                        <div class="mt-10 flex items-center justify-content-end px-3">
                            <div class="py-2 ml-auto md:w-3/5 w-[320px]">
                                <div class="flex justify-between mb-3">
                                    <div class="text-right flex-1">Subtotal:</div>
                                    <div class="text-right w-40">
                                        <span class="font-medium">{{ formatPrice(subtotal) }}</span>
                                    </div>
                                </div>
                                <div v-for="sum in taxSumList" :key="sum.tax" class="flex justify-between mb-4">
                                    <div class="text-xs text-right flex-1">Tax ({{ sum.tax }}%):</div>
                                    <div class="text-xs text-right w-40">
                                        <span>{{ formatPrice(sum.total) }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between mb-4">
                                    <div class="text-right flex-1">Total Tax:</div>
                                    <div class="text-right w-40">
                                        <span>{{ formatPrice(totalTax) }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between mb-4">
                                    <div class="text-right flex-1">Withholding:</div>
                                    <div class="text-right w-40">
                                        <span>{{ formatPrice(totalWithholding) }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between mb-4">
                                    <div class="text-right flex-1">Discount:</div>
                                    <div class="text-right w-40">
                                        <InputText v-model="invoice.discount" class="p-inputtext-sm w-20" />
                                    </div>
                                </div>

                                <div class="py-2 border-t border-b">
                                    <div class="flex justify-between">
                                        <div class="text-xl text-right flex-1">Total:</div>
                                        <div class="text-right w-40">
                                            <div class="text-xl font-bold">{{ formatPrice(subtotal + totalTax - totalWithholding - invoice.discount) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full border border-1 border-[var(--surface-border)] rounded p-3 mt-5 flex items-center justify-content-start font-normal">
                            <div v-html="invoice.notes" />
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </div>
</template>
