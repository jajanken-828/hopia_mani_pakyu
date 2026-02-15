<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import {
    Plane,
    Calendar as CalendarIcon,
    ChevronLeft,
    ChevronRight,
    MoreHorizontal,
    Clock,
    FileText,
    CheckCircle2,
    AlertCircle
} from 'lucide-vue-next';

// Form logic
const form = useForm({
    leave_type: '',
    start_date: '',
    end_date: '',
    reason: '',
});

const submit = () => {
    form.post(route('employee.leave.store'), {
        onFinish: () => form.reset(),
    });
};

// Mock Data for UI
const leaveBalances = [
    { type: 'Sick Leave', used: 2, total: 10, color: 'stroke-rose-500', text: 'text-rose-500' },
    { type: 'Vacation', used: 5, total: 15, color: 'stroke-blue-600', text: 'text-blue-600' },
];

const leaveHistory = [
    { type: 'Sick Leave', range: 'Feb 02 - Feb 03', status: 'Approved', statusClass: 'bg-emerald-100 text-emerald-700' },
    { type: 'Vacation', range: 'Jan 10 - Jan 15', status: 'Completed', statusClass: 'bg-slate-100 text-slate-600' },
    { type: 'Personal', range: 'Mar 01 - Mar 02', status: 'Pending', statusClass: 'bg-amber-100 text-amber-700' },
];
</script>

<template>

    <Head title="Leave Requests" />

    <AuthenticatedLayout>
        <div class="p-6 bg-[#f8faff] min-h-screen">
            <div class="max-w-[1400px] mx-auto">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    <div class="lg:col-span-8 space-y-8">

                        <div class="flex justify-between items-center">
                            <div>
                                <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tight">
                                    Leave <span class="text-blue-600 font-light">Management</span>
                                </h1>
                                <p class="text-sm text-slate-500 font-medium">Request time off and track your balances
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div v-for="balance in leaveBalances" :key="balance.type"
                                class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 flex items-center gap-8 relative overflow-hidden transition-transform hover:scale-[1.02]">
                                <div class="relative size-24 flex items-center justify-center flex-shrink-0">
                                    <svg class="size-full -rotate-90" viewBox="0 0 36 36">
                                        <circle cx="18" cy="18" r="16" fill="none" class="stroke-slate-100"
                                            stroke-width="3"></circle>
                                        <circle cx="18" cy="18" r="16" fill="none" :class="balance.color"
                                            stroke-width="3"
                                            :stroke-dasharray="`${(balance.used / balance.total) * 100}, 100`"
                                            stroke-linecap="round"></circle>
                                    </svg>
                                    <span class="absolute text-xl font-black text-slate-800 italic">{{ balance.total -
                                        balance.used }}</span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">
                                        Available Days</p>
                                    <h3 :class="['text-xl font-black uppercase italic', balance.text]">{{ balance.type
                                        }}</h3>
                                    <p class="text-xs text-slate-400 font-medium mt-1">{{ balance.used }} days used of
                                        {{ balance.total }}</p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-blue-600 rounded-[2.5rem] p-10 shadow-xl shadow-blue-200 text-white relative overflow-hidden">
                            <div class="relative z-10">
                                <h3 class="text-xl font-black uppercase italic mb-8 flex items-center gap-3">
                                    <Plane class="size-6 rotate-45" /> New Leave Application
                                </h3>

                                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <InputLabel for="leave_type" value="Leave Type"
                                            class="text-blue-100 text-[10px] font-bold uppercase ml-1" />
                                        <select id="leave_type" v-model="form.leave_type"
                                            class="w-full bg-white/10 border-white/20 rounded-xl text-sm font-bold text-white focus:ring-white/30 focus:border-white/40">
                                            <option value="" disabled class="text-slate-900">Select Type</option>
                                            <option value="sick" class="text-slate-900">Sick Leave</option>
                                            <option value="vacation" class="text-slate-900">Vacation</option>
                                            <option value="personal" class="text-slate-900">Personal</option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <InputLabel for="start" value="Start Date"
                                                class="text-blue-100 text-[10px] font-bold uppercase ml-1" />
                                            <TextInput id="start" type="date" v-model="form.start_date"
                                                class="w-full bg-white/10 border-white/20 rounded-xl text-white text-xs" />
                                        </div>
                                        <div class="space-y-2">
                                            <InputLabel for="end" value="End Date"
                                                class="text-blue-100 text-[10px] font-bold uppercase ml-1" />
                                            <TextInput id="end" type="date" v-model="form.end_date"
                                                class="w-full bg-white/10 border-white/20 rounded-xl text-white text-xs" />
                                        </div>
                                    </div>

                                    <div class="md:col-span-2 space-y-2">
                                        <InputLabel for="reason" value="Reason / Details"
                                            class="text-blue-100 text-[10px] font-bold uppercase ml-1" />
                                        <textarea id="reason" v-model="form.reason" rows="3"
                                            class="w-full bg-white/10 border-white/20 rounded-2xl text-white text-sm focus:ring-white/30"
                                            placeholder="Explain the reason for your request..."></textarea>
                                    </div>

                                    <div class="md:col-span-2 pt-4">
                                        <button type="submit" :disabled="form.processing"
                                            class="w-full bg-white text-blue-600 font-black uppercase tracking-[0.2em] py-4 rounded-2xl shadow-lg hover:bg-blue-50 transition-all text-xs">
                                            Submit Request
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="absolute -right-20 -top-20 size-64 bg-white/5 rounded-full blur-3xl"></div>
                        </div>

                    </div>

                    <div class="lg:col-span-4">
                        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 h-full">

                            <div class="mb-10">
                                <div class="flex justify-between items-center mb-6">
                                    <h2 class="text-lg font-black text-slate-800 italic">Leave Tracker</h2>
                                    <div class="flex gap-2">
                                        <button class="p-2 rounded-xl hover:bg-slate-50 transition-colors">
                                            <ChevronLeft class="size-4 text-slate-400" />
                                        </button>
                                        <button class="p-2 rounded-xl hover:bg-slate-50 transition-colors">
                                            <ChevronRight class="size-4 text-slate-400" />
                                        </button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-7 gap-1 text-center">
                                    <span v-for="d in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="d"
                                        class="text-[10px] font-black text-slate-300 uppercase pb-2">{{ d }}</span>
                                    <div v-for="i in 28" :key="i" :class="[
                                        'size-9 flex items-center justify-center text-xs font-bold rounded-xl transition-all',
                                        i === 15 ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-slate-600 hover:bg-slate-50',
                                        [2, 3].includes(i) ? 'border-b-2 border-rose-500' : ''
                                    ]">
                                        {{ i }}
                                    </div>
                                </div>
                            </div>

                            <hr class="border-slate-50 mb-8" />

                            <div class="space-y-6 relative">
                                <div class="absolute left-[15px] top-0 h-full w-[2px] bg-slate-50"></div>
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-8 mb-4">
                                    Application Status</h4>

                                <div v-for="history in leaveHistory" :key="history.range"
                                    class="flex gap-4 relative group">
                                    <div
                                        class="size-8 rounded-full bg-white border-4 border-slate-50 z-10 shadow-sm flex items-center justify-center">
                                        <CheckCircle2 v-if="history.status === 'Approved'"
                                            class="size-3 text-emerald-500" />
                                        <Clock v-else class="size-3 text-amber-500" />
                                    </div>
                                    <div
                                        class="flex-1 bg-slate-50/50 p-4 rounded-[1.5rem] border border-slate-100 group-hover:bg-white transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p
                                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                    {{ history.range }}</p>
                                                <p class="text-sm font-bold text-slate-800 mt-1 uppercase">{{
                                                    history.type }}</p>
                                            </div>
                                            <span
                                                :class="['text-[8px] font-black uppercase px-2 py-1 rounded-lg tracking-tighter', history.statusClass]">
                                                {{ history.status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-10 p-5 bg-rose-50 rounded-[1.5rem] border border-rose-100 flex gap-4">
                                <AlertCircle class="size-5 text-rose-500 flex-shrink-0" />
                                <p
                                    class="text-[10px] font-medium text-rose-700 leading-relaxed uppercase tracking-tight">
                                    Requests must be submitted at least 3 days in advance for approval.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>