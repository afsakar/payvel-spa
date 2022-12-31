import { defineStore } from 'pinia';
import axios from 'axios';

export const useTaxStore = defineStore('tax', {
    state: () => ({
        taxes: null,
        deletedTaxes: null,
        tax: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        taxList: (state) => state.taxes,
        deletedTaxList: (state) => state.deletedTaxes,
        successMessage: (state) => state.formSuccess,
        errors: (state) => state.formErrors,
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getTaxes() {
            await this.getToken();
            await axios.get('/api/v1/taxes').then((res) => {
                this.taxes = res.data;
                this.deletedTaxes = res.data.deletedTaxes;
            });
        },
        async getTax(id) {
            await this.getToken();
            await axios.get(`/api/v1/taxes/${id}`).then((res) => {
                this.tax = res.data;
            });
        },
        async createTax(data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post('/api/v1/taxes', data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Tax created successfully';
                    this.getTaxes();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async updateTax(data, id) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post(`/api/v1/taxes/${id}`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Tax updated successfully';
                    this.getTaxes();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async deleteTax(id) {
            await this.getToken();
            await axios.delete(`/api/v1/taxes/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getTaxes();
                this.respStatus = true;
            });
        },
        async restoreTax(id) {
            await this.getToken();
            await axios.post(`/api/v1/taxes/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getTaxes();
                this.respStatus = true;
            });
        },
        async forceDeleteTax(id) {
            await this.getToken();
            await axios.delete(`/api/v1/taxes/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getTaxes();
                this.respStatus = true;
            });
        },
    }
});
