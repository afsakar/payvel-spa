import { defineStore } from 'pinia';
import axios from 'axios';

export const useAccountTypeStore = defineStore('accountType', {
    state: () => ({
        accountTypes: null,
        deletedAccountTypes: null,
        accountType: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        accountTypesList: (state) => state.accountTypes,
        errors: (state) => state.formErrors,
        successMessage: (state) => state.formSuccess,
        deletedAccountTypeList: (state) => state.deletedAccountTypes
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getAccountTypes() {
            await axios.get('/api/v1/account-types').then((res) => {
                this.accountTypes = res.data;
                this.deletedAccountTypes = res.data.deletedAccountTypes;
            });
        },
        async getAccountType(id) {
            await axios.get(`/api/v1/account-types/${id}`).then((res) => {
                this.accountType = res.data;
            });
        },
        async createAccountType(data) {
            this.formErrors = [];
            await axios
                .post('/api/v1/account-types', data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = res.data.message;
                    this.getAccountTypes();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async updateAccountType(id, data) {
            this.formErrors = [];
            await axios
                .post(`/api/v1/account-types/${id}`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = res.data.message;
                    this.getAccountTypes();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async deleteAccountType(id) {
            await axios.delete(`/api/v1/account-types/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getAccountTypes();
                this.respStatus = true;
            });
        },
        async restoreAccountType(id) {
            await axios.post(`/api/v1/account-types/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getAccountTypes();
                this.respStatus = true;
            });
        },
        async forceDeleteAccountType(id) {
            await axios.delete(`/api/v1/account-types/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getAccountTypes();
                this.respStatus = true;
            });
        }
    }
});
