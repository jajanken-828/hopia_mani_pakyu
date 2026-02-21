<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
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
    AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
    orders: {
        type: Object,
        default: () => ({ data: [], links: {}, meta: {} }),
    },
    filters: { type: Object, default: () => ({ search: '' }) },
});

// Tab State for Order Queue
const activeTab = ref('pending'); // 'pending', 'processing', 'shipped', 'completed'

// Mock Data for Order Architect
const orderList = ref([
    { id: 'ORD-8821', partner: 'TechLogistics Corp', item_count: 12, total: '₱14,200.00', status: 'pending', date: '2 Mins Ago', priority: 'high' },
    { id: 'ORD-8819', partner: 'Manila Build Supplies', item_count: 5, total: '₱5,450.00', status: 'processing', date: '45 Mins Ago', priority: 'normal' },
    { id: 'ORD-8815', partner: 'Cebu Textile Hub', item_count: 24, total: '₱42,000.00', status: 'shipped', date: '3 Hours Ago', priority: 'normal' },
]);

const stats = computed(() => [
    { label: 'Orders To Process', value: '08', icon: Clock, color: 'text-orange-500', bg: 'bg-orange-50' },
    { label: 'In-Transit Today', value: '14', icon: Truck, color: 'text-blue-600', bg: 'bg-blue-50' },
    { label: 'Success Rate', value: '99.2%', icon: ShieldCheck, color: 'text-emerald-600', bg: 'bg-emerald-50' },
    { label: 'Daily Volume', value: '₱124K', icon: ShoppingBag, color: 'text-indigo-600', bg: 'bg-indigo-50' },
]);

const getStatusStyles = (status) => {
    switch (status) {
        case 'pending': return 'bg-orange-50 text-orange-600 border-orange-100';
        case 'processing': return 'bg-blue-50 text-blue-600 border-blue-100';
        case 'shipped': return 'bg-indigo-50 text-indigo-600 border-indigo-100';
        case 'completed': return 'bg-emerald-50 text-emerald-600 border-emerald-100';
        default: return 'bg-gray-50 text-gray-400 border-gray-100';
    }
};

const updateStatus = (id, newStatus) => {
    console.log(`Updating ${id} to ${newStatus}`);
    // router.patch(route('eco.staff.orders.update', id), { status: newStatus });
};
</script>

<template>

    <Head title="Order Architect - Staff Portal" />

    <AuthenticatedLayout>
        <div class="max-w-[1600px] mx-auto space-y-8 p-4 lg:p-10">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <div
                        class="flex items-center gap-2 text-indigo-600 font-black text-[10px] uppercase tracking-[0.2em]">
                        <PackageCheck class="h-3.5 w-3.5" />
                        Fulfillment & Logistics
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter uppercase">
                        Order <span class="text-indigo-600">Architect</span>
                    </h1>
                    <p class="text-sm font-medium text-gray-500 italic">
                        Real-time orchestration of wholesale textile fulfillment
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        class="flex items-center gap-2 px-6 py-3 rounded-[1.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition-all">
                        <Printer class="h-4 w-4" />
                        Batch Packing Slips
                    </button>
                    <button
                        class="flex items-center gap-2 px-6 py-3 rounded-[1.5rem] bg-indigo-600 text-white shadow-lg shadow-indigo-500/20 text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all">
                        <ArrowDownToLine class="h-4 w-4" />
                        Manifest Export
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div v-for="stat in stats" :key="stat.label"
                    class="p-7 rounded-[2.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ stat.label }}</p>
                    <div class="flex items-center justify-between">
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{
                            stat.value }}</h3>
                        <div :class="stat.bg" class="p-2.5 rounded-xl">
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
                        <button v-for="tab in ['pending', 'processing', 'shipped', 'completed']" :key="tab"
                            @click="activeTab = tab"
                            :class="activeTab === tab ? 'bg-white dark:bg-gray-800 shadow-sm text-indigo-600' : 'text-gray-400'"
                            class="flex-1 lg:flex-none px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                            {{ tab }}
                        </button>
                    </div>

                    <div class="flex items-center gap-4 w-full lg:w-auto">
                        <div class="relative flex-1 lg:w-80 group">
                            <Search
                                class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors" />
                            <input type="text" placeholder="Search Order ID or Client..."
                                class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-950 text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder:text-gray-400">
                        </div>
                        <button
                            class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-indigo-600 transition-colors">
                            <Filter class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-gray-800/30 text-[10px] font-black uppercase text-gray-400 tracking-[0.15em]">
                                <th class="px-8 py-5">Order ID & Liaison</th>
                                <th class="px-8 py-5">Fulfillment Load</th>
                                <th class="px-8 py-5">Total Valuation</th>
                                <th class="px-8 py-5 text-center">Status Index</th>
                                <th class="px-8 py-5 text-right px-10">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <tr v-for="order in orderList" :key="order.id"
                                class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all">
                                <td class="px-8 py-7">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-12 w-12 rounded-[1rem] bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 border border-indigo-100 dark:border-indigo-800/50 shadow-sm relative">
                                            <User class="h-6 w-6" />
                                            <span v-if="order.priority === 'high'"
                                                class="absolute -top-1 -right-1 h-3 w-3 bg-rose-500 rounded-full border-2 border-white"></span>
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                                                {{ order.id }}</p>
                                            <p
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">
                                                {{ order.partner }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-7">
                                    <div class="flex flex-col">
                                        <p
                                            class="text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-tight">
                                            {{ order.item_count }} Items</p>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase flex items-center gap-1">
                                            <Clock class="h-3 w-3" /> Received {{ order.date }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-8 py-7">
                                    <p class="text-sm font-black text-gray-900 dark:text-white italic tracking-tighter">
                                        {{ order.total }}</p>
                                </td>
                                <td class="px-8 py-7">
                                    <div class="flex justify-center">
                                        <span :class="getStatusStyles(order.status)"
                                            class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border transition-all">
                                            {{ order.status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-7">
                                    <div class="flex justify-end gap-2 px-2">
                                        <button v-if="order.status === 'pending'"
                                            @click="updateStatus(order.id, 'processing')"
                                            class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-blue-600 transition-all"
                                            title="Move to Processing">
                                            <ChevronRight class="h-4 w-4" />
                                        </button>
                                        <button v-if="order.status === 'processing'"
                                            @click="updateStatus(order.id, 'shipped')"
                                            class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-indigo-600 transition-all"
                                            title="Dispatch Order">
                                            <Truck class="h-4 w-4" />
                                        </button>
                                        <button
                                            class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-gray-900 transition-all">
                                            <Printer class="h-4 w-4" />
                                        </button>
                                        <button
                                            class="flex items-center gap-2 px-5 py-2.5 bg-gray-950 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-md">
                                            Review Details
                                            <ExternalLink class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="px-8 py-6 border-t border-gray-50 dark:border-gray-800 flex items-center justify-between">
                    <p
                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic flex items-center gap-2">
                        <AlertCircle class="h-3.5 w-3.5 text-indigo-500" />
                        8 orders currently require manual pick-list generation.
                    </p>
                    <div class="flex gap-2">
                        <button
                            class="px-4 py-2 bg-gray-50 dark:bg-gray-800 rounded-lg text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-indigo-600 transition-all">Prev</button>
                        <button
                            class="px-4 py-2 bg-gray-950 dark:bg-gray-800 rounded-lg text-[10px] font-black uppercase tracking-widest text-white transition-all">Next</button>
                    </div>
                </div>
            </div>

            <div
                class="flex flex-col md:flex-row items-center justify-between p-8 rounded-[2.5rem] bg-indigo-50 dark:bg-indigo-900/10 border border-dashed border-indigo-200 dark:border-indigo-800">
                <div class="flex items-center gap-5 text-center md:text-left mb-4 md:mb-0">
                    <div
                        class="h-14 w-14 rounded-[1.5rem] bg-white dark:bg-gray-900 flex items-center justify-center text-indigo-600 shadow-sm">
                        <Printer class="h-7 w-7" />
                    </div>
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-tight text-gray-900 dark:text-white">Batch
                            Fulfillment Active</h4>
                        <p class="text-xs font-medium text-gray-500 italic">Generate and print all packing slips for
                            "Processing" orders in one click.</p>
                    </div>
                </div>
                <button
                    class="flex items-center gap-2 text-xs font-black text-indigo-600 uppercase tracking-widest hover:underline px-8 py-3 rounded-2xl bg-white dark:bg-gray-900 shadow-sm active:scale-95 transition-all">
                    Generate Batch Slips
                    <Printer class="h-4 w-4" />
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.tracking-tighter {
    letter-spacing: -0.05em;
}

.tracking-tight {
    letter-spacing: -0.02em;
}

::-webkit-scrollbar {
    width: 5px;
    height: 5px;
}

::-webkit-scrollbar-thumb {
    background: #E5E7EB;
    border-radius: 10px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>