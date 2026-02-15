<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted, onUnmounted } from 'vue'
import {
    Clock,
    LogIn,
    LogOut,
    Calendar,
    MapPin,
    AlertCircle,
    CheckCircle2,
    Timer,
    Coffee
} from 'lucide-vue-next'

const props = defineProps({
    attendance_status: {
        type: Object,
        default: () => ({
            is_clocked_in: false,
            last_action: '08:45 AM',
            total_hours_today: '0h 0m'
        })
    },
    recent_logs: {
        type: Array,
        default: () => [
            { id: 1, date: '2026-02-08', clock_in: '08:45 AM', clock_out: '05:30 PM', status: 'Present', hours: '8h 45m' },
            { id: 2, date: '2026-02-07', clock_in: '09:02 AM', clock_out: '06:05 PM', status: 'Late', hours: '9h 03m' },
        ]
    }
})

// Live Clock Logic
const currentTime = ref(new Date().toLocaleTimeString())
let timerInterval

onMounted(() => {
    timerInterval = setInterval(() => {
        currentTime.value = new Date().toLocaleTimeString()
    }, 1000)
})

onUnmounted(() => clearInterval(timerInterval))

const form = useForm({})

const handleClockAction = (type) => {
    form.post(route('attendance.store', { type }), {
        preserveScroll: true
    })
}
</script>

<template>

    <Head title="Attendance Log" />

    <AuthenticatedLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                Attendance <span class="text-blue-600">Log</span>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Record your daily work hours and breaks.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-1 space-y-6">
                <div
                    class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700 shadow-xl shadow-blue-500/5 text-center">
                    <div class="inline-flex p-4 bg-blue-50 dark:bg-blue-900/20 rounded-3xl text-blue-600 mb-6">
                        <Clock class="h-10 w-10 animate-pulse" />
                    </div>

                    <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter mb-2">
                        {{ currentTime }}
                    </h2>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-[0.2em] mb-8">
                        {{ new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' }) }}
                    </p>

                    <!-- <div class="grid grid-cols-1 gap-4">
                        <button @click="handleClockAction('in')" :disabled="attendance_status.is_clocked_in" :class="[
                            'flex items-center justify-center gap-3 py-5 rounded-2xl font-black uppercase tracking-widest transition-all',
                            !attendance_status.is_clocked_in
                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30 hover:bg-blue-700 active:scale-95'
                                : 'bg-slate-100 text-slate-400 cursor-not-allowed'
                        ]">
                            <LogIn class="h-5 w-5" />
                            Clock In
                        </button>

                        <button @click="handleClockAction('out')" :disabled="!attendance_status.is_clocked_in" :class="[
                            'flex items-center justify-center gap-3 py-5 rounded-2xl font-black uppercase tracking-widest transition-all border-2',
                            attendance_status.is_clocked_in
                                ? 'border-rose-600 text-rose-600 hover:bg-rose-50'
                                : 'border-slate-100 text-slate-300 cursor-not-allowed'
                        ]">
                            <LogOut class="h-5 w-5" />
                            Clock Out
                        </button>
                    </div> -->

                    <div class="mt-8 pt-8 border-t border-slate-50 dark:border-slate-700 grid grid-cols-2 gap-4">
                        <div class="text-left">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span :class="[
                                    'h-2 w-2 rounded-full',
                                    attendance_status.is_clocked_in ? 'bg-emerald-500 animate-ping' : 'bg-slate-300'
                                ]"></span>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                    {{ attendance_status.is_clocked_in ? 'Working' : 'Offline' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Today's Total</p>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 mt-1">
                                {{ attendance_status.total_hours_today }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-amber-50 dark:bg-amber-900/10 p-6 rounded-[2rem] border border-amber-100 dark:border-amber-800 flex gap-4">
                    <AlertCircle class="h-6 w-6 text-amber-600 shrink-0" />
                    <p class="text-xs text-amber-800 dark:text-amber-400 font-medium leading-relaxed">
                        Remember to clock out for your scheduled 1-hour lunch break. Continued failure to clock out may
                        affect payroll calculations.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div
                    class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-50 dark:border-slate-700 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl text-emerald-600">
                                <History class="h-5 w-5" />
                            </div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Attendance History</h2>
                        </div>
                        <button class="text-xs font-black text-blue-600 uppercase tracking-widest hover:underline">View
                            Full Report</button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                                    <th
                                        class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        Date</th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        Clock In</th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        Clock Out</th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        Hours</th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <tr v-for="log in recent_logs" :key="log.id"
                                    class="group hover:bg-slate-50/50 transition-all">
                                    <td class="px-8 py-5">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ log.date
                                        }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div
                                            class="flex items-center gap-2 text-sm font-semibold text-slate-600 dark:text-slate-300">
                                            <LogIn class="h-3 w-3 text-emerald-500" />
                                            {{ log.clock_in }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div
                                            class="flex items-center gap-2 text-sm font-semibold text-slate-600 dark:text-slate-300">
                                            <LogOut class="h-3 w-3 text-rose-500" />
                                            {{ log.clock_out }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-sm font-black text-slate-900 dark:text-white">{{ log.hours
                                        }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span :class="[
                                            log.status === 'Present' ? 'text-emerald-600 bg-emerald-50' : 'text-amber-600 bg-amber-50',
                                            'px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider'
                                        ]">
                                            {{ log.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div
                        class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 flex items-center gap-4">
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl text-blue-600">
                            <Timer class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Avg. Clock In</p>
                            <p class="text-lg font-bold text-slate-900 dark:text-white">08:52 AM</p>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 flex items-center gap-4">
                        <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-2xl text-purple-600">
                            <Coffee class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">On-Time Rate</p>
                            <p class="text-lg font-bold text-slate-900 dark:text-white">94%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>