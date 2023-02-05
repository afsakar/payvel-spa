import { createRouter, createWebHistory } from 'vue-router';

import auth from '@/middleware/auth';
import selectedCompany from '@/middleware/selectedCompany';
import redirectIfAuthenticated from '@/middleware/redirectIfAuthenticated';

import AppLayout from '@/layout/AppLayout.vue';
import Login from '@/views/pages/auth/Login.vue';
import Register from '@/views/pages/auth/Register.vue';
import ForgotPassword from '@/views/pages/auth/ForgotPassword.vue';
import PasswordReset from '@/views/pages/auth/PasswordReset.vue';
import NotFound from '@/views/pages/NotFound.vue';
import SelectCompany from '@/views/pages/SelectCompany.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: AppLayout,
            children: [
                {
                    name: 'dashboard',
                    path: '/',
                    component: () => import('@/views/Dashboard.vue')
                },
                {
                    path: '/companies',
                    name: 'companies.index',
                    component: () => import('@/views/pages/company/List.vue')
                },
                {
                    path: '/currencies',
                    name: 'currencies.index',
                    component: () => import('@/views/pages/currency/List.vue')
                },
                {
                    path: '/account-types',
                    name: 'accountTypes.index',
                    component: () => import('@/views/pages/accountType/List.vue')
                },
                {
                    path: '/accounts',
                    name: 'accounts.index',
                    component: () => import('@/views/pages/account/List.vue')
                },
                {
                    path: '/taxes',
                    name: 'taxes.index',
                    component: () => import('@/views/pages/tax/List.vue')
                },
                {
                    path: '/withholdings',
                    name: 'withholdings.index',
                    component: () => import('@/views/pages/withholding/List.vue')
                },
                {
                    path: '/units',
                    name: 'units.index',
                    component: () => import('@/views/pages/unit/List.vue')
                },
                {
                    path: '/corporations',
                    name: 'corporations.index',
                    component: () => import('@/views/pages/corporation/List.vue')
                },
                {
                    path: '/agreements',
                    name: 'agreements.index',
                    component: () => import('@/views/pages/agreement/List.vue')
                },
                {
                    path: '/agreements/:agreementID/media',
                    name: 'agreements.media',
                    component: () => import('@/views/pages/agreement/media/List.vue')
                },
                {
                    path: '/categories',
                    name: 'categories.index',
                    component: () => import('@/views/pages/category/List.vue')
                },
                {
                    path: '/materials',
                    name: 'materials.index',
                    component: () => import('@/views/pages/material/List.vue')
                }
            ],
            meta: {
                middleware: [auth, selectedCompany]
            }
        },
        {
            path: '/select-company',
            name: 'SelectCompany',
            component: SelectCompany,
            meta: {
                middleware: auth
            }
        },
        {
            path: '/login',
            name: 'Login',
            component: Login,
            meta: {
                middleware: redirectIfAuthenticated
            }
        },
        {
            name: 'Register',
            path: '/register',
            component: Register,
            meta: {
                middleware: redirectIfAuthenticated
            }
        },
        {
            name: 'ForgotPassword',
            path: '/forgot-password',
            component: ForgotPassword,
            meta: {
                middleware: redirectIfAuthenticated
            }
        },
        {
            name: 'PasswordReset',
            path: '/password-reset/:token',
            component: PasswordReset,
            meta: {
                middleware: redirectIfAuthenticated
            }
        },
        {
            name: 'NotFound',
            path: '/:pathMatch(.*)*',
            component: NotFound
        }
    ]
});

function nextFactory(context, middleware, index) {
    const subsequentMiddleware = middleware[index];
    // If no subsequent Middleware exists,
    // the default `next()` callback is returned.
    if (!subsequentMiddleware) return context.next;
    return (...parameters) => {
        // Run the default Vue Router `next()` callback first.
        context.next(...parameters);
        // Then run the subsequent Middleware with a new
        // `nextMiddleware()` callback.
        const nextMiddleware = nextFactory(context, middleware, index + 1);
        subsequentMiddleware({ ...context, next: nextMiddleware });
    };
}

router.beforeEach((to, from, next) => {
    if (to.meta.middleware) {
        const middleware = Array.isArray(to.meta.middleware) ? to.meta.middleware : [to.meta.middleware];

        const context = {
            from,
            next,
            router,
            to
        };
        const nextMiddleware = nextFactory(context, middleware, 1);

        return middleware[0]({ ...context, next: nextMiddleware });
    }

    return next();
});

export default router;
