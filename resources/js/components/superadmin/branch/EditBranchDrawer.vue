<script setup lang="ts">
import { X } from '@lucide/vue';
import BranchForm from '@/components/superadmin/branch/BranchForm.vue';
import { Button } from '@/components/ui/button';
import {
    Drawer,
    DrawerClose,
    DrawerContent,
    DrawerDescription,
    DrawerHeader,
    DrawerTitle,
} from '@/components/ui/drawer';
import type { Branch } from '@/types/branch';

defineProps<{ branch: Branch | null }>();

const emit = defineEmits<{ 'update:branch': [Branch | null] }>();

function onOpenChange(open: boolean): void {
    if (!open) {
        emit('update:branch', null);
    }
}
</script>

<template>
    <Drawer
        swipe-direction="right"
        :open="branch !== null"
        @update:open="onOpenChange"
    >
        <DrawerContent class="w-full rounded-none sm:max-w-md">
            <DrawerHeader class="border-b px-6 pt-6 pb-4 text-left">
                <DrawerTitle>Edit Branch</DrawerTitle>
                <DrawerDescription>
                    Update the information for "{{ branch?.name }}".
                </DrawerDescription>
                <DrawerClose as-child class="absolute top-6 right-6">
                    <Button variant="ghost" size="icon" class="size-7">
                        <span class="sr-only">Close</span>
                        <X class="size-4" />
                    </Button>
                </DrawerClose>
            </DrawerHeader>
            <div class="flex-1 overflow-y-auto px-6 py-5">
                <BranchForm
                    v-if="branch"
                    :key="branch.id"
                    :branch="branch"
                    @submitted="emit('update:branch', null)"
                />
            </div>
        </DrawerContent>
    </Drawer>
</template>
