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
                    path: '/companies/trash',
                    name: 'companies.trash',
                    component: () => import('@/views/pages/company/Trash.vue')
                },
                {
                    path: '/currencies',
                    name: 'currencies.index',
                    component: () => import('@/views/pages/currency/List.vue')
                },
                {
                    path: '/currencies/trash',
                    name: 'currencies.trash',
                    component: () => import('@/views/pages/currency/Trash.vue')
                },
                {
                    path: '/account-types',
                    name: 'accountTypes.index',
                    component: () => import('@/views/pages/accountType/List.vue')
                },
                {
                    path: '/account-types/trash',
                    name: 'accountTypes.trash',
                    component: () => import('@/views/pages/accountType/Trash.vue')
                },
                {
                    path: '/accounts',
                    name: 'accounts.index',
                    component: () => import('@/views/pages/account/List.vue')
                },
                {
                    path: '/accounts/trash',
                    name: 'accounts.trash',
                    component: () => import('@/views/pages/account/Trash.vue')
                },
                {
                    path: '/taxes',
                    name: 'taxes.index',
                    component: () => import('@/views/pages/tax/List.vue')
                },
                {
                    path: '/taxes/trash',
                    name: 'taxes.trash',
                    component: () => import('@/views/pages/tax/Trash.vue')
                },
                {
                    path: '/withholdings',
                    name: 'withholdings.index',
                    component: () => import('@/views/pages/withholding/List.vue')
                },
                {
                    path: '/withholdings/trash',
                    name: 'withholdings.trash',
                    component: () => import('@/views/pages/withholding/Trash.vue')
                },
                {
                    path: '/units',
                    name: 'units.index',
                    component: () => import('@/views/pages/unit/List.vue')
                },
                {
                    path: '/units/trash',
                    name: 'units.trash',
                    component: () => import('@/views/pages/unit/Trash.vue')
                },
                {
                    path: '/corporations',
                    name: 'corporations.index',
                    component: () => import('@/views/pages/corporation/List.vue')
                },
                {
                    path: '/corporations/trash',
                    name: 'corporations.trash',
                    component: () => import('@/views/pages/corporation/Trash.vue')
                },
                {
                    path: '/agreements',
                    name: 'agreements.index',
                    component: () => import('@/views/pages/agreement/List.vue')
                },
                {
                    path: '/agreements/trash',
                    name: 'agreements.trash',
                    component: () => import('@/views/pages/agreement/Trash.vue')
                },
                {
                    path: '/agreements/:agreementID/media',
                    name: 'agreements.media',
                    component: () => import('@/views/pages/agreement/media/List.vue')
                },
                {
                    path: '/waybills',
                    name: 'waybills.index',
                    component: () => import('@/views/pages/waybill/List.vue')
                },
                {
                    path: '/waybills/:waybillID/items',
                    name: 'waybills.items',
                    component: () => import('@/views/pages/waybill/ItemList.vue')
                },
                {
                    path: '/waybills/trash',
                    name: 'waybills.trash',
                    component: () => import('@/views/pages/waybill/Trash.vue')
                },
                {
                    path: '/invoices',
                    name: 'invoices.index',
                    component: () => import('@/views/pages/invoice/List.vue')
                },
                {
                    path: '/invoices/:invoiceID/items',
                    name: 'invoices.items',
                    component: () => import('@/views/pages/invoice/ItemList.vue')
                },
                {
                    path: '/invoices/trash',
                    name: 'invoices.trash',
                    component: () => import('@/views/pages/invoice/Trash.vue')
                },
                {
                    path: '/bills',
                    name: 'bills.index',
                    component: () => import('@/views/pages/bill/List.vue')
                },
                {
                    path: '/bills/:billID/items',
                    name: 'bills.items',
                    component: () => import('@/views/pages/bill/ItemList.vue')
                },
                {
                    path: '/bills/trash',
                    name: 'bills.trash',
                    component: () => import('@/views/pages/bill/Trash.vue')
                },
                {
                    path: '/categories',
                    name: 'categories.index',
                    component: () => import('@/views/pages/category/List.vue')
                },
                {
                    path: '/categories/trash',
                    name: 'categories.trash',
                    component: () => import('@/views/pages/category/Trash.vue')
                },
                {
                    path: '/materials',
                    name: 'materials.index',
                    component: () => import('@/views/pages/material/List.vue')
                },
                {
                    path: '/materials/trash',
                    name: 'materials.trash',
                    component: () => import('@/views/pages/material/Trash.vue')
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
