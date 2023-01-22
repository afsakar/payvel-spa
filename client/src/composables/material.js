import { defineStore } from 'pinia';
import axios from 'axios';

export const useMaterialStore = defineStore('material', {
    state: () => ({
        materials: null,
        deletedMaterials: null,
        material: null,
        currencies: null,
        units: null,
        taxes: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        materialList: (state) => state.materials,
        currenciesList: (state) => state.currencies,
        unitsList: (state) => state.units,
        taxesList: (state) => state.taxes,
        errors: (state) => state.formErrors,
        successMessage: (state) => state.formSuccess,
        deletedMaterialList: (state) => state.deletedMaterials
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getMaterials() {
            await this.getToken();
            await axios.get('/api/v1/materials').then((res) => {
                this.materials = res.data;
                this.deletedMaterials = res.data.deletedMaterials;
            });
        },
        async getMaterial(id) {
            await this.getToken();
            await axios.get(`/api/v1/materials/${id}`).then((res) => {
                this.material = res.data;
            });
        },
        async createMaterial(data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post('/api/v1/materials', data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = res.data.message;
                    this.getMaterials();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async updateMaterial(id, data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post(`/api/v1/materials/${id}`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = res.data.message;
                    this.getMaterials();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async deleteMaterial(id) {
            await this.getToken();
            await axios.delete(`/api/v1/materials/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getMaterials();
                this.respStatus = true;
            });
        },
        async restoreMaterial(id) {
            await this.getToken();
            await axios.post(`/api/v1/materials/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getMaterials();
                this.respStatus = true;
            });
        },
        async forceDeleteMaterial(id) {
            await this.getToken();
            await axios.delete(`/api/v1/materials/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getMaterials();
                this.respStatus = true;
            });
        },
        async getCurrencies() {
            await this.getToken();
            await axios.get('/api/v1/currencies').then((res) => {
                this.currencies = res.data.data;
            });
        },
        async getUnits() {
            await this.getToken();
            await axios.get('/api/v1/units').then((res) => {
                this.units = res.data.data;
            });
        },
        async getTaxes() {
            await this.getToken();
            await axios.get('/api/v1/taxes').then((res) => {
                this.taxes = res.data.data;
            });
        }
    }
});
