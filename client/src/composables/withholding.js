import { defineStore } from 'pinia';
import axios from 'axios';

export const useWithholdingStore = defineStore('withholding', {
    state: () => ({
        withholdings: null,
        deletedWithholdings: null,
        withholding: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        withholdingList: (state) => state.withholdings,
        deletedWithholdingList: (state) => state.deletedWithholdings,
        successMessage: (state) => state.formSuccess,
        errors: (state) => state.formErrors
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getWithholdings() {
            await this.getToken();
            await axios.get('/api/v1/withholdings').then((res) => {
                this.withholdings = res.data;
                this.deletedWithholdings = res.data.deletedWithholdings;
            });
        },
        async getWithholding(id) {
            await this.getToken();
            await axios.get(`/api/v1/withholdings/${id}`).then((res) => {
                this.withholding = res.data;
            });
        },
        async createWithholding(data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post('/api/v1/withholdings', data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Withholding created successfully';
                    this.getWithholdings();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async updateWithholding(data, id) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post(`/api/v1/withholdings/${id}`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Withholding updated successfully';
                    this.getWithholdings();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async deleteWithholding(id) {
            await this.getToken();
            await axios.delete(`/api/v1/withholdings/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getWithholdings();
                this.respStatus = true;
            });
        },
        async restoreWithholding(id) {
            await this.getToken();
            await axios.post(`/api/v1/withholdings/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getWithholdings();
                this.respStatus = true;
            });
        },
        async forceDeleteWithholding(id) {
            await this.getToken();
            await axios.delete(`/api/v1/withholdings/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getWithholdings();
                this.respStatus = true;
            });
        },
    }
});
