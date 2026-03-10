<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Search, Plus, ChevronDown, ArrowRightLeft, AlertTriangle, X,
    Edit2, Trash2, ArrowUpDown, Send, PackageCheck, Warehouse,
    TrendingUp, TrendingDown, Package, Layers, FlaskConical,
    CheckCircle, DollarSign, BarChart2, ShoppingCart, ClipboardList,
    ArrowRight, Zap,
} from 'lucide-vue-next';

// ─── Props from Inertia ───────────────────────────────────────────────────────
const props = defineProps({
    warehouses: { type: Array, default: () => [] },
    materials: { type: Array, default: () => [] },
});

const warehouses = ref(props.warehouses);
const materials = ref(props.materials);

watch(() => props.warehouses, v => (warehouses.value = v), { deep: true });
watch(() => props.materials, v => (materials.value = v), { deep: true });

// ─── UI State ─────────────────────────────────────────────────────────────────
const searchQuery = ref('');
const categoryFilter = ref('All');
const statusFilter = ref('All');
const sortField = ref('name');
const sortDir = ref('asc');
const expandedMat = ref(null);
const processing = ref(false);

const showDelegateModal = ref(false);
const showAddModal = ref(false);
const showProcurementModal = ref(false);
const delegateSuccess = ref(false);
const procurementSuccess = ref(false);

const delegateForm = ref({ materialId: null, fromWarehouse: null, toWarehouse: null, qty: '' });
const newMaterial = ref({ name: '', category: '', unit: '', quantity: '', unit_cost: '', reorder_point: '' });

// ─── Procurement Request Form ─────────────────────────────────────────────────
const procurementTarget = ref(null);
const procurementForm = ref({
    material_id: null,
    material_name: '',
    category: '',
    unit: '',
    current_stock: 0,
    reorder_point: 0,
    required_qty: '',
    urgency: 'Medium',
    notes: '',
});

// ─── Helpers ──────────────────────────────────────────────────────────────────
const totalQty = (mat) => Object.values(mat.stock).reduce((a, b) => a + Number(b), 0);

const matStatus = (mat) => {
    const t = totalQty(mat);
    if (t <= 0) return 'Out of Stock';
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
const getColor = (c) => colorMap[c] ?? colorMap.blue;

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
        items = items.filter(m => m.name.toLowerCase().includes(q) || m.mat_id.toLowerCase().includes(q));
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
    warehouses.value.map(w => ({ ...w, qty: Number(mat.stock[w.id] ?? mat.stock[String(w.id)] ?? 0) }));

const delegateMaterial = computed(() =>
    materials.value.find(m => m.id === delegateForm.value.materialId) ?? null
);
const fromOptions = computed(() =>
    !delegateMaterial.value ? [] :
        warehouses.value.filter(w => (Number(delegateMaterial.value.stock[w.id] ?? delegateMaterial.value.stock[String(w.id)] ?? 0)) > 0)
);
const toOptions = computed(() =>
    warehouses.value.filter(w => w.id !== delegateForm.value.fromWarehouse)
);
const maxQty = computed(() =>
    delegateMaterial.value && delegateForm.value.fromWarehouse
        ? Number(delegateMaterial.value.stock[delegateForm.value.fromWarehouse] ?? delegateMaterial.value.stock[String(delegateForm.value.fromWarehouse)] ?? 0)
        : 0
);

// Suggested required qty: gap between reorder point and current stock, min 1
const suggestedQty = computed(() => {
    if (!procurementTarget.value) return 1;
    const current = totalQty(procurementTarget.value);
    const reorder = procurementTarget.value.reorder;
    const gap = Math.max(reorder - current, 1);
    return gap;
});

// Auto urgency based on stock status
const autoUrgency = (mat) => {
    const s = matStatus(mat);
    if (s === 'Out of Stock') return 'High';
    if (s === 'Low Stock') return 'Medium';
    return 'Low';
};

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

const openProcurement = (mat) => {
    procurementTarget.value = mat;
    procurementForm.value = {
        material_id: mat.id,
        material_name: mat.name,
        category: mat.category,
        unit: mat.unit,
        current_stock: totalQty(mat),
        reorder_point: mat.reorder,
        required_qty: suggestedQty.value,
        urgency: autoUrgency(mat),
        notes: '',
    };
    procurementSuccess.value = false;
    showProcurementModal.value = true;
};

const confirmDelegate = () => {
    const { materialId, fromWarehouse, toWarehouse, qty } = delegateForm.value;
    const amount = Number(qty);
    if (!materialId || !fromWarehouse || !toWarehouse || !amount) return;
    processing.value = true;
    router.post(route('inv.manager.material.delegate'), {
        material_id: materialId,
        from_warehouse: fromWarehouse,
        to_warehouse: toWarehouse,
        quantity: amount,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            delegateSuccess.value = true;
            setTimeout(() => { showDelegateModal.value = false; delegateSuccess.value = false; }, 1200);
        },
        onFinish: () => (processing.value = false),
    });
};

const submitProcurementRequest = () => {
    if (!procurementForm.value.required_qty || Number(procurementForm.value.required_qty) <= 0) return;
    processing.value = true;
    router.post(route('inv.manager.material.procurement'), {
        material_id: procurementForm.value.material_id,
        material_name: procurementForm.value.material_name,
        category: procurementForm.value.category,
        unit: procurementForm.value.unit,
        current_stock: procurementForm.value.current_stock,
        reorder_point: procurementForm.value.reorder_point,
        required_qty: Number(procurementForm.value.required_qty),
        urgency: procurementForm.value.urgency,
        notes: procurementForm.value.notes,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            procurementSuccess.value = true;
            setTimeout(() => {
                showProcurementModal.value = false;
                procurementSuccess.value = false;
            }, 1800);
        },
        onFinish: () => (processing.value = false),
    });
};

const addMaterial = () => {
    if (!newMaterial.value.name) return;
    processing.value = true;
    router.post(route('inv.manager.material.store'), {
        name: newMaterial.value.name,
        category: newMaterial.value.category || 'Raw Material',
        unit: newMaterial.value.unit || 'pcs',
        quantity: newMaterial.value.quantity || 0,
        unit_cost: newMaterial.value.unit_cost || 0,
        reorder_point: newMaterial.value.reorder_point || 0,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            newMaterial.value = { name: '', category: '', unit: '', quantity: '', unit_cost: '', reorder_point: '' };
            showAddModal.value = false;
        },
        onFinish: () => (processing.value = false),
    });
};

const deleteMaterial = (id) => {
    if (!confirm('Delete this material from the master list?')) return;
    router.delete(route('inv.manager.material.destroy', { id }), {
        preserveScroll: true,
        onSuccess: () => {
            if (expandedMat.value === id) expandedMat.value = null;
        },
    });
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
                <div :class="['p-2.5 rounded-xl shadow-lg', getColor(wh.color).btn]">
                    <Warehouse class="w-4 h-4" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-black text-slate-800 dark:text-slate-200 truncate">{{ wh.name }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ wh.location }}</p>
                </div>
                <div class="ml-auto text-right flex-shrink-0">
                    <p class="text-lg font-black text-slate-900 dark:text-white">
                        {{materials.filter(m => Number(m.stock[wh.id] ?? m.stock[String(wh.id)] ?? 0) > 0).length}}
                    </p>
                    <p class="text-[10px] text-slate-400">SKUs</p>
                </div>
            </div>
        </div>

        <!-- ── Low/Out-of-Stock Alert Banner ──────────────────────────────── -->
        <div v-if="globalStats.lowStock + globalStats.outOfStock > 0"
            class="mb-5 flex items-center justify-between gap-4 px-5 py-3.5 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl">
            <div class="flex items-center gap-3">
                <AlertTriangle class="w-5 h-5 text-amber-500 flex-shrink-0" />
                <p class="text-sm font-bold text-amber-800 dark:text-amber-300">
                    {{ globalStats.lowStock + globalStats.outOfStock }} material(s) need restocking —
                    <span class="font-black">{{ globalStats.outOfStock }} out of stock</span>,
                    {{ globalStats.lowStock }} low.
                    Use the <span class="underline decoration-dotted">Procurement</span> button on each row to request a
                    restock.
                </p>
            </div>
            <button @click="statusFilter = 'Low Stock'"
                class="text-xs font-black text-amber-700 dark:text-amber-300 whitespace-nowrap hover:underline">
                Filter Low →
            </button>
        </div>

        <!-- ── Main Table Card ────────────────────────────────────────────── -->
        <div
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">

            <!-- Toolbar -->
            <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <div class="relative flex-1 min-w-[180px]">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" />
                    <input v-model="searchQuery" type="text" placeholder="Search materials…"
                        class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                </div>
                <div class="relative">
                    <select v-model="categoryFilter"
                        class="appearance-none pl-3 pr-8 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium">
                        <option v-for="c in categories" :key="c">{{ c }}</option>
                    </select>
                    <ChevronDown
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                </div>
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
                                { label: 'MAT ID', field: 'mat_id' },
                                { label: 'Material Name', field: 'name' },
                                { label: 'Category', field: 'category' },
                                { label: 'Total Qty', field: 'totalQty' },
                                { label: 'Unit Cost (₱)', field: 'cost' },
                                { label: 'Total Value', field: 'totalValue' },
                                { label: 'Warehouses', field: null },
                                { label: 'Status', field: null },
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
                        <tr v-if="filteredMaterials.length === 0">
                            <td colspan="10" class="px-5 py-20 text-center text-slate-400 text-sm font-medium">
                                <Package class="w-10 h-10 mx-auto mb-3 opacity-30" />
                                No materials found.
                            </td>
                        </tr>

                        <template v-for="mat in filteredMaterials" :key="mat.id">
                            <tr
                                :class="['hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors group', expandedMat === mat.id ? 'bg-slate-50/60 dark:bg-slate-800/40' : '']">
                                <td class="pl-5 py-4">
                                    <button @click="expandedMat = expandedMat === mat.id ? null : mat.id"
                                        class="p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition text-slate-400">
                                        <ChevronDown
                                            :class="['w-3.5 h-3.5 transition-transform', expandedMat === mat.id ? 'rotate-180' : '']" />
                                    </button>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="font-mono text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-md">
                                        {{ mat.mat_id }}
                                    </span>
                                </td>
                                <td
                                    class="px-5 py-4 font-semibold text-slate-800 dark:text-slate-200 max-w-[220px] truncate">
                                    {{ mat.name }}
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="['text-[10px] font-bold px-2.5 py-1 rounded-full', catColor[mat.category] ?? 'bg-slate-100 text-slate-500']">
                                        {{ mat.category }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="['font-black text-base', totalQty(mat) === 0 ? 'text-red-500' : totalQty(mat) <= mat.reorder ? 'text-amber-600' : 'text-slate-900 dark:text-white']">
                                        {{ Number(totalQty(mat)).toLocaleString() }}
                                    </span>
                                    <span class="text-slate-400 text-xs ml-1">{{ mat.unit }}</span>
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-300">₱{{ fmt(mat.cost)
                                }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-300">₱{{
                                    fmt(totalQty(mat) * mat.cost) }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span v-for="wh in warehouses" :key="wh.id"
                                            :title="wh.name + ': ' + Number(mat.stock[wh.id] ?? mat.stock[String(wh.id)] ?? 0) + ' ' + mat.unit"
                                            :class="['w-5 h-5 rounded-full flex items-center justify-center text-[8px] font-black transition-all',
                                                Number(mat.stock[wh.id] ?? mat.stock[String(wh.id)] ?? 0) > 0
                                                    ? getColor(wh.color).btn + ' shadow-sm'
                                                    : 'bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600'
                                            ]">
                                            {{ wh.name.charAt(0) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="['text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full', statusStyle(matStatus(mat))]">
                                        {{ matStatus(mat) }}
                                    </span>
                                </td>

                                <!-- ── Actions Column ─────────────────────── -->
                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">

                                        <!-- Delegate Button -->
                                        <button @click="openDelegate(mat)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-black rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition shadow-sm whitespace-nowrap">
                                            <ArrowRightLeft class="w-3 h-3" /> Delegate
                                        </button>

                                        <!-- Procurement Button: always shown, pulsing if Low/Out -->
                                        <button @click="openProcurement(mat)" :class="[
                                            'inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-black rounded-lg transition shadow-sm whitespace-nowrap',
                                            matStatus(mat) === 'Out of Stock'
                                                ? 'bg-red-600 hover:bg-red-700 text-white ring-2 ring-red-400/40'
                                                : matStatus(mat) === 'Low Stock'
                                                    ? 'bg-amber-500 hover:bg-amber-600 text-white ring-2 ring-amber-400/40'
                                                    : 'bg-slate-700 hover:bg-slate-800 dark:bg-slate-600 dark:hover:bg-slate-500 text-white'
                                        ]">
                                            <ShoppingCart class="w-3 h-3" />
                                            Procurement
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
                                            Warehouse Distribution</p>
                                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                            <div v-for="wh in warehouseBreakdown(mat)" :key="wh.id"
                                                :class="['rounded-xl border p-3.5 transition-all', wh.qty > 0 ? 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-100 dark:border-slate-800 opacity-60']">
                                                <div class="flex items-center gap-2 mb-2.5">
                                                    <span
                                                        :class="['w-2 h-2 rounded-full flex-shrink-0', getColor(wh.color).dot]" />
                                                    <p
                                                        class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">
                                                        {{ wh.name }}</p>
                                                </div>
                                                <p
                                                    :class="['text-2xl font-black', wh.qty > 0 ? 'text-slate-900 dark:text-white' : 'text-slate-300 dark:text-slate-600']">
                                                    {{ Number(wh.qty).toLocaleString() }}
                                                </p>
                                                <p class="text-[10px] text-slate-400 mt-0.5">{{ mat.unit }}</p>
                                                <div
                                                    class="mt-3 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                    <div :class="['h-full rounded-full transition-all', getColor(wh.color).bar]"
                                                        :style="{ width: totalQty(mat) > 0 ? (wh.qty / totalQty(mat) * 100) + '%' : '0%' }" />
                                                </div>
                                                <p class="text-[10px] text-slate-400 mt-1">
                                                    {{ totalQty(mat) > 0 ? Math.round(wh.qty / totalQty(mat) * 100) : 0
                                                    }}% of total
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Reorder Alert + Quick Procurement CTA -->
                                        <div v-if="matStatus(mat) !== 'In Stock'"
                                            class="mt-3 flex flex-wrap items-center gap-3">
                                            <div
                                                class="flex items-center gap-2 px-3 py-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                                                <AlertTriangle class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" />
                                                <p class="text-xs font-bold text-amber-700 dark:text-amber-400">
                                                    Reorder point: {{ mat.reorder.toLocaleString() }} {{ mat.unit }} —
                                                    total stock is
                                                    <span
                                                        :class="matStatus(mat) === 'Out of Stock' ? 'text-red-600' : ''">
                                                        {{ matStatus(mat) === 'Out of Stock' ? 'depleted' :
                                                        'belowthreshold' }}
                                                    </span>
                                                </p>
                                            </div>
                                            <button @click="openProcurement(mat)"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-black rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                                                <ShoppingCart class="w-3.5 h-3.5" />
                                                Request Procurement
                                            </button>
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
                    Showing <span class="font-bold text-slate-600 dark:text-slate-300">{{ filteredMaterials.length
                    }}</span>
                    of <span class="font-bold text-slate-600 dark:text-slate-300">{{ materials.length }}</span>
                    materials
                </p>
                <div class="flex items-center gap-3">
                    <span v-if="globalStats.lowStock > 0"
                        class="flex items-center gap-1.5 text-[10px] font-black text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full ring-1 ring-amber-200">
                        <AlertTriangle class="w-3 h-3" /> {{ globalStats.lowStock }} low stock
                    </span>
                    <span v-if="globalStats.outOfStock > 0"
                        class="flex items-center gap-1.5 text-[10px] font-black text-red-600 bg-red-50 px-2.5 py-1 rounded-full ring-1 ring-red-200">
                        {{ globalStats.outOfStock }} out of stock
                    </span>
                </div>
            </div>
        </div>


        <!-- ══ PROCUREMENT REQUEST MODAL ════════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showProcurementModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                @click.self="showProcurementModal = false">
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-lg overflow-hidden">

                    <!-- Success State -->
                    <div v-if="procurementSuccess" class="flex flex-col items-center py-14 gap-4 px-6">
                        <div
                            class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <CheckCircle class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                        </div>
                        <p class="text-lg font-black text-slate-900 dark:text-white">Request Sent to SCM!</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center">
                            The procurement request for <strong>{{ procurementForm.material_name }}</strong>
                            has been forwarded to the SCM Procurement module.
                        </p>
                        <div
                            class="flex items-center gap-2 mt-1 px-4 py-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 text-xs font-bold text-blue-700 dark:text-blue-300">
                            <ArrowRight class="w-4 h-4" />
                            SCM Manager will review and send RFQ to suppliers.
                        </div>
                    </div>

                    <!-- Form State -->
                    <template v-else>
                        <!-- Modal Header -->
                        <div :class="[
                            'px-6 py-5 border-b border-slate-100 dark:border-slate-700',
                            procurementForm.urgency === 'High'
                                ? 'bg-red-50 dark:bg-red-900/20'
                                : procurementForm.urgency === 'Medium'
                                    ? 'bg-amber-50 dark:bg-amber-900/20'
                                    : 'bg-slate-50 dark:bg-slate-800'
                        ]">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div :class="[
                                        'p-2.5 rounded-xl flex-shrink-0',
                                        procurementForm.urgency === 'High'
                                            ? 'bg-red-100 dark:bg-red-900/40'
                                            : procurementForm.urgency === 'Medium'
                                                ? 'bg-amber-100 dark:bg-amber-900/40'
                                                : 'bg-slate-100 dark:bg-slate-700'
                                    ]">
                                        <ShoppingCart :class="[
                                            'w-5 h-5',
                                            procurementForm.urgency === 'High' ? 'text-red-600 dark:text-red-400'
                                                : procurementForm.urgency === 'Medium' ? 'text-amber-600 dark:text-amber-400'
                                                    : 'text-slate-500'
                                        ]" />
                                    </div>
                                    <div>
                                        <h3 class="text-base font-black text-slate-900 dark:text-white">Procurement
                                            Request</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            This request will appear in the SCM Procurement module for sourcing.
                                        </p>
                                    </div>
                                </div>
                                <button @click="showProcurementModal = false"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-white/60 dark:hover:bg-slate-700 transition flex-shrink-0">
                                    <X class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <div class="p-6 space-y-5">
                            <!-- Material Info Card (read-only) -->
                            <div
                                class="px-4 py-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            <span
                                                class="font-mono text-[10px] text-slate-400 bg-white dark:bg-slate-700 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-600">
                                                {{ procurementTarget?.mat_id }}
                                            </span>
                                            <span
                                                :class="['text-[10px] font-bold px-2 py-0.5 rounded-full', catColor[procurementForm.category] ?? 'bg-slate-100 text-slate-500']">
                                                {{ procurementForm.category }}
                                            </span>
                                        </div>
                                        <p class="font-black text-slate-800 dark:text-slate-200 text-sm truncate">{{
                                            procurementForm.material_name }}</p>
                                    </div>
                                    <span
                                        :class="['text-[10px] font-black px-2.5 py-1 rounded-full flex-shrink-0', statusStyle(matStatus(procurementTarget))]">
                                        {{ matStatus(procurementTarget) }}
                                    </span>
                                </div>

                                <!-- Stock Overview -->
                                <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                                    <div
                                        class="bg-white dark:bg-slate-700 rounded-lg px-2 py-2 border border-slate-200 dark:border-slate-600">
                                        <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide">
                                            Current Stock</p>
                                        <p
                                            :class="['text-base font-black mt-0.5', procurementForm.current_stock <= 0 ? 'text-red-600' : 'text-slate-800 dark:text-white']">
                                            {{ Number(procurementForm.current_stock).toLocaleString() }}
                                        </p>
                                        <p class="text-[10px] text-slate-400">{{ procurementForm.unit }}</p>
                                    </div>
                                    <div
                                        class="bg-white dark:bg-slate-700 rounded-lg px-2 py-2 border border-slate-200 dark:border-slate-600">
                                        <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide">
                                            Reorder Point</p>
                                        <p class="text-base font-black text-slate-800 dark:text-white mt-0.5">
                                            {{ Number(procurementForm.reorder_point).toLocaleString() }}
                                        </p>
                                        <p class="text-[10px] text-slate-400">{{ procurementForm.unit }}</p>
                                    </div>
                                    <div
                                        class="bg-amber-50 dark:bg-amber-900/20 rounded-lg px-2 py-2 border border-amber-200 dark:border-amber-700">
                                        <p class="text-[10px] text-amber-600 font-semibold uppercase tracking-wide">Gap
                                        </p>
                                        <p class="text-base font-black text-amber-700 dark:text-amber-300 mt-0.5">
                                            {{ procurementForm.current_stock < procurementForm.reorder_point ?
                                                (procurementForm.reorder_point -
                                                    procurementForm.current_stock).toLocaleString() : '—' }} </p>
                                                <p class="text-[10px] text-amber-500">below reorder</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Required Quantity -->
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Required Quantity <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 flex items-center gap-2">
                                    <input v-model="procurementForm.required_qty" type="number" min="1"
                                        :placeholder="'e.g. ' + suggestedQty"
                                        class="flex-1 px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-bold" />
                                    <span class="text-sm font-bold text-slate-500 dark:text-slate-400 flex-shrink-0">
                                        {{ procurementForm.unit }}
                                    </span>
                                    <button v-if="suggestedQty > 0" @click="procurementForm.required_qty = suggestedQty"
                                        class="px-3 py-2.5 text-xs font-black text-blue-600 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl hover:bg-blue-100 transition whitespace-nowrap">
                                        Suggested
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">
                                    Suggested: <span class="font-bold text-slate-600 dark:text-slate-300">{{
                                        Number(suggestedQty).toLocaleString() }} {{ procurementForm.unit }}</span>
                                    (reorder gap)
                                </p>
                            </div>

                            <!-- Urgency -->
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Urgency
                                    Level</label>
                                <div class="mt-1.5 grid grid-cols-3 gap-2">
                                    <button v-for="level in ['High', 'Medium', 'Low']" :key="level"
                                        @click="procurementForm.urgency = level" :class="[
                                            'py-2.5 text-xs font-black rounded-xl border-2 transition-all',
                                            procurementForm.urgency === level
                                                ? level === 'High'
                                                    ? 'border-red-500 bg-red-600 text-white shadow-md shadow-red-500/20'
                                                    : level === 'Medium'
                                                        ? 'border-amber-500 bg-amber-500 text-white shadow-md shadow-amber-500/20'
                                                        : 'border-blue-500 bg-blue-600 text-white shadow-md shadow-blue-500/20'
                                                : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-500'
                                        ]">
                                        {{ level === 'High' ? '🔴' : level === 'Medium' ? '🟡' : '🔵' }}
                                        {{ level }}
                                    </button>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Notes / Special Instructions
                                    <span class="normal-case font-medium text-slate-400">(optional)</span>
                                </label>
                                <textarea v-model="procurementForm.notes" rows="2"
                                    placeholder="e.g. Specific grade required, packaging preference, preferred supplier..."
                                    class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 resize-none"></textarea>
                            </div>

                            <!-- Flow Info -->
                            <div
                                class="flex items-center gap-2 text-[10px] font-bold text-slate-400 dark:text-slate-500 px-1">
                                <span class="flex items-center gap-1">
                                    <span
                                        class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[9px] font-black">INV</span>
                                    Inventory
                                </span>
                                <ArrowRight class="w-3 h-3 flex-shrink-0" />
                                <span class="flex items-center gap-1">
                                    <span
                                        class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[9px] font-black">SCM</span>
                                    Procurement
                                </span>
                                <ArrowRight class="w-3 h-3 flex-shrink-0" />
                                <span>RFQ → Quotation → PO → Invoice → Payment</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div
                            class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex gap-3 bg-slate-50 dark:bg-slate-800/50">
                            <button @click="showProcurementModal = false"
                                class="flex-1 py-2.5 text-sm font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition">
                                Cancel
                            </button>
                            <button @click="submitProcurementRequest"
                                :disabled="processing || !procurementForm.required_qty || Number(procurementForm.required_qty) <= 0"
                                class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 text-sm font-black rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 disabled:opacity-40 disabled:cursor-not-allowed">
                                <Send class="w-4 h-4" />
                                {{ processing ? 'Sending...' : 'Send to SCM Procurement' }}
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </Teleport>


        <!-- ══ DELEGATE MODAL ═══════════════════════════════════════════════ -->
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
                                    <span class="font-mono text-[10px] text-slate-400">{{ delegateMaterial.mat_id
                                    }}</span>
                                    <span class="text-[10px] text-slate-400">·</span>
                                    <span class="text-[10px] font-bold text-slate-500">
                                        {{ Number(totalQty(delegateMaterial)).toLocaleString() }} {{
                                            delegateMaterial.unit }} total
                                    </span>
                                </div>
                            </div>
                            <span
                                :class="['text-[10px] font-black px-2 py-1 rounded-full', statusStyle(matStatus(delegateMaterial))]">
                                {{ matStatus(delegateMaterial) }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">From
                                    Warehouse *</label>
                                <div class="relative mt-1">
                                    <select v-model="delegateForm.fromWarehouse"
                                        @change="delegateForm.toWarehouse = null; delegateForm.qty = ''"
                                        class="w-full appearance-none pl-3 pr-8 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium">
                                        <option :value="null">Select source warehouse…</option>
                                        <option v-for="wh in fromOptions" :key="wh.id" :value="wh.id">
                                            {{ wh.name }} ({{ Number(delegateMaterial?.stock[wh.id] ??
                                                delegateMaterial?.stock[String(wh.id)] ?? 0).toLocaleString() }} {{
                                                delegateMaterial?.unit }})
                                        </option>
                                    </select>
                                    <ChevronDown
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                </div>
                                <p v-if="fromOptions.length === 0" class="text-[10px] text-red-500 mt-1 font-bold">
                                    No warehouse currently holds this material.
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-px bg-slate-100 dark:bg-slate-800" />
                                <div
                                    class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-full border border-blue-200 dark:border-blue-800">
                                    <ArrowRightLeft class="w-3.5 h-3.5 text-blue-500" />
                                </div>
                                <div class="flex-1 h-px bg-slate-100 dark:bg-slate-800" />
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">To
                                    Warehouse *</label>
                                <div class="relative mt-1">
                                    <select v-model="delegateForm.toWarehouse" :disabled="!delegateForm.fromWarehouse"
                                        class="w-full appearance-none pl-3 pr-8 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option :value="null">Select destination warehouse…</option>
                                        <option v-for="wh in toOptions" :key="wh.id" :value="wh.id">
                                            {{ wh.name }}
                                            <template
                                                v-if="Number(delegateMaterial?.stock[wh.id] ?? delegateMaterial?.stock[String(wh.id)] ?? 0) > 0">
                                                ({{ Number(delegateMaterial?.stock[wh.id] ??
                                                    delegateMaterial?.stock[String(wh.id)] ?? 0).toLocaleString() }} {{
                                                    delegateMaterial?.unit }} existing)
                                            </template>
                                        </option>
                                    </select>
                                    <ChevronDown
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Quantity
                                    to Transfer *</label>
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

                        <div class="mt-6 flex gap-3">
                            <button @click="showDelegateModal = false"
                                class="flex-1 py-2.5 text-sm font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                Cancel
                            </button>
                            <button @click="confirmDelegate"
                                :disabled="processing || !delegateForm.fromWarehouse || !delegateForm.toWarehouse || !delegateForm.qty || Number(delegateForm.qty) > maxQty || Number(delegateForm.qty) <= 0"
                                class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 text-sm font-bold rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 disabled:opacity-40 disabled:cursor-not-allowed">
                                <Send class="w-4 h-4" /> Confirm Delegation
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </Teleport>


        <!-- ══ ADD MATERIAL MODAL ════════════════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showAddModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                @click.self="showAddModal = false">
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">Add Material</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Mat ID is auto-generated. Initial quantity goes to
                                the Main Warehouse.</p>
                        </div>
                        <button @click="showAddModal = false"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Category
                                *</label>
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
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Unit
                                *</label>
                            <input v-model="newMaterial.unit" type="text" placeholder="rolls, kg, pcs…"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Material Name
                                *</label>
                            <input v-model="newMaterial.name" type="text" placeholder="Full material name"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Initial
                                Quantity</label>
                            <input v-model="newMaterial.quantity" type="number" min="0" placeholder="0"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reorder
                                Point</label>
                            <input v-model="newMaterial.reorder_point" type="number" min="0" placeholder="0"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Unit Cost (₱)
                                *</label>
                            <input v-model="newMaterial.unit_cost" type="number" min="0" step="0.01" placeholder="0.00"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200" />
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button @click="showAddModal = false"
                            class="flex-1 py-2.5 text-sm font-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Cancel
                        </button>
                        <button @click="addMaterial" :disabled="processing || !newMaterial.name"
                            class="flex-1 py-2.5 text-sm font-bold rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:opacity-80 transition shadow-sm disabled:opacity-40 disabled:cursor-not-allowed">
                            Add to Master List
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

    </AuthenticatedLayout>
</template>