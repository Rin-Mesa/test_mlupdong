import { createRouter, createWebHistory } from 'vue-router'
import DashboardLayout from '../layouts/DashboardLayout.vue'
import CustomerLayout from '../layouts/CustomerLayout.vue'

const routes = [
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/LoginView.vue')
    },
    {
        path: '/',
        component: DashboardLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: 'floor-plan',
                name: 'floor-plan',
                component: () => import('../views/FloorPlan.vue'),
                meta: { role: 'waiter' }
            },
            {
                path: '',
                name: 'dashboard',
                component: () => import('../views/DashboardHome.vue'),
                meta: { role: 'admin' }
            },
            {
                path: 'menu-manager',
                name: 'menu-manager',
                component: () => import('../views/MenuManager.vue'),
                meta: { role: 'admin' }
            },
            {
                path: 'tables',
                name: 'tables',
                component: () => import('../views/Tables.vue'),
                meta: { role: 'admin' }
            },
            {
                path: 'kitchen',
                name: 'kitchen',
                component: () => import('../views/KitchenView.vue'),
                meta: { role: 'chef' }
            },
            {
                path: 'staff',
                name: 'staff',
                component: () => import('../views/StaffManager.vue'),
                meta: { role: 'admin' }
            },
            {
                path: 'reports',
                name: 'reports',
                component: () => import('../views/Reports.vue'),
                meta: { role: 'admin' }
            },
            {
                path: 'settings',
                name: 'settings',
                component: () => import('../views/Settings.vue'),
                meta: { role: 'admin' }
            }
        ]
    },
    {
        path: '/menu/:token',
        component: CustomerLayout,
        children: [
            {
                path: '',
                name: 'customer-menu',
                component: () => import('../views/customer/MenuView.vue')
            },
            {
                path: 'order-status/:id',
                name: 'order-status',
                component: () => import('../views/customer/OrderStatus.vue')
            },
            {
                path: 'history',
                name: 'order-history',
                component: () => import('../views/customer/OrderHistory.vue')
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    const userRole = localStorage.getItem('user_role');
    const isAuthenticated = !!userRole;

    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'login' });
    } else if (to.matched.some(record => record.meta.role)) {
        const requiredRole = to.meta.role;
        // Simple logic: Admin can see everything, others only their role
        if (userRole === 'admin' || userRole === requiredRole) {
            next();
        } else {
            // Redirect to their own dashboard if they try to access something else
            if (userRole === 'chef') next({ name: 'kitchen' });
            else if (userRole === 'waiter') next({ name: 'floor-plan' });
            else next({ name: 'login' });
        }
    } else {
        next();
    }
});

export default router
