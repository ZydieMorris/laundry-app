<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { UserRoleValue } from '@/types/user';

const props = defineProps<{ role: UserRoleValue }>();

const roleLabels: Record<UserRoleValue, string> = {
    super_admin: 'Super Admin',
    branch_admin: 'Branch Admin',
    staff: 'Staff',
    customer: 'Customer',
};

const roleClasses: Record<UserRoleValue, string> = {
    super_admin: 'bg-indigo-100 text-indigo-800',
    branch_admin: 'bg-sky-100 text-sky-800',
    staff: 'bg-amber-100 text-amber-800',
    customer: 'bg-slate-100 text-slate-600',
};
</script>

<template>
    <span
        :class="
            cn(
                'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                roleClasses[props.role],
            )
        "
    >
        {{ roleLabels[props.role] }}
    </span>
</template>
