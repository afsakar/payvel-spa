import { defineStore } from 'pinia';
import axios from 'axios';

export const useAccountStore = defineStore('account', {
    state: () => ({
        accounts: null,
        accountTypes: null,
        currencies: null,
        deletedAccounts: null,
        account: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        accountsList: (state) => state.accounts,
        accountTypesList: (state) => state.accountTypes,
        currenciesList: (state) => state.currencies,
        errors: (state) => state.formErrors,
        successMessage: (state) => state.formSuccess,
        deletedAccountList: (state) => state.deletedAccounts
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getAccounts() {
            await this.getToken();
            await axios.get('/api/v1/accounts').then((res) => {
                this.accounts = res.data;
            });
        },
        async getDeletedAccounts() {
            await this.getToken();
            await axios.get('/api/v1/accounts/trash').then((res) => {
                this.deletedAccounts = res.data;
            });
        },
        async getAccount(id) {
            await this.getToken();
            await axios.get(`/api/v1/accounts/${id}`).then((res) => {
                this.account = res.data;
            });
        },
        async createAccount(data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post('/api/v1/accounts', data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = res.data.message;
                    this.getAccounts();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async updateAccount(id, data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post(`/api/v1/accounts/${id}`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = res.data.message;
                    this.getAccounts();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async deleteAccount(id) {
            await this.getToken();
            await axios.delete(`/api/v1/accounts/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getAccounts();
                this.respStatus = true;
            });
        },
        async restoreAccount(id) {
            await this.getToken();
            await axios.post(`/api/v1/accounts/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getAccounts();
                this.respStatus = true;
            });
        },
        async forceDeleteAccount(id) {
            await this.getToken();
            await axios.delete(`/api/v1/accounts/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getAccounts();
                this.respStatus = true;
            });
        },
        async getAccountTypes() {
            await this.getToken();
            await axios.get('/api/v1/account-types').then((res) => {
                this.accountTypes = res.data.data;
            });
        },
        async getCurrencies() {
            await this.getToken();
            await axios.get('/api/v1/currencies').then((res) => {
                this.currencies = res.data.data;
            });
        }
    }
});
