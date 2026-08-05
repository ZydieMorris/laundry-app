<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { store, update } from '@/routes/superadmin/branches';
import type { Branch, BranchStatus } from '@/types/branch';

const props = defineProps<{ branch?: Branch | null }>();

const emit = defineEmits<{ submitted: [] }>();

const form = useForm({
    name: props.branch?.name ?? '',
    code: props.branch?.code ?? '',
    contact_number: props.branch?.contact_number ?? '',
    email: props.branch?.email ?? '',
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
                placeholder="e.g. Downtown Laundry"
                autofocus
                required
            />
            <InputError class="mt-1" :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="code">Branch Code</Label>
            <Input
                id="code"
                v-model="form.code"
                placeholder="e.g. BR-DOWNTOWN"
            />
            <p class="text-xs text-muted-foreground">
                Leave empty to auto-generate a unique code.
            </p>
            <InputError class="mt-1" :message="form.errors.code" />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="contact_number">Contact Number</Label>
                <Input
                    id="contact_number"
                    v-model="form.contact_number"
                    placeholder="+1 555-0101"
                />
                <InputError
                    class="mt-1"
                    :message="form.errors.contact_number"
                />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email Address</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    placeholder="branch@laundry.test"
                />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label for="address">Complete Address</Label>
            <textarea
                id="address"
                v-model="form.address"
                rows="3"
                placeholder="Street, district, city"
                class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-4 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50"
            />
            <InputError class="mt-1" :message="form.errors.address" />
        </div>

        <div class="grid gap-2">
            <Label for="status">Status</Label>
            <Select v-model="form.status">
                <SelectTrigger id="status" class="w-full">
                    <SelectValue placeholder="Select status" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="active">Active</SelectItem>
                    <SelectItem value="inactive">Inactive</SelectItem>
                </SelectContent>
            </Select>
            <InputError class="mt-1" :message="form.errors.status" />
        </div>

        <div class="mt-2 flex justify-end gap-3">
            <Button
                type="button"
                variant="outline"
                :disabled="form.processing"
                @click="
                    form.reset();
                    form.clearErrors();
                "
            >
                Reset
            </Button>
            <Button type="submit" :disabled="form.processing">
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
