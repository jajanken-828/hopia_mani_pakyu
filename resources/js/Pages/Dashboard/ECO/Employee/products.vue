<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Search, Package, Image as ImageIcon, X } from 'lucide-vue-next';

const props = defineProps({
    products: Array,
});

const showCreateModal = ref(false);

const form = useForm({
    name: '',
    sku: '',
    category: '',
    price: '',
    stock: 0,
    status: 'draft',
    image: null,
});

const submit = () => {
    form.post(route('eco.employee.products.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        },
        onError: (errors) => {
            // Keep modal open – errors will be displayed
            console.error(errors);
        },
    });
};
</script>

<template>

    <Head title="Product Management" />
    <AuthenticatedLayout>
        <div class="max-w-[1600px] mx-auto p-10 space-y-8">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-4xl font-black uppercase">Product <span class="text-indigo-600">Architect</span></h1>
                <button @click="showCreateModal = true"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black uppercase text-[10px] hover:bg-indigo-700 transition-all">
                    <Plus class="inline h-4 w-4 mr-2" /> Add Product
                </button>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-[2.5rem] border overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[10px] font-black uppercase text-gray-400">
                        <tr>
                            <th class="px-8 py-5 text-center">Preview</th>
                            <th class="px-8 py-5">Details</th>
                            <th class="px-8 py-5">Stock</th>
                            <th class="px-8 py-5">Price</th>
                            <th class="px-8 py-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="product in products" :key="product.id">
                            <td class="px-8 py-6 flex justify-center">
                                <img v-if="product.image_url" :src="product.image_url"
                                    class="h-14 w-14 rounded-xl object-cover" />
                                <div v-else
                                    class="h-14 w-14 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">
                                    <ImageIcon class="h-6 w-6" />
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <p class="font-black uppercase text-sm">{{ product.name }}</p>
                                <p class="text-[10px] text-indigo-600">{{ product.sku }}</p>
                                <p class="text-[9px] text-gray-400 uppercase tracking-wider">{{ product.category }}</p>
                            </td>
                            <td class="px-8 py-6 font-bold text-gray-500">{{ product.stock }} Units</td>
                            <td class="px-8 py-6 font-black italic">₱{{ product.price }}</td>
                            <td class="px-8 py-6 text-right">
                                <button class="text-red-500 uppercase font-black text-[10px]">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-[2.5rem] w-full max-w-lg p-8 space-y-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-black uppercase">Add New <span class="text-indigo-600">Product</span></h2>
                    <button @click="showCreateModal = false">
                        <X class="h-6 w-6" />
                    </button>
                </div>

                <form @submit.prevent="submit" class="grid grid-cols-2 gap-4" enctype="multipart/form-data">
                    <!-- Product Name -->
                    <div class="col-span-2">
                        <label class="text-[10px] font-black uppercase text-gray-400">Product Name *</label>
                        <input v-model="form.name" type="text"
                            class="w-full rounded-xl border-gray-100 bg-gray-50 p-3 text-sm" />
                        <p v-if="form.errors.name" class="text-red-500 text-[9px] mt-1">{{ form.errors.name }}</p>
                    </div>

                    <!-- SKU -->
                    <div class="col-span-1">
                        <label class="text-[10px] font-black uppercase text-gray-400">SKU *</label>
                        <input v-model="form.sku" type="text"
                            class="w-full rounded-xl border-gray-100 bg-gray-50 p-3 text-sm" />
                        <p v-if="form.errors.sku" class="text-red-500 text-[9px] mt-1">{{ form.errors.sku }}</p>
                    </div>

                    <!-- Category (NEW) -->
                    <div class="col-span-1">
                        <label class="text-[10px] font-black uppercase text-gray-400">Category *</label>
                        <input v-model="form.category" type="text"
                            class="w-full rounded-xl border-gray-100 bg-gray-50 p-3 text-sm"
                            placeholder="e.g. Apparel" />
                        <p v-if="form.errors.category" class="text-red-500 text-[9px] mt-1">{{ form.errors.category }}
                        </p>
                    </div>

                    <!-- Price -->
                    <div class="col-span-1">
                        <label class="text-[10px] font-black uppercase text-gray-400">Price (₱) *</label>
                        <input v-model="form.price" type="number" step="0.01"
                            class="w-full rounded-xl border-gray-100 bg-gray-50 p-3 text-sm" />
                        <p v-if="form.errors.price" class="text-red-500 text-[9px] mt-1">{{ form.errors.price }}</p>
                    </div>

                    <!-- Stock (NEW) -->
                    <div class="col-span-1">
                        <label class="text-[10px] font-black uppercase text-gray-400">Stock *</label>
                        <input v-model="form.stock" type="number" min="0"
                            class="w-full rounded-xl border-gray-100 bg-gray-50 p-3 text-sm" />
                        <p v-if="form.errors.stock" class="text-red-500 text-[9px] mt-1">{{ form.errors.stock }}</p>
                    </div>

                    <!-- Status (optional, but good to have) -->
                    <div class="col-span-2">
                        <label class="text-[10px] font-black uppercase text-gray-400">Status</label>
                        <div class="flex gap-4 mt-1">
                            <label class="flex items-center gap-2">
                                <input type="radio" value="published" v-model="form.status" />
                                <span class="text-xs font-medium">Published</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" value="draft" v-model="form.status" />
                                <span class="text-xs font-medium">Draft</span>
                            </label>
                        </div>
                        <p v-if="form.errors.status" class="text-red-500 text-[9px] mt-1">{{ form.errors.status }}</p>
                    </div>

                    <!-- Product Image -->
                    <div class="col-span-2">
                        <label class="text-[10px] font-black uppercase text-gray-400">Product Image</label>
                        <input type="file" @input="form.image = $event.target.files[0]" accept="image/*"
                            class="w-full text-sm" />
                        <p v-if="form.errors.image" class="text-red-500 text-[9px] mt-1">{{ form.errors.image }}</p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="form.processing"
                        class="col-span-2 bg-indigo-600 text-white py-4 rounded-2xl font-black uppercase tracking-widest shadow-lg disabled:opacity-50">
                        {{ form.processing ? 'Saving...' : 'Save Product' }}
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>