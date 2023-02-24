<script setup>
import { onMounted, ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useAccountStore } from '@/composables/account';

const props = defineProps({
    account: {
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

const account = useAccountStore();
const toast = useToast();
const formData = ref(new FormData());
const emit = defineEmits(['toggleModal']);
const showModal = ref(false);
const selectedCurrency = ref({
    name: '',
    code: 'TRY',
    symbol: '₺',
    position: 'after'
});

onMounted(async () => {
    await account.getAccountTypes();
    await account.getCurrencies();
    if (props.isEdit) {
        await account.getAccount(props.account.id);
        selectedCurrency.value = account.currenciesList.find((currency) => currency.id === account.account.data.currency_id);
    }
});

function toggleModal() {
    showModal.value = !showModal.value;
    emit('toggleModal', showModal.value);
}

const form = ref({
    name: props.isEdit && props.account.name !== null ? props.account.name : '',
    account_type_id: props.isEdit && props.account.account_type_id !== null ? props.account.account_type_id : '',
    currency_id: props.isEdit && props.account.currency_id !== null ? props.account.currency_id : '',
    balance: props.isEdit && props.account.balance !== null ? props.account.balance : 0,
    description: props.isEdit && props.account.description !== null ? props.account.description : ''
});

const submit = async () => {
    formData.value.append('name', form.value.name);
    formData.value.append('account_type_id', form.value.account_type_id);
    formData.value.append('currency_id', form.value.currency_id);
    formData.value.append('balance', form.value.balance);
    formData.value.append('description', form.value.description);
    if (props.isEdit) {
        formData.value.append('_method', 'PUT');
        await account.updateAccount(props.account.id, formData.value).then(() => {
            formData.value = new FormData();
            if (account.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Account updated successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    } else {
        await account.createAccount(formData.value).then(() => {
            formData.value = new FormData();
            if (account.respStatus) {
                toggleModal();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Account created successfully!', life: 3000 });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
    }
};

function changeCurrency(e) {
    selectedCurrency.value = account.currenciesList.find((currency) => currency.id === e.value);
    console.log(selectedCurrency.value.code);
}
</script>

<template>
    <form class="grid" @submit.prevent="submit">
        <div class="field col-12 m-0">
            <label>Name</label>
            <InputText class="w-full" placeholder="Name" v-model="form.name" />
            <span v-if="account.errors.name" id="name" class="block p-error">{{ account.errors.name[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Account Type</label>
            <Dropdown :options="account.accountTypesList" option-label="name" option-value="id" class="w-full" placeholder="Select Account Type" v-model="form.account_type_id" />
            <span v-if="account.errors.account_type_id" id="account_type_id" class="block p-error">{{ account.errors.account_type_id[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Currency</label>
            <Dropdown :options="account.currenciesList" option-label="name" option-value="id" class="w-full" placeholder="Select Currency" v-model="form.currency_id" @change="changeCurrency($event)" />
            <span v-if="account.errors.currency_id" id="currency_id" class="block p-error">{{ account.errors.currency_id[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Balance</label>
            <InputNumber
                mode="decimal"
                :minFractionDigits="2"
                :maxFractionDigits="2"
                class="w-full"
                placeholder="Balance"
                v-model="form.balance"
                :currency="selectedCurrency.code"
                :prefix="selectedCurrency.position === 'before' ? selectedCurrency.symbol + ' ' : ''"
                :suffix="selectedCurrency.position === 'after' ? ' ' + selectedCurrency.symbol : ''"
            />
            <span v-if="account.errors.balance" id="balance" class="block p-error">{{ account.errors.balance[0] }}</span>
        </div>

        <div class="field col-12 m-0">
            <label>Description</label>
            <Textarea class="w-full" placeholder="Description" v-model="form.description" />
            <span v-if="account.errors.description" id="description" class="block p-error">{{ account.errors.description[0] }}</span>
        </div>

        <Button type="submit" label="Submit" class="w-full p-3 text-lg bg-primary hover:bg-primary-600 mt-2 mx-3"></Button>
    </form>
</template>
