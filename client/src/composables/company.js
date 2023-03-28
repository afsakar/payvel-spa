import { defineStore } from 'pinia';
import axios from 'axios';
import router from '@/router';
import { useStorage } from '@vueuse/core';

export const useCompanyStore = defineStore('company', {
    state: () => ({
        companies: null,
        deletedCompanies: null,
        company: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        companyList: (state) => state.companies,
        errors: (state) => state.formErrors,
        successMessage: (state) => state.formSuccess,
        deletedCompanyList: (state) => state.deletedCompanies
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getCompanies() {
            await axios.get('/api/v1/companies').then((res) => {
                this.companies = res.data;
                this.deletedCompanies = res.data.deletedCompanies;
            });
        },
        async selectCompany(company) {
            this.formErrors = [];
            if (localStorage.getItem('selectedCompany')) {
                localStorage.removeItem('selectedCompany');
            }
            useStorage('selectedCompany', company);
            setTimeout(() => {
                window.location.assign('/');
            }, 500);
        },
        async forgetCompany() {
            localStorage.removeItem('selectedCompany');
            await router.push({ name: 'SelectCompany' });
        },
        async getCompany(id) {
            await axios.get(`/api/v1/companies/${id}`).then((res) => {
                this.company = res.data;
            });
        },
        async createCompany(data) {
            this.formErrors = [];
            await axios
                .post('/api/v1/companies', data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Company created successfully';
                    this.getCompanies();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async updateCompany(data, id) {
            this.formErrors = [];
            await axios
                .post(`/api/v1/companies/${id}`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = 'Company updated successfully';
                    this.getCompanies();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async deleteCompany(id) {
            await axios.delete(`/api/v1/companies/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCompanies();
                this.respStatus = true;
            });
        },
        async restoreCompany(id) {
            await axios.post(`/api/v1/companies/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCompanies();
                this.respStatus = true;
            });
        },
        async forceDeleteCompany(id) {
            await axios.delete(`/api/v1/companies/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCompanies();
                this.respStatus = true;
            });
        }
    }
});
