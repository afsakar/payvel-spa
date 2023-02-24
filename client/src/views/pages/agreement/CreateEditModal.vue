<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAgreementStore } from '@/composables/agreement';
import { useToast } from 'primevue/usetoast';
import Editor from 'primevue/editor';

const props = defineProps({
    agreement: {
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

const agreementStore = useAgreementStore();
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
    await agreementStore.getCorporations();
});

const form = ref({
    name: props.isEdit && props.agreement.name !== null ? props.agreement.name : '',
    content: props.isEdit && props.agreement.content !== null ? props.agreement.content : '',
    corporation_id: props.isEdit && props.agreement.corporation_id !== null ? props.agreement.corporation_id : ''
});

const submit = async () => {
    formData.value.append('name', form.value.name);
    formData.value.append('content', form.value.content);
    formData.value.append('company_id', selectedCompany.value?.id);
    formData.value.append('corporation_id', form.value.corporation_id);
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await agreementStore.updateAgreement(props.agreement.id, formData.value).then(() => {
            formData.value = new FormData();
            if (agreementStore.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Agreement updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await agreementStore.createAgreement(formData.value).then(() => {
            formData.value = new FormData();
            if (agreementStore.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Agreement created successfully!', life: 3000 });
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
            <InputText class="w-full" placeholder="Name" v-model="form.name" />
            <span v-if="agreementStore.errors.name" id="name" class="block p-error">{{ agreementStore.errors.name[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <Editor editorStyle="height: 250px" class="w-full" v-model="form.content">
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
            <span v-if="agreementStore.errors.content" id="content" class="block p-error">{{ agreementStore.errors.content[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Corporation</label>
            <Dropdown :options="agreementStore.corporationsList" option-label="name" option-value="id" class="w-full" placeholder="Select Corporation" v-model="form.corporation_id" />
            <span v-if="agreementStore.errors.corporation_id" id="currency_id" class="block p-error">{{ agreementStore.errors.corporation_id[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
