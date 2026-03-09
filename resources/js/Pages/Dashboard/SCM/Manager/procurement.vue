<script setup>
import { ref, onMounted, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/dashboard/StatsCard.vue';
import {
    Boxes, Package, Truck, AlertTriangle, ClipboardList,
    FileText, CheckCircle, Users, ShieldCheck, ChevronDown, ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ totalInventory: '45,280', pendingOrders: 12, stockAlerts: 3 })
    },
    pendingOrders: {
        type: Array,
        default: () => [
            { id: 'ORD-101', productName: 'Summer Cotton Polo', targetQty: 5000, deadline: '2026-03-15' },
            { id: 'ORD-102', productName: 'Indigo Slim Denim', targetQty: 2500, deadline: '2026-03-22' },
            { id: 'ORD-103', productName: 'Fleece Zip Hoodie', targetQty: 1800, deadline: '2026-03-30' }
        ]
    }
});

// --- State Management ---
const activeProcess = ref('material_request');
const selectedOrder = ref(null);
const materialNeeds = ref([]);
const selectedMaterialForSourcing = ref(null);

// Folder-like toggle groups for procurement workflow
const procurementGroups = ref([
    {
        id: 'planning',
        title: 'Planning & Requests',
        icon: ClipboardList,
        open: true,
        children: [
            { id: 'material_request', name: 'Material Request', sub: '(Comes from inventory)', img: '/image_18607f.png' },
            { id: 'rfq', name: 'Request for Quotation', img: '/image_185d99.png' },
        ]
    },
    {
        id: 'sourcing',
        title: 'Sourcing & Quotations',
        icon: FileText,
        open: false,
        children: [
            { id: 'quotation', name: 'Supplier Quotation', img: '/image_185d59.png' },
            { id: 'supplier', name: 'Supplier', sub: '(Vendor Management)' },
        ]
    },
    {
        id: 'ordering',
        title: 'Ordering & Receipt',
        icon: Package,
        open: false,
        children: [
            { id: 'purchase_order', name: 'Purchase Order', img: '/image_185d1c.png' },
            { id: 'purchase_receipt', name: 'Purchase Receipt', img: '/image_185cc3.png' },
        ]
    },
    {
        id: 'payment',
        title: 'Payment & Reports',
        icon: CheckCircle,
        open: false,
        children: [
            { id: 'purchase_invoice', name: 'Purchase Invoice' },
            { id: 'payment', name: 'Make Payment' },
            { id: 'reports', name: 'Requested Items', icon: AlertTriangle, img: '/image_1859fc.png' }
        ]
    }
]);

const currentStepData = computed(() => {
    for (const group of procurementGroups.value) {
        const step = group.children.find(s => s.id === activeProcess.value);
        if (step) return { ...step, groupIcon: group.icon };
    }
    return procurementGroups.value[0].children[0]; // Fallback
});

// ── Dummy Vendor Data ──────────────────────────────────────────────────────────
const vendorQuotes = ref([
    { id: 1, name: 'Heritage Textile Mills', price: 8.45, leadTime: '10 days', rating: 4.9, status: 'Preferred' },
    { id: 2, name: 'Eastern Yarn Co.', price: 7.90, leadTime: '18 days', rating: 4.5, status: 'Verified' },
    { id: 3, name: 'Skyline Fabric Ltd', price: 9.10, leadTime: '5 days', rating: 4.7, status: 'Approved' }
]);

// ── Dummy RFQ Data ─────────────────────────────────────────────────────────────
const rfqList = ref([
    { id: 'RFQ-2026-001', material: '200GSM Combed Cotton', qty: 1750, unit: 'm', sentTo: 'Heritage Textile Mills', sentDate: '2026-03-01', dueDate: '2026-03-08', status: 'Responded' },
    { id: 'RFQ-2026-002', material: '200GSM Combed Cotton', qty: 1750, unit: 'm', sentTo: 'Eastern Yarn Co.', sentDate: '2026-03-01', dueDate: '2026-03-08', status: 'Responded' },
    { id: 'RFQ-2026-003', material: '200GSM Combed Cotton', qty: 1750, unit: 'm', sentTo: 'Skyline Fabric Ltd', sentDate: '2026-03-01', dueDate: '2026-03-08', status: 'Pending' },
    { id: 'RFQ-2026-004', material: 'Reactive Dye (Navy)', qty: 100, unit: 'L', sentTo: 'ChemPro Supplies', sentDate: '2026-03-02', dueDate: '2026-03-09', status: 'Responded' },
    { id: 'RFQ-2026-005', material: 'Branded Resin Buttons', qty: 15000, unit: 'pcs', sentTo: 'Trim World Int\'l', sentDate: '2026-03-03', dueDate: '2026-03-10', status: 'Overdue' }
]);

// ── Dummy Quotation Comparison Data ───────────────────────────────────────────
const quotations = ref([
    {
        id: 'QT-2026-001', rfqRef: 'RFQ-2026-001', vendor: 'Heritage Textile Mills',
        material: '200GSM Combed Cotton', qty: 1750, unit: 'm',
        unitPrice: 8.45, totalPrice: 14787.50, leadTime: '10 days',
        validity: '2026-03-31', terms: 'Net 30', rating: 4.9, recommended: true
    },
    {
        id: 'QT-2026-002', rfqRef: 'RFQ-2026-002', vendor: 'Eastern Yarn Co.',
        material: '200GSM Combed Cotton', qty: 1750, unit: 'm',
        unitPrice: 7.90, totalPrice: 13825.00, leadTime: '18 days',
        validity: '2026-03-28', terms: 'Net 45', rating: 4.5, recommended: false
    },
    {
        id: 'QT-2026-004', rfqRef: 'RFQ-2026-004', vendor: 'ChemPro Supplies',
        material: 'Reactive Dye (Navy)', qty: 100, unit: 'L',
        unitPrice: 42.00, totalPrice: 4200.00, leadTime: '7 days',
        validity: '2026-03-25', terms: 'Net 30', rating: 4.6, recommended: true
    }
]);

// ── Dummy Purchase Order Data ──────────────────────────────────────────────────
const purchaseOrders = ref([
    {
        id: 'PO-2026-0041', vendor: 'Heritage Textile Mills', date: '2026-03-05',
        deliveryDate: '2026-03-15', status: 'Confirmed',
        items: [
            { name: '200GSM Combed Cotton', qty: 1750, unit: 'm', unitPrice: 8.45, total: 14787.50 }
        ],
        subtotal: 14787.50, tax: 1478.75, grandTotal: 16266.25
    },
    {
        id: 'PO-2026-0042', vendor: 'ChemPro Supplies', date: '2026-03-05',
        deliveryDate: '2026-03-12', status: 'Sent',
        items: [
            { name: 'Reactive Dye (Navy)', qty: 100, unit: 'L', unitPrice: 42.00, total: 4200.00 }
        ],
        subtotal: 4200.00, tax: 420.00, grandTotal: 4620.00
    },
    {
        id: 'PO-2026-0043', vendor: 'Trim World Int\'l', date: '2026-03-06',
        deliveryDate: '2026-03-18', status: 'Draft',
        items: [
            { name: 'Branded Resin Buttons', qty: 15000, unit: 'pcs', unitPrice: 0.18, total: 2700.00 }
        ],
        subtotal: 2700.00, tax: 270.00, grandTotal: 2970.00
    }
]);
const selectedPO = ref(null);

// ── Dummy Purchase Receipt Data ────────────────────────────────────────────────
const purchaseReceipts = ref([
    {
        id: 'GRN-2026-031', poRef: 'PO-2026-0041', vendor: 'Heritage Textile Mills',
        receivedDate: '2026-03-14', receivedBy: 'Mark Santos',
        items: [
            { name: '200GSM Combed Cotton', ordered: 1750, received: 1750, unit: 'm', condition: 'Good' }
        ],
        status: 'Complete', qcStatus: 'Passed'
    },
    {
        id: 'GRN-2026-032', poRef: 'PO-2026-0042', vendor: 'ChemPro Supplies',
        receivedDate: '2026-03-11', receivedBy: 'Ana Reyes',
        items: [
            { name: 'Reactive Dye (Navy)', ordered: 100, received: 95, unit: 'L', condition: 'Good' }
        ],
        status: 'Partial', qcStatus: 'Passed'
    },
    {
        id: 'GRN-2026-033', poRef: 'PO-2026-0043', vendor: 'Trim World Int\'l',
        receivedDate: '—', receivedBy: '—',
        items: [
            { name: 'Branded Resin Buttons', ordered: 15000, received: 0, unit: 'pcs', condition: '—' }
        ],
        status: 'Pending', qcStatus: '—'
    }
]);

// ── Dummy Purchase Invoice Data ────────────────────────────────────────────────
const purchaseInvoices = ref([
    {
        id: 'INV-HTM-8821', poRef: 'PO-2026-0041', vendor: 'Heritage Textile Mills',
        invoiceDate: '2026-03-14', dueDate: '2026-04-13',
        amount: 16266.25, status: 'Unpaid', paymentTerms: 'Net 30'
    },
    {
        id: 'INV-CPS-4410', poRef: 'PO-2026-0042', vendor: 'ChemPro Supplies',
        invoiceDate: '2026-03-11', dueDate: '2026-04-10',
        amount: 4389.00, status: 'Paid', paymentTerms: 'Net 30'
    },
    {
        id: 'INV-TWI-2290', poRef: 'PO-2026-0043', vendor: 'Trim World Int\'l',
        invoiceDate: '—', dueDate: '—',
        amount: 2970.00, status: 'Awaiting', paymentTerms: 'Net 30'
    }
]);

// ── Dummy Payment Data ─────────────────────────────────────────────────────────
const payments = ref([
    {
        id: 'PAY-2026-051', invoiceRef: 'INV-CPS-4410', vendor: 'ChemPro Supplies',
        paidDate: '2026-03-18', amount: 4389.00, method: 'Bank Transfer',
        reference: 'TXN-00448812', status: 'Cleared'
    }
]);
const paymentQueue = ref([
    { invoiceRef: 'INV-HTM-8821', vendor: 'Heritage Textile Mills', dueDate: '2026-04-13', amount: 16266.25, priority: 'High' },
    { invoiceRef: 'INV-TWI-2290', vendor: 'Trim World Int\'l', dueDate: '—', amount: 2970.00, priority: 'Low' }
]);

// ── Dummy Requested Items Report Data ─────────────────────────────────────────
const reportItems = ref([
    { id: 1, material: '200GSM Combed Cotton', requestedQty: 1750, unit: 'm', requestedBy: 'Production Dept', requestDate: '2026-03-01', poRef: 'PO-2026-0041', status: 'Received' },
    { id: 2, material: 'Reactive Dye (Navy)', requestedQty: 100, unit: 'L', requestedBy: 'Dye House', requestDate: '2026-03-02', poRef: 'PO-2026-0042', status: 'Partial' },
    { id: 3, material: 'Branded Resin Buttons', requestedQty: 15000, unit: 'pcs', requestedBy: 'Trim Store', requestDate: '2026-03-03', poRef: 'PO-2026-0043', status: 'Pending' },
    { id: 4, material: 'Poly Bag (12x16)', requestedQty: 8000, unit: 'pcs', requestedBy: 'Finishing Dept', requestDate: '2026-03-04', poRef: '—', status: 'Sourcing' },
    { id: 5, material: 'Hang Tag Ribbon', requestedQty: 5000, unit: 'pcs', requestedBy: 'QC Dept', requestDate: '2026-03-05', poRef: '—', status: 'Sourcing' }
]);

// ── Logic ──────────────────────────────────────────────────────────────────────
const toggleGroup = (groupId) => {
    const group = procurementGroups.value.find(g => g.id === groupId);
    if (group) group.open = !group.open;
};

const selectOrder = (order) => {
    selectedOrder.value = order;
    materialNeeds.value = [
        { id: 1, name: '200GSM Combed Cotton', type: 'Fabric', needed: (order.targetQty * 0.35).toFixed(2), stock: 850, unit: 'm' },
        { id: 2, name: 'Reactive Dye (Navy)', type: 'Chemicals', needed: (order.targetQty * 0.02).toFixed(2), stock: 15, unit: 'L' },
        { id: 3, name: 'Branded Resin Buttons', type: 'Trim', needed: (order.targetQty * 3), stock: 12000, unit: 'pcs' }
    ];
    selectedMaterialForSourcing.value = materialNeeds.value[0];
};

const openSourcing = (material) => {
    selectedMaterialForSourcing.value = material;
    activeProcess.value = 'supplier';
};

const statusColor = (status) => {
    const map = {
        'Responded': 'text-green-600 bg-green-50 dark:bg-green-900/20',
        'Pending': 'text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20',
        'Overdue': 'text-red-600 bg-red-50 dark:bg-red-900/20',
        'Confirmed': 'text-blue-600 bg-blue-50 dark:bg-blue-900/20',
        'Sent': 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20',
        'Draft': 'text-gray-600 bg-gray-100 dark:bg-gray-700',
        'Complete': 'text-green-600 bg-green-50 dark:bg-green-900/20',
        'Partial': 'text-orange-600 bg-orange-50 dark:bg-orange-900/20',
        'Paid': 'text-green-600 bg-green-50 dark:bg-green-900/20',
        'Unpaid': 'text-red-600 bg-red-50 dark:bg-red-900/20',
        'Awaiting': 'text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20',
        'Cleared': 'text-green-600 bg-green-50 dark:bg-green-900/20',
        'Received': 'text-green-600 bg-green-50 dark:bg-green-900/20',
        'Sourcing': 'text-purple-600 bg-purple-50 dark:bg-purple-900/20',
        'High': 'text-red-600 bg-red-50 dark:bg-red-900/20',
        'Low': 'text-blue-600 bg-blue-50 dark:bg-blue-900/20',
        'Passed': 'text-green-600 bg-green-50 dark:bg-green-900/20'
    };
    return map[status] || 'text-gray-600 bg-gray-100 dark:bg-gray-700';
};

onMounted(() => {
    if (props.pendingOrders.length > 0) {
        selectOrder(props.pendingOrders[0]);
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">SCM Management Dashboard</h2>
                <p class="text-sm text-gray-500">Procurement Workflow Navigation</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <StatCard title="Total Inventory" :value="stats.totalInventory" :icon="Boxes" />
            <StatCard title="Pending Orders" :value="stats.pendingOrders" :icon="Package" />
            <StatCard title="Active Vendors" value="24" :icon="Users" />
            <StatCard title="Stock Alerts" :value="stats.stockAlerts" :icon="AlertTriangle"
                colorClass="text-rose-600 bg-rose-50" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 space-y-4">
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase mb-4">Procurement Workflow</h3>
                    <div class="space-y-2">
                        <div v-for="group in procurementGroups" :key="group.id">
                            <button @click="toggleGroup(group.id)"
                                class="w-full flex items-center justify-between p-3 bg-gray-100 dark:bg-gray-700 rounded-lg text-left font-bold text-gray-800 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                <div class="flex items-center">
                                    <component :is="group.icon" class="w-5 h-5 mr-2 text-blue-600" />
                                    {{ group.title }}
                                </div>
                                <component :is="group.open ? ChevronDown : ChevronRight"
                                    class="w-5 h-5 text-gray-500" />
                            </button>
                            <transition name="fade">
                                <div v-show="group.open" class="mt-1 space-y-1 pl-6">
                                    <button v-for="step in group.children" :key="step.id"
                                        @click="activeProcess = step.id"
                                        :class="activeProcess === step.id ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600' : 'hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                        class="w-full text-left p-2 rounded-lg text-sm transition">
                                        {{ step.name }}
                                        <span v-if="step.sub" class="text-xs text-gray-500">{{ step.sub }}</span>
                                    </button>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Panel -->
            <div
                class="lg:col-span-8 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div v-if="currentStepData">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                            <component :is="currentStepData.icon || currentStepData.groupIcon"
                                class="w-6 h-6 mr-2 text-blue-600" />
                            {{ currentStepData.name }}
                        </h3>
                        <button v-if="activeProcess === 'reports'"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold">Export</button>
                    </div>

                    <!-- ══ MATERIAL REQUEST ══ -->
                    <div v-if="activeProcess === 'material_request'" class="space-y-6">
                        <!-- Order Selector -->
                        <div class="flex gap-2 flex-wrap">
                            <button v-for="order in pendingOrders" :key="order.id" @click="selectOrder(order)"
                                :class="selectedOrder?.id === order.id ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                {{ order.id }}
                            </button>
                        </div>
                        <div v-if="selectedOrder" class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-sm">
                            <p class="font-bold text-blue-800 dark:text-blue-300">{{ selectedOrder.productName }}</p>
                            <p class="text-blue-600 dark:text-blue-400">Target Qty: {{
                                selectedOrder.targetQty.toLocaleString() }} pcs &nbsp;|&nbsp; Deadline: {{
                                    selectedOrder.deadline }}</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-4 py-3">Material</th>
                                        <th class="px-4 py-3">Type</th>
                                        <th class="px-4 py-3">Needed</th>
                                        <th class="px-4 py-3">In Stock</th>
                                        <th class="px-4 py-3">Gap</th>
                                        <th class="px-4 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="mat in materialNeeds" :key="mat.id"
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ mat.name }}
                                        </td>
                                        <td class="px-4 py-3">{{ mat.type }}</td>
                                        <td class="px-4 py-3">{{ mat.needed }} {{ mat.unit }}</td>
                                        <td class="px-4 py-3">{{ mat.stock }} {{ mat.unit }}</td>
                                        <td class="px-4 py-3">
                                            <span
                                                :class="(mat.stock < mat.needed) ? 'text-red-600 bg-red-50 dark:bg-red-900/20' : 'text-green-600 bg-green-50 dark:bg-green-900/20'"
                                                class="px-2 py-1 rounded text-xs font-medium">
                                                {{ (mat.stock < mat.needed) ? '-' + (mat.needed - mat.stock).toFixed(2)
                                                    : 'Sufficient' }} {{ (mat.stock < mat.needed) ? mat.unit : '' }}
                                                    </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button @click="openSourcing(mat)"
                                                class="text-xs text-blue-600 font-semibold hover:underline">Make
                                                Quotation</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ══ REQUEST FOR QUOTATION ══ -->
                    <div v-else-if="activeProcess === 'rfq'" class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">RFQ ID</th>
                                    <th class="px-4 py-3">Material</th>
                                    <th class="px-4 py-3">Qty</th>
                                    <th class="px-4 py-3">Sent To</th>
                                    <th class="px-4 py-3">Sent Date</th>
                                    <th class="px-4 py-3">Due Date</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="rfq in rfqList" :key="rfq.id"
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ rfq.id
                                    }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ rfq.material }}
                                    </td>
                                    <td class="px-4 py-3">{{ rfq.qty }} {{ rfq.unit }}</td>
                                    <td class="px-4 py-3">{{ rfq.sentTo }}</td>
                                    <td class="px-4 py-3">{{ rfq.sentDate }}</td>
                                    <td class="px-4 py-3">{{ rfq.dueDate }}</td>
                                    <td class="px-4 py-3">
                                        <span :class="statusColor(rfq.status)"
                                            class="px-2 py-1 rounded text-xs font-medium">{{ rfq.status }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ══ SUPPLIER QUOTATION ══ -->
                    <div v-else-if="activeProcess === 'quotation'" class="space-y-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Comparing quotations received — recommended
                            vendor highlighted.</p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-4 py-3">QT ID</th>
                                        <th class="px-4 py-3">Vendor</th>
                                        <th class="px-4 py-3">Material</th>
                                        <th class="px-4 py-3">Unit Price</th>
                                        <th class="px-4 py-3">Total</th>
                                        <th class="px-4 py-3">Lead Time</th>
                                        <th class="px-4 py-3">Terms</th>
                                        <th class="px-4 py-3">Rating</th>
                                        <th class="px-4 py-3">Pick</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="qt in quotations" :key="qt.id"
                                        :class="qt.recommended ? 'bg-green-50 dark:bg-green-900/10' : 'bg-white dark:bg-gray-800'"
                                        class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{
                                            qt.id }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                            {{ qt.vendor }}
                                            <span v-if="qt.recommended" class="ml-1 text-xs text-green-600 font-bold">★
                                                Best</span>
                                        </td>
                                        <td class="px-4 py-3">{{ qt.material }}</td>
                                        <td class="px-4 py-3">${{ qt.unitPrice.toFixed(2) }}</td>
                                        <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white">${{
                                            qt.totalPrice.toLocaleString() }}</td>
                                        <td class="px-4 py-3">{{ qt.leadTime }}</td>
                                        <td class="px-4 py-3">{{ qt.terms }}</td>
                                        <td class="px-4 py-3">⭐ {{ qt.rating }}</td>
                                        <td class="px-4 py-3">
                                            <button
                                                class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition">Select</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ══ SUPPLIER / VENDOR MANAGEMENT ══ -->
                    <div v-else-if="activeProcess === 'supplier'" class="space-y-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Registered vendors available for sourcing.
                        </p>
                        <div class="grid grid-cols-1 gap-4">
                            <div v-for="vendor in vendorQuotes" :key="vendor.id"
                                class="flex items-center justify-between p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 hover:shadow-md transition">
                                <div>
                                    <p class="font-bold text-gray-800 dark:text-white">{{ vendor.name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Lead Time: {{ vendor.leadTime }}
                                        &nbsp;|&nbsp; Unit Price: ${{ vendor.price.toFixed(2) }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm">⭐ {{ vendor.rating }}</span>
                                    <span
                                        :class="vendor.status === 'Preferred' ? 'text-blue-600 bg-blue-50 dark:bg-blue-900/20' : vendor.status === 'Verified' ? 'text-green-600 bg-green-50 dark:bg-green-900/20' : 'text-purple-600 bg-purple-50 dark:bg-purple-900/20'"
                                        class="px-2 py-1 rounded text-xs font-semibold">{{ vendor.status }}</span>
                                    <button class="text-xs text-blue-600 font-semibold hover:underline">View</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══ PURCHASE ORDER ══ -->
                    <div v-else-if="activeProcess === 'purchase_order'" class="space-y-4">
                        <div v-for="po in purchaseOrders" :key="po.id"
                            class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700 cursor-pointer"
                                @click="selectedPO = selectedPO?.id === po.id ? null : po">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-xs font-bold text-gray-700 dark:text-gray-300">{{ po.id
                                    }}</span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-white">{{ po.vendor
                                    }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-500">Delivery: {{ po.deliveryDate }}</span>
                                    <span :class="statusColor(po.status)"
                                        class="px-2 py-1 rounded text-xs font-medium">{{ po.status }}</span>
                                    <component :is="selectedPO?.id === po.id ? ChevronDown : ChevronRight"
                                        class="w-4 h-4 text-gray-400" />
                                </div>
                            </div>
                            <div v-if="selectedPO?.id === po.id" class="px-4 pb-4 bg-white dark:bg-gray-800">
                                <table class="w-full text-sm mt-3">
                                    <thead class="text-xs text-gray-500 uppercase">
                                        <tr>
                                            <th class="py-2 text-left">Item</th>
                                            <th class="py-2 text-right">Qty</th>
                                            <th class="py-2 text-right">Unit Price</th>
                                            <th class="py-2 text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in po.items" :key="i"
                                            class="border-t dark:border-gray-700">
                                            <td class="py-2 text-gray-800 dark:text-white">{{ item.name }}</td>
                                            <td class="py-2 text-right text-gray-600 dark:text-gray-400">{{ item.qty }}
                                                {{ item.unit }}</td>
                                            <td class="py-2 text-right text-gray-600 dark:text-gray-400">${{
                                                item.unitPrice.toFixed(2) }}</td>
                                            <td class="py-2 text-right font-semibold text-gray-800 dark:text-white">${{
                                                item.total.toLocaleString() }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="mt-3 text-right text-sm space-y-1">
                                    <p class="text-gray-500">Subtotal: <span
                                            class="text-gray-800 dark:text-white font-medium">${{
                                                po.subtotal.toLocaleString() }}</span></p>
                                    <p class="text-gray-500">Tax (10%): <span
                                            class="text-gray-800 dark:text-white font-medium">${{
                                                po.tax.toLocaleString() }}</span></p>
                                    <p class="text-blue-600 font-bold">Grand Total: ${{ po.grandTotal.toLocaleString()
                                    }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══ PURCHASE RECEIPT ══ -->
                    <div v-else-if="activeProcess === 'purchase_receipt'" class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">GRN ID</th>
                                    <th class="px-4 py-3">PO Ref</th>
                                    <th class="px-4 py-3">Vendor</th>
                                    <th class="px-4 py-3">Item</th>
                                    <th class="px-4 py-3">Ordered</th>
                                    <th class="px-4 py-3">Received</th>
                                    <th class="px-4 py-3">Condition</th>
                                    <th class="px-4 py-3">Receipt Status</th>
                                    <th class="px-4 py-3">QC</th>
                                    <th class="px-4 py-3">Received By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="grn in purchaseReceipts" :key="grn.id"
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ grn.id
                                    }}</td>
                                    <td class="px-4 py-3">{{ grn.poRef }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ grn.vendor }}
                                    </td>
                                    <td class="px-4 py-3">{{ grn.items[0]?.name }}</td>
                                    <td class="px-4 py-3">{{ grn.items[0]?.ordered }} {{ grn.items[0]?.unit }}</td>
                                    <td class="px-4 py-3">{{ grn.items[0]?.received }} {{ grn.items[0]?.unit }}</td>
                                    <td class="px-4 py-3">{{ grn.items[0]?.condition }}</td>
                                    <td class="px-4 py-3">
                                        <span :class="statusColor(grn.status)"
                                            class="px-2 py-1 rounded text-xs font-medium">{{ grn.status }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span :class="statusColor(grn.qcStatus)"
                                            class="px-2 py-1 rounded text-xs font-medium">{{ grn.qcStatus }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ grn.receivedBy }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ══ PURCHASE INVOICE ══ -->
                    <div v-else-if="activeProcess === 'purchase_invoice'" class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Invoice ID</th>
                                    <th class="px-4 py-3">PO Ref</th>
                                    <th class="px-4 py-3">Vendor</th>
                                    <th class="px-4 py-3">Invoice Date</th>
                                    <th class="px-4 py-3">Due Date</th>
                                    <th class="px-4 py-3">Amount</th>
                                    <th class="px-4 py-3">Terms</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="inv in purchaseInvoices" :key="inv.id"
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ inv.id
                                    }}</td>
                                    <td class="px-4 py-3">{{ inv.poRef }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ inv.vendor }}
                                    </td>
                                    <td class="px-4 py-3">{{ inv.invoiceDate }}</td>
                                    <td class="px-4 py-3">{{ inv.dueDate }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white">${{
                                        inv.amount.toLocaleString() }}</td>
                                    <td class="px-4 py-3">{{ inv.paymentTerms }}</td>
                                    <td class="px-4 py-3">
                                        <span :class="statusColor(inv.status)"
                                            class="px-2 py-1 rounded text-xs font-medium">{{ inv.status }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <button class="text-xs text-blue-600 hover:underline font-semibold">{{
                                            inv.status === 'Paid' ? 'View' : 'Pay Now' }}</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ══ MAKE PAYMENT ══ -->
                    <div v-else-if="activeProcess === 'payment'" class="space-y-6">
                        <!-- Payment Queue -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Payment Queue</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th class="px-4 py-3">Invoice Ref</th>
                                            <th class="px-4 py-3">Vendor</th>
                                            <th class="px-4 py-3">Due Date</th>
                                            <th class="px-4 py-3">Amount</th>
                                            <th class="px-4 py-3">Priority</th>
                                            <th class="px-4 py-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="pq in paymentQueue" :key="pq.invoiceRef"
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{
                                                pq.invoiceRef }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ pq.vendor
                                            }}</td>
                                            <td class="px-4 py-3">{{ pq.dueDate }}</td>
                                            <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white">${{
                                                pq.amount.toLocaleString() }}</td>
                                            <td class="px-4 py-3">
                                                <span :class="statusColor(pq.priority)"
                                                    class="px-2 py-1 rounded text-xs font-medium">{{ pq.priority
                                                    }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <button
                                                    class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">Pay</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Completed Payments -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Completed Payments</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th class="px-4 py-3">Payment ID</th>
                                            <th class="px-4 py-3">Invoice Ref</th>
                                            <th class="px-4 py-3">Vendor</th>
                                            <th class="px-4 py-3">Paid Date</th>
                                            <th class="px-4 py-3">Amount</th>
                                            <th class="px-4 py-3">Method</th>
                                            <th class="px-4 py-3">Reference</th>
                                            <th class="px-4 py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="pay in payments" :key="pay.id"
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{
                                                pay.id }}</td>
                                            <td class="px-4 py-3">{{ pay.invoiceRef }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{
                                                pay.vendor }}</td>
                                            <td class="px-4 py-3">{{ pay.paidDate }}</td>
                                            <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white">${{
                                                pay.amount.toLocaleString() }}</td>
                                            <td class="px-4 py-3">{{ pay.method }}</td>
                                            <td class="px-4 py-3 font-mono text-xs">{{ pay.reference }}</td>
                                            <td class="px-4 py-3">
                                                <span :class="statusColor(pay.status)"
                                                    class="px-2 py-1 rounded text-xs font-medium">{{ pay.status
                                                    }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ══ REQUESTED ITEMS REPORT ══ -->
                    <div v-else-if="activeProcess === 'reports'" class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">#</th>
                                    <th scope="col" class="px-6 py-3">Material</th>
                                    <th scope="col" class="px-6 py-3">Qty</th>
                                    <th scope="col" class="px-6 py-3">Requested By</th>
                                    <th scope="col" class="px-6 py-3">Request Date</th>
                                    <th scope="col" class="px-6 py-3">PO Ref</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Operate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in reportItems" :key="item.id"
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4">{{ item.id }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ item.material }}
                                    </td>
                                    <td class="px-6 py-4">{{ item.requestedQty }} {{ item.unit }}</td>
                                    <td class="px-6 py-4">{{ item.requestedBy }}</td>
                                    <td class="px-6 py-4">{{ item.requestDate }}</td>
                                    <td class="px-6 py-4">{{ item.poRef }}</td>
                                    <td class="px-6 py-4">
                                        <span :class="statusColor(item.status)"
                                            class="px-2 py-1 rounded text-xs font-medium">{{ item.status }}</span>
                                    </td>
                                    <td class="px-6 py-4 flex gap-2">
                                        <button class="text-blue-600 hover:text-blue-900 text-xs">Notice</button>
                                        <button class="text-blue-600 hover:text-blue-900 text-xs">Reply</button>
                                        <button class="text-blue-600 hover:text-blue-900 text-xs">Cancel</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ══ FALLBACK ══ -->
                    <div v-else class="text-center text-gray-500 py-10">
                        Content for {{ currentStepData.name }} goes here.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>