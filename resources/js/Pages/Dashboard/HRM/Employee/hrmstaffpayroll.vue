<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Wallet,
    Search,
    FileDown,
    Clock,
    AlertCircle,
    CheckCircle2,
    TrendingUp,
    Filter,
    Plus,
    Save,
    CalendarDays,
    ShieldCheck,
    X
} from 'lucide-vue-next';

const props = defineProps({
    payroll_data: Array,
    stats: Object,
});

const searchQuery = ref('');
const isHolidayModalOpen = ref(false);
const isStatutoryModalOpen = ref(false);

// Main Payroll Form
const form = useForm({
    employee_id: '',
    base_salary: '',
    piece_count: '',
    overtime_hours: '',
});

// Holiday Modal Form
const holidayForm = useForm({
    holiday_date: '',
    holiday_name: '',
    holiday_type: 'Regular', // Regular (200%) or Special (130%)
    premium_rate: 1.0
});

// Statutory Contribution Modal Form
const statForm = useForm({
    sss_contribution: '',
    philhealth_rate: '',
    pagibig_fixed: '',
    tax_bracket: ''
});

const submitPayrollEntry = () => {
    form.post(route('hrm.employee.payroll.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const filteredPayroll = computed(() => {
    return props.payroll_data?.filter(item =>
        item.employee_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        item.employee_id.includes(searchQuery.value)
    );
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(value);
};
</script>

<template>

    <Head title="Payroll Processing" />

    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 bg-[#f8faff] min-h-screen">
            <div class="max-w-[1400px] mx-auto space-y-8">

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 uppercase tracking-tight italic">
                            Payroll <span class="text-blue-600 font-light">Processor</span>
                        </h1>
                        <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">
                            HRM Staff Terminal | Piece-Rate & Statutory Module
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <button @click="isHolidayModalOpen = true"
                            class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-slate-50 transition-all">
                            <CalendarDays class="size-4 text-rose-500" /> Set Holidays
                        </button>
                        <button @click="isStatutoryModalOpen = true"
                            class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-slate-50 transition-all">
                            <ShieldCheck class="size-4 text-blue-600" /> statutory Rates
                        </button>
                        <button
                            class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-wider shadow-lg hover:bg-slate-800 transition-all">
                            <FileDown class="size-4" /> Export CSV
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] p-6 sm:p-8 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-6">
                        <Plus class="size-5 text-blue-600" />
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">New Entry Input</h3>
                    </div>

                    <form @submit.prevent="submitPayrollEntry"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label
                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Employee
                                ID</label>
                            <input v-model="form.employee_id" type="text"
                                class="w-full bg-slate-50 border-none rounded-xl text-xs font-bold p-3 focus:ring-2 ring-blue-500/20"
                                placeholder="EMP-000">
                        </div>
                        <div>
                            <label
                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Base
                                Salary</label>
                            <input v-model="form.base_salary" type="number"
                                class="w-full bg-slate-50 border-none rounded-xl text-xs font-bold p-3 focus:ring-2 ring-blue-500/20"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label
                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Piece
                                Count (Qty)</label>
                            <input v-model="form.piece_count" type="number"
                                class="w-full bg-slate-50 border-none rounded-xl text-xs font-bold p-3 focus:ring-2 ring-blue-500/20"
                                placeholder="0">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">OT
                                Hours</label>
                            <input v-model="form.overtime_hours" type="number"
                                class="w-full bg-slate-50 border-none rounded-xl text-xs font-bold p-3 focus:ring-2 ring-blue-500/20"
                                placeholder="0">
                        </div>
                        <div class="lg:col-span-4 flex justify-end">
                            <button :disabled="form.processing"
                                class="bg-blue-600 text-white px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                                <Save class="size-4" /> Save Entry
                            </button>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-600 p-6 rounded-[2rem] shadow-xl shadow-blue-200 text-white">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-2xl">
                                <Wallet class="size-6" />
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-blue-100 uppercase tracking-widest">Total Payout
                                </p>
                                <p class="text-xl font-black italic">{{ formatCurrency(stats?.total_payout || 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-4 text-slate-800">
                            <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                                <Clock class="size-6" />
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending Audit
                                </p>
                                <p class="text-xl font-black italic">{{ stats?.pending_approvals || 0 }} Requests</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-4 text-slate-800">
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                                <TrendingUp class="size-6" />
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Processing
                                    Rate</p>
                                <p class="text-xl font-black italic">94.2%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div
                        class="p-6 sm:p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/20">
                        <div class="relative w-full md:w-96">
                            <Search class="absolute left-4 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                            <input v-model="searchQuery" type="text" placeholder="SEARCH EMPLOYEE ID OR NAME..."
                                class="w-full pl-12 pr-4 py-3 bg-white border-none rounded-2xl text-[10px] font-bold tracking-widest focus:ring-2 ring-blue-500/20 shadow-sm" />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-[800px]">
                            <thead>
                                <tr
                                    class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">
                                    <th class="px-8 py-5">Employee Info</th>
                                    <th class="px-8 py-5">Base Salary</th>
                                    <th class="px-8 py-5 text-center">Piece Rate / OT</th>
                                    <th class="px-8 py-5">Net Salary</th>
                                    <th class="px-8 py-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="item in filteredPayroll" :key="item.id"
                                    class="hover:bg-slate-50/30 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-slate-800 uppercase italic">{{
                                                item.employee_name }}</span>
                                            <span
                                                class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{
                                                item.employee_id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-xs font-bold text-slate-600">{{
                                        formatCurrency(item.base_salary) }}</td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col items-center">
                                            <div v-if="item.ot_hours > 20"
                                                class="flex items-center gap-1 text-[8px] font-black text-rose-500 uppercase tracking-tighter mb-1">
                                                <AlertCircle class="size-3" /> High OT Warning
                                            </div>
                                            <span class="text-[10px] font-mono font-bold text-slate-500">
                                                {{ item.piece_count || 0 }} Qty / {{ item.ot_hours }} Hrs
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-sm font-black text-blue-600 italic">{{
                                        formatCurrency(item.net_pay) }}</td>
                                    <td class="px-8 py-6 text-right">
                                        <button
                                            class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                            <CheckCircle2 class="size-5" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isHolidayModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="isHolidayModalOpen = false"></div>
            <div
                class="relative bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                    <h2 class="text-xl font-black text-slate-900 uppercase italic">Holiday <span
                            class="text-rose-500 font-light">Calendar</span></h2>
                    <button @click="isHolidayModalOpen = false"
                        class="p-2 hover:bg-slate-50 rounded-xl transition-colors">
                        <X class="size-5 text-slate-400" />
                    </button>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label
                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Holiday
                                Name</label>
                            <input v-model="holidayForm.holiday_name" type="text"
                                class="w-full bg-slate-50 border-none rounded-xl text-xs font-bold p-3"
                                placeholder="e.g., Christmas Day">
                        </div>
                        <div>
                            <label
                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Date</label>
                            <input v-model="holidayForm.holiday_date" type="date"
                                class="w-full bg-slate-50 border-none rounded-xl text-xs font-bold p-3">
                        </div>
                        <div>
                            <label
                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Type</label>
                            <select v-model="holidayForm.holiday_type"
                                class="w-full bg-slate-50 border-none rounded-xl text-xs font-bold p-3">
                                <option>Regular</option>
                                <option>Special Non-Working</option>
                            </select>
                        </div>
                    </div>
                    <button
                        class="w-full bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:scale-[1.02] transition-all">Add
                        to Schedule</button>
                </div>
            </div>
        </div>

        <div v-if="isStatutoryModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="isStatutoryModalOpen = false"></div>
            <div
                class="relative bg-white w-full max-w-xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                    <h2 class="text-xl font-black text-slate-900 uppercase italic">Contribution <span
                            class="text-blue-600 font-light">Settings</span></h2>
                    <button @click="isStatutoryModalOpen = false"
                        class="p-2 hover:bg-slate-50 rounded-xl transition-colors">
                        <X class="size-5 text-slate-400" />
                    </button>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <label class="text-[8px] font-black text-blue-600 uppercase tracking-widest mb-2 block">SSS
                                Premium (%)</label>
                            <input v-model="statForm.sss_contribution" type="number"
                                class="w-full bg-transparent border-none text-sm font-black p-0" placeholder="14.0">
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <label
                                class="text-[8px] font-black text-emerald-600 uppercase tracking-widest mb-2 block">PhilHealth
                                Rate (%)</label>
                            <input v-model="statForm.philhealth_rate" type="number"
                                class="w-full bg-transparent border-none text-sm font-black p-0" placeholder="5.0">
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <label
                                class="text-[8px] font-black text-amber-600 uppercase tracking-widest mb-2 block">Pag-IBIG
                                Fixed</label>
                            <input v-model="statForm.pagibig_fixed" type="number"
                                class="w-full bg-transparent border-none text-sm font-black p-0" placeholder="200.00">
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <label
                                class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Withholding
                                Tax</label>
                            <select v-model="statForm.tax_bracket"
                                class="w-full bg-transparent border-none text-sm font-black p-0">
                                <option>Train Law 2026</option>
                                <option>Exempted</option>
                            </select>
                        </div>
                    </div>
                    <button
                        class="w-full bg-blue-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-blue-100 hover:scale-[1.02] transition-all">Update
                        Global Brackets</button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
.font-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

button:active {
    transform: scale(0.98);
}
</style>