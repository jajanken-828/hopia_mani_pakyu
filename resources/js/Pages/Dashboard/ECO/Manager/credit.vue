<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus,
    Search,
    Pencil,
    Trash2,
    Users,
    CreditCard,
    Calendar,
    DollarSign,
    Check,
    X,
    Eye,
    Clock,
    Filter,
    ArrowDownToLine,
    TrendingUp,
    ShieldCheck,
    ChevronRight,
    Wallet
} from 'lucide-vue-next';

const props = defineProps({
    creditAccounts: {
        type: Object,
        default: () => ({ data: [], links: {}, meta: {} }),
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showScheduleModal = ref(false);
const selectedCreditAccount = ref(null);
const search = ref(props.filters.search);

let searchTimeout;
const updateSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            route('eco.manager.credit'),
            { search: search.value },
            { preserveState: true, replace: true }
        );
    }, 300);
};

const createForm = useForm({
    customer_name: '',
    total_amount: '',
    paid_amount: 0,
    term_type: 'one-time',
    installment_months: 12,
    status: 'active',
});

const editForm = useForm({
    customer_name: '',
    total_amount: '',
    paid_amount: 0,
    term_type: 'one-time',
    installment_months: 12,
    status: 'active',
});

const toggleStatus = (account) => {
    router.patch(route('eco.manager.credit.toggle-status', account.id), {}, { preserveState: true, preserveScroll: true });
};

const viewSchedule = (account) => {
    selectedCreditAccount.value = account;
    showScheduleModal.value = true;
};

const remainingBalance = (account) => (account.total_amount - account.paid_amount).toFixed(2);
const progressPercentage = (account) => account.total_amount === 0 ? 0 : ((account.paid_amount / account.total_amount) * 100).toFixed(1);

const generateSchedule = (account) => {
    if (account.term_type === 'one-time') {
        return [{ due_date: 'Agreement Date', amount: account.total_amount, status: account.paid_amount >= account.total_amount ? 'paid' : 'pending' }];
    } else {
        const monthly = account.total_amount / account.installment_months;
        const schedule = [];
        const today = new Date();
        for (let i = 1; i <= account.installment_months; i++) {
            const due = new Date(today);
            due.setMonth(today.getMonth() + i);
            const formattedDate = due.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            const paidThisInstallment = Math.min(monthly, Math.max(0, account.paid_amount - (i - 1) * monthly));
            schedule.push({
                installment: i,
                due_date: formattedDate,
                amount: monthly.toFixed(2),
                paid: paidThisInstallment > 0 ? paidThisInstallment.toFixed(2) : '0.00',
                status: paidThisInstallment >= monthly ? 'paid' : paidThisInstallment > 0 ? 'partial' : 'pending',
            });
        }
        return schedule;
    }
};

const stats = computed(() => {
    const accounts = props.creditAccounts.data || [];
    const totalOutstanding = accounts.reduce((acc, a) => acc + (a.total_amount - a.paid_amount), 0).toFixed(2);
    const avgProgress = accounts.length ? (accounts.reduce((acc, a) => acc + (a.paid_amount / a.total_amount), 0) / accounts.length * 100).toFixed(1) : 0;
    return [
        { label: 'Credit Accounts', value: accounts.length, icon: Users, color: 'text-blue-600', bg: 'bg-blue-50' },
        { label: 'Active Plans', value: accounts.filter(a => a.status === 'active').length, icon: Check, color: 'text-emerald-600', bg: 'bg-emerald-50' },
        { label: 'Outstanding Balance', value: `₱${totalOutstanding}`, icon: DollarSign, color: 'text-purple-600', bg: 'bg-purple-50' },
        { label: 'Avg. Progress', value: `${avgProgress}%`, icon: TrendingUp, color: 'text-amber-600', bg: 'bg-amber-50' },
    ];
});
</script>

<template>

    <Head title="Credit Management - Partner Portal" />

    <AuthenticatedLayout>
        <div class="max-w-[1600px] mx-auto space-y-8 p-4 lg:p-10">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <div
                        class="flex items-center gap-2 text-indigo-600 font-black text-[10px] uppercase tracking-[0.2em]">
                        <ShieldCheck class="h-3.5 w-3.5" />
                        Financial Exposure & Credit
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter uppercase">
                        Credit <span class="text-indigo-600">Architect</span>
                    </h1>
                    <p class="text-sm font-medium text-gray-500 italic">
                        Monitor outstanding balances and installment schedules
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        class="flex items-center gap-2 px-6 py-3 rounded-[1.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition-all">
                        <ArrowDownToLine class="h-4 w-4" />
                        Export Ledger
                    </button>
                    <button @click="showCreateModal = true"
                        class="flex items-center gap-2 px-6 py-3 rounded-[1.5rem] bg-indigo-600 text-white shadow-lg shadow-indigo-500/20 text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all">
                        <Plus class="h-4 w-4" />
                        New Account
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
                    <div class="flex items-center gap-4 w-full lg:w-auto">
                        <div class="relative flex-1 lg:w-96 group">
                            <Search
                                class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors" />
                            <input v-model="search" @input="updateSearch" type="text"
                                placeholder="Search customer or account ID..."
                                class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-950 text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder:text-gray-400">
                        </div>
                        <button
                            class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-indigo-600 transition-colors">
                            <Filter class="h-5 w-5" />
                        </button>
                    </div>

                    <div
                        class="text-right text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] hidden sm:block">
                        Active Plans: <span class="text-indigo-600">Authenticated Ledger</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-gray-800/30 text-[10px] font-black uppercase text-gray-400 tracking-[0.15em]">
                                <th class="px-8 py-5">Corporate Customer</th>
                                <th class="px-8 py-5">Financial Summary</th>
                                <th class="px-8 py-5 hidden md:table-cell">Term Configuration</th>
                                <th class="px-8 py-5 text-center">Authentication</th>
                                <th class="px-8 py-5 text-right px-10">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <tr v-for="account in creditAccounts.data" :key="account.id"
                                class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-12 w-12 rounded-[1rem] bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 border border-indigo-100 dark:border-indigo-800/50 shadow-sm">
                                            <Users class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter italic">
                                                {{ account.customer_name }}
                                            </p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Acc
                                                Index #{{ account.id }}026</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-[10px] font-black text-gray-400 uppercase w-16">Total:</span>
                                            <span class="text-xs font-black text-gray-900 dark:text-white italic">₱{{
                                                account.total_amount }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-[10px] font-black text-emerald-600 uppercase w-16">Paid:</span>
                                            <span class="text-xs font-black text-emerald-600 italic">₱{{
                                                account.paid_amount }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-[10px] font-black text-rose-500 uppercase w-16">Bal:</span>
                                            <span class="text-xs font-black text-rose-500 italic">₱{{
                                                remainingBalance(account) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 hidden md:table-cell">
                                    <div class="flex items-center gap-3">
                                        <component :is="account.term_type === 'one-time' ? CreditCard : Calendar"
                                            class="h-4 w-4 text-gray-400" />
                                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                            {{ account.term_type === 'one-time' ? 'Single Tranche' :
                                                `${account.installment_months} Mo. Schedule` }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-center">
                                        <button @click="toggleStatus(account)" :class="[
                                            'px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all border active:scale-95',
                                            account.status === 'active' ? 'bg-green-50 text-green-600 border-green-100 dark:bg-green-900/20' : 'bg-red-50 text-red-400 border-red-100 dark:bg-red-900/20'
                                        ]">
                                            {{ account.status === 'active' ? 'Active' : 'Disabled' }}
                                        </button>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-2 px-2">
                                        <button @click="viewSchedule(account)"
                                            class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-indigo-600 transition-all">
                                            <Eye class="h-4 w-4" />
                                        </button>
                                        <button @click="openEditModal(account)"
                                            class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-indigo-600 transition-all">
                                            <Pencil class="h-4 w-4" />
                                        </button>
                                        <button @click="confirmDelete(account)"
                                            class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-red-500 transition-all">
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                        <button
                                            class="px-5 py-2.5 bg-gray-950 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-md">
                                            Ledger
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="flex flex-col md:flex-row items-center justify-between p-8 rounded-[2.5rem] bg-indigo-50 dark:bg-indigo-900/10 border border-dashed border-indigo-200 dark:border-indigo-800">
                <div class="flex items-center gap-5 text-center md:text-left mb-4 md:mb-0">
                    <div
                        class="h-14 w-14 rounded-[1.5rem] bg-white dark:bg-gray-900 flex items-center justify-center text-indigo-600 shadow-sm">
                        <Wallet class="h-7 w-7" />
                    </div>
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-tight text-gray-900 dark:text-white">Credit
                            Ceiling Warning</h4>
                        <p class="text-xs font-medium text-gray-500">Accounts exceeding 85% utilization will be flagged
                            for manual liquidity review.</p>
                    </div>
                </div>
                <button
                    class="flex items-center gap-2 text-xs font-black text-indigo-600 uppercase tracking-widest hover:underline px-6 py-3 rounded-2xl bg-white dark:bg-gray-900 shadow-sm">
                    Policy Guidelines
                    <ChevronRight class="h-4 w-4" />
                </button>
            </div>
        </div>

        <div v-if="showScheduleModal && selectedCreditAccount"
            class="fixed inset-0 z-[100] overflow-y-auto bg-gray-950/60 backdrop-blur-md flex items-center justify-center p-4">
            <div
                class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl w-full max-w-2xl overflow-hidden border border-gray-100 dark:border-gray-800 transform transition-all">
                <div class="px-8 py-6 bg-indigo-600 text-white flex justify-between items-center">
                    <h3 class="font-black text-sm uppercase tracking-[0.2em] italic">Amortization Schedule</h3>
                    <button type="button" @click="showScheduleModal = false"
                        class="text-white/50 hover:text-white transition">
                        <X class="h-6 w-6" />
                    </button>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                        <div
                            class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Total</p>
                            <p class="text-sm font-black text-gray-900 dark:text-white italic">₱{{
                                selectedCreditAccount.total_amount }}</p>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Paid</p>
                            <p class="text-sm font-black text-emerald-600 italic">₱{{ selectedCreditAccount.paid_amount
                                }}</p>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <p class="text-[9px] font-black text-rose-500 uppercase tracking-widest">Remaining</p>
                            <p class="text-sm font-black text-rose-500 italic">₱{{
                                remainingBalance(selectedCreditAccount) }}</p>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest">Progress</p>
                            <p class="text-sm font-black text-indigo-600 italic">{{
                                progressPercentage(selectedCreditAccount) }}%</p>
                        </div>
                    </div>

                    <div
                        class="max-h-[350px] overflow-y-auto no-scrollbar border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                                <tr class="text-[9px] font-black text-gray-400 uppercase tracking-widest italic">
                                    <th class="px-6 py-4">#</th>
                                    <th class="px-6 py-4">Due Date</th>
                                    <th class="px-6 py-4">Tranche</th>
                                    <th class="px-6 py-4">Recovered</th>
                                    <th class="px-6 py-4 text-right">Auth</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                <tr v-for="(payment, index) in generateSchedule(selectedCreditAccount)" :key="index"
                                    class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-all">
                                    <td class="px-6 py-4 text-[10px] font-black text-gray-400 italic">{{
                                        payment.installment || '—' }}</td>
                                    <td class="px-6 py-4 text-[11px] font-black text-gray-700 dark:text-gray-300">{{
                                        payment.due_date }}</td>
                                    <td class="px-6 py-4 text-[11px] font-black text-gray-900 dark:text-white italic">
                                        ₱{{ payment.amount }}</td>
                                    <td class="px-6 py-4 text-[11px] font-black text-emerald-600 italic">₱{{
                                        payment.paid || '0.00' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <span :class="[
                                            'px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest',
                                            payment.status === 'paid' ? 'bg-green-50 text-green-600 dark:bg-green-900/20' : payment.status === 'partial' ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/20' : 'bg-gray-50 text-gray-400 dark:bg-gray-800'
                                        ]">
                                            {{ payment.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="px-8 py-6 bg-gray-50 dark:bg-gray-800/50 flex justify-end">
                    <button @click="showScheduleModal = false"
                        class="px-8 py-3 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md hover:bg-indigo-700 transition-all">
                        Dismiss Schedule
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