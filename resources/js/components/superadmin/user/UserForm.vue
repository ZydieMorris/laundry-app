<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
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
import { store, update } from '@/routes/superadmin/users';
import type { BranchOption, User, UserRoleValue } from '@/types/user';

const props = defineProps<{
    user?: User | null;
    branches: BranchOption[];
}>();

const emit = defineEmits<{ submitted: [] }>();

const currentRole = computed<UserRoleValue | undefined>(
    () => props.user?.roles[0]?.name,
);

const roleOptions = computed<UserRoleValue[]>(() => {
    const roles: UserRoleValue[] = ['super_admin', 'branch_admin', 'staff'];

    if (currentRole.value === 'customer') {
        roles.push('customer');
    }

    return roles;
});

const requiresBranch = computed(() =>
    ['branch_admin', 'staff'].includes(form.role),
);

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    contact_number: props.user?.contact_number ?? '',
    password: '',
    role: (currentRole.value ?? 'branch_admin') as UserRoleValue,
    branch_id: props.user?.branch?.id ?? null,
    status: props.user?.status ?? 'active',
});

function submit(): void {
    const options = {
        onSuccess: () => {
            form.reset('password');
            emit('submitted');
        },
    };

    if (props.user) {
        form.put(update.url(props.user), options);
    } else {
        form.post(store.url(), options);
    }
}
</script>

<template>
    <form class="grid gap-4" @submit.prevent="submit">
        <div class="grid gap-2">
            <Label for="name">Full Name</Label>
            <Input
                id="name"
                v-model="form.name"
                placeholder="Enter full name"
                class="placeholder:text-sm"
                autofocus
                required
            />
            <InputError class="mt-1" :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="email">Email Address</Label>
            <Input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="Enter email address"
                class="placeholder:text-sm"
                required
            />
            <InputError class="mt-1" :message="form.errors.email" />
        </div>

        <div class="grid gap-2">
            <Label for="contact_number">Contact Number</Label>
            <Input
                id="contact_number"
                v-model="form.contact_number"
                placeholder="Enter contact number"
                class="placeholder:text-sm"
            />
            <InputError class="mt-1" :message="form.errors.contact_number" />
        </div>

        <div class="grid gap-2">
            <Label for="role">Role</Label>
            <Select v-model="form.role">
                <SelectTrigger id="role" class="w-full">
                    <SelectValue placeholder="Select role" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem
                        v-for="role in roleOptions"
                        :key="role"
                        :value="role"
                    >
                        {{
                            {
                                super_admin: 'Super Admin',
                                branch_admin: 'Branch Admin',
                                staff: 'Staff',
                                customer: 'Customer',
                            }[role]
                        }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError class="mt-1" :message="form.errors.role" />
        </div>

        <div v-if="requiresBranch" class="grid gap-2">
            <Label for="branch_id">Branch</Label>
            <Select v-model="form.branch_id">
                <SelectTrigger id="branch_id" class="w-full">
                    <SelectValue placeholder="Select branch" />
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
            <InputError class="mt-1" :message="form.errors.branch_id" />
        </div>

        <template v-if="!user">
            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    placeholder="Enter password"
                    class="placeholder:text-sm"
                    autocomplete="new-password"
                    required
                />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>
        </template>

        <div
            class="sticky bottom-0 -mx-6 mt-4 border-t bg-background px-6 pt-4"
        >
            <Button type="submit" class="w-full" :disabled="form.processing">
                {{
                    form.processing
                        ? 'Saving...'
                        : user
                          ? 'Save Changes'
                          : 'Create User'
                }}
            </Button>
        </div>
    </form>
</template>
