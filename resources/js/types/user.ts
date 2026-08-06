export type UserStatus = 'active' | 'inactive';

export type UserRoleValue =
    'super_admin' | 'branch_admin' | 'staff' | 'customer';

export interface UserRole {
    id: number;
    name: UserRoleValue;
}

export interface User {
    id: number;
    name: string;
    email: string;
    contact_number: string | null;
    status: UserStatus;
    branch: { id: number; name: string } | null;
    roles: UserRole[];
    created_at: string;
}

export interface BranchOption {
    id: number;
    name: string;
}

export interface UserFilters {
    search: string;
    role: string;
    branch_id: number | null;
    status: string;
}

export interface UserPaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface UserPagination {
    data: User[];
    current_page: number;
    last_page: number;
    first_page_url: string | null;
    last_page_url: string | null;
    next_page_url: string | null;
    prev_page_url: string | null;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: UserPaginationLink[];
}
