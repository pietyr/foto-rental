import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import AppLayout from '@/components/layout/AppLayout.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: AppLayout,
      children: [
        { path: '', redirect: '/equipment' },
        {
          path: 'login',
          name: 'login',
          component: () => import('@/views/auth/LoginView.vue'),
          meta: { guest: true },
        },
        {
          path: 'register',
          name: 'register',
          component: () => import('@/views/auth/RegisterView.vue'),
          meta: { guest: true },
        },
        {
          path: 'equipment',
          name: 'equipment',
          component: () => import('@/views/equipment/EquipmentListView.vue'),
          meta: { requiresAuth: true },
        },
        {
          path: 'equipment/:id',
          name: 'equipment-detail',
          component: () => import('@/views/equipment/EquipmentDetailView.vue'),
          meta: { requiresAuth: true },
        },
        {
          path: 'rentals',
          name: 'rentals',
          component: () => import('@/views/rentals/MyRentalsView.vue'),
          meta: { requiresAuth: true },
        },
        {
          path: 'admin',
          name: 'admin',
          component: () => import('@/views/admin/AdminDashboardView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'admin/categories',
          name: 'admin-categories',
          component: () => import('@/views/admin/AdminCategoriesView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'admin/equipment',
          name: 'admin-equipment',
          component: () => import('@/views/admin/AdminEquipmentView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'admin/rentals',
          name: 'admin-rentals',
          component: () => import('@/views/admin/AdminRentalsView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'admin/users',
          name: 'admin-users',
          component: () => import('@/views/admin/AdminUsersView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
      ],
    },
  ],
});

router.beforeEach(async (to) => {
  const auth = useAuthStore();

  if (!auth.user && localStorage.getItem('token')) {
    await auth.fetchUser();
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } };
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return { name: 'equipment' };
  }

  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: 'equipment' };
  }

  return true;
});

export default router;
