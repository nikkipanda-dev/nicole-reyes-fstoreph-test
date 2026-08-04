import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData {
    [key: string]: unknown;
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: {
        location: string;
        url: string;
        port: null | number;
        defaults: Record<string, unknown>;
        routes: Record<string, string>;
    };
}

export interface User {
    id: number;
    first_name: string;
    last_name: string;
    email_address: string;
    mobile_number: string;
    address: string;
    status: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Product {
    id: number;
    product_name: string;
    product_description: string;
    quantity: number;
    price: string;
    status: string;
    created_at: string;
    updated_at: string;
}

export interface Order {
    id: number;
    product_name: string;
    price: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
