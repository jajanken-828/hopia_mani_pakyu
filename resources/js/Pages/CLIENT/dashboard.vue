<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    ShoppingBag,
    Search,
    Filter,
    Star,
    CreditCard,
    ArrowRight,
    Plus,
    Minus,
    Zap,
    Clock,
    Image as ImageIcon
} from 'lucide-vue-next';

const props = defineProps({
    products: Array, // Data from real database [!code ++]
    stats: {
        type: Object,
        default: () => ({
            pending_orders: 0,
            completed_orders: 0,
            recent_orders: []
        })
    }
});

const page = usePage();
const client = computed(() => page.props.auth?.client);

// Categories for the filter bar (derived from real data or static)
const categories = ['All Fabrics', 'Cotton', 'Polyester', 'Silk', 'Knitted', 'Denim'];
const activeCategory = ref('All Fabrics');

// Filter the real products based on category
const filteredProducts = computed(() => {
    if (activeCategory.value === 'All Fabrics') return props.products;
    return props.products.filter(p => p.category === activeCategory.value);
});
</script>

<template>

    <Head title="Partner Storefront" />

    <AuthenticatedLayout>
        <div class="max-w-[1400px] mx-auto space-y-10">

            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div class="space-y-1">
                    <div
                        class="flex items-center gap-2 text-blue-600 font-black text-[10px] uppercase tracking-[0.2em]">
                        <Zap class="h-3 w-3 fill-current" />
                        Exclusive B2B Storefront
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter uppercase">
                        Welcome, <span class="text-blue-600">{{ client?.company_name || 'Partner' }}</span>
                    </h1>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-tight">
                        Authorized Buyer: {{ client?.contact_person ?? 'Representative' }}
                    </p>
                </div>

                <div
                    class="flex items-center gap-4 bg-white dark:bg-gray-900 p-4 rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-xl shadow-gray-200/50">
                    <div
                        class="h-12 w-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                        <CreditCard class="h-6 w-6" />
                    </div>
                    <div class="pr-4 border-r border-gray-100 dark:border-gray-800">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Available Credit</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white">
                            ₱{{ client?.credit_limit ? parseFloat(client.credit_limit).toLocaleString() : '0' }}
                        </p>
                    </div>
                    <Link href="#"
                        class="h-10 w-10 rounded-full bg-gray-900 dark:bg-white flex items-center justify-center text-white dark:text-gray-900 transition-transform hover:scale-110">
                        <Plus class="h-5 w-5" />
                    </Link>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-center gap-4">
                <div class="flex-1 flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 no-scrollbar">
                    <button v-for="cat in categories" :key="cat" @click="activeCategory = cat" :class="[
                        'px-6 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all duration-300 whitespace-nowrap border',
                        activeCategory === cat
                            ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20'
                            : 'bg-white dark:bg-gray-900 text-gray-500 border-gray-100 dark:border-gray-800 hover:border-blue-200'
                    ]">
                        {{ cat }}
                    </button>
                </div>

                <div class="relative w-full md:w-64 group">
                    <Search
                        class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-blue-600 transition-colors" />
                    <input type="text" placeholder="Search catalog..."
                        class="w-full pl-11 pr-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-900 text-xs font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Premium
                        Collection</h2>
                    <div class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <Filter class="h-3 w-3" /> Sort By: Popularity
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div v-for="product in filteredProducts" :key="product.id"
                        class="group relative flex flex-col rounded-[2.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 transition-all duration-500 hover:shadow-2xl hover:shadow-blue-500/5 hover:-translate-y-2">

                        <div
                            class="relative aspect-square overflow-hidden rounded-[2rem] bg-gray-50 dark:bg-gray-800 mb-6">
                            <img v-if="product.image_url" :src="product.image_url" :alt="product.name"
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" />
                            <div v-else class="h-full w-full flex items-center justify-center text-gray-300">
                                <ImageIcon class="h-12 w-12" />
                            </div>

                            <div
                                class="absolute top-4 right-4 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md px-2 py-1 rounded-xl flex items-center gap-1 shadow-sm">
                                <Star class="h-3 w-3 text-yellow-500 fill-current" />
                                <span class="text-[10px] font-black text-gray-900 dark:text-white">New</span>
                            </div>
                        </div>

                        <div class="space-y-1 px-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3
                                        class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                        {{ product.name }}
                                    </h3>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">
                                        SKU: {{ product.sku }} • {{ product.category }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-black text-blue-600">₱{{
                                        parseFloat(product.price).toLocaleString() }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase">per unit</p>
                                </div>
                            </div>

                            <div class="mt-6 flex items-center gap-3">
                                <div
                                    class="flex-1 flex items-center justify-between rounded-2xl bg-gray-50 dark:bg-gray-800/50 p-1 border border-gray-100 dark:border-gray-700">
                                    <button class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                        <Minus class="h-4 w-4" />
                                    </button>
                                    <span class="text-xs font-black text-gray-900 dark:text-white">1</span>
                                    <button class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                        <Plus class="h-4 w-4" />
                                    </button>
                                </div>

                                <button
                                    class="h-11 w-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/30 transition-all hover:bg-blue-700 active:scale-90">
                                    <ShoppingBag class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="filteredProducts.length === 0" class="py-20 text-center">
                    <Package class="h-12 w-12 text-gray-200 mx-auto mb-4" />
                    <p class="text-gray-400 font-black uppercase tracking-widest text-xs">No products found in this
                        category</p>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[2.5rem] bg-gray-900 p-8 text-white mt-12">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <div
                            class="h-16 w-16 rounded-[2rem] bg-white/10 flex items-center justify-center backdrop-blur-xl">
                            <Clock class="h-8 w-8 text-blue-400" />
                        </div>
                        <div>
                            <h4 class="text-lg font-black uppercase tracking-tight">Live Order Tracking</h4>
                            <p class="text-sm text-gray-400 font-medium">You have <span
                                    class="text-blue-400 font-bold">{{ stats.pending_orders }} orders</span> in the
                                production pipeline.</p>
                        </div>
                    </div>
                    <Link :href="route('client.orders')"
                        class="group flex items-center gap-3 bg-white text-gray-900 px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-transform hover:scale-105 active:scale-95">
                        View Order Status
                        <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>