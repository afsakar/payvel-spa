<script setup>
import { ref, onMounted, watch } from 'vue';
import { useInvoiceStore } from '@/composables/invoice';
import { useToast } from 'primevue/usetoast';
import Editor from 'primevue/editor';
import axios from 'axios';
import { selectedCompany } from '@/composables/utils';

const props = defineProps({
    invoice: {
        type: Object,
        required: true,
        default: () => ({})
    },
    isEdit: {
        type: Boolean,
        required: true,
        default: false
    }
});

const invoiceStore = useInvoiceStore();
const toast = useToast();
const formData = ref(new FormData());
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);
const waybillList = ref([]);

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

onMounted(async () => {
    await invoiceStore.getCorporations();
    await getWaybills();
    await invoiceStore.getWithholdings();
});

const statusList = ref([
    { label: 'Draft', value: 'draft' },
    { label: 'Paid', value: 'paid' },
    { label: 'Canceled', value: 'canceled' }
]);

const form = ref({
    number: props.isEdit && props.invoice.number !== null ? props.invoice.number : '',
    notes: props.isEdit && props.invoice.notes !== null ? props.invoice.notes : '',
    corporation_id: props.isEdit && props.invoice.corporation_id !== null ? props.invoice.corporation_id : '',
    status: props.isEdit && props.invoice.status !== null ? props.invoice.status : '',
    discount: props.isEdit && props.invoice.discount !== null ? props.invoice.discount : '',
    issue_date: props.isEdit && props.invoice.issue_date !== null ? new Date(props.invoice.issue_date) : '',
    waybill_id: props.isEdit && props.invoice.waybill_id !== null ? props.invoice.waybill_id : '',
    withholding_id: props.isEdit && props.invoice.withholding_id !== null ? props.invoice.withholding_id : ''
});

async function getWaybills() {
    await axios.get(`/api/v1/waybills/${selectedCompany.value.id}?all=true&corporation_id=${form.value.corporation_id}&waybill_id=${form.value.waybill_id}`).then((res) => {
        waybillList.value = res.data.data;
    });
}

watch(
    form.value,
    async () => {
        if (form.value.corporation_id !== '') {
            await getWaybills({ target: { value: form.value.corporation_id } });
        }
    },
    { deep: true }
);

const submit = async () => {
    formData.value.append('number', form.value.number);
    formData.value.append('notes', form.value.notes);
    formData.value.append('company_id', selectedCompany.value?.id);
    formData.value.append('corporation_id', form.value.corporation_id);
    formData.value.append('waybill_id', form.value.waybill_id);
    formData.value.append('withholding_id', form.value.withholding_id);
    formData.value.append('status', form.value.status);
    formData.value.append('discount', form.value.discount);
    formData.value.append('issue_date', new Date(form.value.issue_date).toISOString());
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await invoiceStore.updateInvoice(props.invoice.id, formData.value).then(() => {
            formData.value = new FormData();
            if (invoiceStore.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Invoice updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await invoiceStore.createInvoice(formData.value).then(() => {
            formData.value = new FormData();
            if (invoiceStore.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Invoice created successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="grid">
        <div class="field col-12 m-0">
            <label>Number</label>
            <InputText class="w-full" placeholder="Number" v-model="form.number" />
            <span v-if="invoiceStore.errors.number" id="number" class="block p-error">{{ invoiceStore.errors.number[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Discount</label>
            <InputText class="w-full" placeholder="Discount" v-model="form.discount" />
            <span v-if="invoiceStore.errors.discount" id="discount" class="block p-error">{{ invoiceStore.errors.discount[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Issue Date</label>
            <Calendar class="w-full" inputId="issue_date" v-model="form.issue_date" autocomplete="off" dateFormat="dd/mm/yy" />
            <span v-if="invoiceStore.errors.issue_date" id="issue_date" class="block p-error">{{ invoiceStore.errors.issue_date[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Content</label>
            <Editor editorStyle="height: 150px" class="w-full" v-model="form.notes">
                <template #toolbar>
                    <span class="ql-formats">
                        <select class="ql-size">
                            <option value="small"></option>
                            <!-- Note a missing, thus falsy value, is used to reset to default -->
                            <option selected></option>
                            <option value="large"></option>
                            <option value="huge"></option>
                        </select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-bold"></button>
                        <button class="ql-italic"></button>
                        <button class="ql-underline"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-list" value="ordered"></button>
                        <button class="ql-list" value="bullet"></button>
                        <select class="ql-align">
                            <option class="ql-picker-item"></option>
                            <option class="ql-picker-item" value="center"></option>
                            <option class="ql-picker-item" value="right"></option>
                            <option class="ql-picker-item" value="justify"></option>
                        </select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-clean" type="button"></button>
                    </span>
                </template>
            </Editor>
            <span v-if="invoiceStore.errors.notes" id="notes" class="block p-error">{{ invoiceStore.errors.notes[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Corporation</label>
            <Dropdown
                @change="getWaybills($event)"
                :options="invoiceStore.corporationsList"
                :virtualScrollerOptions="{ itemSize: 38 }"
                :filter="true"
                option-label="name"
                option-value="id"
                class="w-full"
                placeholder="Select Corporation"
                v-model="form.corporation_id"
            />
            <span v-if="invoiceStore.errors.corporation_id" id="currency_id" class="block p-error">{{ invoiceStore.errors.corporation_id[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Waybill</label>
            <Dropdown
                :disabled="form.corporation_id === ''"
                :options="waybillList"
                :virtualScrollerOptions="{ itemSize: 38 }"
                :filter="true"
                option-label="number"
                option-value="id"
                class="w-full"
                placeholder="Select Waybill"
                v-model="form.waybill_id"
            />
            <span v-if="invoiceStore.errors.waybill_id" id="waybill_id" class="block p-error">{{ invoiceStore.errors.waybill_id[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Withholding</label>
            <Dropdown :options="invoiceStore.withholdingList" :virtualScrollerOptions="{ itemSize: 38 }" :filter="true" option-label="name" option-value="id" class="w-full" placeholder="Select Withholding" v-model="form.withholding_id" />
            <span v-if="invoiceStore.errors.withholding_id" id="withholding_id" class="block p-error">{{ invoiceStore.errors.withholding_id[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Status</label>
            <Dropdown :options="statusList" option-label="label" option-value="value" class="w-full" placeholder="Select Status" v-model="form.status" />
            <span v-if="invoiceStore.errors.status" id="status" class="block p-error">{{ invoiceStore.errors.status[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
