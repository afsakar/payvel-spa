import { defineStore } from 'pinia';
import axios from 'axios';

export const useAgreementStore = defineStore('agreement', {
    state: () => ({
        agreements: null,
        corporations: null,
        medias: [],
        deletedAgreements: null,
        agreement: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        agreementsList: (state) => state.agreements,
        corporationsList: (state) => state.corporations,
        mediasList: (state) => state.medias,
        errors: (state) => state.formErrors,
        successMessage: (state) => state.formSuccess,
        deletedAgreementList: (state) => state.deletedAgreements
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getAgreements(companyID) {
            await this.getToken();
            await axios.get(`/api/v1/agreements/${companyID}`).then((res) => {
                this.agreements = res.data;
                this.deletedAgreements = res.data.deleted_agreements;
            });
        },
        async getAgreement(id) {
            await this.getToken();
            await axios.get(`/api/v1/agreements/get-agreement/${id}`).then((res) => {
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
                this.respStatus = true;
            });
        },
        async restoreAgreement(id) {
            await this.getToken();
            await axios.post(`/api/v1/agreements/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.respStatus = true;
            });
        },
        async forceDeleteAgreement(id) {
            await this.getToken();
            await axios.delete(`/api/v1/agreements/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.respStatus = true;
            });
        },
        async getCorporations() {
            await this.getToken();
            await axios.get('/api/v1/corporations?all=true').then((res) => {
                this.corporations = res.data.data;
            });
        },
        async getMedias(companyID, agreementID) {
            await this.getToken();
            await axios.get(`/api/v1/agreements/${agreementID}/media`).then((res) => {
                this.medias = res.data.data;
            });
        },
        async getMedia(companyID, agreementID, mediaID) {
            await this.getToken();
            await axios.get(`/api/v1/agreements/${agreementID}/media/${mediaID}`).then((res) => {
                console.log(res.data);
            });
        },
        async uploadMedia(companyID, agreementID, data) {
            await this.getToken();
            await axios
                .post(`/api/v1/agreements/${agreementID}/media`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                        console.log(res);
                    }
                    this.formSuccess = res.data.message;
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                        console.log(err);
                    }
                });
        },
        async deleteMedia(companyID, agreementID, mediaID) {
            await this.getToken();
            await axios.delete(`/api/v1/agreements/${agreementID}/media/${mediaID}`).then((res) => {
                this.formSuccess = res.data.message;
                this.respStatus = true;
            });
        }
    }
});
