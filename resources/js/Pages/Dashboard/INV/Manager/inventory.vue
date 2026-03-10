<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Warehouse, Plus, Package, Search, Filter, ChevronDown,
    TrendingUp, TrendingDown, AlertTriangle, CheckCircle,
    X, Edit2, Trash2, BarChart2, ArrowUpDown,
} from 'lucide-vue-next';

// ─── Props from Inertia ───────────────────────────────────────────────────────
const props = defineProps({
    warehouses: { type: Array, default: () => [] },
    inventoryData: { type: Object, default: () => ({}) },
    masterMaterials: { type: Array, default: () => [] },
});

// ─── Reactive copies (so local state + server refreshes both work) ────────────
const warehouses = ref(props.warehouses);
const inventoryData = ref(props.inventoryData);

// Keep in sync with Inertia page updates
import { watch } from 'vue';
watch(() => props.warehouses, v => (warehouses.value = v), { deep: true });
watch(() => props.inventoryData, v => (inventoryData.value = v), { deep: true });

// ─── State ────────────────────────────────────────────────────────────────────
const activeWarehouseId = ref(warehouses.value[0]?.id ?? null);
const searchQuery = ref('');
const categoryFilter = ref('All');
const statusFilter = ref('All');
const showAddWarehouse = ref(false);
const showAddItem = ref(false);
const sortField = ref('name');
const sortDir = ref('asc');
const processing = ref(false);

// Add Warehouse Form
const newWarehouse = ref({ name: '', location: '', manager: '' });

// Add Item Form  — just material + qty (comes from master materials dropdown)
const newItem = ref({ material_id: '', quantity: '' });

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
        if (typeof va === 'string') { va = va.toLowerCase(); vb = vb.toLowerCase(); }
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

// ─── Methods ──────────────────────────────────────────────────────────────────
const setSort = (field) => {
    if (sortField.value === field) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    else { sortField.value = field; sortDir.value = 'asc'; }
};

const addWarehouse = () => {
    if (!newWarehouse.value.name || !newWarehouse.value.location) return;
    processing.value = true;
    router.post(route('inv.manager.warehouse.store'), newWarehouse.value, {
        preserveScroll: true,
        onSuccess: () => {
            newWarehouse.value = { name: '', location: '', manager: '' };
            showAddWarehouse.value = false;
        },
        onFinish: () => (processing.value = false),
    });
};

const addItem = () => {
    if (!newItem.value.material_id || !newItem.value.quantity) return;
    processing.value = true;
    router.post(route('inv.manager.inventory.item.store'), {
        warehouse_id: activeWarehouseId.value,
        material_id: newItem.value.material_id,
        quantity: newItem.value.quantity,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            newItem.value = { material_id: '', quantity: '' };
            showAddItem.value = false;
        },
        onFinish: () => (processing.value = false),
    });
};

const deleteItem = (wmId) => {
    if (!confirm('Remove this item from the warehouse?')) return;
    router.delete(route('inv.manager.inventory.item.destroy', { wmId }), {
        preserveScroll: true,
    });
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
const getColor = (color) => colorMap[color] ?? colorMap.blue;

const statusStyle = (status) => ({
    'In Stock': 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
    'Low Stock': 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
    'Out of Stock': 'bg-red-50 text-red-600 ring-1 ring-red-200',
}[status] ?? '');

const fmt = (n) => Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

// Selected material label for the dropdown
const selectedMaterialLabel = computed(() => {
    if (!newItem.value.material_id) return 'Select material…';
    const m = props.masterMaterials.find(m => m.id == newItem.value.material_id);
    return m ? `${m.mat_id} — ${m.name}` : 'Select material…';
});
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

        <!-- Empty state when no warehouses -->
        <div v-if="warehouses.length === 0" class="flex flex-col items-center justify-center py-24 text-slate-400">
            <Warehouse class="w-12 h-12 mb-4 opacity-30" />
            <p class="font-bold text-slate-500">No warehouses yet.</p>
            <p class="text-sm mt-1">Click "Add Warehouse" to get started.</p>
        </div>

        <template v-else>
            <!-- ── Warehouse Toggle Tabs ─────────────────────────────────────── -->
            <div class="flex flex-wrap gap-3 mb-8">
                <button v-for="wh in warehouses" :key="wh.id"
                    @click="activeWarehouseId = wh.id; categoryFilter = 'All'; statusFilter = 'All'; searchQuery = ''"
                    :class="[
                        'relative flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-200 border',
                        activeWarehouseId === wh.id
                            ? `${getColor(wh.color).btn} shadow-lg border-transparent`
                            : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'
                    ]">
                    <span
                        :class="['w-2 h-2 rounded-full flex-shrink-0', activeWarehouseId === wh.id ? 'bg-white/60' : getColor(wh.color).dot]" />
                    {{ wh.name }}
                    <span :class="[
                        'text-[10px] font-black px-2 py-0.5 rounded-full',
                        activeWarehouseId === wh.id ? 'bg-white/20 text-white' : getColor(wh.color).badge
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
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mt-0.5">{{
                                activeWarehouse?.name }}</h3>
                            <p class="text-sm text-slate-500 mt-0.5">📍 {{ activeWarehouse?.location }}</p>
                        </div>
                        <div
                            :class="['w-10 h-10 rounded-xl flex items-center justify-center', getColor(activeWarehouse?.color ?? 'blue').btn]">
                            <Warehouse class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="text-xs text-slate-500">
                        Manager:
                        <span class="font-bold text-slate-700 dark:text-slate-300">
                            {{ activeWarehouse?.manager || '—' }}
                        </span>
                    </div>
                    <!-- SKU breakdown bar -->
                    <div class="grid grid-cols-3 gap-2">
                        <div
                            :class="['rounded-xl p-2.5 text-center', getColor(activeWarehouse?.color ?? 'blue').badge.replace('ring-1', '')]">
                            <p class="text-lg font-black text-slate-900 dark:text-white">{{ stats.inStock }}</p>
                            <p class="text-[10px] text-slate-400 font-bold">In Stock</p>
                        </div>
                        <div class="rounded-xl p-2.5 text-center bg-amber-50 dark:bg-amber-900/10">
                            <p class="text-lg font-black text-amber-600">{{ stats.lowStock }}</p>
                            <p class="text-[10px] text-slate-400 font-bold">Low</p>
                        </div>
                        <div class="rounded-xl p-2.5 text-center bg-red-50 dark:bg-red-900/10">
                            <p class="text-lg font-black text-red-500">{{ stats.outOfStock }}</p>
                            <p class="text-[10px] text-slate-400 font-bold">Out</p>
                        </div>
                    </div>
                </div>

                <!-- Stat Cards -->
                <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div v-for="val in [
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
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" />
                            <input v-model="searchQuery" type="text" placeholder="Search SKU or name..."
                                class="pl-9 pr-4 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 w-56 text-slate-700 dark:text-slate-200 placeholder-slate-400" />
                        </div>
                        <div class="relative">
                            <select v-model="categoryFilter"
                                class="appearance-none pl-3 pr-8 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium">
                                <option v-for="cat in categories" :key="cat">{{ cat }}</option>
                            </select>
                            <ChevronDown
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                        </div>
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
                        :class="['inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-xl shadow-lg transition flex-shrink-0', getColor(activeWarehouse?.color ?? 'blue').btn]">
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
                                <td colspan="7" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">
                                    No inventory items found.
                                </td>
                            </tr>
                            <tr v-for="item in filteredInventory" :key="item.wm_id"
                                class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors group">
                                <td class="px-5 py-4">
                                    <span
                                        class="font-mono text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-md">
                                        {{ item.id }}
                                    </span>
                                </td>
                                <td
                                    class="px-5 py-4 font-semibold text-slate-800 dark:text-slate-200 max-w-[200px] truncate">
                                    {{ item.name }}
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-full">
                                        {{ item.category }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="['font-black text-base', item.qty === 0 ? 'text-red-500' : item.qty <= item.reorder ? 'text-amber-600' : 'text-slate-900 dark:text-white']">
                                        {{ Number(item.qty).toLocaleString() }}
                                    </span>
                                    <span class="text-slate-400 text-xs ml-1">{{ item.unit }}</span>
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
                                        <button @click="deleteItem(item.wm_id)"
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
                <div
                    class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <p class="text-xs text-slate-400 font-medium">
                        Showing <span class="font-bold text-slate-600 dark:text-slate-300">{{ filteredInventory.length
                            }}</span> of
                        <span class="font-bold text-slate-600 dark:text-slate-300">{{ activeInventory.length }}</span>
                        items
                    </p>
                    <div class="flex items-center gap-1.5">
                        <span :class="['w-2 h-2 rounded-full', getColor(activeWarehouse?.color ?? 'blue').dot]" />
                        <span class="text-xs font-bold text-slate-400">{{ activeWarehouse?.name }}</span>
                    </div>
                </div>
            </div>
        </template>

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
                                Name
                                *</label>
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
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Warehouse
                                Manager</label>
                            <input v-model="newWarehouse.manager" type="text" placeholder="Full Name"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button @click="showAddWarehouse = false"
                            class="flex-1 py-2.5 text-sm font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Cancel
                        </button>
                        <button @click="addWarehouse" :disabled="processing"
                            class="flex-1 py-2.5 text-sm font-bold rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:opacity-80 transition shadow-sm disabled:opacity-50">
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
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">Add Item to Inventory</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Adding to: <span
                                    class="font-bold text-slate-600 dark:text-slate-300">{{ activeWarehouse?.name
                                    }}</span></p>
                        </div>
                        <button @click="showAddItem = false"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <X class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Material Dropdown (from master materials) -->
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Select
                                Material
                                *</label>
                            <div class="relative mt-1">
                                <select v-model="newItem.material_id"
                                    class="w-full appearance-none pl-3 pr-8 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium">
                                    <option value="">— Select from master materials —</option>
                                    <option v-for="m in masterMaterials" :key="m.id" :value="m.id">
                                        {{ m.mat_id }} — {{ m.name }} ({{ m.unit }})
                                    </option>
                                </select>
                                <ChevronDown
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                            </div>
                            <p v-if="masterMaterials.length === 0" class="text-[10px] text-amber-600 mt-1 font-bold">
                                No master materials found. Add materials in the Master Materials page first.
                            </p>
                        </div>

                        <!-- Show selected material info -->
                        <div v-if="newItem.material_id"
                            class="px-4 py-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                            <template v-for="m in [masterMaterials.find(x => x.id == newItem.material_id)]"
                                :key="m?.id">
                                <div v-if="m" class="flex items-center gap-3">
                                    <Package class="w-4 h-4 text-slate-400 flex-shrink-0" />
                                    <div>
                                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ m.name }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ m.category }} · ₱{{ fmt(m.cost)
                                            }} per {{ m.unit }}</p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Quantity to
                                Add
                                *</label>
                            <input v-model="newItem.quantity" type="number" min="0" placeholder="0"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                            <p class="text-[10px] text-slate-400 mt-1">
                                If this material already exists in the warehouse, the quantity will be added to the
                                current
                                stock.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button @click="showAddItem = false"
                            class="flex-1 py-2.5 text-sm font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Cancel
                        </button>
                        <button @click="addItem" :disabled="processing || !newItem.material_id || !newItem.quantity"
                            :class="['flex-1 py-2.5 text-sm font-bold rounded-xl shadow-lg transition disabled:opacity-40', getColor(activeWarehouse?.color ?? 'blue').btn]">
                            Add to Inventory
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

    </AuthenticatedLayout>
</template>