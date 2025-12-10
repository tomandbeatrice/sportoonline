import { RouteRecordRaw } from 'vue-router';

const adminRoutes: RouteRecordRaw[] = [
  {
    path: '/admin',
    name: 'AdminPanel',
    component: () => import('@/layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: () => import('@/views/admin/AdminDashboardNew.vue'),
      },
      {
        path: 'export-logs',
        name: 'ExportLogAnaliz',
        component: () => import('@/views/admin/ExportLogAnaliz.vue'),
        meta: { requiresAuth: true, role: 'admin' }
      },
      {
        path: 'export-files',
        name: 'ExportFileList',
        component: () => import('@/views/admin/ExportFileList.vue'),
      },
      {
        path: 'segment-export',
        name: 'SegmentExport',
        component: () => import('@/views/admin/SegmentExport.vue'),
      },
      {
        path: 'vendor/branding-dashboard',
        name: 'VendorBrandingDashboard',
        component: () => import('@/views/admin/VendorBrandingDashboard.vue'),
      }
    ]
  }
];

export default adminRoutes;