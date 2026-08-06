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
import type { BranchOption } from '@/types/user';

defineProps<{ open: boolean; branches: BranchOption[] }>();

const emit = defineEmits<{ 'update:open': [boolean] }>();
</script>

<template>
    <Drawer
        swipe-direction="right"
        :open="open"
        @update:open="emit('update:open', $event)"
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
                                Create User
                            </DrawerTitle>
                            <DrawerDescription>
                                Create a new user account and assign a role.
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
                    :branches="branches"
                    @submitted="emit('update:open', false)"
                />
            </div>
        </DrawerContent>
    </Drawer>
</template>
