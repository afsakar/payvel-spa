import { defineStore } from 'pinia';
import axios from 'axios';

export const useCurrencyStore = defineStore('currency', {
    state: () => ({
        currencies: null,
        deletedCurrencies: null,
        currency: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        currencyList: (state) => state.currencies,
        errors: (state) => state.formErrors,
        successMessage: (state) => state.formSuccess,
        deletedCurrencyList: (state) => state.deletedCurrencies
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getCurrencies() {
            await this.getToken();
            await axios.get('/api/v1/currencies').then((res) => {
                this.currencies = res.data;
                this.deletedCurrencies = res.data.deletedCurrencies;
            });
        },
        async getCurrency(id) {
            await this.getToken();
            await axios.get(`/api/v1/currencies/${id}`).then((res) => {
                this.currency = res.data;
            });
        },
        async createCurrency(data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post('/api/v1/currencies', data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Currency created successfully';
                    this.getCurrencies();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async updateCurrency(data, id) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post(`/api/v1/currencies/${id}`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Currency updated successfully';
                    this.getCurrencies();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async deleteCurrency(id) {
            await this.getToken();
            await axios.delete(`/api/v1/currencies/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCurrencies();
                this.respStatus = true;
            });
        },
        async restoreCurrency(id) {
            await this.getToken();
            await axios.post(`/api/v1/currencies/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCurrencies();
                this.respStatus = true;
            });
        },
        async forceDeleteCurrency(id) {
            await this.getToken();
            await axios.delete(`/api/v1/currencies/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCurrencies();
                this.respStatus = true;
            });
        }
    }
});
