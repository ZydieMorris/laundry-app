<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store, update } from '@/routes/superadmin/branches';
import type { Branch, BranchStatus } from '@/types/branch';

const props = defineProps<{ branch?: Branch | null }>();

const emit = defineEmits<{ submitted: [] }>();

const form = useForm({
    name: props.branch?.name ?? '',
    owner: props.branch?.owner ?? '',
    code: props.branch?.code ?? '',
    address: props.branch?.address ?? '',
    status: (props.branch?.status ?? 'active') as BranchStatus,
});

function submit(): void {
    const options = { onSuccess: () => emit('submitted') };

    if (props.branch) {
        form.put(update.url(props.branch), options);
    } else {
        form.post(store.url(), options);
    }
}
</script>

<template>
    <form class="grid gap-4" @submit.prevent="submit">
        <div class="grid gap-2">
            <Label for="name">Branch Name</Label>
            <Input
                id="name"
                v-model="form.name"
                placeholder="Enter branch name"
                class="placeholder:text-sm"
                autofocus
                required
            />
            <InputError class="mt-1" :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="owner">Owner</Label>
            <Input
                id="owner"
                v-model="form.owner"
                placeholder="Enter owner name"
                class="placeholder:text-sm"
            />
            <InputError class="mt-1" :message="form.errors.owner" />
        </div>

        <div class="grid gap-2">
            <Label for="code">Branch Code</Label>
            <Input
                id="code"
                v-model="form.code"
                placeholder="Enter branch code"
                class="placeholder:text-sm"
            />
            <InputError class="mt-1" :message="form.errors.code" />
        </div>

        <div class="grid gap-2">
            <Label for="address">Complete Address</Label>
            <textarea
                id="address"
                v-model="form.address"
                rows="3"
                placeholder="Enter complete address"
                class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none placeholder:text-sm placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-4 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50"
            />
            <InputError class="mt-1" :message="form.errors.address" />
        </div>

        <div
            class="sticky bottom-0 -mx-6 mt-4 border-t bg-background px-6 pt-4"
        >
            <Button type="submit" class="w-full" :disabled="form.processing">
                {{
                    form.processing
                        ? 'Saving...'
                        : branch
                          ? 'Save Changes'
                          : 'Create Branch'
                }}
            </Button>
        </div>
    </form>
</template>
