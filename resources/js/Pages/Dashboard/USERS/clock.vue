<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import {
    Clock,
    Calendar as CalendarIcon,
    ChevronLeft,
    ChevronRight,
    MoreHorizontal,
    History,
    Timer,
    CheckCircle2,
    Power
} from 'lucide-vue-next';

const isClockedIn = ref(true);

// --- REAL-TIME CLOCK LOGIC ---
const currentTime = ref('');
let timerInterval = null;

const updateTime = () => {
    const now = new Date();
    currentTime.value = now.toLocaleTimeString('en-US', {
        hour12: true,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    }).replace(/\s?[APM]/g, '');
};

onMounted(() => {
    updateTime();
    timerInterval = setInterval(updateTime, 1000);
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});

// Mock data
const attendanceHistory = [
    { date: 'Feb 15, 2026', clockIn: '08:00 AM', clockOut: '05:00 PM', status: 'On-Time', color: 'bg-emerald-100 text-emerald-700 border-emerald-200' },
    { date: 'Feb 14, 2026', clockIn: '08:15 AM', clockOut: '05:05 PM', status: 'Late', color: 'bg-amber-100 text-amber-700 border-amber-200' },
    { date: 'Feb 13, 2026', clockIn: '---', clockOut: '---', status: 'Absent', color: 'bg-rose-100 text-rose-700 border-rose-200' },
];

const stats = [
    { label: 'Attendance Rate', value: '92%', icon: CheckCircle2 },
    { label: 'Total Lates', value: '2', icon: Timer },
    { label: 'Shift Status', value: 'On-Duty', icon: Clock },
];
</script>

<template>

    <Head title="Attendance Portal" />

    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 bg-[#f8faff] min-h-screen">
            <div class="max-w-[1400px] mx-auto">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

                    <div class="lg:col-span-8 space-y-6 lg:space-y-8">

                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="w-full md:w-auto">
                                <h1
                                    class="text-2xl sm:text-3xl font-black text-slate-900 uppercase tracking-tight italic">
                                    Duty <span class="text-blue-600 font-light">Command</span>
                                </h1>
                                <p
                                    class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">
                                    Status: {{ isClockedIn ? 'Active Shift' : 'Off Duty' }}
                                </p>
                            </div>

                            <div
                                class="w-full md:w-auto flex items-center p-1.5 bg-white rounded-2xl sm:rounded-[2rem] shadow-sm border border-slate-100">
                                <div class="px-4 sm:px-6 py-2 text-right border-r border-slate-100 mr-2">
                                    <p
                                        class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                        System Time</p>
                                    <p class="text-sm sm:text-lg font-mono font-black text-slate-800 italic">
                                        {{ currentTime }}
                                    </p>
                                </div>

                                <button @click="isClockedIn = !isClockedIn" :class="[
                                    'group flex-1 md:flex-none flex items-center justify-center gap-2 sm:gap-3 px-4 sm:px-8 py-3 sm:py-4 rounded-xl sm:rounded-[1.5rem] transition-all duration-500 font-black uppercase text-[10px] sm:text-[11px] tracking-[0.15em] overflow-hidden',
                                    isClockedIn
                                        ? 'bg-rose-500 text-white shadow-lg shadow-rose-200'
                                        : 'bg-emerald-500 text-white shadow-lg shadow-emerald-200'
                                ]">
                                    <Power class="size-3 sm:size-4" />
                                    <span>{{ isClockedIn ? 'Clock Out' : 'Clock In' }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-8 shadow-sm border border-slate-100 flex flex-col items-center justify-center">
                                <div class="relative size-28 sm:size-36 flex items-center justify-center">
                                    <svg class="size-full -rotate-90" viewBox="0 0 36 36">
                                        <circle cx="18" cy="18" r="16" fill="none" class="stroke-slate-50"
                                            stroke-width="3"></circle>
                                        <circle cx="18" cy="18" r="16" fill="none"
                                            class="stroke-blue-600 transition-all duration-1000" stroke-width="3"
                                            stroke-dasharray="92, 100" stroke-linecap="round"></circle>
                                    </svg>
                                    <div class="absolute text-center">
                                        <span class="text-2xl sm:text-4xl font-black text-slate-800 italic">92</span>
                                        <p class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase">Avg</p>
                                    </div>
                                </div>
                                <div
                                    class="mt-6 flex gap-4 sm:gap-6 text-[8px] sm:text-[9px] font-black uppercase tracking-widest">
                                    <span class="flex items-center gap-2">
                                        <div class="size-1.5 rounded-full bg-blue-600"></div> Present
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <div class="size-1.5 rounded-full bg-slate-200"></div> Exceptions
                                    </span>
                                </div>
                            </div>

                            <div
                                class="bg-blue-600 rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-8 shadow-xl shadow-blue-200 text-white flex flex-col justify-between overflow-hidden relative">
                                <div class="z-10">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p
                                                class="text-blue-100 text-[9px] sm:text-[10px] font-black uppercase tracking-widest">
                                                Earning Summary</p>
                                            <p class="text-[10px] sm:text-xs text-blue-200 mt-1 font-medium">Monthly
                                                Estimate</p>
                                        </div>
                                        <div
                                            class="bg-white/10 p-2 sm:p-2.5 rounded-xl sm:rounded-2xl backdrop-blur-md">
                                            <History class="size-4 sm:size-5 text-white" />
                                        </div>
                                    </div>
                                    <div class="mt-6 sm:mt-8 flex items-baseline gap-2">
                                        <span
                                            class="text-4xl sm:text-5xl font-black italic tracking-tight">$4,280</span>
                                        <span
                                            class="text-blue-200 text-[8px] sm:text-[10px] font-black uppercase">Net</span>
                                    </div>
                                </div>
                                <button
                                    class="mt-6 sm:mt-8 z-10 w-fit bg-white/10 hover:bg-white/20 px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg sm:rounded-xl text-[8px] sm:text-[10px] font-black uppercase tracking-widest border border-white/5 transition-all">
                                    Full Ledger
                                </button>
                                <div
                                    class="absolute -right-8 -bottom-8 size-32 sm:size-48 bg-white/10 rounded-full blur-3xl">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                            <div v-for="stat in stats" :key="stat.label"
                                class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-[2rem] border border-slate-50 shadow-sm flex items-center gap-4">
                                <div class="p-2 sm:p-3 bg-blue-50 rounded-xl sm:rounded-2xl text-blue-600">
                                    <component :is="stat.icon" class="size-4 sm:size-5" />
                                </div>
                                <div>
                                    <p
                                        class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                        {{ stat.label }}</p>
                                    <p class="text-lg sm:text-xl font-black text-slate-800 italic">{{ stat.value }}</p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white rounded-[2rem] sm:rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                            <div
                                class="p-6 sm:p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/20">
                                <h3
                                    class="text-[10px] sm:text-xs font-black text-slate-800 uppercase tracking-[0.2em] flex items-center gap-2">
                                    <div class="size-2 bg-blue-600 rounded-full animate-pulse"></div> Recent Cycles
                                </h3>
                                <button
                                    class="text-[8px] sm:text-[9px] font-black text-blue-600 uppercase tracking-widest border-b-2 border-blue-600/10">Export</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left min-w-[500px]">
                                    <thead>
                                        <tr
                                            class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                            <th class="px-6 py-5">Timeline</th>
                                            <th class="px-6 py-5 text-center">In/Out Cycle</th>
                                            <th class="px-6 py-5 text-right">Metric</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        <tr v-for="log in attendanceHistory" :key="log.date"
                                            class="hover:bg-slate-50/50 transition-colors group">
                                            <td class="px-6 py-5">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="size-8 rounded-lg bg-slate-50 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                                                        <CalendarIcon class="size-4" />
                                                    </div>
                                                    <span class="text-xs sm:text-sm font-black text-slate-700 italic">{{
                                                        log.date }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5">
                                                <div
                                                    class="flex items-center justify-center gap-2 font-mono text-[9px] sm:text-[11px] font-black text-slate-500">
                                                    <span class="bg-slate-100 px-2 py-1 rounded-md">{{ log.clockIn
                                                    }}</span>
                                                    <ChevronRight class="size-3 text-blue-300" />
                                                    <span class="bg-slate-100 px-2 py-1 rounded-md">{{ log.clockOut
                                                    }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 text-right">
                                                <span :class="log.color"
                                                    class="px-3 sm:px-4 py-1.5 rounded-lg sm:rounded-xl text-[8px] sm:text-[9px] font-black uppercase tracking-tighter border shadow-sm">
                                                    {{ log.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div
                            class="bg-white rounded-[2rem] sm:rounded-[2.5rem] shadow-sm border border-slate-100 p-6 sm:p-8 h-full">
                            <div class="flex justify-between items-center mb-8 sm:mb-10">
                                <div>
                                    <h2 class="text-lg sm:text-xl font-black text-slate-800 italic uppercase">February
                                        2026</h2>
                                    <p
                                        class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] flex items-center gap-1.5 mt-1">
                                    <div class="size-1.5 rounded-full bg-emerald-500"></div> Live Shift Board
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-7 gap-1 sm:gap-1.5 mb-8 sm:mb-10">
                                <span v-for="d in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="d"
                                    class="text-center text-[8px] sm:text-[9px] font-black text-slate-300 uppercase tracking-widest pb-2">{{
                                        d }}</span>
                                <div v-for="i in 28" :key="i" :class="[
                                    'aspect-square flex items-center justify-center text-[10px] sm:text-[11px] font-black rounded-lg sm:rounded-2xl border transition-all',
                                    i === 15 ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 border-blue-600' : 'text-slate-600 border-transparent hover:bg-blue-50 hover:text-blue-600 hover:border-blue-100'
                                ]">
                                    {{ i }}
                                </div>
                            </div>

                            <div class="space-y-6 sm:space-y-8 relative">
                                <div class="absolute left-[13px] sm:left-[16px] top-0 h-full w-[1.5px] bg-slate-50">
                                </div>
                                <h4
                                    class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-8 sm:ml-10 opacity-70">
                                    Timeline</h4>

                                <div class="flex gap-4 sm:gap-5 relative">
                                    <div
                                        class="size-6 sm:size-8 rounded-full bg-blue-600 border-[3px] border-white z-10 shadow-sm flex items-center justify-center">
                                        <Clock class="size-3 text-white" />
                                    </div>
                                    <div
                                        class="flex-1 bg-slate-50/50 p-4 sm:p-5 rounded-2xl sm:rounded-[1.8rem] border border-slate-50">
                                        <p
                                            class="text-[8px] sm:text-[9px] font-black text-blue-600 uppercase tracking-widest">
                                            08:00 AM</p>
                                        <p
                                            class="text-xs font-black text-slate-800 mt-1 uppercase italic tracking-tight leading-tight">
                                            Shift Initialization</p>
                                        <p class="text-[8px] text-slate-400 font-bold uppercase mt-2 tracking-tighter">
                                            Line A: Manufacturing</p>
                                    </div>
                                </div>

                                <div class="flex gap-4 sm:gap-5 relative">
                                    <div
                                        class="size-6 sm:size-8 rounded-full bg-amber-400 border-[3px] border-white z-10 shadow-sm flex items-center justify-center">
                                        <Timer class="size-3 text-white" />
                                    </div>
                                    <div
                                        class="flex-1 bg-amber-50/30 p-4 sm:p-5 rounded-2xl sm:rounded-[1.8rem] border border-amber-50/50">
                                        <p
                                            class="text-[8px] sm:text-[9px] font-black text-amber-600 uppercase tracking-widest">
                                            12:00 PM</p>
                                        <p
                                            class="text-xs font-black text-slate-800 mt-1 uppercase italic tracking-tight leading-tight">
                                            Interval Break</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.font-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

button:active {
    transform: scale(0.96);
}
</style>