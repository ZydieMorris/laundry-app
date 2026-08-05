<script setup lang="ts">
import { LayoutGrid, Store } from '@lucide/vue';
import { onBeforeUnmount, onMounted } from 'vue';
import { updateTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/superadmin';
import { index as branchesIndex } from '@/routes/superadmin/branches';
import type { Appearance, BreadcrumbItem, NavItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const navItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Branches',
        href: branchesIndex.url(),
        icon: Store,
    },
];

onMounted(() => {
    document.documentElement.classList.add('superadmin-theme');
    document.documentElement.classList.remove('dark');
});

onBeforeUnmount(() => {
    document.documentElement.classList.remove('superadmin-theme');

    const appearance = (localStorage.getItem('appearance') ??
        'system') as Appearance;
    updateTheme(appearance);
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" :nav-items="navItems">
        <slot />
    </AppLayout>
</template>
