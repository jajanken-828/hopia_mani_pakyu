<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import {
    Search,
    Filter,
    ArrowDownToLine,
    ShieldCheck,
    ChevronRight,
    Truck,
    PackageCheck,
    Clock,
    User,
    ExternalLink,
    Printer,
    MoreHorizontal,
    ShoppingBag,
    AlertCircle,
    ClipboardList,
    X,
    Building2,
    FileText,
    Tag,
    Wallet,
    Info
} from 'lucide-vue-next';

const props = defineProps({
    orders: {
        type: Object,
        default: () => ({ data: [], links: [], meta: {} }),
    },
    filters: { type: Object, default: () => ({ search: '' }) },
});

// --- State Management ---
const search = ref(props.filters.search);
const activeTab = ref('all');
const showMonitorModal = ref(false);
const selectedOrder = ref(null);

// --- Workflow Helpers ---
const filteredOrders = computed(() => {
    if (activeTab.value === 'all') return props.orders.data;
    return props.orders.data.filter(order => order.status === activeTab.value);
});

const getStatusStyles = (status) => {
    switch (status) {
        case 'credit_review': return 'bg-orange-50 text-orange-600 border-orange-100';
        case 'tier_assignment': return 'bg-blue-50 text-blue-600 border-blue-100';
        case 'pending_client_approval': return 'bg-purple-50 text-purple-600 border-purple-100';
        case 'approved': return 'bg-emerald-50 text-emerald-600 border-emerald-100';
        case 'rejected': return 'bg-red-50 text-red-600 border-red-100';
        default: return 'bg-gray-50 text-gray-400 border-gray-100';
    }
};

// --- Monitor Logic ---
const openMonitor = (order) => {
    selectedOrder.value = order;
    showMonitorModal.value = true;
};

// --- Stats Logic ---
const stats = computed(() => [
    { label: 'In Review', value: props.orders.data.filter(o => o.status === 'credit_review').length, icon: Clock, color: 'text-orange-500', bg: 'bg-orange-50' },
    { label: 'Tiering Phase', value: props.orders.data.filter(o => o.status === 'tier_assignment').length, icon: ClipboardList, color: 'text-blue-600', bg: 'bg-blue-50' },
    { label: 'Awaiting Client', value: props.orders.data.filter(o => o.status === 'pending_client_approval').length, icon: User, color: 'text-purple-600', bg: 'bg-purple-50' },
    { label: 'Live Approvals', value: props.orders.data.filter(o => o.status === 'approved').length, icon: ShieldCheck, color: 'text-emerald-600', bg: 'bg-emerald-50' },
]);

// Search Debounce
watch(search, (value) => {
    router.get(route('eco.employee.ordermng'), { search: value }, {
        preserveState: true,
        replace: true
    });
});
</script>

<template>

    <Head title="Order Architect - Staff Monitoring" />

    <AuthenticatedLayout>
        <div class="max-w-[1600px] mx-auto space-y-8 p-4 lg:p-10">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <div
                        class="flex items-center gap-2 text-indigo-600 font-black text-[10px] uppercase tracking-[0.2em]">
                        <PackageCheck class="h-3.5 w-3.5" />
                        Fulfillment Monitoring
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter uppercase">
                        Order <span class="text-indigo-600">Architect</span>
                    </h1>
                </div>

                <button
                    class="flex items-center gap-2 px-6 py-3 rounded-[1.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition-all">
                    <ArrowDownToLine class="h-4 w-4" />
                    Export Pipeline
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div v-for="stat in stats" :key="stat.label"
                    class="p-7 rounded-[2.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ stat.label }}</p>
                    <div class="flex items-center justify-between">
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ stat.value }}
                        </h3>
                        <div :class="stat.bg" class="p-2.5 rounded-xl transition-colors">
                            <component :is="stat.icon" :class="stat.color" class="h-6 w-6" />
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div
                    class="p-8 border-b border-gray-50 dark:border-gray-800 flex flex-col lg:flex-row justify-between items-center gap-6">
                    <div
                        class="flex p-1.5 bg-gray-50 dark:bg-gray-950 rounded-2xl w-full lg:w-auto overflow-x-auto no-scrollbar">
                        <button
                            v-for="tab in ['all', 'credit_review', 'tier_assignment', 'pending_client_approval', 'approved']"
                            :key="tab" @click="activeTab = tab"
                            :class="activeTab === tab ? 'bg-white dark:bg-gray-800 shadow-sm text-indigo-600' : 'text-gray-400'"
                            class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap">
                            {{ tab.replace(/_/g, ' ') }}
                        </button>
                    </div>

                    <div class="relative w-full lg:w-80 group">
                        <Search
                            class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors" />
                        <input v-model="search" type="text" placeholder="Search PO # or Client..."
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-950 text-[10px] font-black uppercase tracking-widest">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-gray-800/30 text-[10px] font-black uppercase text-gray-400 tracking-[0.15em]">
                                <th class="px-8 py-5 italic">Purchase Order & Client</th>
                                <th class="px-8 py-5">Workflow Valuation</th>
                                <th class="px-8 py-5 text-center">Status Index</th>
                                <th class="px-8 py-5 text-right px-10">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <tr v-for="order in filteredOrders" :key="order.id"
                                class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all">
                                <td class="px-8 py-7">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-12 w-12 rounded-[1rem] bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 border border-indigo-100 dark:border-indigo-800 shadow-sm">
                                            <ShoppingBag class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter italic">
                                                {{ order.po_number }}</p>
                                            <p
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">
                                                {{ order.client?.company_name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-7">
                                    <div class="flex flex-col">
                                        <p class="text-xs font-black text-gray-700 dark:text-gray-300">₱{{
                                            parseFloat(order.total_amount).toLocaleString() }}</p>
                                        <p class="text-[9px] font-bold text-indigo-500 uppercase italic">Tier: {{
                                            order.tier_level || 'Calculating...' }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-7 text-center">
                                    <span :class="getStatusStyles(order.status)"
                                        class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border">
                                        {{ order.status.replace(/_/g, ' ') }}
                                    </span>
                                </td>
                                <td class="px-8 py-7 text-right px-10">
                                    <button @click="openMonitor(order)"
                                        class="flex items-center gap-2 ml-auto px-5 py-2.5 bg-gray-950 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-all active:scale-95 shadow-lg shadow-black/10">
                                        Monitor
                                        <ExternalLink class="h-3.5 w-3.5" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="showMonitorModal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-md transition-opacity duration-300">
            <div
                class="bg-white dark:bg-gray-900 w-full max-w-4xl rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">

                <div class="px-10 py-8 bg-indigo-600 text-white flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div
                            class="h-12 w-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-sm">
                            <Info class="h-6 w-6" />
                        </div>
                        <div>
                            <h3 class="text-2xl font-black uppercase tracking-tighter italic">Order Breakdown</h3>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80">Monitoring PO #{{
                                selectedOrder.po_number }}</p>
                        </div>
                    </div>
                    <button @click="showMonitorModal = false"
                        class="p-3 bg-white/10 rounded-2xl hover:bg-white/20 transition-all">
                        <X class="h-6 w-6" />
                    </button>
                </div>

                <div class="p-10 grid grid-cols-1 lg:grid-cols-12 gap-10">

                    <div class="lg:col-span-7 space-y-8">
                        <div class="space-y-4">
                            <div
                                class="flex items-center gap-2 text-indigo-600 text-[10px] font-black uppercase tracking-widest">
                                <Building2 class="h-3.5 w-3.5" />
                                Partner Information
                            </div>
                            <div
                                class="p-6 bg-gray-50 dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700/50">
                                <h4 class="text-lg font-black text-gray-900 dark:text-white uppercase italic">{{
                                    selectedOrder.client?.company_name }}</h4>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">Status: {{
                                    selectedOrder.client?.status || 'Active' }} Partner</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div
                                class="flex items-center gap-2 text-indigo-600 text-[10px] font-black uppercase tracking-widest">
                                <FileText class="h-3.5 w-3.5" />
                                Manifest Breakdown
                            </div>
                            <div class="space-y-3 max-h-[300px] overflow-y-auto no-scrollbar pr-2">
                                <div v-for="item in selectedOrder.items" :key="item.id"
                                    class="flex items-center justify-between p-4 bg-white dark:bg-gray-850 rounded-2xl border border-gray-50 shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-10 w-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                                            <PackageCheck class="h-5 w-5" />
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-gray-900 dark:text-white uppercase">{{
                                                item.product?.name || 'Textile SKU' }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                Qty: {{ item.quantity }} × ₱{{
                                                parseFloat(item.unit_price).toLocaleString() }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs font-black text-gray-900 dark:text-white italic">₱{{
                                        (item.quantity * item.unit_price).toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div
                            class="bg-gray-50 dark:bg-gray-800 rounded-[3rem] p-8 border border-indigo-50 dark:border-indigo-900/20 flex flex-col h-full">
                            <h5
                                class="text-[10px] font-black uppercase tracking-widest text-indigo-600 mb-8 border-b border-indigo-100 pb-4">
                                Financial Finalization</h5>

                            <div class="space-y-6 flex-grow">
                                <div class="flex justify-between items-center group">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 bg-white rounded-lg shadow-sm">
                                            <Wallet class="h-4 w-4 text-gray-400" />
                                        </div>
                                        <span class="text-[10px] font-black text-gray-500 uppercase">Subtotal</span>
                                    </div>
                                    <span class="text-sm font-black text-gray-900 dark:text-white italic">₱{{
                                        parseFloat(selectedOrder.subtotal).toLocaleString() }}</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 bg-emerald-50 rounded-lg shadow-sm">
                                            <Tag class="h-4 w-4 text-emerald-600" />
                                        </div>
                                        <span class="text-[10px] font-black text-emerald-600 uppercase">{{
                                            selectedOrder.tier_level || 'Normal' }} Tier Save</span>
                                    </div>
                                    <span class="text-sm font-black text-emerald-600 italic">-₱{{
                                        parseFloat(selectedOrder.discount_amount).toLocaleString() }}</span>
                                </div>

                                <div class="pt-6 border-t border-dashed border-gray-200 flex justify-between items-end">
                                    <div>
                                        <span
                                            class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] block mb-1">Final
                                            Payable</span>
                                        <p
                                            class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter italic">
                                            ₱{{ parseFloat(selectedOrder.total_amount).toLocaleString() }}</p>
                                    </div>
                                    <div
                                        class="h-10 w-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white rotate-12 shadow-lg shadow-indigo-500/30">
                                        <ShieldCheck class="h-6 w-6" />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-10 pt-8 border-t border-indigo-100 dark:border-indigo-900/50">
                                <p class="text-[9px] font-bold text-gray-400 uppercase italic leading-relaxed">
                                    Authorized ERP Verification: This monitoring log represents a live database snapshot
                                    of the B2B fulfillment workflow.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="px-10 py-8 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                    <button
                        class="px-8 py-3.5 rounded-2xl bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 text-[10px] font-black uppercase tracking-widest text-gray-600 hover:bg-gray-100 transition-all flex items-center gap-2">
                        <Printer class="h-4 w-4" />
                        Print Manifest
                    </button>
                    <button @click="showMonitorModal = false"
                        class="px-10 py-3.5 rounded-2xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-black uppercase tracking-widest shadow-xl hover:scale-105 active:scale-95 transition-all">
                        Close Log
                    </button>
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