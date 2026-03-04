<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    Users,
    TrendingUp,
    Clock,
    CheckCircle,
    BarChart3,
    ArrowUpRight,
    Trophy,
    Activity
} from 'lucide-vue-next';

// Define props to receive real data from DashboardController
const props = defineProps({
    user: Object,
    stats: {
        type: Object,
        default: () => ({
            totalPipelineValue: 0,
            activeInquiries: 0,
            pendingApprovals: 0,
            conversionRate: 0
        })
    },
    dailyActivityCount: {
        type: Number,
        default: 0
    },
    leaderboard: {
        type: Array,
        default: () => []
    }
});

// Mapping props to local display logic
const dashboardStats = [
    {
        label: 'Pipeline Value',
        value: `₱${parseFloat(props.stats.totalPipelineValue).toLocaleString()}`,
        icon: BarChart3,
        color: 'text-indigo-600',
        bg: 'bg-indigo-50'
    },
    {
        label: 'Active Inquiries',
        value: props.stats.activeInquiries,
        icon: Clock,
        color: 'text-amber-600',
        bg: 'bg-amber-50'
    },
    {
        label: 'Pending Approvals',
        value: props.stats.pendingApprovals,
        icon: CheckCircle,
        color: 'text-rose-600',
        bg: 'bg-rose-50'
    },
    {
        label: 'Conversion Rate',
        value: `${props.stats.conversionRate}%`,
        icon: TrendingUp,
        color: 'text-emerald-600',
        bg: 'bg-emerald-50'
    },
];
</script>

<template>

    <Head title="CRM Manager Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6 lg:p-10 space-y-10">

            <div class="flex justify-between items-end">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">
                        Sales <span class="text-indigo-600">Oversight</span>
                    </h1>
                    <p class="text-gray-500 text-sm font-medium mt-1">Real-time performance metrics for CRM operations.
                    </p>
                </div>
                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl border border-gray-100 shadow-sm">
                    <Activity class="h-4 w-4 text-indigo-500" />
                    <span class="text-xs font-black uppercase tracking-widest text-gray-400">Today's Activities:</span>
                    <span class="text-lg font-black text-gray-900">{{ dailyActivityCount }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="stat in dashboardStats" :key="stat.label"
                    class="bg-white p-7 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ stat.label }}</p>
                        <p class="text-2xl font-black text-gray-900 mt-1 italic tracking-tighter">{{ stat.value }}</p>
                    </div>
                    <div :class="[stat.bg, stat.color]"
                        class="h-14 w-14 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <component :is="stat.icon" class="h-6 w-6" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-8 bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="text-sm font-black uppercase tracking-widest text-gray-900 flex items-center gap-2">
                            <Trophy class="h-4 w-4 text-amber-500" />
                            Performance Leaderboard
                        </h3>
                        <Link :href="route('crm.oversight')"
                            class="text-[10px] font-black uppercase text-indigo-600 hover:underline">Full Analytics
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-[10px] font-black uppercase text-gray-400 tracking-[0.15em]">
                                <tr>
                                    <th class="px-8 py-5">Staff Member</th>
                                    <th class="px-8 py-5">Closed Deals</th>
                                    <th class="px-8 py-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="(staff, index) in leaderboard" :key="staff.id"
                                    class="group hover:bg-gray-50/50 transition-all">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-xs">
                                                {{ staff.name.charAt(0) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-gray-900 uppercase tracking-tighter">
                                                    {{ staff.name }}</p>
                                                <p class="text-[10px] font-bold text-gray-400">{{ staff.email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 font-black text-indigo-600 italic">
                                        {{ staff.won_deals }} <span
                                            class="text-[10px] text-gray-400 font-normal ml-1">Won</span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <Link :href="route('crm.customerprofile')"
                                            class="inline-flex p-2 bg-gray-50 text-gray-400 rounded-lg hover:bg-indigo-600 hover:text-white transition-all">
                                            <ArrowUpRight class="h-4 w-4" />
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="!leaderboard.length">
                                    <td colspan="3"
                                        class="px-8 py-20 text-center text-gray-300 font-black uppercase italic">No
                                        active staff performance recorded</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    <div
                        class="bg-indigo-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-indigo-100 relative overflow-hidden">
                        <div class="relative z-10">
                            <h4 class="text-lg font-black uppercase tracking-tighter italic">Approval Queue</h4>
                            <p class="text-indigo-100 text-xs mt-1 mb-6 font-medium">There are {{
                                props.stats.pendingApprovals }} orders awaiting managerial review.</p>
                            <Link :href="route('crm.approval')"
                                class="inline-flex w-full py-4 bg-white text-indigo-600 rounded-2xl text-[10px] font-black uppercase tracking-widest justify-center shadow-lg hover:bg-indigo-50 transition-all">
                                Go to Approvals
                            </Link>
                        </div>
                        <CheckCircle class="absolute -right-4 -bottom-4 h-32 w-32 text-white/10" />
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                        <h4 class="text-xs font-black uppercase tracking-widest text-gray-900 mb-4">Quick Strategy</h4>
                        <div class="space-y-4">
                            <Link :href="route('crm.strategy')"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-indigo-50 transition-all border border-transparent hover:border-indigo-100 group">
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-gray-500 group-hover:text-indigo-600">Revenue
                                    Forecast</span>
                                <ArrowUpRight class="h-4 w-4 text-gray-300 group-hover:text-indigo-600" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.tracking-tighter {
    letter-spacing: -0.05em;
}
</style>