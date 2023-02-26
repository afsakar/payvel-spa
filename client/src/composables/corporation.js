import { defineStore } from 'pinia';
import axios from 'axios';

export const useCorporationStore = defineStore('corporation', {
    state: () => ({
        corporations: null,
        deletedCorporations: null,
        corporation: null,
        currencies: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        corporationList: (state) => state.corporations,
        deletedCorporationList: (state) => state.deletedCorporations,
        currenciesList: (state) => state.currencies,
        successMessage: (state) => state.formSuccess,
        errors: (state) => state.formErrors
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getCorporations() {
            await this.getToken();
            await axios.get('/api/v1/corporations').then((res) => {
                this.corporations = res.data;
                this.deletedCorporations = res.data.deleted_corporations;
            });
        },
        async getCorporation(id) {
            await this.getToken();
            await axios.get(`/api/v1/corporations/${id}`).then((res) => {
                this.corporation = res.data;
            });
        },
        async createCorporation(data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post('/api/v1/corporations', data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Corporation created successfully';
                    this.getCorporations();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async updateCorporation(data, id) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post(`/api/v1/corporations/${id}`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Corporation updated successfully';
                    this.getCorporations();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async deleteCorporation(id) {
            await this.getToken();
            await axios.delete(`/api/v1/corporations/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCorporations();
                this.respStatus = true;
            });
        },
        async restoreCorporation(id) {
            await this.getToken();
            await axios.post(`/api/v1/corporations/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCorporations();
                this.respStatus = true;
            });
        },
        async forceDeleteCorporation(id) {
            await this.getToken();
            await axios.delete(`/api/v1/corporations/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCorporations();
                this.respStatus = true;
            });
        },
        async getCurrencies() {
            await this.getToken();
            await axios.get('/api/v1/currencies?all=true').then((res) => {
                this.currencies = res.data.data;
            });
        }
    }
});
