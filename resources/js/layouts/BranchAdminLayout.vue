<script setup lang="ts">
import { LayoutGrid } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/branch_admin';
import type { BreadcrumbItem, NavItem } from '@/types';

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
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" :nav-items="navItems">
        <slot />
    </AppLayout>
</template>
