<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus,
    Search,
    Pencil,
    Trash2,
    Package,
    Layers,
    Boxes,
    BarChart3,
    X,
    Filter,
    ArrowDownToLine,
    ClipboardList,
    Archive,
    Zap,
    ChevronRight,
    ArrowUpRight
} from 'lucide-vue-next';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({ data: [], links: [], meta: {} }),
    },
    filters: { type: Object, default: () => ({ search: '' }) },
});

// Tab State
const activeTab = ref('inventory');

const search = ref(props.filters.search);
let searchTimeout;
const updateSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('eco.employee.dashboard'), { search: search.value }, { preserveState: true, replace: true });
    }, 300);
};

// Data
const catalogItems = [
    { id: 1, name: 'Industrial Grade Silk', sku: 'TEX-SILK-001', stock: 1200, category: 'Raw Materials', bulk_min: 50, price: '1,200', unit: 'kg' },
    { id: 2, name: 'Premium Cotton Roll', sku: 'TEX-COT-042', stock: 450, category: 'Fabrics', bulk_min: 20, price: '850', unit: 'roll' },
];

const stats = computed(() => [
    { label: 'Total SKUs', value: '142', icon: Package, color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-900/20' },
    { label: 'Low Stock', value: '05', icon: Archive, color: 'text-rose-600', bg: 'bg-rose-50 dark:bg-rose-900/20' },
    { label: 'Active Tiers', value: '12', icon: Layers, color: 'text-indigo-600', bg: 'bg-indigo-50 dark:bg-indigo-900/20' },
    { label: 'Pending', value: '03', icon: ClipboardList, color: 'text-amber-600', bg: 'bg-amber-50 dark:bg-amber-900/20' },
]);
</script>

<template>

    <Head title="Catalog Management" />

    <AuthenticatedLayout>
        <div class="max-w-[1400px] mx-auto space-y-8">

            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div class="space-y-1">
                    <div
                        class="flex items-center gap-2 text-blue-600 font-black text-[10px] uppercase tracking-[0.2em]">
                        <Zap class="h-3 w-3 fill-current" />
                        E-Commerce Operations
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter uppercase">
                        Catalog <span class="text-blue-600">Master</span>
                    </h1>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-tight">
                        Managing Wholesale Infrastructure & Pricing
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        class="flex items-center gap-2 px-5 py-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm text-[11px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition-all">
                        <ArrowDownToLine class="h-4 w-4" />
                        Export CSV
                    </button>
                    <button
                        class="flex items-center gap-2 px-6 py-3 rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-500/20 text-[11px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all active:scale-95">
                        <Plus class="h-4 w-4" />
                        Add New Item
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="stat in stats" :key="stat.label"
                    class="group bg-white dark:bg-gray-900 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-800 flex items-center justify-between transition-all hover:shadow-xl hover:-translate-y-1">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ stat.label }}</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ stat.value }}</p>
                    </div>
                    <div :class="[stat.bg, stat.color]"
                        class="h-14 w-14 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110">
                        <component :is="stat.icon" class="h-7 w-7" />
                    </div>
                </div>
            </div>

            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-gray-100 dark:border-gray-800 pb-2">
                <div class="flex items-center gap-8">
                    <button @click="activeTab = 'inventory'"
                        :class="[activeTab === 'inventory' ? 'text-blue-600 after:w-full' : 'text-gray-400 after:w-0 hover:text-gray-600']"
                        class="relative pb-4 text-[11px] font-black uppercase tracking-widest transition-all after:absolute after:bottom-0 after:left-0 after:h-1 after:bg-blue-600 after:rounded-t-full after:transition-all">
                        Inventory List
                    </button>
                    <button @click="activeTab = 'bulk-pricing'"
                        :class="[activeTab === 'bulk-pricing' ? 'text-blue-600 after:w-full' : 'text-gray-400 after:w-0 hover:text-gray-600']"
                        class="relative pb-4 text-[11px] font-black uppercase tracking-widest transition-all after:absolute after:bottom-0 after:left-0 after:h-1 after:bg-blue-600 after:rounded-t-full after:transition-all">
                        Bulk Pricing Rules
                    </button>
                </div>

                <div class="flex items-center gap-4 mb-4">
                    <div class="relative w-64 group">
                        <Search
                            class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-blue-600 transition-colors" />
                        <input v-model="search" @input="updateSearch" type="text" placeholder="Search Master SKU..."
                            class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-800 text-xs font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <button
                        class="p-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 text-gray-400 hover:text-blue-600 transition-all">
                        <Filter class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table v-if="activeTab === 'inventory'" class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-gray-800/30 text-[10px] font-black uppercase text-gray-400 tracking-[0.15em]">
                                <th class="px-8 py-5">Product Master</th>
                                <th class="px-8 py-5">Global SKU</th>
                                <th class="px-8 py-5">Department</th>
                                <th class="px-8 py-5">Stock Analysis</th>
                                <th class="px-8 py-5 text-right px-8">Unit Price</th>
                                <th class="px-8 py-5 text-center">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <tr v-for="item in catalogItems" :key="item.id"
                                class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-12 w-12 rounded-[1.25rem] bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 border border-blue-100 dark:border-blue-800/50">
                                            <Boxes class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                                                {{ item.name }}</p>
                                            <p
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">
                                                Industrial Grade</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-[11px] font-black text-gray-500 font-mono tracking-tighter">{{
                                    item.sku }}</td>
                                <td class="px-8 py-6">
                                    <span
                                        class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-gray-800 text-[9px] font-black text-gray-500 uppercase tracking-widest">
                                        {{ item.category }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col gap-1.5 w-32">
                                        <span
                                            class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{
                                            item.stock }} Units</span>
                                        <div
                                            class="h-1.5 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                            <div :class="item.stock < 500 ? 'bg-rose-500' : 'bg-blue-600'"
                                                class="h-full transition-all duration-1000"
                                                :style="`width: ${Math.min((item.stock / 1500) * 100, 100)}%`"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <p class="text-sm font-black text-blue-600 tracking-tighter">₱{{ item.price }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase">per {{ item.unit }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-blue-600 transition-all shadow-sm">
                                            <Pencil class="h-4 w-4" />
                                        </button>
                                        <button
                                            class="px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all active:scale-95 shadow-md">
                                            Edit Specs
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table v-if="activeTab === 'bulk-pricing'" class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-gray-800/30 text-[10px] font-black uppercase text-gray-400 tracking-[0.15em]">
                                <th class="px-8 py-5">Item Name</th>
                                <th class="px-8 py-5">Minimum Threshold</th>
                                <th class="px-8 py-5">Bulk Tier Rate</th>
                                <th class="px-8 py-5 text-right px-8">Status</th>
                                <th class="px-8 py-5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <tr v-for="item in catalogItems" :key="item.id + 'bulk'"
                                class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all">
                                <td
                                    class="px-8 py-6 font-black text-sm text-gray-900 dark:text-white uppercase tracking-tighter">
                                    {{ item.name }}</td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2 text-[11px] font-black text-gray-500 uppercase">
                                        <Layers class="h-3.5 w-3.5 text-blue-600" />
                                        {{ item.bulk_min }} {{ item.unit }}s
                                    </div>
                                </td>
                                <td class="px-8 py-6 font-black text-emerald-600 tracking-tighter text-sm">
                                    ₱{{ (parseFloat(item.price.replace(',', '')) * 0.9).toLocaleString() }}
                                    <span
                                        class="ml-2 text-[9px] font-black bg-emerald-50 text-emerald-600 px-2 py-1 rounded-lg">10%
                                        OFF</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span
                                        class="text-[9px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-2 py-1 rounded-lg">Active
                                        Rule</span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <button
                                        class="text-[10px] font-black uppercase text-blue-600 hover:underline flex items-center justify-center gap-1 mx-auto group">
                                        Configure Rules
                                        <ArrowUpRight
                                            class="h-3 w-3 transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="flex items-center justify-between p-8 rounded-[2.5rem] bg-blue-600 text-white shadow-xl shadow-blue-500/20 relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-white/10 to-transparent"></div>
                <div class="relative z-10">
                    <h4 class="text-sm font-black uppercase tracking-tight">Need to update mass pricing?</h4>
                    <p class="text-[11px] font-bold opacity-80 uppercase tracking-wide">Use the bulk upload feature to
                        update over 100+ SKUs at once.</p>
                </div>
                <button
                    class="relative z-10 px-6 py-3 bg-white text-blue-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95">
                    Bulk Import Tool
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.tracking-tighter {
    letter-spacing: -0.05em;
}

.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>