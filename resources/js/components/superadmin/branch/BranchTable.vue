<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { MoreHorizontal, Pencil, Power } from '@lucide/vue';
import { ref } from 'vue';
import BranchStatusBadge from '@/components/superadmin/branch/BranchStatusBadge.vue';
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
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { update } from '@/routes/superadmin/branches';
import type { Branch, BranchStatus } from '@/types/branch';

withDefaults(
    defineProps<{
        branches: Branch[];
        loading?: boolean;
    }>(),
    {
        loading: false,
    },
);

const emit = defineEmits<{ edit: [branch: Branch] }>();

const toggleBranch = ref<Branch | null>(null);

function formatDate(value: string): string {
    return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(
        new Date(value),
    );
}

function confirmToggle(): void {
    if (toggleBranch.value === null) {
        return;
    }

    const status: BranchStatus =
        toggleBranch.value.status === 'active' ? 'inactive' : 'active';

    router.put(
        update.url(toggleBranch.value),
        { status },
        { preserveScroll: true, preserveState: true },
    );

    toggleBranch.value = null;
}
</script>

<template>
    <div>
        <Table>
            <TableHeader class="bg-muted/50">
                <TableRow>
                    <TableHead>Branch Name</TableHead>
                    <TableHead>Code</TableHead>
                    <TableHead>Contact</TableHead>
                    <TableHead>Email</TableHead>
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
                    <TableRow v-for="branch in branches" :key="branch.id">
                        <TableCell class="max-w-52 truncate font-medium">
                            {{ branch.name }}
                        </TableCell>
                        <TableCell>
                            <span
                                class="rounded-md bg-muted px-2 py-0.5 font-mono text-xs"
                            >
                                {{ branch.code }}
                            </span>
                        </TableCell>
                        <TableCell class="text-muted-foreground">
                            {{ branch.contact_number ?? '—' }}
                        </TableCell>
                        <TableCell
                            class="max-w-52 truncate text-muted-foreground"
                        >
                            {{ branch.email ?? '—' }}
                        </TableCell>
                        <TableCell>
                            <BranchStatusBadge :status="branch.status" />
                        </TableCell>
                        <TableCell class="text-muted-foreground">
                            {{ formatDate(branch.created_at) }}
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
                                <DropdownMenuContent align="end" class="w-44">
                                    <DropdownMenuItem
                                        @click="emit('edit', branch)"
                                    >
                                        <Pencil class="size-4" />
                                        Edit
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem
                                        :class="
                                            branch.status === 'active'
                                                ? 'text-destructive focus:text-destructive'
                                                : ''
                                        "
                                        @click="toggleBranch = branch"
                                    >
                                        <Power class="size-4" />
                                        {{
                                            branch.status === 'active'
                                                ? 'Deactivate'
                                                : 'Activate'
                                        }}
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </template>
            </TableBody>
        </Table>

        <AlertDialog
            :open="toggleBranch !== null"
            @update:open="(open) => (open ? null : (toggleBranch = null))"
        >
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>
                        {{
                            toggleBranch?.status === 'active'
                                ? 'Deactivate branch?'
                                : 'Activate branch?'
                        }}
                    </AlertDialogTitle>
                    <AlertDialogDescription>
                        {{
                            toggleBranch?.status === 'active'
                                ? `"${toggleBranch?.name}" will be deactivated. It remains in the system for historical records.`
                                : `"${toggleBranch?.name}" will be reactivated.`
                        }}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction
                        :class="
                            toggleBranch?.status === 'active'
                                ? 'bg-destructive text-white'
                                : ''
                        "
                        @click="confirmToggle"
                    >
                        {{
                            toggleBranch?.status === 'active'
                                ? 'Deactivate'
                                : 'Activate'
                        }}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
