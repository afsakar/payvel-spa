import { defineStore } from 'pinia';
import axios from 'axios';

export const useCategoryStore = defineStore('category', {
    state: () => ({
        categories: null,
        deletedCategories: null,
        category: null,
        formErrors: [],
        formSuccess: null,
        respStatus: null
    }),
    getters: {
        categoryList: (state) => state.categories,
        errors: (state) => state.formErrors,
        successMessage: (state) => state.formSuccess,
        deletedCategoryList: (state) => state.deletedCategories
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getCategories() {
            await axios.get('/api/v1/categories').then((res) => {
                this.categories = res.data;
                this.deletedCategories = res.data.deletedCategories;
            });
        },
        async getCategory(id) {
            await axios.get(`/api/v1/categories/${id}`).then((res) => {
                this.category = res.data;
            });
        },
        async createCategory(data) {
            this.formErrors = [];
            await axios
                .post('/api/v1/categories', data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = res.data.message;
                    this.getCategories();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async updateCategory(id, data) {
            this.formErrors = [];
            await axios
                .post(`/api/v1/categories/${id}`, data)
                .then((res) => {
                    if (res.status === 422) {
                        this.formErrors = res.data.errors;
                    }
                    this.formSuccess = res.data.message;
                    this.getCategories();
                    this.respStatus = true;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.formErrors = err.response.data.errors;
                    }
                });
        },
        async deleteCategory(id) {
            await axios.delete(`/api/v1/categories/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCategories();
                this.respStatus = true;
            });
        },
        async restoreCategory(id) {
            await axios.post(`/api/v1/categories/restore/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCategories();
                this.respStatus = true;
            });
        },
        async forceDeleteCategory(id) {
            await axios.delete(`/api/v1/categories/force-delete/${id}`).then((res) => {
                this.formSuccess = res.data.message;
                this.getCategories();
                this.respStatus = true;
            });
        }
    }
});
