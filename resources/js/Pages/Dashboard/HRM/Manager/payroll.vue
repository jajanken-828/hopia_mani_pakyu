<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, computed } from 'vue'
import {
    Search,
    Filter,
    MoreHorizontal,
    Download,
    DollarSign,
    CreditCard,
    TrendingUp,
    Clock,
    FileText,
    CheckCircle2,
    Calendar,
    ArrowUpRight
} from 'lucide-vue-next'

const props = defineProps({
    payrollData: {
        type: Array,
        default: () => [
            { id: 'PAY-001', name: 'Alexander Wright', amount: 4500.00, method: 'Direct Deposit', status: 'Processed', date: '2024-02-15' },
            { id: 'PAY-002', name: 'Sarah Jenkins', amount: 3850.50, method: 'Direct Deposit', status: 'Pending', date: '2024-02-15' },
            { id: 'PAY-003', name: 'Michael Chen', amount: 5200.00, method: 'Wire Transfer', status: 'Processed', date: '2024-02-15' },
        ]
    }
})

const searchQuery = ref('')

const filteredPayroll = computed(() => {
    return props.payrollData.filter(item =>
        item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        item.id.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})

const getStatusClass = (status) => {
    switch (status.toLowerCase()) {
        case 'processed': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
        case 'pending': return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
        case 'failed': return 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'
        default: return 'bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-400'
    }
}

const stats = computed(() => [
    { label: 'Total Payroll', value: '$13,550.50', icon: DollarSign, color: 'text-blue-600', bg: 'bg-blue-50' },
    { label: 'Total Deductions', value: '$1,240.00', icon: TrendingUp, color: 'text-rose-600', bg: 'bg-rose-50' },
    { label: 'Upcoming Payments', value: '12 Employees', icon: Clock, color: 'text-amber-600', bg: 'bg-amber-50' },
    { label: 'Last Cycle', value: 'Completed', icon: CheckCircle2, color: 'text-emerald-600', bg: 'bg-emerald-50' },
])
</script>

<template>

    <Head title="Payroll Management" />

    <AuthenticatedLayout>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                    Payroll <span class="text-blue-600">Management</span>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Process salaries, track deductions,
                    and manage disbursements.</p>
            </div>
            <div class="flex gap-3">
                <button
                    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl font-bold text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition-all">
                    <Download class="h-4 w-4" />
                    Export Reports
                </button>
                <button
                    class="flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                    <CreditCard class="h-4 w-4" />
                    Process Payroll
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div v-for="stat in stats" :key="stat.label"
                class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <div class="flex items-center justify-between">
                    <div :class="[stat.bg, 'p-2.5 rounded-xl']">
                        <component :is="stat.icon" :class="[stat.color, 'h-5 w-5']" />
                    </div>
                    <ArrowUpRight class="h-4 w-4 text-slate-300" />
                </div>
                <div class="mt-4">
                    <p class="text-sm font-medium text-slate-500">{{ stat.label }}</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ stat.value }}</p>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <div
                class="p-6 border-b border-slate-50 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="relative w-full md:w-96">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                    <input v-model="searchQuery" type="text" placeholder="Search by ID or employee name..."
                        class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" />
                </div>
                <button
                    class="flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 font-bold text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition-all">
                    <Filter class="h-4 w-4" />
                    Pay Period
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                Transaction ID</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                Employee</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                Amount</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                Method</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                Status</th>
                            <th
                                class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr v-for="pay in filteredPayroll" :key="pay.id"
                            class="group hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-all">
                            <td class="px-6 py-4 text-sm font-bold text-slate-400">
                                {{ pay.id }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center font-bold text-xs mr-3">
                                        {{pay.name.split(' ').map(n => n[0]).join('')}}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ pay.name
                                            }}</span>
                                        <span class="text-[10px] text-slate-400 uppercase tracking-wider">{{ pay.date
                                            }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-black text-slate-900 dark:text-white">${{
                                    pay.amount.toLocaleString() }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                    <CreditCard class="h-3.5 w-3.5 text-slate-400" />
                                    {{ pay.method }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="[getStatusClass(pay.status), 'px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1.5']">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ pay.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div
                                    class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button
                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all"
                                        title="View Payslip">
                                        <FileText class="h-4 w-4" />
                                    </button>
                                    <button
                                        class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-all"
                                        title="Approve">
                                        <CheckCircle2 class="h-4 w-4" />
                                    </button>
                                    <button
                                        class="ml-2 p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-lg">
                                        <MoreHorizontal class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-widest">
                <span>Showing {{ filteredPayroll.length }} Transactions</span>
                <div class="flex gap-2">
                    <button
                        class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 transition-all">Previous</button>
                    <button
                        class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 transition-all">Next</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>