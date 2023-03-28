import { defineStore } from 'pinia';
import axios from 'axios';

export const useUnitStore = defineStore('unit', {
    state: () => ({
        units: null,
        deletedUnits: null,
        unit: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        unitList: (state) => state.units,
        deletedUnitList: (state) => state.deletedUnits,
        successMessage: (state) => state.formSuccess,
        errors: (state) => state.formErrors
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getUnits() {
            await axios.get('/api/v1/units').then((res) => {
                this.units = res.data;
                this.deletedUnits = res.data.deletedUnits;
            });
        },
        async getUnit(id) {
            await axios.get(`/api/v1/units/${id}`).then((res) => {
                this.unit = res.data;
            });
        },
        async createUnit(data) {
            this.formErrors = [];
            await axios
                .post('/api/v1/units', data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Unit created successfully';
                    this.getUnits();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async updateUnit(data, id) {
            this.formErrors = [];
            await axios
                .post(`/api/v1/units/${id}`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Unit updated successfully';
                    this.getUnits();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async deleteUnit(id) {
            await axios.delete(`/api/v1/units/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getUnits();
                this.respStatus = true;
            });
        },
        async restoreUnit(id) {
            await axios.post(`/api/v1/units/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getUnits();
                this.respStatus = true;
            });
        },
        async forceDeleteUnit(id) {
            await axios.delete(`/api/v1/units/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getUnits();
                this.respStatus = true;
            });
        }
    }
});
