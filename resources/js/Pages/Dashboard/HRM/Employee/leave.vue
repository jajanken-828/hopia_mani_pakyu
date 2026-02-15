<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'
import {
    Calendar,
    Clock,
    CheckCircle2,
    XCircle,
    Plus,
    FileText,
    Info,
    History
} from 'lucide-vue-next'

const props = defineProps({
    leave_balances: {
        type: Object,
        default: () => ({
            sick: 10,
            vacation: 15,
            emergency: 5
        })
    },
    leave_requests: {
        type: Array,
        default: () => [
            { id: 1, type: 'Vacation', start_date: '2024-03-10', end_date: '2024-03-15', status: 'Pending', days: 5 },
            { id: 2, type: 'Sick Leave', start_date: '2024-02-05', end_date: '2024-02-06', status: 'Approved', days: 1 },
        ]
    }
})

const form = useForm({
    type: '',
    start_date: '',
    end_date: '',
    reason: '',
})

const submitRequest = () => {
    form.post(route('leaves.store'), {
        onSuccess: () => form.reset(),
    })
}

const getStatusClass = (status) => {
    switch (status.toLowerCase()) {
        case 'approved': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30'
        case 'pending': return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30'
        case 'rejected': return 'bg-rose-100 text-rose-700 dark:bg-rose-900/30'
        default: return 'bg-slate-100 text-slate-700'
    }
}
</script>

<template>

    <Head title="Leave Management" />

    <AuthenticatedLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                Leave <span class="text-blue-600">Management</span>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Plan your time off and track your leave
                balances.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div v-for="(balance, type) in leave_balances" :key="type"
                class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ type }} Balance</p>
                        <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ balance }} <span
                                class="text-sm font-medium text-slate-400">Days</span></h3>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl text-blue-600">
                        <Calendar class="h-6 w-6" />
                    </div>
                </div>
                <div class="mt-4 w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full transition-all duration-1000"
                        :style="{ width: (balance / 20) * 100 + '%' }"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-slate-800 p-8 rounded-[2rem] border border-slate-100 dark:border-slate-700 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl text-indigo-600">
                            <Plus class="h-5 w-5" />
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">New Request</h2>
                    </div>

                    <form @submit.prevent="submitRequest" class="space-y-5">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Leave
                                Type</label>
                            <select v-model="form.type"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="" disabled>Select Type</option>
                                <option value="sick">Sick Leave</option>
                                <option value="vacation">Vacation</option>
                                <option value="emergency">Emergency</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Start
                                    Date</label>
                                <input v-model="form.start_date" type="date"
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-sm focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">End
                                    Date</label>
                                <input v-model="form.end_date" type="date"
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-sm focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Reason</label>
                            <textarea v-model="form.reason" rows="4" placeholder="Briefly explain the reason..."
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <button type="submit" :disabled="form.processing"
                            class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-500/20 active:scale-95 disabled:opacity-50">
                            Submit Request
                        </button>
                    </form>
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
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Leave History</h2>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                                    <th
                                        class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        Type</th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        Duration</th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        Days</th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <tr v-for="request in leave_requests" :key="request.id"
                                    class="group hover:bg-slate-50/50 transition-all">
                                    <td class="px-8 py-5">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ request.type
                                            }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">{{
                                                request.start_date }}</span>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase">To {{
                                                request.end_date }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-sm font-black text-slate-900 dark:text-white">{{ request.days
                                            }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span
                                            :class="[getStatusClass(request.status), 'px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-2']">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current animate-pulse"></span>
                                            {{ request.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        class="p-8 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
                        <p class="text-xs font-bold text-slate-400 flex items-center gap-2">
                            <Info class="h-4 w-4" />
                            All leave requests are subject to approval by your department manager.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>