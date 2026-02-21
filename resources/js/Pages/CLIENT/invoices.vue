<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    Receipt,
    Download,
    ExternalLink,
    Search,
    Filter,
    CheckCircle2,
    Clock,
    AlertCircle,
    ArrowUpRight,
    FileText
} from 'lucide-vue-next';

const page = usePage();
const client = computed(() => page.props.auth?.client);

// Sample Invoice Data
const invoices = ref([
    { id: 'INV-2026-001', date: 'Feb 15, 2026', due: 'Mar 15, 2026', amount: '45,200.00', status: 'Paid', method: 'Credit Line' },
    { id: 'INV-2026-002', date: 'Feb 10, 2026', due: 'Mar 10, 2026', amount: '12,850.00', status: 'Pending', method: 'Bank Transfer' },
    { id: 'INV-2026-003', date: 'Jan 28, 2026', due: 'Feb 28, 2026', amount: '89,400.00', status: 'Overdue', method: 'Credit Line' },
]);

const getStatusClass = (status) => {
    switch (status) {
        case 'Paid': return 'bg-green-50 text-green-600 dark:bg-green-900/20';
        case 'Pending': return 'bg-orange-50 text-orange-600 dark:bg-orange-900/20';
        case 'Overdue': return 'bg-red-50 text-red-600 dark:bg-red-900/20';
        default: return 'bg-gray-50 text-gray-600';
    }
};
</script>

<template>

    <Head title="Invoices - Partner Portal" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto space-y-8">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <div
                        class="flex items-center gap-2 text-blue-600 font-black text-[10px] uppercase tracking-[0.2em]">
                        <Receipt class="h-3.5 w-3.5" />
                        Billing & Financials
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase">
                        Your <span class="text-blue-600">Invoices</span>
                    </h1>
                    <p class="text-sm font-medium text-gray-500">
                        View and download statements for {{ client?.company_name }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        class="flex items-center gap-2 px-5 py-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm text-xs font-black uppercase tracking-widest text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition-all">
                        <Download class="h-4 w-4" />
                        Statement Export
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="p-6 rounded-[2.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Outstanding</p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">₱102,250.00
                    </h3>
                    <div
                        class="mt-4 flex items-center gap-2 text-[10px] font-bold text-orange-600 bg-orange-50 dark:bg-orange-900/20 w-fit px-2 py-1 rounded-lg">
                        <Clock class="h-3 w-3" /> 2 Invoices Pending
                    </div>
                </div>

                <div
                    class="p-6 rounded-[2.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Credit Utilization
                    </p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">42% Used
                    </h3>
                    <div class="mt-4 w-full bg-gray-100 dark:bg-gray-800 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-blue-600 h-full w-[42%]"></div>
                    </div>
                </div>

                <div class="p-6 rounded-[2.5rem] bg-blue-600 shadow-xl shadow-blue-500/20 text-white">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-1">Last Payment</p>
                    <h3 class="text-2xl font-black uppercase tracking-tighter">₱45,200.00</h3>
                    <p class="mt-4 text-[10px] font-bold opacity-90 uppercase">Processed on Feb 15, 2026</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div
                    class="p-8 border-b border-gray-50 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="relative w-full sm:w-72 group">
                        <Search
                            class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-blue-600 transition-colors" />
                        <input type="text" placeholder="Search Invoice ID..."
                            class="w-full pl-11 pr-4 py-3 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-950 text-xs font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                    </div>
                    <div class="flex items-center gap-2">
                        <Filter class="h-4 w-4 text-gray-400" />
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Filter by
                            Status</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-gray-800/30 text-[10px] font-black uppercase text-gray-400 tracking-[0.15em]">
                                <th class="px-8 py-5">Invoice Details</th>
                                <th class="px-8 py-5">Issue Date</th>
                                <th class="px-8 py-5">Due Date</th>
                                <th class="px-8 py-5 text-right">Amount</th>
                                <th class="px-8 py-5 text-center">Status</th>
                                <th class="px-8 py-5 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <tr v-for="invoice in invoices" :key="invoice.id"
                                class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-gray-800 flex items-center justify-center text-gray-500">
                                            <FileText class="h-5 w-5" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                                                {{ invoice.id }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">{{
                                                invoice.method }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-sm font-bold text-gray-500">{{ invoice.date }}</td>
                                <td class="px-8 py-6 text-sm font-bold text-gray-400">{{ invoice.due }}</td>
                                <td class="px-8 py-6 text-right font-black text-gray-900 dark:text-white text-sm">₱{{
                                    invoice.amount }}</td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-center">
                                        <span :class="getStatusClass(invoice.status)"
                                            class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest">
                                            {{ invoice.status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-center gap-2">
                                        <button
                                            class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-blue-600 transition-colors">
                                            <Download class="h-4 w-4" />
                                        </button>
                                        <button
                                            class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-blue-600 transition-colors">
                                            <ExternalLink class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="flex items-center justify-between p-6 rounded-[2rem] bg-slate-50 dark:bg-gray-900/50 border border-dashed border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-4">
                    <div
                        class="h-12 w-12 rounded-2xl bg-white dark:bg-gray-900 flex items-center justify-center text-blue-600 shadow-sm">
                        <AlertCircle class="h-6 w-6" />
                    </div>
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-tight text-gray-900 dark:text-white">Need help
                            with billing?</h4>
                        <p class="text-xs font-medium text-gray-500">Contact our accounts department for credit line
                            extensions or payment inquiries.</p>
                    </div>
                </div>
                <Link href="#"
                    class="flex items-center gap-2 text-xs font-black text-blue-600 uppercase tracking-widest hover:underline">
                    Open Support Ticket
                    <ArrowUpRight class="h-4 w-4" />
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.tracking-tighter {
    letter-spacing: -0.05em;
}
</style>