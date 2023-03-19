import { defineStore } from 'pinia';
import axios from 'axios';

export const useBillStore = defineStore('bill', {
    state: () => ({
        bills: null,
        corporations: null,
        deletedBills: null,
        bill: null,
        withholdings: null,
        waybills: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        billsList: (state) => state.bills,
        corporationsList: (state) => state.corporations,
        withholdingList: (state) => state.withholdings,
        waybillList: (state) => state.waybills,
        errors: (state) => state.formErrors,
        successMessage: (state) => state.formSuccess,
        deletedBillList: (state) => state.deletedBills
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getBills(companyID) {
            await this.getToken();
            await axios.get(`/api/v1/bills/${companyID}`).then((res) => {
                this.bills = res.data;
                this.deletedBills = res.data.deleted_bills;
            });
        },
        async getBill(id) {
            await this.getToken();
            await axios.get(`/api/v1/bills/get-bill/${id}`).then((res) => {
                this.bill = res.data;
            });
        },
        async createBill(data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post('/api/v1/bills', data)
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
        async updateBill(id, data) {
            this.formErrors = [];
            await this.getToken();
            await axios
                .post(`/api/v1/bills/${id}`, data)
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
        async deleteBill(id) {
            await this.getToken();
            await axios.delete(`/api/v1/bills/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.respStatus = true;
            });
        },
        async restoreBill(id) {
            await this.getToken();
            await axios.post(`/api/v1/bills/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.respStatus = true;
            });
        },
        async forceDeleteBill(id) {
            await this.getToken();
            await axios.delete(`/api/v1/bills/force-delete/${id}`).then((res) => {
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
        async getMaterials(query = null) {
            await this.getToken();
            await axios.get(`/api/v1/materials?all=true${query}`).then((res) => {
                this.materials = res.data.data;
            });
        },
        async storeBillItems(billID, data) {
            await this.getToken();
            await axios
                .post(`/api/v1/bills/${billID}/items`, data)
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
