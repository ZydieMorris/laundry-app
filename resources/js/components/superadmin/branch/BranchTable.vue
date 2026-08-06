<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { MoreHorizontal, Pencil } from '@lucide/vue';
import { ref } from 'vue';
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
                    <TableHead>Owner</TableHead>
                    <TableHead>Address</TableHead>
                    <TableHead>Active</TableHead>
                    <TableHead class="w-12 text-right">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <template v-if="loading">
                    <TableRow v-for="row in 6" :key="row">
                        <TableCell v-for="column in 6" :key="column">
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
                        <TableCell class="max-w-40 truncate">
                            {{ branch.owner ?? '—' }}
                        </TableCell>
                        <TableCell
                            class="max-w-56 truncate text-muted-foreground"
                        >
                            {{ branch.address ?? '—' }}
                        </TableCell>
                        <TableCell>
                            <Switch
                                :checked="branch.status === 'active'"
                                :aria-label="
                                    branch.status === 'active'
                                        ? 'Deactivate branch'
                                        : 'Activate branch'
                                "
                                @update:checked="toggleBranch = branch"
                            />
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
                                        @click="emit('edit', branch)"
                                    >
                                        <Pencil class="size-4" />
                                        Edit
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
