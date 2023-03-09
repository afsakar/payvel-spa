import { defineStore } from 'pinia';
import axios from 'axios';

export const useInvoiceStore = defineStore('invoice', {
    state: () => ({
        invoices: null,
        corporations: null,
        deletedInvoices: null,
        invoice: null,
        withholdings: null,
        waybills: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        invoicesList: (state) => state.invoices,
        corporationsList: (state) => state.corporations,
        withholdingList: (state) => state.withholdings,
        waybillList: (state) => state.waybills,
        errors: (state) => state.formErrors,
        successMessage: (state) => state.formSuccess,
        deletedInvoiceList: (state) => state.deletedInvoices
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getInvoices(companyID) {
            await this.getToken();
            await axios.get(`/api/v1/invoices/${companyID}`).then((res) => {
                this.invoices = res.data;
                this.deletedInvoices = res.data.deleted_invoices;
            });
        },
        async getInvoice(id) {
            await this.getToken();
            await axios.get(`/api/v1/invoices/get-invoice/${id}`).then((res) => {
                this.invoice = res.data;
            });
        },
        async createInvoice(data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post('/api/v1/invoices', data)
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
        async updateInvoice(id, data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post(`/api/v1/invoices/${id}`, data)
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
        async deleteInvoice(id) {
            await this.getToken();
            await axios.delete(`/api/v1/invoices/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.respStatus = true;
            });
        },
        async restoreInvoice(id) {
            await this.getToken();
            await axios.post(`/api/v1/invoices/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.respStatus = true;
            });
        },
        async forceDeleteInvoice(id) {
            await this.getToken();
            await axios.delete(`/api/v1/invoices/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.respStatus = true;
            });
        },
        async getCorporations() {
            await this.getToken();
            await axios.get(`/api/v1/corporations?all=true`).then((res) => {
                this.corporations = res.data.data;
            });
        },
        async getWithholdings() {
            await this.getToken();
            await axios.get(`/api/v1/withholdings?all=true`).then((res) => {
                this.withholdings = res.data.data;
            });
        },
        async getWaybills() {
            await this.getToken();
            await axios.get(`/api/v1/waybills?all=true`).then((res) => {
                this.waybills = res.data.data;
            });
        },
        async getMaterials() {
            await this.getToken();
            await axios.get(`/api/v1/materials?all=true`).then((res) => {
                this.materials = res.data.data;
            });
        },
        async storeInvoiceItems(invoiceID, data) {
            await this.getToken();
            await axios
                .post(`/api/v1/invoices/${invoiceID}/items`, data)
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
