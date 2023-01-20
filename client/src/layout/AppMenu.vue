<script setup>
import { computed, ref } from 'vue';
import { useConfirm } from 'primevue/useconfirm';

import AppMenuItem from './AppMenuItem.vue';
import { useCompanyStore } from '@/composables/company';
import router from '@/router';

const selectedCompany = computed(() => {
    return JSON.parse(localStorage.getItem('selectedCompany'));
});

const company = useCompanyStore();

const confirm = useConfirm();
const changeCompany = (event) => {
    confirm.require({
        target: event.currentTarget,
        header: 'Confirmation',
        message: 'Are you sure you want to change company?',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'bg-primary hover:bg-primary-600',
        accept: async () => {
            await company.forgetCompany();
            await router.push({ name: 'SelectCompany' });
        }
    });
};

const model = ref([
    {
        label: `${selectedCompany.value?.name}`,
        items: [
            {
                label: 'Change Company',
                icon: 'pi pi-fw pi-sync',
                command: ($event) => {
                    changeCompany($event);
                }
            },
            {
                separator: true
            },
            { label: 'Dashboard', icon: 'ti pi-fw ti-home', to: '/' },
            { label: 'Currencies', icon: 'ti pi-fw ti-currency-dollar', to: '/currencies' },
            {
                label: 'Settings',
                icon: 'ti pi-fw ti-settings',
                items: [{ label: 'Currencies', to: '/currencies' }]
            },
            {
                label: 'Taxes',
                icon: 'ti pi-fw ti-receipt-tax',
                items: [
                    { label: 'Taxes', to: '/taxes' },
                    { label: 'Withholdings', to: '/withholdings' }
                ]
            },
            {
                label: 'Accounts',
                icon: 'ti pi-fw ti-building-bank',
                items: [
                    { label: 'Account Types', to: '/account-types' },
                    { label: 'Accounts', to: '/accounts' }
                ]
            }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
