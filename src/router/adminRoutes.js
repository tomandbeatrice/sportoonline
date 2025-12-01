{
  path: '/admin/strategy-cockpit',
  name: 'StrategyCockpit',
  component: () => import('@/views/admin/StrategyCockpit.vue'),
  meta: {
    requiresAuth: true,
    role: 'admin',
    title: 'Strateji Cockpit',
    icon: '📊'
  }
}