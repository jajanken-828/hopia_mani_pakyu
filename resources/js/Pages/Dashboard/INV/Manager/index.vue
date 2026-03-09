<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Boxes,
    Warehouse,
    TrendingUp,
    TrendingDown,
    AlertTriangle,
    CheckCircle,
    ArrowRight,
    Package,
    RefreshCw,
    ShoppingCart,
    BarChart2,
    Activity,
    Clock,
    ChevronRight,
    ArrowUpRight,
    ArrowDownRight,
    Zap,
} from 'lucide-vue-next';

// ── Dummy Data ────────────────────────────────────────────────────────────────

const props = defineProps({
    auth: Object,
});

const isLoaded = ref(false);
onMounted(() => setTimeout(() => (isLoaded.value = true), 50));

const warehouses = [
    { id: 1, name: 'Main Warehouse', location: 'Cavite, PH', skus: 7, capacity: 5000, used: 3812, color: 'blue' },
    { id: 2, name: 'North Storage Facility', location: 'Bulacan, PH', skus: 5, capacity: 3000, used: 1240, color: 'emerald' },
    { id: 3, name: 'South Distribution Hub', location: 'Laguna, PH', skus: 6, capacity: 4500, used: 4100, color: 'amber' },
    { id: 4, name: 'East Textile Depot', location: 'Rizal, PH', skus: 3, capacity: 2000, used: 560, color: 'violet' },
];

const kpis = [
    { label: 'Total SKUs', value: '21', delta: '+3', up: true, icon: Boxes, accent: 'blue' },
    { label: 'In Stock', value: '14', delta: '+2', up: true, icon: CheckCircle, accent: 'emerald' },
    { label: 'Low Stock Alerts', value: '5', delta: '+1', up: false, icon: AlertTriangle, accent: 'amber' },
    { label: 'Out of Stock', value: '2', delta: '−1', up: true, icon: TrendingDown, accent: 'red' },
    { label: 'Total Warehouses', value: '4', delta: '0', up: true, icon: Warehouse, accent: 'violet' },
    { label: 'Avg Capacity Used', 'value': '67%', delta: '+4%', up: false, icon: BarChart2, accent: 'slate' },
];

const alertItems = [
    { sku: 'SKU-002', name: 'Polyester Blend (Navy)', warehouse: 'Main Warehouse', qty: 95, reorder: 100, type: 'low' },
    { sku: 'SKU-005', name: 'Zipper (20cm Black)', warehouse: 'Main Warehouse', qty: 0, reorder: 500, type: 'out' },
    { sku: 'SKU-101', name: 'Silk Fabric (Ivory)', warehouse: 'North Storage Facility', qty: 60, reorder: 80, type: 'low' },
    { sku: 'SKU-105', name: 'Nylon Mesh', warehouse: 'North Storage Facility', qty: 0, reorder: 40, type: 'out' },
    { sku: 'SKU-203', name: 'Shrink Wrap Roll', warehouse: 'South Distribution Hub', qty: 45, reorder: 50, type: 'low' },
    { sku: 'SKU-303', name: 'Fusible Interfacing', warehouse: 'East Textile Depot', qty: 30, reorder: 40, type: 'low' },
    { sku: 'SKU-101', name: 'Silk Fabric (Ivory)', warehouse: 'North Storage Facility', qty: 60, reorder: 80, type: 'low' },
];

const recentActivity = [
    { time: '2 min ago', action: 'Stock received', item: 'Cotton Fabric Roll (White)', qty: '+120 rolls', color: 'emerald', warehouse: 'Main Warehouse' },
    { time: '14 min ago', action: 'Stock dispatched', item: 'Denim Fabric (Dark)', qty: '−45 rolls', color: 'red', warehouse: 'South Distribution Hub' },
    { time: '1 hr ago', action: 'Reorder triggered', item: 'Polyester Blend (Navy)', qty: '95 rolls', color: 'amber', warehouse: 'Main Warehouse' },
    { time: '2 hrs ago', action: 'New item added', item: 'Foam Padding (5mm)', qty: '+600 sheets', color: 'blue', warehouse: 'South Distribution Hub' },
    { time: '3 hrs ago', action: 'Stock adjusted', item: 'Thread Spool (Black)', qty: '840 spools', color: 'violet', warehouse: 'Main Warehouse' },
    { time: 'Yesterday', action: 'Stock received', item: 'Wool Blend Fabric', qty: '+88 rolls', color: 'emerald', warehouse: 'East Textile Depot' },
    { time: 'Yesterday', action: 'Out of stock flagged', item: 'Zipper (20cm Black)', qty: '0 pcs', color: 'red', warehouse: 'Main Warehouse' },
];

// Weekly stock movement (dummy sparkline data)
const sparklineData = [42, 58, 51, 67, 74, 63, 80, 72, 88, 76, 91, 85];
const maxSpark = Math.max(...sparklineData);

// Category breakdown
const categories = [
    { name: 'Raw Material', count: 9, pct: 43, color: 'bg-blue-500' },
    { name: 'Accessory', count: 6, pct: 29, color: 'bg-violet-500' },
    { name: 'Chemical', count: 3, pct: 14, color: 'bg-emerald-500' },
    { name: 'Packaging', count: 3, pct: 14, color: 'bg-amber-500' },
];

// ── Helpers ───────────────────────────────────────────────────────────────────
const colorMap = {
    blue: { bg: 'bg-blue-50 dark:bg-blue-900/20', text: 'text-blue-600', bar: 'bg-blue-500', dot: 'bg-blue-500', ring: 'ring-blue-500/30' },
    emerald: { bg: 'bg-emerald-50 dark:bg-emerald-900/20', text: 'text-emerald-600', bar: 'bg-emerald-500', dot: 'bg-emerald-500', ring: 'ring-emerald-500/30' },
    amber: { bg: 'bg-amber-50 dark:bg-amber-900/20', text: 'text-amber-600', bar: 'bg-amber-500', dot: 'bg-amber-500', ring: 'ring-amber-500/30' },
    violet: { bg: 'bg-violet-50 dark:bg-violet-900/20', text: 'text-violet-600', bar: 'bg-violet-500', dot: 'bg-violet-500', ring: 'ring-violet-500/30' },
    red: { bg: 'bg-red-50 dark:bg-red-900/20', text: 'text-red-500', bar: 'bg-red-500', dot: 'bg-red-500', ring: 'ring-red-500/30' },
    slate: { bg: 'bg-slate-100 dark:bg-slate-800', text: 'text-slate-600', bar: 'bg-slate-400', dot: 'bg-slate-400', ring: 'ring-slate-400/30' },
};

const capPct = (w) => Math.round((w.used / w.capacity) * 100);
const capColor = (p) => p >= 90 ? 'bg-red-500' : p >= 70 ? 'bg-amber-500' : 'bg-emerald-500';
const capTextColor = (p) => p >= 90 ? 'text-red-500' : p >= 70 ? 'text-amber-500' : 'text-emerald-600';

// Build SVG polyline path from sparkline data
const sparkPath = computed(() => {
    const w = 120, h = 40, pad = 4;
    return sparklineData.map((v, i) => {
        const x = pad + (i / (sparklineData.length - 1)) * (w - pad * 2);
        const y = h - pad - ((v / maxSpark) * (h - pad * 2));
        return `${i === 0 ? 'M' : 'L'}${x.toFixed(1)},${y.toFixed(1)}`;
    }).join(' ');
});
</script>

<template>

    <Head title="Inventory Dashboard | Monti Textile" />

    <AuthenticatedLayout>

        <!-- ── Page Header ───────────────────────────────────────────────────── -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4"
            :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
            style="transition: opacity .5s ease, transform .5s ease;">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em]">Live
                        Overview</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Inventory Dashboard</h1>
                <p class="text-slate-500 text-sm mt-0.5">Real-time stock overview across all warehouse locations.</p>
            </div>
            <Link :href="route('inv.manager.inventory')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-bold rounded-xl hover:opacity-80 transition shadow-sm flex-shrink-0">
                <Boxes class="w-4 h-4" />
                Manage Inventory
                <ArrowRight class="w-3.5 h-3.5" />
            </Link>
        </div>

        <!-- ── KPI Cards ─────────────────────────────────────────────────────── -->
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
            <div v-for="(kpi, i) in kpis" :key="kpi.label"
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex flex-col gap-3 hover:shadow-md transition-shadow"
                :style="`transition-delay: ${i * 60}ms`"
                :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                style="transition: opacity .5s ease, transform .5s ease;">
                <div class="flex items-start justify-between">
                    <div :class="['w-9 h-9 rounded-xl flex items-center justify-center', colorMap[kpi.accent].bg]">
                        <component :is="kpi.icon" :class="['w-4 h-4', colorMap[kpi.accent].text]" />
                    </div>
                    <span :class="[
                        'text-[10px] font-black px-2 py-0.5 rounded-full flex items-center gap-0.5',
                        kpi.up ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20' : 'bg-red-50 text-red-500 dark:bg-red-900/20'
                    ]">
                        <component :is="kpi.up ? ArrowUpRight : ArrowDownRight" class="w-3 h-3" />
                        {{ kpi.delta }}
                    </span>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-900 dark:text-white leading-none">{{ kpi.value }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">{{ kpi.label }}</p>
                </div>
            </div>
        </div>

        <!-- ── Middle Row ────────────────────────────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

            <!-- Warehouse Capacity Panel -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden"
                :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                style="transition: opacity .6s ease .2s, transform .6s ease .2s;">
                <div
                    class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Warehouse class="w-4 h-4 text-slate-400" />
                        <h2 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Warehouse
                            Capacity</h2>
                    </div>
                    <Link :href="route('inv.manager.inventory')"
                        class="text-[11px] font-bold text-blue-600 hover:underline flex items-center gap-1">
                        Full View
                        <ChevronRight class="w-3 h-3" />
                    </Link>
                </div>
                <div class="p-5 space-y-5">
                    <div v-for="wh in warehouses" :key="wh.id" class="group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2.5">
                                <span :class="['w-2 h-2 rounded-full flex-shrink-0', colorMap[wh.color].dot]" />
                                <div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-white leading-none">{{ wh.name
                                        }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ wh.location }} · {{ wh.skus }} SKUs
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p :class="['text-sm font-black', capTextColor(capPct(wh))]">{{ capPct(wh) }}%</p>
                                <p class="text-[10px] text-slate-400">{{ wh.used.toLocaleString() }} / {{
                                    wh.capacity.toLocaleString() }}</p>
                            </div>
                        </div>
                        <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div :class="['h-full rounded-full transition-all duration-700', capColor(capPct(wh))]"
                                :style="`width: ${isLoaded ? capPct(wh) : 0}%`" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden"
                :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                style="transition: opacity .6s ease .3s, transform .6s ease .3s;">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <BarChart2 class="w-4 h-4 text-slate-400" />
                    <h2 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">By Category
                    </h2>
                </div>
                <div class="p-5 space-y-4">
                    <!-- Mini donut visual using stacked bars -->
                    <div class="flex h-3 rounded-full overflow-hidden gap-0.5 mb-5">
                        <div v-for="cat in categories" :key="cat.name"
                            :class="[cat.color, 'transition-all duration-700 rounded-sm']"
                            :style="`width: ${isLoaded ? cat.pct : 0}%`" />
                    </div>
                    <div v-for="cat in categories" :key="cat.name" class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span :class="['w-2.5 h-2.5 rounded-sm flex-shrink-0', cat.color]" />
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ cat.name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-400">{{ cat.count }} SKUs</span>
                            <span class="text-xs font-black text-slate-600 dark:text-slate-300 w-8 text-right">{{
                                cat.pct }}%</span>
                        </div>
                    </div>

                    <!-- Sparkline -->
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Weekly
                                Movement</span>
                            <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-0.5">
                                <TrendingUp class="w-3 h-3" /> +12%
                            </span>
                        </div>
                        <svg viewBox="0 0 120 40" class="w-full h-10" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="sparkFill" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.15" />
                                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            <path :d="sparkPath + ' L116,40 L4,40 Z'" fill="url(#sparkFill)" />
                            <path :d="sparkPath" fill="none" stroke="#3b82f6" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Bottom Row ─────────────────────────────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- Stock Alerts -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden"
                :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                style="transition: opacity .6s ease .35s, transform .6s ease .35s;">
                <div
                    class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <AlertTriangle class="w-4 h-4 text-amber-500" />
                        <h2 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Stock
                            Alerts</h2>
                        <span
                            class="text-[10px] font-black bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 px-2 py-0.5 rounded-full">
                            {{ alertItems.length }}
                        </span>
                    </div>
                    <Link :href="route('inv.manager.inventory')"
                        class="text-[11px] font-bold text-blue-600 hover:underline flex items-center gap-1">
                        Manage
                        <ChevronRight class="w-3 h-3" />
                    </Link>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-72 overflow-y-auto">
                    <div v-for="alert in alertItems" :key="alert.sku + alert.warehouse"
                        class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors group">
                        <div :class="[
                            'w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0',
                            alert.type === 'out' ? 'bg-red-50 dark:bg-red-900/20' : 'bg-amber-50 dark:bg-amber-900/20'
                        ]">
                            <component :is="alert.type === 'out' ? Package : AlertTriangle"
                                :class="['w-3.5 h-3.5', alert.type === 'out' ? 'text-red-500' : 'text-amber-500']" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{ alert.name }}
                            </p>
                            <p class="text-[11px] text-slate-400">{{ alert.warehouse }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p
                                :class="['text-sm font-black', alert.type === 'out' ? 'text-red-500' : 'text-amber-600']">
                                {{ alert.qty }} units
                            </p>
                            <p class="text-[10px] text-slate-400">Reorder: {{ alert.reorder }}</p>
                        </div>
                        <span :class="[
                            'text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-full flex-shrink-0',
                            alert.type === 'out'
                                ? 'bg-red-50 text-red-500 ring-1 ring-red-200 dark:bg-red-900/20 dark:ring-red-800'
                                : 'bg-amber-50 text-amber-600 ring-1 ring-amber-200 dark:bg-amber-900/20 dark:ring-amber-800'
                        ]">
                            {{ alert.type === 'out' ? 'Out' : 'Low' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden"
                :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                style="transition: opacity .6s ease .45s, transform .6s ease .45s;">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <Activity class="w-4 h-4 text-slate-400" />
                    <h2 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Recent
                        Activity</h2>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-72 overflow-y-auto">
                    <div v-for="(act, i) in recentActivity" :key="i"
                        class="flex items-start gap-3 px-5 py-3.5 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                        <div class="mt-0.5 flex-shrink-0">
                            <span
                                :class="['w-2 h-2 rounded-full block mt-1.5', colorMap[act.color]?.dot ?? 'bg-slate-400']" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ act.action }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ act.item }}</p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span :class="[
                                    'text-[10px] font-black',
                                    act.color === 'emerald' ? 'text-emerald-600' : act.color === 'red' ? 'text-red-500' : act.color === 'amber' ? 'text-amber-600' : act.color === 'blue' ? 'text-blue-600' : 'text-violet-600'
                                ]">{{ act.qty }}</span>
                                <span class="text-[10px] text-slate-400">· {{ act.warehouse }}</span>
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-400 flex-shrink-0 flex items-center gap-0.5 mt-0.5">
                            <Clock class="w-2.5 h-2.5" />
                            {{ act.time }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Quick Actions Footer ───────────────────────────────────────────── -->
        <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4"
            :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            style="transition: opacity .6s ease .55s, transform .6s ease .55s;">
            <Link :href="route('inv.manager.inventory')"
                class="group flex items-center justify-between p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                        <Boxes class="w-4 h-4 text-blue-600" />
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-800 dark:text-white">Inventory Manager</p>
                        <p class="text-[11px] text-slate-400">Browse & manage all SKUs</p>
                    </div>
                </div>
                <ChevronRight
                    class="w-4 h-4 text-slate-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition-all" />
            </Link>

            <div
                class="group flex items-center justify-between p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-md transition-all cursor-pointer">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                        <RefreshCw class="w-4 h-4 text-emerald-600" />
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-800 dark:text-white">Sync Stock</p>
                        <p class="text-[11px] text-slate-400">Reconcile warehouse levels</p>
                    </div>
                </div>
                <ChevronRight
                    class="w-4 h-4 text-slate-300 group-hover:text-emerald-500 group-hover:translate-x-0.5 transition-all" />
            </div>

            <div
                class="group flex items-center justify-between p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-amber-300 dark:hover:border-amber-700 hover:shadow-md transition-all cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                        <Zap class="w-4 h-4 text-amber-600" />
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-800 dark:text-white">Reorder Queue</p>
                        <p class="text-[11px] text-slate-400">{{alertItems.filter(a => a.type === 'low').length}}
                            items pending reorder</p>
                    </div>
                </div>
                <ChevronRight
                    class="w-4 h-4 text-slate-300 group-hover:text-amber-500 group-hover:translate-x-0.5 transition-all" />
            </div>
        </div>

    </AuthenticatedLayout>
</template>