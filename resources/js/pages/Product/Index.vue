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
import { Eye, Package, Pencil, Plus } from 'lucide-vue-next';
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

// Create modal
const createOpen = ref(false);
const createForm = useForm({
    product_name: '',
    product_description: '',
    quantity: 0,
    price: '',
});
const openCreate = () => {
    createForm.clearErrors();
    createForm.reset();
    createOpen.value = true;
};
const submitCreate = () => {
    createForm.post(route('products.store'), {
        preserveScroll: true,
        onSuccess: () => {
            createOpen.value = false;
            createForm.reset();
        },
    });
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
    status: '',
});
const openEdit = (product: Product) => {
    editForm.clearErrors();
    editingProductId.value = product.id;
    editForm.product_name = product.product_name;
    editForm.product_description = product.product_description;
    editForm.quantity = product.quantity;
    editForm.price = product.price;
    editForm.status = product.status;
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
</script>

<template>
    <Head title="Products" />

    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <div class="space-y-0.5">
                <h1 class="text-xl font-semibold tracking-tight">Products</h1>
                <p class="text-sm text-muted-foreground">Manage your catalog, pricing, and inventory.</p>
            </div>
            <Button @click="openCreate">
                <Plus class="h-4 w-4" />
                Add product
            </Button>
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
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </Card>
    </div>

    <Dialog v-model:open="createOpen">
        <DialogContent>
            <form class="space-y-4" @submit.prevent="submitCreate">
                <DialogHeader>
                    <DialogTitle>Add product</DialogTitle>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="create_product_name">Product name</Label>
                    <Input id="create_product_name" v-model="createForm.product_name" />
                    <InputError :message="createForm.errors.product_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="create_product_description">Description</Label>
                    <Textarea id="create_product_description" v-model="createForm.product_description" />
                    <InputError :message="createForm.errors.product_description" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="create_quantity">Quantity</Label>
                        <Input id="create_quantity" type="number" min="0" v-model="createForm.quantity" />
                        <InputError :message="createForm.errors.quantity" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="create_price">Price</Label>
                        <Input id="create_price" type="number" min="0" step="0.01" v-model="createForm.price" />
                        <InputError :message="createForm.errors.price" />
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Button type="submit" :disabled="createForm.processing">Create</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

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

                <div class="grid gap-2">
                    <Label for="edit_status">Status</Label>
                    <select
                        id="edit_status"
                        v-model="editForm.status"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                    >
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                    <InputError :message="editForm.errors.status" />
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
</template>
