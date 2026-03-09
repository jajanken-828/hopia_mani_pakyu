<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Warehouse,
    Plus,
    Package,
    Search,
    Filter,
    ChevronDown,
    TrendingUp,
    TrendingDown,
    AlertTriangle,
    CheckCircle,
    X,
    Edit2,
    Trash2,
    BarChart2,
    ArrowUpDown,
} from 'lucide-vue-next';

// ─── Dummy Data ───────────────────────────────────────────────────────────────
const warehouses = ref([
    {
        id: 1,
        name: 'Main Warehouse',
        location: 'Cavite, PH',
        manager: 'Juan Dela Cruz',
        capacity: 5000,
        used: 3812,
        color: 'blue',
    },
    {
        id: 2,
        name: 'North Storage Facility',
        location: 'Bulacan, PH',
        manager: 'Maria Santos',
        capacity: 3000,
        used: 1240,
        color: 'emerald',
    },
    {
        id: 3,
        name: 'South Distribution Hub',
        location: 'Laguna, PH',
        manager: 'Pedro Reyes',
        capacity: 4500,
        used: 4100,
        color: 'amber',
    },
    {
        id: 4,
        name: 'East Textile Depot',
        location: 'Rizal, PH',
        manager: 'Ana Lim',
        capacity: 2000,
        used: 560,
        color: 'violet',
    },
]);

const inventoryData = ref({
    1: [
        { id: 'SKU-001', name: 'Cotton Fabric Roll (White)', category: 'Raw Material', qty: 480, unit: 'rolls', reorder: 100, cost: 850.00, status: 'In Stock' },
        { id: 'SKU-002', name: 'Polyester Blend (Navy)', category: 'Raw Material', qty: 95, unit: 'rolls', reorder: 100, cost: 920.00, status: 'Low Stock' },
        { id: 'SKU-003', name: 'Dye — Reactive Red', category: 'Chemical', qty: 320, unit: 'kg', reorder: 50, cost: 215.00, status: 'In Stock' },
        { id: 'SKU-004', name: 'Elastic Band (1in)', category: 'Accessory', qty: 12000, unit: 'meters', reorder: 2000, cost: 12.50, status: 'In Stock' },
        { id: 'SKU-005', name: 'Zipper (20cm Black)', category: 'Accessory', qty: 0, unit: 'pcs', reorder: 500, cost: 8.75, status: 'Out of Stock' },
        { id: 'SKU-006', name: 'Linen Fabric (Beige)', category: 'Raw Material', qty: 210, unit: 'rolls', reorder: 80, cost: 1100.00, status: 'In Stock' },
        { id: 'SKU-007', name: 'Thread Spool (Black)', category: 'Accessory', qty: 840, unit: 'spools', reorder: 200, cost: 45.00, status: 'In Stock' },
    ],
    2: [
        { id: 'SKU-101', name: 'Silk Fabric (Ivory)', category: 'Raw Material', qty: 60, unit: 'rolls', reorder: 80, cost: 2400.00, status: 'Low Stock' },
        { id: 'SKU-102', name: 'Dye — Indigo Blue', category: 'Chemical', qty: 180, unit: 'kg', reorder: 50, cost: 310.00, status: 'In Stock' },
        { id: 'SKU-103', name: 'Button Set (Brass)', category: 'Accessory', qty: 5500, unit: 'pcs', reorder: 1000, cost: 3.20, status: 'In Stock' },
        { id: 'SKU-104', name: 'Velcro Strip (White)', category: 'Accessory', qty: 2200, unit: 'meters', reorder: 500, cost: 18.00, status: 'In Stock' },
        { id: 'SKU-105', name: 'Nylon Mesh', category: 'Raw Material', qty: 0, unit: 'rolls', reorder: 40, cost: 760.00, status: 'Out of Stock' },
    ],
    3: [
        { id: 'SKU-201', name: 'Denim Fabric (Dark)', category: 'Raw Material', qty: 390, unit: 'rolls', reorder: 100, cost: 1350.00, status: 'In Stock' },
        { id: 'SKU-202', name: 'Packaging Box (L)', category: 'Packaging', qty: 1800, unit: 'pcs', reorder: 500, cost: 25.00, status: 'In Stock' },
        { id: 'SKU-203', name: 'Shrink Wrap Roll', category: 'Packaging', qty: 45, unit: 'rolls', reorder: 50, cost: 420.00, status: 'Low Stock' },
        { id: 'SKU-204', name: 'Fleece Lining (Grey)', category: 'Raw Material', qty: 275, unit: 'rolls', reorder: 60, cost: 980.00, status: 'In Stock' },
        { id: 'SKU-205', name: 'Label Printer Ribbon', category: 'Supplies', qty: 120, unit: 'pcs', reorder: 30, cost: 95.00, status: 'In Stock' },
        { id: 'SKU-206', name: 'Foam Padding (5mm)', category: 'Packaging', qty: 600, unit: 'sheets', reorder: 100, cost: 55.00, status: 'In Stock' },
    ],
    4: [
        { id: 'SKU-301', name: 'Wool Blend Fabric', category: 'Raw Material', qty: 88, unit: 'rolls', reorder: 50, cost: 3200.00, status: 'In Stock' },
        { id: 'SKU-302', name: 'Snap Fasteners', category: 'Accessory', qty: 3400, unit: 'pcs', reorder: 800, cost: 4.50, status: 'In Stock' },
        { id: 'SKU-303', name: 'Fusible Interfacing', category: 'Raw Material', qty: 30, unit: 'rolls', reorder: 40, cost: 540.00, status: 'Low Stock' },
    ],
});

// ─── State ────────────────────────────────────────────────────────────────────
const activeWarehouseId = ref(1);
const searchQuery = ref('');
const categoryFilter = ref('All');
const statusFilter = ref('All');
const showAddWarehouse = ref(false);
const showAddItem = ref(false);
const sortField = ref('name');
const sortDir = ref('asc');

// Add Warehouse Form
const newWarehouse = ref({ name: '', location: '', manager: '', capacity: '' });
const warehouseColors = ['blue', 'emerald', 'amber', 'violet', 'rose', 'cyan'];

// Add Item Form
const newItem = ref({ id: '', name: '', category: '', qty: '', unit: '', reorder: '', cost: '' });

// ─── Computed ─────────────────────────────────────────────────────────────────
const activeWarehouse = computed(() => warehouses.value.find(w => w.id === activeWarehouseId.value));

const activeInventory = computed(() => inventoryData.value[activeWarehouseId.value] || []);

const categories = computed(() => {
    const cats = [...new Set(activeInventory.value.map(i => i.category))];
    return ['All', ...cats];
});

const filteredInventory = computed(() => {
    let items = [...activeInventory.value];
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        items = items.filter(i => i.name.toLowerCase().includes(q) || i.id.toLowerCase().includes(q));
    }
    if (categoryFilter.value !== 'All') items = items.filter(i => i.category === categoryFilter.value);
    if (statusFilter.value !== 'All') items = items.filter(i => i.status === statusFilter.value);
    items.sort((a, b) => {
        let va = a[sortField.value], vb = b[sortField.value];
        if (typeof va === 'string') va = va.toLowerCase(), vb = vb.toLowerCase();
        if (va < vb) return sortDir.value === 'asc' ? -1 : 1;
        if (va > vb) return sortDir.value === 'asc' ? 1 : -1;
        return 0;
    });
    return items;
});

const stats = computed(() => {
    const inv = activeInventory.value;
    return {
        total: inv.length,
        inStock: inv.filter(i => i.status === 'In Stock').length,
        lowStock: inv.filter(i => i.status === 'Low Stock').length,
        outOfStock: inv.filter(i => i.status === 'Out of Stock').length,
    };
});

const capacityPct = computed(() => {
    if (!activeWarehouse.value) return 0;
    return Math.round((activeWarehouse.value.used / activeWarehouse.value.capacity) * 100);
});

// ─── Methods ──────────────────────────────────────────────────────────────────
const setSort = (field) => {
    if (sortField.value === field) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    else { sortField.value = field; sortDir.value = 'asc'; }
};

const addWarehouse = () => {
    if (!newWarehouse.value.name || !newWarehouse.value.location) return;
    const id = Date.now();
    const color = warehouseColors[warehouses.value.length % warehouseColors.length];
    warehouses.value.push({ id, ...newWarehouse.value, capacity: Number(newWarehouse.value.capacity) || 1000, used: 0, color });
    inventoryData.value[id] = [];
    newWarehouse.value = { name: '', location: '', manager: '', capacity: '' };
    showAddWarehouse.value = false;
};

const addItem = () => {
    if (!newItem.value.name || !newItem.value.id) return;
    inventoryData.value[activeWarehouseId.value].push({
        ...newItem.value,
        qty: Number(newItem.value.qty) || 0,
        reorder: Number(newItem.value.reorder) || 0,
        cost: Number(newItem.value.cost) || 0,
        status: Number(newItem.value.qty) === 0 ? 'Out of Stock' : Number(newItem.value.qty) <= Number(newItem.value.reorder) ? 'Low Stock' : 'In Stock',
    });
    newItem.value = { id: '', name: '', category: '', qty: '', unit: '', reorder: '', cost: '' };
    showAddItem.value = false;
};

const deleteItem = (sku) => {
    inventoryData.value[activeWarehouseId.value] = inventoryData.value[activeWarehouseId.value].filter(i => i.id !== sku);
};

// ─── Helpers ──────────────────────────────────────────────────────────────────
const colorMap = {
    blue: { btn: 'bg-blue-600 text-white shadow-blue-500/30', badge: 'bg-blue-50 text-blue-700 ring-1 ring-blue-200', bar: 'bg-blue-500', dot: 'bg-blue-500', ring: 'ring-blue-500' },
    emerald: { btn: 'bg-emerald-600 text-white shadow-emerald-500/30', badge: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200', bar: 'bg-emerald-500', dot: 'bg-emerald-500', ring: 'ring-emerald-500' },
    amber: { btn: 'bg-amber-500 text-white shadow-amber-500/30', badge: 'bg-amber-50 text-amber-700 ring-1 ring-amber-200', bar: 'bg-amber-500', dot: 'bg-amber-500', ring: 'ring-amber-500' },
    violet: { btn: 'bg-violet-600 text-white shadow-violet-500/30', badge: 'bg-violet-50 text-violet-700 ring-1 ring-violet-200', bar: 'bg-violet-500', dot: 'bg-violet-500', ring: 'ring-violet-500' },
    rose: { btn: 'bg-rose-600 text-white shadow-rose-500/30', badge: 'bg-rose-50 text-rose-700 ring-1 ring-rose-200', bar: 'bg-rose-500', dot: 'bg-rose-500', ring: 'ring-rose-500' },
    cyan: { btn: 'bg-cyan-600 text-white shadow-cyan-500/30', badge: 'bg-cyan-50 text-cyan-700 ring-1 ring-cyan-200', bar: 'bg-cyan-500', dot: 'bg-cyan-500', ring: 'ring-cyan-500' },
};

const statusStyle = (status) => ({
    'In Stock': 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
    'Low Stock': 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
    'Out of Stock': 'bg-red-50 text-red-600 ring-1 ring-red-200',
}[status] ?? '');

const fmt = (n) => Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
</script>

<template>

    <Head title="Inventory Management | Monti Textile" />

    <AuthenticatedLayout>

        <!-- ── Page Header ──────────────────────────────────────────────── -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Inventory Management</h1>
                <p class="text-slate-500 text-sm mt-0.5">Monitor stock levels across all warehouse locations.</p>
            </div>
            <button @click="showAddWarehouse = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-bold rounded-xl hover:opacity-80 transition shadow-sm">
                <Plus class="w-4 h-4" />
                Add Warehouse
            </button>
        </div>

        <!-- ── Warehouse Toggle Tabs ─────────────────────────────────────── -->
        <div class="flex flex-wrap gap-3 mb-8">
            <button v-for="wh in warehouses" :key="wh.id"
                @click="activeWarehouseId = wh.id; categoryFilter = 'All'; statusFilter = 'All'; searchQuery = ''"
                :class="[
                    'relative flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-200 border',
                    activeWarehouseId === wh.id
                        ? `${colorMap[wh.color].btn} shadow-lg border-transparent`
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'
                ]">
                <span
                    :class="['w-2 h-2 rounded-full flex-shrink-0', activeWarehouseId === wh.id ? 'bg-white/60' : colorMap[wh.color].dot]" />
                {{ wh.name }}
                <span :class="[
                    'text-[10px] font-black px-2 py-0.5 rounded-full',
                    activeWarehouseId === wh.id ? 'bg-white/20 text-white' : colorMap[wh.color].badge
                ]">
                    {{ (inventoryData[wh.id] || []).length }} SKUs
                </span>
            </button>
        </div>

        <!-- ── Warehouse Info + Stats ─────────────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">

            <!-- Warehouse Card -->
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex flex-col gap-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active Warehouse</p>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white mt-0.5">{{ activeWarehouse?.name }}
                        </h3>
                        <p class="text-sm text-slate-500 mt-0.5">📍 {{ activeWarehouse?.location }}</p>
                    </div>
                    <div
                        :class="['w-10 h-10 rounded-xl flex items-center justify-center', colorMap[activeWarehouse?.color || 'blue'].btn]">
                        <Warehouse class="w-5 h-5" />
                    </div>
                </div>
                <div class="text-xs text-slate-500">Manager: <span
                        class="font-bold text-slate-700 dark:text-slate-300">{{ activeWarehouse?.manager }}</span></div>
                <!-- Capacity Bar -->
                <div>
                    <div class="flex justify-between text-xs font-bold text-slate-500 mb-1.5">
                        <span>Capacity Used</span>
                        <span
                            :class="capacityPct >= 90 ? 'text-red-500' : capacityPct >= 70 ? 'text-amber-500' : 'text-emerald-600'">
                            {{ capacityPct }}%
                        </span>
                    </div>
                    <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div :class="['h-full rounded-full transition-all duration-500', capacityPct >= 90 ? 'bg-red-500' : capacityPct >= 70 ? 'bg-amber-500' : colorMap[activeWarehouse?.color || 'blue'].bar]"
                            :style="`width: ${capacityPct}%`" />
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">{{ activeWarehouse?.used.toLocaleString() }} / {{
                        activeWarehouse?.capacity.toLocaleString() }} units</p>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div v-for="(val, key) in [
                    { label: 'Total SKUs', value: stats.total, icon: Package, color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-900/20' },
                    { label: 'In Stock', value: stats.inStock, icon: CheckCircle, color: 'text-emerald-600', bg: 'bg-emerald-50 dark:bg-emerald-900/20' },
                    { label: 'Low Stock', value: stats.lowStock, icon: TrendingDown, color: 'text-amber-600', bg: 'bg-amber-50 dark:bg-amber-900/20' },
                    { label: 'Out of Stock', value: stats.outOfStock, icon: AlertTriangle, color: 'text-red-500', bg: 'bg-red-50 dark:bg-red-900/20' },
                ]" :key="val.label"
                    class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex flex-col gap-3">
                    <div :class="['w-9 h-9 rounded-xl flex items-center justify-center', val.bg]">
                        <component :is="val.icon" :class="['w-4.5 h-4.5', val.color]" />
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-900 dark:text-white">{{ val.value }}</p>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ val.label }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Inventory Table ────────────────────────────────────────────── -->
        <div
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">

            <!-- Table Toolbar -->
            <div
                class="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
                <div class="flex flex-wrap gap-3 items-center flex-1">
                    <!-- Search -->
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" />
                        <input v-model="searchQuery" type="text" placeholder="Search SKU or name..."
                            class="pl-9 pr-4 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 w-56 text-slate-700 dark:text-slate-200 placeholder-slate-400" />
                    </div>

                    <!-- Category Filter -->
                    <div class="relative">
                        <select v-model="categoryFilter"
                            class="appearance-none pl-3 pr-8 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium">
                            <option v-for="cat in categories" :key="cat">{{ cat }}</option>
                        </select>
                        <ChevronDown
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                    </div>

                    <!-- Status Filter -->
                    <div class="relative">
                        <select v-model="statusFilter"
                            class="appearance-none pl-3 pr-8 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium">
                            <option>All</option>
                            <option>In Stock</option>
                            <option>Low Stock</option>
                            <option>Out of Stock</option>
                        </select>
                        <ChevronDown
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                    </div>
                </div>

                <button @click="showAddItem = true"
                    :class="['inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-xl shadow-lg transition flex-shrink-0', colorMap[activeWarehouse?.color || 'blue'].btn]">
                    <Plus class="w-4 h-4" /> Add Item
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th v-for="col in [
                                { label: 'SKU', field: 'id' },
                                { label: 'Item Name', field: 'name' },
                                { label: 'Category', field: 'category' },
                                { label: 'Qty', field: 'qty' },
                                { label: 'Reorder Pt.', field: 'reorder' },
                                { label: 'Unit Cost (₱)', field: 'cost' },
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
                        <tr v-if="filteredInventory.length === 0">
                            <td colspan="8" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">
                                No inventory items found.
                            </td>
                        </tr>
                        <tr v-for="item in filteredInventory" :key="item.id"
                            class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="px-5 py-4">
                                <span
                                    class="font-mono text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-md">{{
                                    item.id }}</span>
                            </td>
                            <td
                                class="px-5 py-4 font-semibold text-slate-800 dark:text-slate-200 max-w-[200px] truncate">
                                {{ item.name }}
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-full">{{
                                    item.category }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    :class="['font-black text-base', item.qty === 0 ? 'text-red-500' : item.qty <= item.reorder ? 'text-amber-600' : 'text-slate-900 dark:text-white']">
                                    {{ item.qty.toLocaleString() }}
                                </span>
                                <span class="text-slate-400 text-xs ml-1">{{ item.unit }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-sm">
                                {{ item.reorder.toLocaleString() }} {{ item.unit }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                ₱{{ fmt(item.cost) }}
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    :class="['text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full', statusStyle(item.status)]">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div
                                    class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                        <Edit2 class="w-3.5 h-3.5" />
                                    </button>
                                    <button @click="deleteItem(item.id)"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <Trash2 class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <p class="text-xs text-slate-400 font-medium">
                    Showing <span class="font-bold text-slate-600 dark:text-slate-300">{{ filteredInventory.length
                        }}</span> of
                    <span class="font-bold text-slate-600 dark:text-slate-300">{{ activeInventory.length }}</span> items
                </p>
                <div class="flex items-center gap-1.5">
                    <span :class="['w-2 h-2 rounded-full', colorMap[activeWarehouse?.color || 'blue'].dot]" />
                    <span class="text-xs font-bold text-slate-400">{{ activeWarehouse?.name }}</span>
                </div>
            </div>
        </div>

        <!-- ── Add Warehouse Modal ────────────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="showAddWarehouse"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                @click.self="showAddWarehouse = false">
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">New Warehouse</h3>
                        <button @click="showAddWarehouse = false"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Warehouse
                                Name *</label>
                            <input v-model="newWarehouse.name" type="text" placeholder="e.g. West Storage Unit"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Location
                                *</label>
                            <input v-model="newWarehouse.location" type="text" placeholder="e.g. Pampanga, PH"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div>
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Manager</label>
                            <input v-model="newWarehouse.manager" type="text" placeholder="Full Name"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total
                                Capacity (units)</label>
                            <input v-model="newWarehouse.capacity" type="number" placeholder="e.g. 3000"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button @click="showAddWarehouse = false"
                            class="flex-1 py-2.5 text-sm font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Cancel
                        </button>
                        <button @click="addWarehouse"
                            class="flex-1 py-2.5 text-sm font-bold rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:opacity-80 transition shadow-sm">
                            Add Warehouse
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- ── Add Item Modal ─────────────────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="showAddItem"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                @click.self="showAddItem = false">
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">Add Inventory Item</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Adding to: <span
                                    class="font-bold text-slate-600 dark:text-slate-300">{{ activeWarehouse?.name
                                    }}</span></p>
                        </div>
                        <button @click="showAddItem = false"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">SKU *</label>
                            <input v-model="newItem.id" type="text" placeholder="e.g. SKU-999"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-mono" />
                        </div>
                        <div>
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</label>
                            <input v-model="newItem.category" type="text" placeholder="e.g. Raw Material"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Item Name
                                *</label>
                            <input v-model="newItem.name" type="text" placeholder="Full item name"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div>
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Quantity</label>
                            <input v-model="newItem.qty" type="number" placeholder="0"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Unit</label>
                            <input v-model="newItem.unit" type="text" placeholder="e.g. rolls, kg, pcs"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reorder
                                Point</label>
                            <input v-model="newItem.reorder" type="number" placeholder="0"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Unit Cost
                                (₱)</label>
                            <input v-model="newItem.cost" type="number" placeholder="0.00"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button @click="showAddItem = false"
                            class="flex-1 py-2.5 text-sm font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Cancel
                        </button>
                        <button @click="addItem"
                            :class="['flex-1 py-2.5 text-sm font-bold rounded-xl shadow-lg transition', colorMap[activeWarehouse?.color || 'blue'].btn]">
                            Add to Inventory
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

    </AuthenticatedLayout>
</template>