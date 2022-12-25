import { defineStore } from 'pinia';
import axios from 'axios';
import router from '@/router';
import { useStorage } from '@vueuse/core';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        authUser: null,
        authErrors: [],
        authSuccess: null,
        authToken: useStorage('authToken', null),
    }),
    getters: {
        user: (state) => state.authUser,
        errors: (state) => state.authErrors,
        successMessage: (state) => state.authSuccess
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async login(data) {
            this.authErrors = [];
            await this.getToken();
            await axios
                .post('/login', data)
                .then(() => {
                    useStorage('isLoggedIn', true);
                    router.push('/');
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.authErrors = err.response.data.errors;
                    }
                });
        },
        async register(data) {
            this.authErrors = [];
            await this.getToken();
            await axios
                .post('/register', data)
                .then(() => {
                    useStorage('isLoggedIn', true);
                    router.push('/');
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.authErrors = err.response.data.errors;
                    }
                });
        },
        // async getUser() {
        //     await this.getToken();
        //     const data = await axios.get('/api/user');
        //     this.authUser = data.data;
        // },
        async getUser() {
            await this.getToken();
            await axios.get('/api/user').then((res) => {
                if (res.status === 401) {
                    this.authUser = null;
                    localStorage.removeItem('isLoggedIn');
                    localStorage.removeItem('selectedCompany');
                    router.push('/login');
                } else {
                    this.authUser = res.data;
                }
            });
        },
        async logout() {
            await axios.post('/logout').then(() => {
                this.authUser = null;
                localStorage.removeItem('isLoggedIn');
                localStorage.removeItem('selectedCompany');
                router.push('/login');
            });
        },
        async forgotPassword(email) {
            this.authErrors = [];
            await this.getToken();
            await axios
                .post('/forgot-password', email)
                .then((resp) => {
                    this.authSuccess = resp.data.status;
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.authErrors = err.response.data.errors;
                    }
                });
        },
        async resetPassword(data) {
            this.authErrors = [];
            axios
                .post('/reset-password', data)
                .then(() => {
                    router.push('/login');
                })
                .catch((err) => {
                    if (err.response.status === 422) {
                        this.authErrors = err.response.data.errors;
                    }
                });
        }
    },
    persist: true
});
