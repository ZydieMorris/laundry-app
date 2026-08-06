<script setup lang="ts">
import { Users, X } from '@lucide/vue';
import UserForm from '@/components/superadmin/user/UserForm.vue';
import { Button } from '@/components/ui/button';
import {
    Drawer,
    DrawerClose,
    DrawerContent,
    DrawerDescription,
    DrawerHeader,
    DrawerTitle,
} from '@/components/ui/drawer';
import type { BranchOption, User } from '@/types/user';

defineProps<{
    user: User | null;
    branches: BranchOption[];
}>();

const emit = defineEmits<{ 'update:user': [User | null] }>();

function onOpenChange(open: boolean): void {
    if (!open) {
        emit('update:user', null);
    }
}
</script>

<template>
    <Drawer
        swipe-direction="right"
        :open="user !== null"
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
                            <Users class="size-5" />
                        </div>
                        <div class="space-y-1">
                            <DrawerTitle class="text-lg">
                                Edit User
                            </DrawerTitle>
                            <DrawerDescription>
                                Update the information for "{{ user?.name }}".
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
                <UserForm
                    v-if="user"
                    :key="user.id"
                    :user="user"
                    :branches="branches"
                    @submitted="emit('update:user', null)"
                />
            </div>
        </DrawerContent>
    </Drawer>
</template>
