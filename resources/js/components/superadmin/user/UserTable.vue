<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { MoreHorizontal, Pencil, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import UserRoleBadge from '@/components/superadmin/user/UserRoleBadge.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Skeleton } from '@/components/ui/skeleton';
import { Switch } from '@/components/ui/switch';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { destroy, update } from '@/routes/superadmin/users';
import type { User, UserStatus } from '@/types/user';

withDefaults(
    defineProps<{
        users: User[];
        loading?: boolean;
    }>(),
    {
        loading: false,
    },
);

const emit = defineEmits<{ edit: [user: User] }>();

const page = usePage();
const currentUserId = computed(() => page.props.auth?.user?.id ?? null);

const toggleUser = ref<User | null>(null);
const deleteUser = ref<User | null>(null);

const isTogglingSelf = computed(
    () => toggleUser.value?.id === currentUserId.value,
);

function confirmToggle(): void {
    if (toggleUser.value === null) {
        return;
    }

    const status: UserStatus =
        toggleUser.value.status === 'active' ? 'inactive' : 'active';

    router.put(
        update.url(toggleUser.value),
        { status },
        { preserveScroll: true, preserveState: true },
    );

    toggleUser.value = null;
}

function confirmDelete(): void {
    if (deleteUser.value === null) {
        return;
    }

    router.delete(destroy.url(deleteUser.value), {
        preserveScroll: true,
        preserveState: true,
    });

    deleteUser.value = null;
}

function formatDate(date: string): string {
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    }).format(new Date(date));
}
</script>

<template>
    <div>
        <Table>
            <TableHeader class="bg-muted/50">
                <TableRow>
                    <TableHead>Full Name</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Role</TableHead>
                    <TableHead>Branch</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Created</TableHead>
                    <TableHead class="w-12 text-right">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <template v-if="loading">
                    <TableRow v-for="row in 6" :key="row">
                        <TableCell v-for="column in 7" :key="column">
                            <Skeleton class="h-5 w-full" />
                        </TableCell>
                    </TableRow>
                </template>

                <template v-else>
                    <TableRow v-for="user in users" :key="user.id">
                        <TableCell class="max-w-48 truncate font-medium">
                            {{ user.name }}
                        </TableCell>
                        <TableCell class="max-w-48 truncate">
                            {{ user.email }}
                        </TableCell>
                        <TableCell>
                            <UserRoleBadge
                                v-if="user.roles[0]"
                                :role="user.roles[0].name"
                            />
                            <span v-else class="text-muted-foreground">—</span>
                        </TableCell>
                        <TableCell class="max-w-40 truncate">
                            {{ user.branch?.name ?? '—' }}
                        </TableCell>
                        <TableCell>
                            <Switch
                                :checked="user.status === 'active'"
                                :disabled="user.id === currentUserId"
                                :aria-label="
                                    user.status === 'active'
                                        ? 'Deactivate user'
                                        : 'Activate user'
                                "
                                @update:checked="toggleUser = user"
                            />
                        </TableCell>
                        <TableCell
                            class="whitespace-nowrap text-muted-foreground"
                        >
                            {{ formatDate(user.created_at) }}
                        </TableCell>
                        <TableCell class="text-right">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="size-8"
                                    >
                                        <span class="sr-only"
                                            >Open actions</span
                                        >
                                        <MoreHorizontal class="size-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" class="w-40">
                                    <DropdownMenuItem
                                        @click="emit('edit', user)"
                                    >
                                        <Pencil class="size-4" />
                                        Edit
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem
                                        :disabled="user.id === currentUserId"
                                        class="text-destructive focus:text-destructive"
                                        @click="deleteUser = user"
                                    >
                                        <Trash2 class="size-4" />
                                        Delete
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </template>
            </TableBody>
        </Table>

        <AlertDialog
            :open="toggleUser !== null"
            @update:open="(open) => (open ? null : (toggleUser = null))"
        >
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>
                        {{
                            toggleUser?.status === 'active'
                                ? 'Deactivate user?'
                                : 'Activate user?'
                        }}
                    </AlertDialogTitle>
                    <AlertDialogDescription>
                        {{
                            toggleUser?.status === 'active'
                                ? `"${toggleUser?.name}" will be deactivated and can no longer sign in.`
                                : `"${toggleUser?.name}" will be reactivated.`
                        }}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction
                        :class="
                            toggleUser?.status === 'active'
                                ? 'bg-destructive text-white'
                                : ''
                        "
                        :disabled="isTogglingSelf"
                        @click="confirmToggle"
                    >
                        {{
                            toggleUser?.status === 'active'
                                ? 'Deactivate'
                                : 'Activate'
                        }}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
        <AlertDialog
            :open="deleteUser !== null"
            @update:open="(open) => (open ? null : (deleteUser = null))"
        >
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete user?</AlertDialogTitle>
                    <AlertDialogDescription>
                        This will permanently remove "{{
                            deleteUser?.name
                        }}" and can't be undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction
                        class="bg-destructive text-white"
                        @click="confirmDelete"
                    >
                        Delete
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
