<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Users, UserCheck, TrendingUp, Calendar, Building2, Briefcase, Award, Clock, BarChart3, PieChart, ArrowRight, Zap } from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
    departmentCounts: Object,
    attendanceTrend: Object,
    recruitmentPipeline: Object,
});

// Helper for greeting
const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 18) return 'Good afternoon';
    return 'Good evening';
};

// Compute max attendance for bar scaling
const maxAttendance = computed(() => Math.max(...props.attendanceTrend.values, 1));

// Compute total employees for department percentages
const totalEmployees = computed(() => props.stats.totalEmployees);
</script>

<template>
    <AuthenticatedLayout>

        <Head title="Human Resource Management " />
        <div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6 sm:space-y-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                        Human Resource Management
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ getGreeting() }}, Monti Team</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <Calendar class="w-4 h-4" />
                    <span>{{ new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
                        }}</span>
                </div>
            </div>

            <!-- Stats Cards (Enhanced) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div
                    class="group bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Employees</p>
                            <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">{{
                                stats.totalEmployees }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl group-hover:bg-blue-100 transition">
                            <Users class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-blue-600 dark:text-blue-400 flex items-center gap-1">
                        <TrendingUp class="w-3 h-3" /> +4% from last month
                    </div>
                </div>

                <div
                    class="group bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Employees</p>
                            <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">{{
                                stats.activeEmployees }}</p>
                        </div>
                        <div
                            class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl group-hover:bg-green-100 transition">
                            <UserCheck class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-green-600 dark:text-green-400">{{ ((stats.activeEmployees /
                        stats.totalEmployees) * 100).toFixed(1) }}% of total</div>
                </div>

                <div
                    class="group bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Trainees</p>
                            <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">{{
                                stats.totalTrainees }}</p>
                        </div>
                        <div
                            class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl group-hover:bg-purple-100 transition">
                            <Award class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-purple-600 dark:text-purple-400">Awaiting evaluation</div>
                </div>

                <div
                    class="group bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Attendance Rate</p>
                            <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">{{
                                stats.attendanceRate }}%</p>
                        </div>
                        <div
                            class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl group-hover:bg-orange-100 transition">
                            <TrendingUp class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-orange-600 dark:text-orange-400">Above target (90%)</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Department Distribution (Horizontal Bars) -->
                <div
                    class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center gap-2 mb-4">
                        <Building2 class="w-4 h-4 text-blue-500" />
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">Department Distribution</h3>
                    </div>
                    <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                        <div v-for="(count, dept) in departmentCounts" :key="dept" class="flex items-center gap-3">
                            <span class="w-16 text-xs font-medium text-gray-600 dark:text-gray-400">{{ dept }}</span>
                            <div class="flex-1 h-6 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-700"
                                    :style="{ width: `${(count / totalEmployees) * 100}%` }"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ count }}</span>
                        </div>
                    </div>
                </div>

                <!-- Attendance Trend (Bar Chart) -->
                <div
                    class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center gap-2 mb-4">
                        <Calendar class="w-4 h-4 text-emerald-500" />
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">Attendance Trend (Last 6 Months)
                        </h3>
                    </div>
                    <div class="h-48 flex items-end gap-2">
                        <div v-for="(value, idx) in attendanceTrend.values" :key="idx"
                            class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-gradient-to-t from-emerald-400 to-emerald-500 rounded-t-lg transition-all duration-500"
                                :style="{ height: `${(value / maxAttendance) * 100}%`, minHeight: '4px' }"></div>
                            <span class="text-[10px] sm:text-xs mt-2 text-gray-500">{{ attendanceTrend.months[idx]
                                }}</span>
                            <span class="text-[9px] font-bold text-gray-600 dark:text-gray-400">{{ value }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recruitment Pipeline -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800">
                <div class="flex items-center gap-2 mb-4">
                    <Briefcase class="w-4 h-4 text-indigo-500" />
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">Recruitment Pipeline</h3>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/10 rounded-xl">
                        <p class="text-2xl font-black text-blue-600 dark:text-blue-400">{{
                            recruitmentPipeline.applications }}</p>
                        <p class="text-xs text-gray-500">Applications</p>
                    </div>
                    <div class="text-center p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-xl">
                        <p class="text-2xl font-black text-yellow-600 dark:text-yellow-400">{{
                            recruitmentPipeline.interviews }}</p>
                        <p class="text-xs text-gray-500">Interviews</p>
                    </div>
                    <div class="text-center p-3 bg-purple-50 dark:bg-purple-900/10 rounded-xl">
                        <p class="text-2xl font-black text-purple-600 dark:text-purple-400">{{
                            recruitmentPipeline.offers }}</p>
                        <p class="text-xs text-gray-500">Offers</p>
                    </div>
                    <div class="text-center p-3 bg-green-50 dark:bg-green-900/10 rounded-xl">
                        <p class="text-2xl font-black text-green-600 dark:text-green-400">{{ recruitmentPipeline.hired
                            }}</p>
                        <p class="text-xs text-gray-500">Hired</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Activity (Placeholder) -->
                <div
                    class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center gap-2 mb-4">
                        <Clock class="w-4 h-4 text-gray-500" />
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">Recent Activity</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="p-1.5 bg-blue-50 rounded-lg mt-0.5">
                                <UserCheck class="w-3 h-3 text-blue-600" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">New employee hired: Sarah Johnson
                                </p>
                                <p class="text-xs text-gray-400">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="p-1.5 bg-green-50 rounded-lg mt-0.5">
                                <Award class="w-3 h-3 text-green-600" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Trainee Mark Williams scored 85%</p>
                                <p class="text-xs text-gray-400">Yesterday</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="p-1.5 bg-orange-50 rounded-lg mt-0.5">
                                <Calendar class="w-3 h-3 text-orange-600" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Leave request approved for 3 days
                                </p>
                                <p class="text-xs text-gray-400">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div
                    class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center gap-2 mb-4">
                        <Zap class="w-4 h-4 text-amber-500" />
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">Quick Actions</h3>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <Link :href="route('hrm.manager.employee')"
                            class="flex-1 min-w-[120px] text-center px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">
                            Manage Employees
                        </Link>
                        <Link :href="route('hrm.manager.payroll')"
                            class="flex-1 min-w-[120px] text-center px-4 py-2 bg-green-600 text-white rounded-xl text-sm font-bold hover:bg-green-700 transition">
                            Payroll
                        </Link>
                        <Link :href="route('hrm.manager.analytics')"
                            class="flex-1 min-w-[120px] text-center px-4 py-2 bg-purple-600 text-white rounded-xl text-sm font-bold hover:bg-purple-700 transition">
                            Advanced Analytics
                        </Link>
                        <Link :href="route('hrm.manager.onboarding')"
                            class="flex-1 min-w-[120px] text-center px-4 py-2 bg-amber-600 text-white rounded-xl text-sm font-bold hover:bg-amber-700 transition">
                            Onboarding
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>