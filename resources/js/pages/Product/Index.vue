<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Badge, type BadgeVariants } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Product } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Eye, Package, Pencil, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

defineOptions({
    layout: (h: any, page: any) =>
        h(AppLayout, { breadcrumbs: [{ title: 'Products', href: '/products' }] }, () => page),
});

defineProps<{
    products: Product[];
}>();

const truncate = (text: string, length = 60) => (text.length > length ? `${text.slice(0, length)}…` : text);

const stickyHeadClass =
    'sticky top-16 z-10 bg-background shadow-[inset_0_-1px_0_hsl(var(--border))] group-has-[[data-collapsible=icon]]/sidebar-wrapper:top-12';

const statusVariant = (status: string): BadgeVariants['variant'] => {
    switch (status) {
        case 'published':
            return 'success';
        case 'draft':
            return 'warning';
        case 'archived':
            return 'secondary';
        default:
            return 'outline';
    }
};

// View modal
const viewOpen = ref(false);
const viewingProduct = ref<Product | null>(null);
const openView = (product: Product) => {
    viewingProduct.value = product;
    viewOpen.value = true;
};

// Edit modal
const editOpen = ref(false);
const editingProductId = ref<number | null>(null);
const editForm = useForm({
    product_name: '',
    product_description: '',
    quantity: 0,
    price: '',
});
const openEdit = (product: Product) => {
    editForm.clearErrors();
    editingProductId.value = product.id;
    editForm.product_name = product.product_name;
    editForm.product_description = product.product_description;
    editForm.quantity = product.quantity;
    editForm.price = product.price;
    editOpen.value = true;
};
const submitEdit = () => {
    if (!editingProductId.value) return;

    editForm.put(route('products.update', [editingProductId.value]), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (editOpen.value = false),
    });
};

// Delete modal
const deleteOpen = ref(false);
const deletingProduct = ref<Product | null>(null);
const deleteForm = useForm({});
const openDelete = (product: Product) => {
    deletingProduct.value = product;
    deleteOpen.value = true;
};
const submitDelete = () => {
    if (!deletingProduct.value) return;

    deleteForm.delete(route('products.destroy', [deletingProduct.value.id]), {
        preserveScroll: true,
        onSuccess: () => (deleteOpen.value = false),
    });
};
</script>

<template>
    <Head title="Products" />

    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <div class="space-y-0.5">
                <h1 class="text-xl font-semibold tracking-tight">Products</h1>
                <p class="text-sm text-muted-foreground">Manage your catalog, pricing, and inventory.</p>
            </div>
        </div>

        <Card class="py-0">
            <Table>
                <TableHeader>
                    <TableRow class="hover:bg-transparent">
                        <TableHead :class="stickyHeadClass">Status</TableHead>
                        <TableHead :class="stickyHeadClass">Product name</TableHead>
                        <TableHead :class="stickyHeadClass">Description</TableHead>
                        <TableHead :class="[stickyHeadClass, 'text-right']">Quantity</TableHead>
                        <TableHead :class="[stickyHeadClass, 'text-right']">Price</TableHead>
                        <TableHead :class="[stickyHeadClass, 'text-right']">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-if="products.length === 0" class="hover:bg-transparent">
                        <TableCell colspan="6" class="py-12 text-center text-muted-foreground">
                            <div class="flex flex-col items-center gap-2">
                                <Package class="h-8 w-8 text-muted-foreground/50" />
                                <p>No products yet.</p>
                            </div>
                        </TableCell>
                    </TableRow>
                    <TableRow v-for="product in products" :key="product.id">
                        <TableCell>
                            <Badge :variant="statusVariant(product.status)" class="capitalize">{{ product.status }}</Badge>
                        </TableCell>
                        <TableCell class="font-medium">{{ product.product_name }}</TableCell>
                        <TableCell class="text-muted-foreground">{{ truncate(product.product_description) }}</TableCell>
                        <TableCell class="text-right tabular-nums">{{ product.quantity }}</TableCell>
                        <TableCell class="text-right font-medium tabular-nums">${{ product.price }}</TableCell>
                        <TableCell class="text-right">
                            <div class="flex justify-end gap-1">
                                <Button variant="ghost" size="icon" @click="openView(product)">
                                    <Eye class="h-4 w-4" />
                                </Button>
                                <Button variant="ghost" size="icon" @click="openEdit(product)">
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button variant="ghost" size="icon" @click="openDelete(product)">
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </Card>
    </div>

    <Dialog v-model:open="viewOpen">
        <DialogContent v-if="viewingProduct">
            <DialogHeader>
                <DialogTitle>{{ viewingProduct.product_name }}</DialogTitle>
                <DialogDescription class="capitalize">{{ viewingProduct.status }}</DialogDescription>
            </DialogHeader>
            <div class="space-y-4 text-sm">
                <p>{{ viewingProduct.product_description }}</p>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="text-muted-foreground">Quantity</p>
                        <p>{{ viewingProduct.quantity }}</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground">Price</p>
                        <p>${{ viewingProduct.price }}</p>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>

    <Dialog v-model:open="editOpen">
        <DialogContent>
            <form class="space-y-4" @submit.prevent="submitEdit">
                <DialogHeader>
                    <DialogTitle>Edit product</DialogTitle>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="edit_product_name">Product name</Label>
                    <Input id="edit_product_name" v-model="editForm.product_name" />
                    <InputError :message="editForm.errors.product_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="edit_product_description">Description</Label>
                    <Textarea id="edit_product_description" v-model="editForm.product_description" />
                    <InputError :message="editForm.errors.product_description" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="edit_quantity">Quantity</Label>
                        <Input id="edit_quantity" type="number" min="0" v-model="editForm.quantity" />
                        <InputError :message="editForm.errors.quantity" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="edit_price">Price</Label>
                        <Input id="edit_price" type="number" min="0" step="0.01" v-model="editForm.price" />
                        <InputError :message="editForm.errors.price" />
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Button type="submit" :disabled="editForm.processing">Save</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <Dialog v-model:open="deleteOpen">
        <DialogContent v-if="deletingProduct">
            <DialogHeader>
                <DialogTitle>Delete "{{ deletingProduct.product_name }}"?</DialogTitle>
                <DialogDescription>This action cannot be undone.</DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="secondary">Cancel</Button>
                </DialogClose>
                <Button variant="destructive" :disabled="deleteForm.processing" @click="submitDelete">Delete</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
