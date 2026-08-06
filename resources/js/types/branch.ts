export type BranchStatus = 'active' | 'inactive';

export interface Branch {
    id: number;
    name: string;
    owner: string | null;
    code: string;
    address: string | null;
    status: BranchStatus;
    created_at: string;
}

export interface BranchFilters {
    search: string;
}

export interface BranchPaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface BranchPagination {
    data: Branch[];
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
    links: BranchPaginationLink[];
}
