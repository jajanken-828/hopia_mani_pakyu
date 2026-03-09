<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Search, Plus, ChevronDown, ArrowRightLeft, AlertTriangle, X,
    Edit2, Trash2, ArrowUpDown, Send, PackageCheck, Warehouse,
    TrendingUp, TrendingDown, Package, Layers, FlaskConical,
    CheckCircle, DollarSign, BarChart2,
} from 'lucide-vue-next';

// ─── Warehouses ──────────────────────────────────────────────────────────────
const warehouses = ref([
    { id: 1, name: 'Main Warehouse', location: 'Cavite, PH', color: 'blue' },
    { id: 2, name: 'North Storage Facility', location: 'Bulacan, PH', color: 'emerald' },
    { id: 3, name: 'South Distribution Hub', location: 'Laguna, PH', color: 'amber' },
    { id: 4, name: 'East Textile Depot', location: 'Rizal, PH', color: 'violet' },
]);

// ─── Master Materials (stock keyed by warehouse id) ──────────────────────────
const materials = ref([
    { id: 'MAT-001', name: 'Cotton Fabric Roll (White)', category: 'Raw Material', unit: 'rolls', reorder: 100, cost: 850.00, stock: { 1: 480, 2: 0, 3: 0, 4: 0 } },
    { id: 'MAT-002', name: 'Polyester Blend (Navy)', category: 'Raw Material', unit: 'rolls', reorder: 100, cost: 920.00, stock: { 1: 95, 2: 0, 3: 0, 4: 0 } },
    { id: 'MAT-003', name: 'Dye — Reactive Red', category: 'Chemical', unit: 'kg', reorder: 50, cost: 215.00, stock: { 1: 320, 2: 0, 3: 0, 4: 0 } },
    { id: 'MAT-004', name: 'Elastic Band (1in)', category: 'Accessory', unit: 'meters', reorder: 2000, cost: 12.50, stock: { 1: 12000, 2: 0, 3: 0, 4: 0 } },
    { id: 'MAT-005', name: 'Zipper (20cm Black)', category: 'Accessory', unit: 'pcs', reorder: 500, cost: 8.75, stock: { 1: 0, 2: 0, 3: 0, 4: 0 } },
    { id: 'MAT-006', name: 'Linen Fabric (Beige)', category: 'Raw Material', unit: 'rolls', reorder: 80, cost: 1100.00, stock: { 1: 210, 2: 0, 3: 0, 4: 0 } },
    { id: 'MAT-007', name: 'Thread Spool (Black)', category: 'Accessory', unit: 'spools', reorder: 200, cost: 45.00, stock: { 1: 840, 2: 0, 3: 0, 4: 0 } },
    { id: 'MAT-101', name: 'Silk Fabric (Ivory)', category: 'Raw Material', unit: 'rolls', reorder: 80, cost: 2400.00, stock: { 1: 0, 2: 60, 3: 0, 4: 0 } },
    { id: 'MAT-102', name: 'Dye — Indigo Blue', category: 'Chemical', unit: 'kg', reorder: 50, cost: 310.00, stock: { 1: 0, 2: 180, 3: 0, 4: 0 } },
    { id: 'MAT-103', name: 'Button Set (Brass)', category: 'Accessory', unit: 'pcs', reorder: 1000, cost: 3.20, stock: { 1: 0, 2: 5500, 3: 0, 4: 0 } },
    { id: 'MAT-104', name: 'Velcro Strip (White)', category: 'Accessory', unit: 'meters', reorder: 500, cost: 18.00, stock: { 1: 0, 2: 2200, 3: 0, 4: 0 } },
    { id: 'MAT-105', name: 'Nylon Mesh', category: 'Raw Material', unit: 'rolls', reorder: 40, cost: 760.00, stock: { 1: 0, 2: 0, 3: 0, 4: 0 } },
    { id: 'MAT-201', name: 'Denim Fabric (Dark)', category: 'Raw Material', unit: 'rolls', reorder: 100, cost: 1350.00, stock: { 1: 0, 2: 0, 3: 390, 4: 0 } },
    { id: 'MAT-202', name: 'Packaging Box (L)', category: 'Packaging', unit: 'pcs', reorder: 500, cost: 25.00, stock: { 1: 0, 2: 0, 3: 1800, 4: 0 } },
    { id: 'MAT-203', name: 'Shrink Wrap Roll', category: 'Packaging', unit: 'rolls', reorder: 50, cost: 420.00, stock: { 1: 0, 2: 0, 3: 45, 4: 0 } },
    { id: 'MAT-204', name: 'Fleece Lining (Grey)', category: 'Raw Material', unit: 'rolls', reorder: 60, cost: 980.00, stock: { 1: 0, 2: 0, 3: 275, 4: 0 } },
    { id: 'MAT-205', name: 'Label Printer Ribbon', category: 'Supplies', unit: 'pcs', reorder: 30, cost: 95.00, stock: { 1: 0, 2: 0, 3: 120, 4: 0 } },
    { id: 'MAT-206', name: 'Foam Padding (5mm)', category: 'Packaging', unit: 'sheets', reorder: 100, cost: 55.00, stock: { 1: 0, 2: 0, 3: 600, 4: 0 } },
    { id: 'MAT-301', name: 'Wool Blend Fabric', category: 'Raw Material', unit: 'rolls', reorder: 50, cost: 3200.00, stock: { 1: 0, 2: 0, 3: 0, 4: 88 } },
    { id: 'MAT-302', name: 'Snap Fasteners', category: 'Accessory', unit: 'pcs', reorder: 800, cost: 4.50, stock: { 1: 0, 2: 0, 3: 0, 4: 3400 } },
    { id: 'MAT-303', name: 'Fusible Interfacing', category: 'Raw Material', unit: 'rolls', reorder: 40, cost: 540.00, stock: { 1: 0, 2: 0, 3: 0, 4: 30 } },
]);

// ─── UI State ─────────────────────────────────────────────────────────────────
const searchQuery = ref('');
const categoryFilter = ref('All');
const statusFilter = ref('All');
const sortField = ref('name');
const sortDir = ref('asc');
const expandedMat = ref(null);

const showDelegateModal = ref(false);
const showAddModal = ref(false);
const delegateSuccess = ref(false);

const delegateForm = ref({ materialId: null, fromWarehouse: null, toWarehouse: null, qty: '' });
const newMaterial = ref({ id: '', name: '', category: '', unit: '', reorder: '', cost: '' });

// ─── Helpers ──────────────────────────────────────────────────────────────────
const totalQty = (mat) => Object.values(mat.stock).reduce((a, b) => a + b, 0);

const matStatus = (mat) => {
    const t = totalQty(mat);
    if (t === 0) return 'Out of Stock';
    if (t <= mat.reorder) return 'Low Stock';
    return 'In Stock';
};

const fmt = (n) => Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

const colorMap = {
    blue: { btn: 'bg-blue-600 text-white shadow-blue-500/30', badge: 'bg-blue-50 text-blue-700 ring-1 ring-blue-200', bar: 'bg-blue-500', dot: 'bg-blue-500' },
    emerald: { btn: 'bg-emerald-600 text-white shadow-emerald-500/30', badge: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200', bar: 'bg-emerald-500', dot: 'bg-emerald-500' },
    amber: { btn: 'bg-amber-500 text-white shadow-amber-500/30', badge: 'bg-amber-50 text-amber-700 ring-1 ring-amber-200', bar: 'bg-amber-500', dot: 'bg-amber-500' },
    violet: { btn: 'bg-violet-600 text-white shadow-violet-500/30', badge: 'bg-violet-50 text-violet-700 ring-1 ring-violet-200', bar: 'bg-violet-500', dot: 'bg-violet-500' },
    rose: { btn: 'bg-rose-600 text-white shadow-rose-500/30', badge: 'bg-rose-50 text-rose-700 ring-1 ring-rose-200', bar: 'bg-rose-500', dot: 'bg-rose-500' },
    cyan: { btn: 'bg-cyan-600 text-white shadow-cyan-500/30', badge: 'bg-cyan-50 text-cyan-700 ring-1 ring-cyan-200', bar: 'bg-cyan-500', dot: 'bg-cyan-500' },
};

const statusStyle = (s) => ({
    'In Stock': 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
    'Low Stock': 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
    'Out of Stock': 'bg-red-50 text-red-600 ring-1 ring-red-200',
}[s] ?? '');

const catColor = {
    'Raw Material': 'bg-blue-50 text-blue-700',
    'Chemical': 'bg-violet-50 text-violet-700',
    'Accessory': 'bg-emerald-50 text-emerald-700',
    'Packaging': 'bg-amber-50 text-amber-700',
    'Supplies': 'bg-cyan-50 text-cyan-700',
};

// ─── Computed ─────────────────────────────────────────────────────────────────
const categories = computed(() => ['All', ...new Set(materials.value.map(m => m.category))]);

const filteredMaterials = computed(() => {
    let items = [...materials.value];
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        items = items.filter(m => m.name.toLowerCase().includes(q) || m.id.toLowerCase().includes(q));
    }
    if (categoryFilter.value !== 'All') items = items.filter(m => m.category === categoryFilter.value);
    if (statusFilter.value !== 'All') items = items.filter(m => matStatus(m) === statusFilter.value);
    items.sort((a, b) => {
        let va, vb;
        if (sortField.value === 'totalQty') { va = totalQty(a); vb = totalQty(b); }
        else if (sortField.value === 'totalValue') { va = totalQty(a) * a.cost; vb = totalQty(b) * b.cost; }
        else { va = a[sortField.value]; vb = b[sortField.value]; }
        if (typeof va === 'string') { va = va.toLowerCase(); vb = vb.toLowerCase(); }
        if (va < vb) return sortDir.value === 'asc' ? -1 : 1;
        if (va > vb) return sortDir.value === 'asc' ? 1 : -1;
        return 0;
    });
    return items;
});

const globalStats = computed(() => {
    const all = materials.value;
    return {
        total: all.length,
        inStock: all.filter(m => matStatus(m) === 'In Stock').length,
        lowStock: all.filter(m => matStatus(m) === 'Low Stock').length,
        outOfStock: all.filter(m => matStatus(m) === 'Out of Stock').length,
        totalValue: all.reduce((acc, m) => acc + totalQty(m) * m.cost, 0),
    };
});

const warehouseBreakdown = (mat) =>
    warehouses.value.map(w => ({ ...w, qty: mat.stock[w.id] || 0 }));

// Delegate modal helpers
const delegateMaterial = computed(() =>
    materials.value.find(m => m.id === delegateForm.value.materialId) ?? null
);
const fromOptions = computed(() =>
    !delegateMaterial.value ? [] :
        warehouses.value.filter(w => (delegateMaterial.value.stock[w.id] || 0) > 0)
);
const toOptions = computed(() =>
    warehouses.value.filter(w => w.id !== delegateForm.value.fromWarehouse)
);
const maxQty = computed(() =>
    delegateMaterial.value && delegateForm.value.fromWarehouse
        ? delegateMaterial.value.stock[delegateForm.value.fromWarehouse] || 0
        : 0
);

// ─── Methods ──────────────────────────────────────────────────────────────────
const setSort = (f) => {
    if (sortField.value === f) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    else { sortField.value = f; sortDir.value = 'asc'; }
};

const openDelegate = (mat) => {
    delegateForm.value = { materialId: mat.id, fromWarehouse: null, toWarehouse: null, qty: '' };
    delegateSuccess.value = false;
    showDelegateModal.value = true;
};

const confirmDelegate = () => {
    const { materialId, fromWarehouse, toWarehouse, qty } = delegateForm.value;
    const amount = Number(qty);
    if (!materialId || !fromWarehouse || !toWarehouse || !amount) return;
    const mat = materials.value.find(m => m.id === materialId);
    if (!mat || (mat.stock[fromWarehouse] || 0) < amount) return;
    mat.stock[fromWarehouse] -= amount;
    mat.stock[toWarehouse] = (mat.stock[toWarehouse] || 0) + amount;
    delegateSuccess.value = true;
    setTimeout(() => { showDelegateModal.value = false; delegateSuccess.value = false; }, 1200);
};

const addMaterial = () => {
    if (!newMaterial.value.name || !newMaterial.value.id) return;
    const stock = {};
    warehouses.value.forEach(w => stock[w.id] = 0);
    materials.value.push({
        id: newMaterial.value.id,
        name: newMaterial.value.name,
        category: newMaterial.value.category || 'Raw Material',
        unit: newMaterial.value.unit || 'pcs',
        reorder: Number(newMaterial.value.reorder) || 0,
        cost: Number(newMaterial.value.cost) || 0,
        stock,
    });
    newMaterial.value = { id: '', name: '', category: '', unit: '', reorder: '', cost: '' };
    showAddModal.value = false;
};

const deleteMaterial = (id) => {
    materials.value = materials.value.filter(m => m.id !== id);
    if (expandedMat.value === id) expandedMat.value = null;
};
</script>

<template>

    <Head title="Master Materials | Monti Textile" />

    <AuthenticatedLayout>

        <!-- ── Page Header ────────────────────────────────────────────────── -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Master Materials</h1>
                <p class="text-slate-500 text-sm mt-0.5">
                    Overview of all raw materials — delegate stock across warehouse locations.
                </p>
            </div>
            <button @click="showAddModal = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-bold rounded-xl hover:opacity-80 transition shadow-sm">
                <Plus class="w-4 h-4" />
                Add Material
            </button>
        </div>

        <!-- ── Global Stats ───────────────────────────────────────────────── -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">

            <!-- Total Materials -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Materials</p>
                    <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-xl">
                        <Layers class="w-4 h-4 text-slate-500" />
                    </div>
                </div>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ globalStats.total }}</p>
                <p class="text-xs text-slate-400 mt-1">unique SKUs</p>
            </div>

            <!-- Total Value -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Value</p>
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                        <TrendingUp class="w-4 h-4 text-blue-500" />
                    </div>
                </div>
                <p class="text-2xl font-black text-slate-900 dark:text-white">
                    ₱{{ globalStats.totalValue >= 1000000
                        ? (globalStats.totalValue / 1000000).toFixed(2) + 'M'
                        : (globalStats.totalValue / 1000).toFixed(1) + 'K' }}
                </p>
                <p class="text-xs text-slate-400 mt-1">across all warehouses</p>
            </div>

            <!-- In Stock -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">In Stock</p>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                        <CheckCircle class="w-4 h-4 text-emerald-500" />
                    </div>
                </div>
                <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ globalStats.inStock }}</p>
                <p class="text-xs text-slate-400 mt-1">materials</p>
            </div>

            <!-- Low Stock -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Low Stock</p>
                    <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                        <AlertTriangle class="w-4 h-4 text-amber-500" />
                    </div>
                </div>
                <p class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ globalStats.lowStock }}</p>
                <p class="text-xs text-slate-400 mt-1">need reorder soon</p>
            </div>

            <!-- Out of Stock -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Out of Stock</p>
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-xl">
                        <TrendingDown class="w-4 h-4 text-red-500" />
                    </div>
                </div>
                <p class="text-3xl font-black text-red-600 dark:text-red-400">{{ globalStats.outOfStock }}</p>
                <p class="text-xs text-slate-400 mt-1">no stock anywhere</p>
            </div>
        </div>

        <!-- ── Warehouse Distribution Strip ───────────────────────────────── -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div v-for="wh in warehouses" :key="wh.id"
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center gap-4">
                <div :class="['p-2.5 rounded-xl', colorMap[wh.color].btn, 'shadow-lg']">
                    <Warehouse class="w-4 h-4" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-black text-slate-800 dark:text-slate-200 truncate">{{ wh.name }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ wh.location }}</p>
                </div>
                <div class="ml-auto text-right flex-shrink-0">
                    <p class="text-lg font-black text-slate-900 dark:text-white">
                        {{materials.filter(m => (m.stock[wh.id] || 0) > 0).length}}
                    </p>
                    <p class="text-[10px] text-slate-400">SKUs</p>
                </div>
            </div>
        </div>

        <!-- ── Main Table Card ────────────────────────────────────────────── -->
        <div
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">

            <!-- Toolbar -->
            <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-800">

                <!-- Search -->
                <div class="relative flex-1 min-w-[180px]">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" />
                    <input v-model="searchQuery" type="text" placeholder="Search materials…"
                        class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                </div>

                <!-- Category -->
                <div class="relative">
                    <select v-model="categoryFilter"
                        class="appearance-none pl-3 pr-8 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium">
                        <option v-for="c in categories" :key="c">{{ c }}</option>
                    </select>
                    <ChevronDown
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                </div>

                <!-- Status -->
                <div class="relative">
                    <select v-model="statusFilter"
                        class="appearance-none pl-3 pr-8 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium">
                        <option>All</option>
                        <option>In Stock</option>
                        <option>Low Stock</option>
                        <option>Out of Stock</option>
                    </select>
                    <ChevronDown
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                </div>

                <p class="text-xs text-slate-400 ml-auto font-medium hidden sm:block">
                    <span class="font-bold text-slate-600 dark:text-slate-300">{{ filteredMaterials.length }}</span>
                    / {{ materials.length }} materials
                </p>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5 w-8"></th>
                            <th v-for="col in [
                                { label: 'MAT ID', field: 'id' },
                                { label: 'Material Name', field: 'name' },
                                { label: 'Category', field: 'category' },
                                { label: 'Total Qty', field: 'totalQty' },
                                { label: 'Unit Cost (₱)', field: 'cost' },
                                { label: 'Total Value', field: 'totalValue' },
                                { label: 'Warehouses', field: null },
                                { label: 'Status', field: 'status' },
                                { label: '', field: null },
                            ]" :key="col.label"
                                class="px-5 py-3.5 text-[10px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">
                                <button v-if="col.field" @click="setSort(col.field)"
                                    class="flex items-center gap-1 hover:text-slate-800 dark:hover:text-white transition">
                                    {{ col.label }}
                                    <ArrowUpDown class="w-3 h-3" />
                                </button>
                                <span v-else>{{ col.label }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                        <!-- Empty state -->
                        <tr v-if="filteredMaterials.length === 0">
                            <td colspan="10" class="px-5 py-20 text-center text-slate-400 text-sm font-medium">
                                <Package class="w-10 h-10 mx-auto mb-3 opacity-30" />
                                No materials found.
                            </td>
                        </tr>

                        <template v-for="mat in filteredMaterials" :key="mat.id">
                            <!-- Main Row -->
                            <tr :class="[
                                'hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors group',
                                expandedMat === mat.id ? 'bg-slate-50/60 dark:bg-slate-800/40' : ''
                            ]">
                                <!-- Expand toggle -->
                                <td class="pl-5 py-4">
                                    <button @click="expandedMat = expandedMat === mat.id ? null : mat.id"
                                        class="p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition text-slate-400">
                                        <ChevronDown
                                            :class="['w-3.5 h-3.5 transition-transform', expandedMat === mat.id ? 'rotate-180' : '']" />
                                    </button>
                                </td>

                                <!-- MAT ID -->
                                <td class="px-5 py-4">
                                    <span
                                        class="font-mono text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-md">
                                        {{ mat.id }}
                                    </span>
                                </td>

                                <!-- Name -->
                                <td
                                    class="px-5 py-4 font-semibold text-slate-800 dark:text-slate-200 max-w-[220px] truncate">
                                    {{ mat.name }}
                                </td>

                                <!-- Category -->
                                <td class="px-5 py-4">
                                    <span
                                        :class="['text-[10px] font-bold px-2.5 py-1 rounded-full', catColor[mat.category] ?? 'bg-slate-100 text-slate-500']">
                                        {{ mat.category }}
                                    </span>
                                </td>

                                <!-- Total Qty -->
                                <td class="px-5 py-4">
                                    <span
                                        :class="['font-black text-base', totalQty(mat) === 0 ? 'text-red-500' : totalQty(mat) <= mat.reorder ? 'text-amber-600' : 'text-slate-900 dark:text-white']">
                                        {{ totalQty(mat).toLocaleString() }}
                                    </span>
                                    <span class="text-slate-400 text-xs ml-1">{{ mat.unit }}</span>
                                </td>

                                <!-- Unit Cost -->
                                <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                    ₱{{ fmt(mat.cost) }}
                                </td>

                                <!-- Total Value -->
                                <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                    ₱{{ fmt(totalQty(mat) * mat.cost) }}
                                </td>

                                <!-- Warehouse dots -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span v-for="wh in warehouses" :key="wh.id"
                                            :title="wh.name + ': ' + (mat.stock[wh.id] || 0) + ' ' + mat.unit" :class="[
                                                'w-5 h-5 rounded-full flex items-center justify-center text-[8px] font-black transition-all',
                                                (mat.stock[wh.id] || 0) > 0
                                                    ? colorMap[wh.color].btn + ' shadow-sm'
                                                    : 'bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600'
                                            ]">
                                            {{ wh.name.charAt(0) }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-5 py-4">
                                    <span
                                        :class="['text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full', statusStyle(matStatus(mat))]">
                                        {{ matStatus(mat) }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="openDelegate(mat)" title="Delegate to warehouse"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-black rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition shadow-sm shadow-blue-500/20 whitespace-nowrap">
                                            <ArrowRightLeft class="w-3 h-3" />
                                            Delegate
                                        </button>
                                        <button
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                            <Edit2 class="w-3.5 h-3.5" />
                                        </button>
                                        <button @click="deleteMaterial(mat.id)"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                            <Trash2 class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Expanded Warehouse Breakdown Row -->
                            <tr v-if="expandedMat === mat.id">
                                <td colspan="10" class="px-5 pb-5 pt-1 bg-slate-50/50 dark:bg-slate-800/20">
                                    <div class="ml-8">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                                            Warehouse Distribution
                                        </p>
                                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                            <div v-for="wh in warehouseBreakdown(mat)" :key="wh.id" :class="[
                                                'rounded-xl border p-3.5 transition-all',
                                                wh.qty > 0
                                                    ? 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700'
                                                    : 'bg-slate-50 dark:bg-slate-800/50 border-slate-100 dark:border-slate-800 opacity-60'
                                            ]">
                                                <div class="flex items-center gap-2 mb-2.5">
                                                    <span
                                                        :class="['w-2 h-2 rounded-full flex-shrink-0', colorMap[wh.color].dot]" />
                                                    <p
                                                        class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">
                                                        {{ wh.name }}</p>
                                                </div>
                                                <p
                                                    :class="['text-2xl font-black', wh.qty > 0 ? 'text-slate-900 dark:text-white' : 'text-slate-300 dark:text-slate-600']">
                                                    {{ wh.qty.toLocaleString() }}
                                                </p>
                                                <p class="text-[10px] text-slate-400 mt-0.5">{{ mat.unit }}</p>
                                                <!-- Mini bar -->
                                                <div
                                                    class="mt-3 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                    <div :class="['h-full rounded-full transition-all', colorMap[wh.color].bar]"
                                                        :style="{ width: totalQty(mat) > 0 ? (wh.qty / totalQty(mat) * 100) + '%' : '0%' }" />
                                                </div>
                                                <p class="text-[10px] text-slate-400 mt-1">
                                                    {{ totalQty(mat) > 0 ? Math.round(wh.qty / totalQty(mat) * 100) : 0
                                                    }}% of total
                                                </p>
                                            </div>
                                        </div>
                                        <!-- Reorder note -->
                                        <div v-if="matStatus(mat) !== 'In Stock'"
                                            class="mt-3 flex items-center gap-2 px-3 py-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl w-fit">
                                            <AlertTriangle class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" />
                                            <p class="text-xs font-bold text-amber-700 dark:text-amber-400">
                                                Reorder point: {{ mat.reorder.toLocaleString() }} {{ mat.unit }} —
                                                total stock is
                                                <span :class="matStatus(mat) === 'Out of Stock' ? 'text-red-600' : ''">
                                                    {{ matStatus(mat) === 'Out of Stock' ? 'depleted' : 'belowthreshold'
                                                    }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>

                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <p class="text-xs text-slate-400 font-medium">
                    Showing
                    <span class="font-bold text-slate-600 dark:text-slate-300">{{ filteredMaterials.length }}</span>
                    of
                    <span class="font-bold text-slate-600 dark:text-slate-300">{{ materials.length }}</span>
                    materials
                </p>
                <div class="flex items-center gap-3">
                    <span v-if="globalStats.lowStock > 0"
                        class="flex items-center gap-1.5 text-[10px] font-black text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full ring-1 ring-amber-200">
                        <AlertTriangle class="w-3 h-3" />
                        {{ globalStats.lowStock }} low stock
                    </span>
                    <span v-if="globalStats.outOfStock > 0"
                        class="flex items-center gap-1.5 text-[10px] font-black text-red-600 bg-red-50 px-2.5 py-1 rounded-full ring-1 ring-red-200">
                        {{ globalStats.outOfStock }} out of stock
                    </span>
                </div>
            </div>
        </div>


        <!-- ══════════════════════════════════════════════════════════════════ -->
        <!--  DELEGATE MODAL                                                   -->
        <!-- ══════════════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showDelegateModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                @click.self="showDelegateModal = false">
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-lg p-6">

                    <!-- Success State -->
                    <div v-if="delegateSuccess" class="flex flex-col items-center py-8 gap-4">
                        <div
                            class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                            <PackageCheck class="w-8 h-8 text-emerald-500" />
                        </div>
                        <p class="text-lg font-black text-slate-900 dark:text-white">Stock Delegated!</p>
                        <p class="text-sm text-slate-400">Warehouse inventory has been updated.</p>
                    </div>

                    <!-- Form State -->
                    <template v-else>
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">Delegate Material</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Transfer stock between warehouse locations.</p>
                            </div>
                            <button @click="showDelegateModal = false"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <X class="w-4 h-4" />
                            </button>
                        </div>

                        <!-- Selected Material Info -->
                        <div v-if="delegateMaterial"
                            class="mb-5 px-4 py-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center gap-3">
                            <div
                                class="p-2 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700">
                                <Package class="w-4 h-4 text-slate-500" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{
                                    delegateMaterial.name }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-mono text-[10px] text-slate-400">{{ delegateMaterial.id }}</span>
                                    <span class="text-[10px] text-slate-400">·</span>
                                    <span class="text-[10px] font-bold text-slate-500">
                                        {{ totalQty(delegateMaterial).toLocaleString() }} {{ delegateMaterial.unit }}
                                        total
                                    </span>
                                </div>
                            </div>
                            <span
                                :class="['text-[10px] font-black px-2 py-1 rounded-full', statusStyle(matStatus(delegateMaterial))]">
                                {{ matStatus(delegateMaterial) }}
                            </span>
                        </div>

                        <div class="space-y-4">

                            <!-- From Warehouse -->
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    From Warehouse *
                                </label>
                                <div class="relative mt-1">
                                    <select v-model="delegateForm.fromWarehouse"
                                        @change="delegateForm.toWarehouse = null; delegateForm.qty = ''"
                                        class="w-full appearance-none pl-3 pr-8 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium">
                                        <option :value="null">Select source warehouse…</option>
                                        <option v-for="wh in fromOptions" :key="wh.id" :value="wh.id">
                                            {{ wh.name }} ({{ (delegateMaterial?.stock[wh.id] || 0).toLocaleString() }}
                                            {{ delegateMaterial?.unit }})
                                        </option>
                                    </select>
                                    <ChevronDown
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                </div>
                                <p v-if="fromOptions.length === 0" class="text-[10px] text-red-500 mt-1 font-bold">
                                    No warehouse currently holds this material.
                                </p>
                            </div>

                            <!-- Arrow -->
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-px bg-slate-100 dark:bg-slate-800" />
                                <div
                                    class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-full border border-blue-200 dark:border-blue-800">
                                    <ArrowRightLeft class="w-3.5 h-3.5 text-blue-500" />
                                </div>
                                <div class="flex-1 h-px bg-slate-100 dark:bg-slate-800" />
                            </div>

                            <!-- To Warehouse -->
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    To Warehouse *
                                </label>
                                <div class="relative mt-1">
                                    <select v-model="delegateForm.toWarehouse" :disabled="!delegateForm.fromWarehouse"
                                        class="w-full appearance-none pl-3 pr-8 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option :value="null">Select destination warehouse…</option>
                                        <option v-for="wh in toOptions" :key="wh.id" :value="wh.id">
                                            {{ wh.name }}
                                            <template v-if="(delegateMaterial?.stock[wh.id] || 0) > 0">
                                                ({{ (delegateMaterial?.stock[wh.id] || 0).toLocaleString() }} {{
                                                    delegateMaterial?.unit }} existing)
                                            </template>
                                        </option>
                                    </select>
                                    <ChevronDown
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Quantity to Transfer *
                                </label>
                                <div class="mt-1 flex items-center gap-2">
                                    <input v-model="delegateForm.qty" type="number" min="1" :max="maxQty"
                                        :disabled="!delegateForm.fromWarehouse"
                                        :placeholder="maxQty > 0 ? 'Max ' + maxQty.toLocaleString() : '0'"
                                        class="flex-1 px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 disabled:opacity-50" />
                                    <button v-if="maxQty > 0" @click="delegateForm.qty = maxQty"
                                        class="px-3 py-2.5 text-xs font-black text-blue-600 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl hover:bg-blue-100 transition">
                                        All
                                    </button>
                                </div>
                                <p v-if="delegateForm.fromWarehouse && maxQty > 0"
                                    class="text-[10px] text-slate-400 mt-1">
                                    Available: <span class="font-bold text-slate-600 dark:text-slate-300">{{
                                        maxQty.toLocaleString() }} {{
                                            delegateMaterial?.unit }}</span>
                                </p>
                                <p v-if="Number(delegateForm.qty) > maxQty && maxQty > 0"
                                    class="text-[10px] text-red-500 font-bold mt-1">
                                    Exceeds available stock.
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex gap-3">
                            <button @click="showDelegateModal = false"
                                class="flex-1 py-2.5 text-sm font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                Cancel
                            </button>
                            <button @click="confirmDelegate"
                                :disabled="!delegateForm.fromWarehouse || !delegateForm.toWarehouse || !delegateForm.qty || Number(delegateForm.qty) > maxQty || Number(delegateForm.qty) <= 0"
                                class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 text-sm font-bold rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 disabled:opacity-40 disabled:cursor-not-allowed">
                                <Send class="w-4 h-4" />
                                Confirm Delegation
                            </button>
                        </div>
                    </template>

                </div>
            </div>
        </Teleport>


        <!-- ══════════════════════════════════════════════════════════════════ -->
        <!--  ADD MATERIAL MODAL                                               -->
        <!-- ══════════════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showAddModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                @click.self="showAddModal = false">
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">Add Material</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Creates the material in the master list with zero
                                stock.
                            </p>
                        </div>
                        <button @click="showAddModal = false"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">MAT ID
                                *</label>
                            <input v-model="newMaterial.id" type="text" placeholder="e.g. MAT-999"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-mono" />
                        </div>
                        <div>
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</label>
                            <div class="relative mt-1">
                                <select v-model="newMaterial.category"
                                    class="w-full appearance-none pl-3 pr-8 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium">
                                    <option value="">Select…</option>
                                    <option>Raw Material</option>
                                    <option>Chemical</option>
                                    <option>Accessory</option>
                                    <option>Packaging</option>
                                    <option>Supplies</option>
                                </select>
                                <ChevronDown
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Material Name
                                *</label>
                            <input v-model="newMaterial.name" type="text" placeholder="Full material name"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Unit</label>
                            <input v-model="newMaterial.unit" type="text" placeholder="rolls, kg, pcs…"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reorder
                                Point</label>
                            <input v-model="newMaterial.reorder" type="number" placeholder="0"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Unit Cost
                                (₱)</label>
                            <input v-model="newMaterial.cost" type="number" placeholder="0.00"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button @click="showAddModal = false"
                            class="flex-1 py-2.5 text-sm font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Cancel
                        </button>
                        <button @click="addMaterial" :disabled="!newMaterial.name || !newMaterial.id"
                            class="flex-1 py-2.5 text-sm font-bold rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:opacity-80 transition shadow-sm disabled:opacity-40 disabled:cursor-not-allowed">
                            Add to Master List
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

    </AuthenticatedLayout>
</template>