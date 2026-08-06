<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { CircleAlert } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { logout } from '@/routes';

const page = usePage();

const isInactive = computed(
    () => page.props.auth?.user?.status === 'inactive',
);

function handleLogout(): void {
    router.post(logout.url());
}
</script>

<template>
    <div
        v-if="isInactive"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 p-4"
    >
        <div
            class="w-full max-w-md rounded-lg border border-border bg-background p-6 shadow-xl"
        >
            <div class="flex flex-col items-center gap-3 text-center">
                <div
                    class="flex size-12 items-center justify-center rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400"
                >
                    <CircleAlert class="size-6" />
                </div>
                <h2 class="text-lg font-semibold">Account deactivated</h2>
                <p class="text-sm text-muted-foreground">
                    Your account has been deactivated. Please contact the super
                    admin to activate your account.
                </p>
                <Button
                    class="mt-2 w-full"
                    variant="destructive"
                    @click="handleLogout"
                >
                    Log out
                </Button>
            </div>
        </div>
    </div>
</template>
