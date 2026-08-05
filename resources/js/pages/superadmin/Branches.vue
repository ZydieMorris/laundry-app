<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Plus, Search, Store } from '@lucide/vue';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import BranchTable from '@/components/superadmin/branch/BranchTable.vue';
import CreateBranchDrawer from '@/components/superadmin/branch/CreateBranchDrawer.vue';
import EditBranchDrawer from '@/components/superadmin/branch/EditBranchDrawer.vue';
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
import { Skeleton } from '@/components/ui/skeleton';
import { index } from '@/routes/superadmin/branches';
import type { Branch, BranchFilters, BranchPagination } from '@/types/branch';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Branches',
                href: index.url(),
            },
        ],
    },
});

const props = defineProps<{
    branches: BranchPagination;
    filters: BranchFilters;
}>();

const createOpen = ref(false);
const editingBranch = ref<Branch | null>(null);
const loading = ref(false);

const search = ref(props.filters.search ?? '');

const isEmpty = computed(
    () => props.branches.data.length === 0 && !loading.value,
);

let searchTimer: number | undefined;

function onSearchInput(): void {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(() => {
        router.get(
            index.url({ query: { search: search.value || undefined } }),
            {},
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                only: ['branches', 'filters'],
            },
        );
    }, 300);
}

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
    window.clearTimeout(searchTimer);
});

function goToPage(page: number): void {
    router.get(
        index.url({ mergeQuery: { page } }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            only: ['branches', 'filters'],
        },
    );
}
</script>

<template>
    <Head title="Branches" />

    <div class="flex h-full flex-col gap-6 overflow-x-auto p-4 sm:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Branches"
                description="Manage all laundry branches across the company."
            />
            <Button @click="createOpen = true">
                <Plus class="size-4" />
                Add Branch
            </Button>
        </div>

        <div class="relative">
            <Search
                class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
            />
            <Input
                v-model="search"
                class="pl-9"
                placeholder="Search by name, code, or email..."
                :aria-label="'Search branches'"
                @input="onSearchInput"
            />
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
                <Store class="size-6 text-muted-foreground" />
            </div>
            <div class="space-y-1">
                <p class="text-base font-medium">
                    {{
                        filters.search ? 'No branches found' : 'No branches yet'
                    }}
                </p>
                <p class="max-w-sm text-sm text-muted-foreground">
                    {{
                        filters.search
                            ? 'Try adjusting your search to find what you are looking for.'
                            : 'Create your first branch to start managing laundry operations.'
                    }}
                </p>
            </div>
            <Button v-if="!filters.search" @click="createOpen = true">
                <Plus class="size-4" />
                Create Branch
            </Button>
        </div>

        <div v-else class="rounded-xl border bg-card shadow-sm">
            <BranchTable
                :branches="branches.data"
                :loading="loading"
                @edit="editingBranch = $event"
            />

            <div v-if="branches.total > branches.per_page" class="border-t p-3">
                <Pagination
                    :page="branches.current_page"
                    :total="branches.total"
                    :items-per-page="branches.per_page"
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
                                :is-active="
                                    item.value === branches.current_page
                                "
                            />
                            <PaginationEllipsis v-else />
                        </template>
                        <PaginationNext />
                        <PaginationLast />
                    </PaginationContent>
                </Pagination>
            </div>
        </div>

        <CreateBranchDrawer v-model:open="createOpen" />
        <EditBranchDrawer
            :branch="editingBranch"
            @update:branch="editingBranch = $event"
        />
    </div>
</template>
