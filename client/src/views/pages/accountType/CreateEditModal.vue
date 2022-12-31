<script setup>
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useAccountTypeStore } from '@/composables/accountType';

const props = defineProps({
    accountType: {
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

const accountType = useAccountTypeStore();
const toast = useToast();
const formData = ref(new FormData());
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);

const options = ref([
    {
        label: 'Active',
        icon: 'pi pi-check-circle text-green-600 mr-2',
        value: 'active'
    },
    {
        label: 'Inactive',
        icon: 'pi pi-times-circle text-red-600 mr-2',
        value: 'inactive'
    }
]);

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

const form = ref({
    name: props.isEdit && props.accountType.name !== null ? props.accountType.name : '',
    status: props.isEdit && props.accountType.status !== null ? props.accountType.status : '',
});

const submit = async () => {
    formData.value.append('name', form.value.name);
    formData.value.append('status', form.value.status);
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await accountType.updateAccountType(props.accountType.id, formData.value).then(() => {
            formData.value = new FormData();
            if (accountType.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Account Type updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await accountType.createAccountType(formData.value).then(() => {
            formData.value = new FormData();
            if (accountType.respStatus) {
                toggleModal();
                emit('newAccountType', form.value);
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Account Type created successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    }
};
</script>

<template>
    <form class="grid" @submit.prevent="submit">
        <div class="field col-12 m-0">
            <label>Name</label>
            <InputText class="w-full" placeholder="Name" v-model="form.name" />
            <span v-if="accountType.errors.name" id="name" class="block p-error">{{ accountType.errors.name[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Status</label>
            <Dropdown :options="options" option-label="label" option-value="value" class="w-full" placeholder="Select Status" v-model="form.status" />
            <span v-if="accountType.errors.status" id="status" class="block p-error">{{ accountType.errors.status[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
