<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus,
    Search,
    Package,
    Layers,
    Boxes,
    BarChart3,
    ArrowDownToLine,
    Archive,
    Zap,
    ChevronRight,
    ShoppingBag,
    Clock,
    AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({ data: [], links: [], meta: {} }),
    },
    stats: {
        type: Object,
        default: () => ({
            todaySales: '0.00',
            monthlyRevenue: '0.00',
            activeProducts: 0,
            lowStockCount: 0,
            activeTiers: 0,
            pendingCredit: 0,
            pendingTiering: 0
        })
    },
    onlineSales: {
        type: Array,
        default: () => []
    },
    // New prop to show specific details of orders in the pipeline
    pipelineDetails: {
        type: Array,
        default: () => []
    },
    filters: { type: Object, default: () => ({ search: '' }) },
});

const activeTab = ref('inventory');
const search = ref(props.filters.search);
let searchTimeout;

const updateSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('eco.employee.dashboard'), { search: search.value }, { preserveState: true, replace: true });
    }, 300);
};

const statsData = computed(() => [
    { label: 'Today Sales', value: `₱${props.stats.todaySales}`, icon: Zap, color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-900/20' },
    { label: 'Low Stock', value: props.stats.lowStockCount, icon: Archive, color: 'text-rose-600', bg: 'bg-rose-50 dark:bg-rose-900/20' },
    { label: 'Active SKUs', value: props.stats.activeProducts, icon: Package, color: 'text-indigo-600', bg: 'bg-indigo-50 dark:bg-indigo-900/20' },
    { label: 'Monthly Rev.', value: `₱${props.stats.monthlyRevenue}`, icon: BarChart3, color: 'text-amber-600', bg: 'bg-amber-50 dark:bg-amber-900/20' },
]);

const getStatusStyles = (status) => {
    switch (status) {
        case 'approved': return 'bg-emerald-50 text-emerald-600 border-emerald-200';
        case 'credit_review': return 'bg-orange-50 text-orange-600 border-orange-200';
        case 'tier_assignment': return 'bg-blue-50 text-blue-600 border-blue-200';
        default: return 'bg-gray-50 text-gray-500 border-gray-200';
    }
};

// Computed properties to separate pipeline details for targeted display
const creditReviewItems = computed(() => props.pipelineDetails.filter(i => i.status === 'credit_review'));
const tierAssignmentItems = computed(() => props.pipelineDetails.filter(i => i.status === 'tier_assignment'));
</script>

<template>

    <Head title="ECO Dashboard - Catalog Master" />

    <AuthenticatedLayout>
        <div class="max-w-[1600px] mx-auto space-y-10 p-4 lg:p-10">

            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div class="space-y-1">
                    <div
                        class="flex items-center gap-2 text-blue-600 font-black text-[10px] uppercase tracking-[0.2em]">
                        <Zap class="h-3 w-3 fill-current" /> Infrastructure Live
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter uppercase">
                        Catalog <span class="text-blue-600">Master</span>
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        class="px-6 py-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 text-[11px] font-black uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition-all">
                        <ArrowDownToLine class="h-4 w-4" /> Export
                    </button>

                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="stat in statsData" :key="stat.label"
                    class="bg-white dark:bg-gray-900 p-7 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ stat.label }}</p>
                        <p
                            class="text-3xl font-black text-gray-900 dark:text-white mt-1 italic tracking-tighter italic">
                            {{ stat.value }}</p>
                    </div>
                    <div :class="[stat.bg, stat.color]" class="h-14 w-14 rounded-2xl flex items-center justify-center">
                        <component :is="stat.icon" class="h-7 w-7" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-50 pb-4">
                        <div class="flex gap-8">
                            <button @click="activeTab = 'inventory'"
                                :class="activeTab === 'inventory' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-400'"
                                class="pb-2 text-[11px] font-black uppercase tracking-widest transition-all">Inventory
                                Ledger</button>
                            <button @click="activeTab = 'pipeline'"
                                :class="activeTab === 'pipeline' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-400'"
                                class="pb-2 text-[11px] font-black uppercase tracking-widest transition-all italic">Department
                                Pipeline</button>
                        </div>
                        <!-- <div class="relative w-64 group">
                            <Search
                                class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-blue-600 transition-colors" />
                            <input v-model="search" @input="updateSearch" type="text" placeholder="Search data..."
                                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white dark:bg-gray-900 border-gray-100 text-xs font-bold shadow-sm focus:ring-2 focus:ring-blue-500 transition-all">
                        </div> -->
                    </div>

                    <div
                        class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">

                        <table v-if="activeTab === 'inventory'" class="w-full text-left">
                            <thead
                                class="bg-gray-50/50 text-[10px] font-black uppercase text-gray-400 tracking-[0.15em]">
                                <tr>
                                    <th class="px-8 py-5">Product Master</th>
                                    <th class="px-8 py-5 text-center italic">Stock Capacity</th>
                                    <th class="px-8 py-5 text-right italic">Unit Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="item in products.data" :key="item.id"
                                    class="group hover:bg-gray-50/30 transition-all">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-sm">
                                                <Boxes class="h-5 w-5" />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                                                    {{ item.name }}</p>
                                                <p class="text-[9px] font-bold text-gray-400 font-mono italic">{{
                                                    item.sku }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 w-64">
                                        <div class="flex items-center justify-between mb-1.5 px-1">
                                            <span class="text-[10px] font-black uppercase italic"
                                                :class="item.stock < 50 ? 'text-rose-500 animate-pulse' : 'text-gray-500'">
                                                {{ item.stock }} In Stock
                                            </span>
                                        </div>
                                        <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                            <div :class="item.stock < 50 ? 'bg-rose-500' : 'bg-blue-600'"
                                                class="h-full transition-all duration-1000 shadow-[0_0_10px_rgba(37,99,235,0.2)]"
                                                :style="`width: ${Math.min((item.stock / 500) * 100, 100)}%`"></div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right font-black text-indigo-600 italic tracking-tighter">
                                        ₱{{ parseFloat(item.price).toLocaleString() }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="activeTab === 'pipeline'" class="p-8 space-y-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div
                                    class="p-6 bg-orange-50/50 rounded-3xl border border-orange-100 flex items-center justify-between">
                                    <div>
                                        <h4
                                            class="text-[10px] font-black uppercase text-orange-900 tracking-widest italic">
                                            Awaiting Credit Review</h4>
                                        <p class="text-3xl font-black text-orange-600 mt-1 italic tracking-tighter">{{
                                            props.stats.pendingCredit }}</p>
                                    </div>
                                    <Clock class="h-8 w-8 text-orange-300" />
                                </div>
                                <div
                                    class="p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100 flex items-center justify-between">
                                    <div>
                                        <h4
                                            class="text-[10px] font-black uppercase text-indigo-900 tracking-widest italic">
                                            Awaiting Bulk Tiering</h4>
                                        <p class="text-3xl font-black text-indigo-600 mt-1 italic tracking-tighter">{{
                                            props.stats.pendingTiering }}</p>
                                    </div>
                                    <Layers class="h-8 w-8 text-indigo-300" />
                                </div>
                            </div>

                            <div class="space-y-8">
                                <div v-if="creditReviewItems.length" class="space-y-4">
                                    <div class="flex items-center gap-2 px-2">
                                        <AlertCircle class="h-4 w-4 text-orange-500" />
                                        <h5
                                            class="text-[11px] font-black uppercase text-gray-500 tracking-widest italic">
                                            Action Required: Credit Queue</h5>
                                    </div>
                                    <div class="border border-gray-100 rounded-3xl overflow-hidden shadow-sm">
                                        <table class="w-full text-left text-sm">
                                            <thead
                                                class="bg-gray-50 text-[10px] font-bold uppercase text-gray-400 tracking-wider">
                                                <tr>
                                                    <th class="px-6 py-4">Partner Agency</th>
                                                    <th class="px-6 py-4 italic">PO Ref</th>
                                                    <th class="px-6 py-4 text-right italic">Valuation</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50 bg-white">
                                                <tr v-for="item in creditReviewItems" :key="item.id"
                                                    class="hover:bg-gray-50/50 transition-colors">
                                                    <td
                                                        class="px-6 py-4 font-black text-gray-700 uppercase tracking-tighter text-xs">
                                                        {{ item.client?.company_name }}</td>
                                                    <td class="px-6 py-4 text-xs font-mono font-bold text-gray-400">#{{
                                                        item.po_number }}</td>
                                                    <td
                                                        class="px-6 py-4 text-right font-black text-orange-600 italic text-xs">
                                                        ₱{{ parseFloat(item.total_amount).toLocaleString() }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div v-if="tierAssignmentItems.length" class="space-y-4">
                                    <div class="flex items-center gap-2 px-2">
                                        <AlertCircle class="h-4 w-4 text-blue-500" />
                                        <h5
                                            class="text-[11px] font-black uppercase text-gray-500 tracking-widest italic">
                                            Action Required: Tier Assignment</h5>
                                    </div>
                                    <div class="border border-gray-100 rounded-3xl overflow-hidden shadow-sm">
                                        <table class="w-full text-left text-sm">
                                            <thead
                                                class="bg-gray-50 text-[10px] font-bold uppercase text-gray-400 tracking-wider">
                                                <tr>
                                                    <th class="px-6 py-4">Partner Agency</th>
                                                    <th class="px-6 py-4 italic">PO Ref</th>
                                                    <th class="px-6 py-4 text-right italic">Valuation</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50 bg-white">
                                                <tr v-for="item in tierAssignmentItems" :key="item.id"
                                                    class="hover:bg-gray-50/50 transition-colors">
                                                    <td
                                                        class="px-6 py-4 font-black text-gray-700 uppercase tracking-tighter text-xs">
                                                        {{ item.client?.company_name }}</td>
                                                    <td class="px-6 py-4 text-xs font-mono font-bold text-gray-400">#{{
                                                        item.po_number }}</td>
                                                    <td
                                                        class="px-6 py-4 text-right font-black text-blue-600 italic text-xs">
                                                        ₱{{ parseFloat(item.total_amount).toLocaleString() }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div v-if="!creditReviewItems.length && !tierAssignmentItems.length"
                                    class="py-12 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-100">
                                    <ShieldCheck class="h-10 w-10 text-emerald-300 mx-auto mb-3" />
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest italic">All
                                        pipeline protocols cleared</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    <div class="flex items-center gap-2 px-2">
                        <ShoppingBag class="h-4 w-4 text-blue-600" />
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-500 italic">Live Order Stream
                        </h3>
                    </div>

                    <div class="space-y-4">
                        <div v-for="sale in onlineSales" :key="sale.id"
                            class="p-5 bg-white dark:bg-gray-900 rounded-[2rem] border border-gray-100 shadow-sm hover:border-blue-200 transition-all group">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p
                                        class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter italic">
                                        {{ sale.client?.company_name }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase italic">#{{ sale.po_number }}
                                    </p>
                                </div>
                                <span :class="getStatusStyles(sale.status)"
                                    class="px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest border border-current italic">
                                    {{ sale.status.replace('_', ' ') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-end border-t border-gray-50 pt-3 italic">
                                <span class="text-sm font-black text-blue-600 italic tracking-tighter">₱{{
                                    parseFloat(sale.total_amount).toLocaleString() }}</span>
                                <Link :href="route('eco.employee.ordermng')"
                                    class="text-[9px] font-black text-gray-400 uppercase group-hover:text-blue-600 flex items-center gap-1 transition-all">
                                    Analyze
                                    <ChevronRight class="h-3 w-3" />
                                </Link>
                            </div>
                        </div>

                        <div v-if="!onlineSales.length"
                            class="text-center py-10 bg-gray-50/50 rounded-[2rem] border border-dashed border-gray-200">
                            <Archive class="h-8 w-8 text-gray-200 mx-auto mb-2" />
                            <p
                                class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic tracking-tighter">
                                System Idle: No recent activity</p>
                        </div>
                    </div>
                </div>
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