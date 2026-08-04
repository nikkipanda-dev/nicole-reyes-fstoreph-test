<script setup lang="ts">
import { Card } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Order } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ShoppingCart } from 'lucide-vue-next';

defineOptions({
    layout: (h: any, page: any) =>
        h(AppLayout, { breadcrumbs: [{ title: 'Orders', href: '/orders' }] }, () => page),
});

defineProps<{
    orders: Order[];
}>();

const stickyHeadClass =
    'sticky top-16 z-10 bg-background shadow-[inset_0_-1px_0_hsl(var(--border))] group-has-[[data-collapsible=icon]]/sidebar-wrapper:top-12';
</script>

<template>
    <Head title="Orders" />

    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="space-y-0.5">
            <h1 class="text-xl font-semibold tracking-tight">Orders</h1>
            <p class="text-sm text-muted-foreground">Track orders placed against your catalog.</p>
        </div>

        <Card class="py-0">
            <Table>
                <TableHeader>
                    <TableRow class="hover:bg-transparent">
                        <TableHead :class="stickyHeadClass">Order ID</TableHead>
                        <TableHead :class="stickyHeadClass">Product Name</TableHead>
                        <TableHead :class="[stickyHeadClass, 'text-right']">Price</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-if="orders.length === 0" class="hover:bg-transparent">
                        <TableCell colspan="3" class="py-12 text-center text-muted-foreground">
                            <div class="flex flex-col items-center gap-2">
                                <ShoppingCart class="h-8 w-8 text-muted-foreground/50" />
                                <p>No orders yet.</p>
                            </div>
                        </TableCell>
                    </TableRow>
                    <TableRow v-for="order in orders" :key="order.id">
                        <TableCell class="font-mono text-muted-foreground">#{{ order.id }}</TableCell>
                        <TableCell class="font-medium">{{ order.product_name }}</TableCell>
                        <TableCell class="text-right font-medium tabular-nums">${{ order.price }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </Card>
    </div>
</template>
