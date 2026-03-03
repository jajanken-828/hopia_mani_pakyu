<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    TrendingUp,
    Users,
    ClipboardCheck,
    AlertCircle,
    ArrowUpRight,
    Activity,
    Trophy,
    DollarSign,
    Target,
    UserCircle,
    Package,
    ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
    user: Object,
    stats: Object, // totalPipelineValue, activeInquiries, pendingApprovals, conversionRate
    dailyActivityCount: Number,
    leaderboard: Array
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        maximumFractionDigits: 0
    }).format(value || 0);
};
</script>

<template>

    <Head title="CRM Manager Dashboard" />
    <AuthenticatedLayout>
        <div v-if="stats" class="p-8 space-y-8 bg-gray-50 dark:bg-zinc-950 min-h-screen">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tighter text-gray-900 dark:text-white uppercase italic">
                        CRM <span class="text-blue-600">Executive</span> Overview
                    </h1>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.3em] mt-1">
                        {{ dailyActivityCount || 0 }} Interactions Recorded Today
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- FIXED: Corrected route name from 'crm.approval.queue' to 'crm.approval' -->
                    <Link :href="route('crm.approval')"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                        Open Approval Queue ({{ stats.pendingApprovals || 0 }})
                        <ArrowUpRight class="w-3.5 h-3.5" />
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stats cards (unchanged) -->
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-gray-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pipeline Value
                        </p>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white">{{
                            formatCurrency(stats.totalPipelineValue) }}</h3>
                    </div>
                    <DollarSign class="absolute -right-2 -bottom-2 w-16 h-16 text-blue-500/10" />
                </div>

                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-gray-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Active Inquiries
                        </p>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ stats.activeInquiries || 0 }}
                        </h3>
                    </div>
                    <Activity class="absolute -right-2 -bottom-2 w-16 h-16 text-emerald-500/10" />
                </div>

                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-gray-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Win Rate</p>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ stats.conversionRate || 0 }}%
                        </h3>
                    </div>
                    <Target class="absolute -right-2 -bottom-2 w-16 h-16 text-purple-500/10" />
                </div>

                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-gray-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pending Review
                        </p>
                        <h3 class="text-2xl font-black"
                            :class="stats.pendingApprovals > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white'">
                            {{ stats.pendingApprovals || 0 }}
                        </h3>
                    </div>
                    <AlertCircle class="absolute -right-2 -bottom-2 w-16 h-16 text-red-500/10" />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-500 italic">Sales Performance
                            Leaderboard</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-gray-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                        <div v-for="(staff, index) in leaderboard" :key="staff.id"
                            class="p-6 flex items-center justify-between border-b border-gray-50 dark:border-zinc-800/50 hover:bg-gray-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-zinc-800 flex items-center justify-center">
                                        <UserCircle class="w-6 h-6 text-gray-400" />
                                    </div>
                                    <Trophy v-if="index === 0"
                                        class="absolute -top-2 -right-2 w-5 h-5 text-amber-500" />
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-black text-gray-900 dark:text-white uppercase italic tracking-tighter">
                                        {{ staff.name }}
                                    </p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">{{ staff.email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-black text-blue-600 leading-none">{{ staff.won_deals }}</span>
                                <p class="text-[9px] font-black text-gray-400 uppercase mt-1">Deals Won</p>
                            </div>
                        </div>
                        <div v-if="!leaderboard || leaderboard.length === 0"
                            class="p-10 text-center text-gray-400 text-xs font-bold uppercase italic">
                            No performance data available.
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-500 italic px-2">Managerial
                        Insights</h2>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="p-6 bg-blue-600 rounded-[2rem] text-left shadow-xl shadow-blue-500/20 block">
                            <ClipboardCheck class="w-6 h-6 text-white/50 mb-3" />
                            <h4 class="text-sm font-black text-white uppercase italic">System Governance</h4>
                            <p class="text-[10px] text-white/60 font-bold uppercase mt-1 italic">Audit lead conversion
                                cycles.</p>
                        </div>

                        <div
                            class="p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border border-gray-200 dark:border-zinc-800 shadow-sm">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 italic">
                                Conversion Progress</h4>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-[10px] font-black uppercase mb-1">
                                        <span class="text-gray-600 dark:text-zinc-400">Current Rate</span>
                                        <span class="text-blue-600">{{ stats.conversionRate || 0 }}%</span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-600 rounded-full transition-all"
                                            :style="{ width: (stats.conversionRate || 0) + '%' }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="flex items-center justify-center h-screen bg-gray-50 dark:bg-zinc-950">
            <p class="text-xs font-black text-gray-400 uppercase animate-pulse">Initializing CRM Executive Suite...</p>
        </div>
    </AuthenticatedLayout>
</template>