<script setup lang="ts">
import { Store, X } from '@lucide/vue';
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
        <DrawerContent
            class="w-full rounded-l-2xl border-l border-border bg-background shadow-2xl sm:max-w-xl lg:max-w-2xl"
        >
            <DrawerHeader class="border-b px-6 pt-6 pb-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700"
                        >
                            <Store class="size-5" />
                        </div>
                        <div class="space-y-1">
                            <DrawerTitle class="text-lg">
                                Edit Branch
                            </DrawerTitle>
                            <DrawerDescription>
                                Update the information for "{{ branch?.name }}".
                            </DrawerDescription>
                        </div>
                    </div>
                    <DrawerClose as-child>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="size-7 shrink-0"
                        >
                            <span class="sr-only">Close</span>
                            <X class="size-4" />
                        </Button>
                    </DrawerClose>
                </div>
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
