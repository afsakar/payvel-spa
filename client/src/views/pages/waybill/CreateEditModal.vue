<script setup>
import { ref, onMounted, computed } from 'vue';
import { useWaybillStore } from '@/composables/waybill';
import { useToast } from 'primevue/usetoast';
import Editor from 'primevue/editor';

const props = defineProps({
    waybill: {
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

const waybillStore = useWaybillStore();
const selectedCompany = computed(() => {
    return JSON.parse(localStorage.getItem('selectedCompany'));
});
const toast = useToast();
const formData = ref(new FormData());
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

onMounted(async () => {
    await waybillStore.getCorporations();
});

const statusList = ref([
    { label: 'Shipping', value: 'shipping' },
    { label: 'Delivered', value: 'delivered' },
    { label: 'Canceled', value: 'canceled' }
]);

const form = ref({
    number: props.isEdit && props.waybill.number !== null ? props.waybill.number : '',
    content: props.isEdit && props.waybill.content !== null ? props.waybill.content : '',
    corporation_id: props.isEdit && props.waybill.corporation_id !== null ? props.waybill.corporation_id : '',
    address: props.isEdit && props.waybill.address !== null ? props.waybill.address : '',
    status: props.isEdit && props.waybill.status !== null ? props.waybill.status : '',
    due_date: props.isEdit && props.waybill.due_date !== null ? new Date(props.waybill.due_date) : '',
    waybill_date: props.isEdit && props.waybill.waybill_date !== null ? new Date(props.waybill.waybill_date) : ''
});

const submit = async () => {
    formData.value.append('number', form.value.number);
    formData.value.append('content', form.value.content);
    formData.value.append('company_id', selectedCompany.value?.id);
    formData.value.append('corporation_id', form.value.corporation_id);
    formData.value.append('status', form.value.status);
    formData.value.append('address', form.value.address);
    formData.value.append('due_date', new Date(form.value.due_date).toISOString());
    formData.value.append('waybill_date', new Date(form.value.waybill_date).toISOString());
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await waybillStore.updateWaybill(props.waybill.id, formData.value).then(() => {
            formData.value = new FormData();
            if (waybillStore.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Waybill updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await waybillStore.createWaybill(formData.value).then(() => {
            formData.value = new FormData();
            if (waybillStore.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Waybill created successfully!', life: 3000 });
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
            <span v-if="waybillStore.errors.number" id="number" class="block p-error">{{ waybillStore.errors.number[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Address</label>
            <Textarea class="w-full" placeholder="Address" v-model="form.address" rows="5" />
            <span v-if="waybillStore.errors.address" id="address" class="block p-error">{{ waybillStore.errors.address[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Waybill Date</label>
            <Calendar class="w-full" inputId="waybill_date" v-model="form.waybill_date" autocomplete="off" dateFormat="dd/mm/yy" />
            <span v-if="waybillStore.errors.waybill_date" id="waybill_date" class="block p-error">{{ waybillStore.errors.waybill_date[0] }}</span>
        </div>

        <div class="field md:col-6 col-12 m-0">
            <label>Due Date</label>
            <Calendar class="w-full" inputId="due_date" v-model="form.due_date" autocomplete="off" dateFormat="dd/mm/yy" />
            <span v-if="waybillStore.errors.due_date" id="due_date" class="block p-error">{{ waybillStore.errors.due_date[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Content</label>
            <Editor editorStyle="height: 150px" class="w-full" v-model="form.content">
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
            <span v-if="waybillStore.errors.content" id="content" class="block p-error">{{ waybillStore.errors.content[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Corporation</label>
            <Dropdown :options="waybillStore.corporationsList" option-label="name" option-value="id" class="w-full" placeholder="Select Corporation" v-model="form.corporation_id" />
            <span v-if="waybillStore.errors.corporation_id" id="currency_id" class="block p-error">{{ waybillStore.errors.corporation_id[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Status</label>
            <Dropdown :options="statusList" option-label="label" option-value="value" class="w-full" placeholder="Select Status" v-model="form.status" />
            <span v-if="waybillStore.errors.status" id="status" class="block p-error">{{ waybillStore.errors.status[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
