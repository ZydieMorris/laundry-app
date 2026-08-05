<script setup lang="ts">
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import type { BranchStatus } from '@/types/branch';

const props = defineProps<{ status: BranchStatus }>();

const label = computed(() =>
    props.status === 'active' ? 'Active' : 'Inactive',
);

const classes = computed(() =>
    props.status === 'active'
        ? 'border-transparent bg-green-100 text-green-800'
        : 'border-transparent bg-slate-100 text-slate-600',
);
</script>

<template>
    <Badge variant="default" :class="classes">
        <span class="size-1.5 rounded-full bg-current" />
        {{ label }}
    </Badge>
</template>
