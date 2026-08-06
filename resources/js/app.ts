import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import BranchAdminLayout from '@/layouts/BranchAdminLayout.vue';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import StaffLayout from '@/layouts/StaffLayout.vue';
import SuperAdminLayout from '@/layouts/SuperAdminLayout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            case name.startsWith('superadmin/'):
                return SuperAdminLayout;
            case name.startsWith('branch_admin/'):
                return BranchAdminLayout;
            case name.startsWith('staff/'):
                return StaffLayout;
            case name.startsWith('customer/'):
                return CustomerLayout;
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
