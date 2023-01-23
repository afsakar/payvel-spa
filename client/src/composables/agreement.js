import { defineStore } from 'pinia';
import axios from 'axios';

export const useAgreementStore = defineStore('agreement', {
    state: () => ({
        agreements: null,
        corporations: null,
        deletedAgreements: null,
        agreement: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        agreementsList: (state) => state.agreements,
        corporationsList: (state) => state.corporations,
        errors: (state) => state.formErrors,
        successMessage: (state) => state.formSuccess,
        deletedAgreementList: (state) => state.deletedAgreements
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getAgreements() {
            await this.getToken();
            await axios.get('/api/v1/agreements').then((res) => {
                this.agreements = res.data;
                this.deletedAgreements = res.data.deleted_agreements;
            });
        },
        async getAgreement(id) {
            await this.getToken();
            await axios.get(`/api/v1/agreements/${id}`).then((res) => {
                this.agreement = res.data;
            });
        },
        async createAgreement(data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post('/api/v1/agreements', data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = res.data.message;
                    this.getAgreements();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async updateAgreement(id, data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post(`/api/v1/agreements/${id}`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = res.data.message;
                    this.getAgreements();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async deleteAgreement(id) {
            await this.getToken();
            await axios.delete(`/api/v1/agreements/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getAgreements();
                this.respStatus = true;
            });
        },
        async restoreAgreement(id) {
            await this.getToken();
            await axios.post(`/api/v1/agreements/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getAgreements();
                this.respStatus = true;
            });
        },
        async forceDeleteAgreement(id) {
            await this.getToken();
            await axios.delete(`/api/v1/agreements/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getAgreements();
                this.respStatus = true;
            });
        },
        async getCorporations() {
            await this.getToken();
            await axios.get('/api/v1/corporations').then((res) => {
                this.corporations = res.data.data;
            });
        }
    }
});
