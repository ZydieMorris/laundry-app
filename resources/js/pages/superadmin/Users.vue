<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { FilterX, Plus, Search, Users as UsersIcon } from '@lucide/vue';
import { watchDebounced } from '@vueuse/core';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import CreateUserDrawer from '@/components/superadmin/user/CreateUserDrawer.vue';
import EditUserDrawer from '@/components/superadmin/user/EditUserDrawer.vue';
import UserTable from '@/components/superadmin/user/UserTable.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { index } from '@/routes/superadmin/users';
import type {
    BranchOption,
    User,
    UserFilters,
    UserPagination,
} from '@/types/user';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Users',
                href: index.url(),
            },
        ],
    },
});

const props = defineProps<{
    users: UserPagination;
    branches: BranchOption[];
    filters: UserFilters;
}>();

const createOpen = ref(false);
const editingUser = ref<User | null>(null);
const loading = ref(false);

const search = ref(props.filters.search ?? '');
const role = ref(props.filters.role ?? '');
const branchId = ref<number | null>(props.filters.branch_id ?? null);
const status = ref(props.filters.status ?? '');

const hasFilters = computed(
    () =>
        search.value !== '' ||
        role.value !== '' ||
        branchId.value !== null ||
        status.value !== '',
);

const isEmpty = computed(() => props.users.data.length === 0 && !loading.value);

function applyFilters(): void {
    router.get(
        index.url({
            query: {
                search: search.value || undefined,
                role: role.value || undefined,
                branch_id: branchId.value ?? undefined,
                status: status.value || undefined,
            },
        }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            only: ['users', 'filters'],
        },
    );
}

function clearFilters(): void {
    search.value = '';
    role.value = '';
    branchId.value = null;
    status.value = '';
}

watchDebounced(search, applyFilters, { debounce: 300, maxWait: 1000 });

watch([role, branchId, status], applyFilters);

let stopLoading: (() => void) | undefined;
let stopLoaded: (() => void) | undefined;

onMounted(() => {
    stopLoading = router.on('start', () => {
        loading.value = true;
    });
    stopLoaded = router.on('finish', () => {
        loading.value = false;
    });
});

onBeforeUnmount(() => {
    stopLoading?.();
    stopLoaded?.();
});

function goToPage(page: number): void {
    router.get(
        index.url({ mergeQuery: { page } }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            only: ['users', 'filters'],
        },
    );
}
</script>

<template>
    <Head title="Users" />

    <div class="flex h-full flex-col gap-6 overflow-x-auto p-4 sm:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading title="Users" description="" />
            <Button @click="createOpen = true">
                <Plus class="size-4" />
                Add User
            </Button>
        </div>

        <div
            class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between"
        >
            <div class="relative">
                <Search
                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    class="w-full pl-9 md:max-w-sm"
                    placeholder="Search by name, email, or contact..."
                    :aria-label="'Search users'"
                />
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <Select v-model="role">
                    <SelectTrigger class="w-40">
                        <SelectValue placeholder="All roles" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="super_admin">Super Admin</SelectItem>
                        <SelectItem value="branch_admin"
                            >Branch Admin</SelectItem
                        >
                        <SelectItem value="staff">Staff</SelectItem>
                        <SelectItem value="customer">Customer</SelectItem>
                    </SelectContent>
                </Select>

                <Select
                    :model-value="
                        branchId === null ? undefined : String(branchId)
                    "
                    @update:model-value="
                        (value) =>
                            (branchId =
                                value === undefined ? null : Number(value))
                    "
                >
                    <SelectTrigger class="w-48">
                        <SelectValue placeholder="All branches" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="branch in branches"
                            :key="branch.id"
                            :value="String(branch.id)"
                        >
                            {{ branch.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>

                <Select v-model="status">
                    <SelectTrigger class="w-36">
                        <SelectValue placeholder="All statuses" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="active">Active</SelectItem>
                        <SelectItem value="inactive">Inactive</SelectItem>
                    </SelectContent>
                </Select>

                <Button
                    v-if="hasFilters"
                    variant="outline"
                    size="sm"
                    @click="clearFilters"
                >
                    <FilterX class="size-4" />
                    Clear
                </Button>
            </div>
        </div>

        <div v-if="loading" class="rounded-xl border bg-card shadow-sm">
            <div class="p-4">
                <Skeleton class="h-6 w-48" />
            </div>
            <div class="space-y-2 p-4 pt-0">
                <Skeleton v-for="row in 6" :key="row" class="h-10 w-full" />
            </div>
        </div>

        <div
            v-else-if="isEmpty"
            class="flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed bg-card px-6 py-16 text-center"
        >
            <div
                class="flex size-12 items-center justify-center rounded-full bg-muted"
            >
                <UsersIcon class="size-6 text-muted-foreground" />
            </div>
            <div class="space-y-1">
                <p class="text-base font-medium">
                    {{ hasFilters ? 'No users found' : 'No users yet' }}
                </p>
                <p class="max-w-sm text-sm text-muted-foreground">
                    {{
                        hasFilters
                            ? 'Try adjusting your search or filters to find what you are looking for.'
                            : 'Create your first user account to start managing the system.'
                    }}
                </p>
            </div>
            <Button v-if="!hasFilters" @click="createOpen = true">
                <Plus class="size-4" />
                Create User
            </Button>
        </div>

        <div v-else class="rounded-xl border bg-card shadow-sm">
            <UserTable
                :users="users.data"
                :loading="loading"
                @edit="editingUser = $event"
            />

            <div v-if="users.total > users.per_page" class="border-t p-3">
                <Pagination
                    :page="users.current_page"
                    :total="users.total"
                    :items-per-page="users.per_page"
                    :siblings="1"
                    show-edges
                    @update:page="goToPage"
                >
                    <PaginationContent v-slot="{ items }">
                        <PaginationFirst />
                        <PaginationPrevious />
                        <template v-for="(item, index) in items" :key="index">
                            <PaginationItem
                                v-if="item.type === 'page'"
                                :value="item.value"
                                :is-active="item.value === users.current_page"
                            />
                            <PaginationEllipsis v-else />
                        </template>
                        <PaginationNext />
                        <PaginationLast />
                    </PaginationContent>
                </Pagination>
            </div>
        </div>

        <CreateUserDrawer :branches="branches" v-model:open="createOpen" />
        <EditUserDrawer
            :user="editingUser"
            :branches="branches"
            @update:user="editingUser = $event"
        />
    </div>
</template>
