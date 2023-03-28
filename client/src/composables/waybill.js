import { defineStore } from 'pinia';
import axios from 'axios';

export const useWaybillStore = defineStore('waybill', {
    state: () => ({
        waybills: null,
        corporations: null,
        deletedWaybills: null,
        waybill: null,
        materials: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        waybillsList: (state) => state.waybills,
        corporationsList: (state) => state.corporations,
        errors: (state) => state.formErrors,
        successMessage: (state) => state.formSuccess,
        deletedWaybillList: (state) => state.deletedWaybills
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getWaybills(companyID) {
            await axios.get(`/api/v1/waybills/${companyID}`).then((res) => {
                this.waybills = res.data;
                this.deletedWaybills = res.data.deleted_waybills;
            });
        },
        async getWaybill(id) {
            await axios.get(`/api/v1/waybills/get-waybill/${id}`).then((res) => {
                this.waybill = res.data;
            });
        },
        async createWaybill(data) {
            this.formErrors = [];
            await axios
                .post('/api/v1/waybills', data)
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
        async updateWaybill(id, data) {
            this.formErrors = [];
            await axios
                .post(`/api/v1/waybills/${id}`, data)
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
        async deleteWaybill(id) {
            await axios.delete(`/api/v1/waybills/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.respStatus = true;
            });
        },
        async restoreWaybill(id) {
            await axios.post(`/api/v1/waybills/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.respStatus = true;
            });
        },
        async forceDeleteWaybill(id) {
            await axios.delete(`/api/v1/waybills/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.respStatus = true;
            });
        },
        async getCorporations() {
            await axios.get(`/api/v1/corporations?all=true`).then((res) => {
                this.corporations = res.data.data;
            });
        },
        async getMaterials(query = null) {
            await axios.get(`/api/v1/materials?all=true${query}`).then((res) => {
                this.materials = res.data.data;
            });
        },
        async storeWaybillItems(waybillID, data) {
            await axios
                .post(`/api/v1/waybills/${waybillID}/items`, data)
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
        }
    }
});
