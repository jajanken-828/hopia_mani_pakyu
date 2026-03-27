<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ShoppingCart, DollarSign, Users, Package, TrendingUp,
    BarChart3, PieChart, Activity, Truck, Factory, UserCheck,
    Calendar, Zap, ArrowUp, ArrowDown, Clock, CheckCircle2
} from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
});

// Helper for greeting
const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 18) return 'Good afternoon';
    return 'Good evening';
};

// Demo data for charts (replace with real props later)
const revenueTrend = computed(() => ({
    months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    values: [450000, 520000, 480000, 610000, 590000, 720000],
}));
const maxRevenue = computed(() => Math.max(...revenueTrend.value.values, 1));

const orderStatuses = computed(() => ({
    pending: 12,
    processing: 8,
    shipped: 15,
    delivered: 42,
}));
const totalOrders = computed(() => {
    const s = orderStatuses.value;
    return s.pending + s.processing + s.shipped + s.delivered;
});
</script>

<template>
    <AuthenticatedLayout>

        <Head title="CEO Dashboard" />
        <div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6 sm:space-y-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                        Executive <span class="text-blue-600">Dashboard</span>
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ getGreeting() }}, Monti Leadership</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <Calendar class="w-4 h-4" />
                    <span>{{ new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
                        }}</span>
                </div>
            </div>

            <!-- Key Metrics Row (Enhanced) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div
                    class="group bg-white dark:bg-zinc-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-800 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Orders</p>
                            <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">{{
                                stats.totalOrders }}</p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1 flex items-center gap-1">
                                <ArrowUp class="w-3 h-3" /> +12%
                            </p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-xl group-hover:bg-blue-200 transition">
                            <ShoppingCart class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </div>
                <div
                    class="group bg-white dark:bg-zinc-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-800 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Revenue</p>
                            <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">₱{{
                                stats.totalRevenue?.toLocaleString() }}</p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1">
                                <ArrowUp class="w-3 h-3" /> +8%
                            </p>
                        </div>
                        <div
                            class="p-3 bg-emerald-100 dark:bg-emerald-900/20 rounded-xl group-hover:bg-emerald-200 transition">
                            <DollarSign class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                    </div>
                </div>
                <div
                    class="group bg-white dark:bg-zinc-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-800 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Employees</p>
                            <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">{{
                                stats.activeEmployees }}</p>
                            <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">+5 this quarter</p>
                        </div>
                        <div
                            class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-xl group-hover:bg-purple-200 transition">
                            <Users class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                </div>
                <div
                    class="group bg-white dark:bg-zinc-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-800 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Leads</p>
                            <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">{{
                                stats.pendingLeads }}</p>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Needs attention</p>
                        </div>
                        <div
                            class="p-3 bg-amber-100 dark:bg-amber-900/20 rounded-xl group-hover:bg-amber-200 transition">
                            <Package class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Module Cards (Enhanced) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div
                    class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-950/20 dark:to-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 p-5 hover:shadow-lg transition">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                            <UserCheck class="w-5 h-5 text-blue-600" />
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white">HRM</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Total Employees</span><span
                                class="font-semibold">42</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Open Positions</span><span
                                class="font-semibold">3</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Attendance Rate</span><span
                                class="font-semibold text-green-600">96%</span></div>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-green-50 to-white dark:from-green-950/20 dark:to-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 p-5 hover:shadow-lg transition">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-xl">
                            <Truck class="w-5 h-5 text-green-600" />
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white">SCM</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Active Suppliers</span><span
                                class="font-semibold">24</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Pending Orders</span><span
                                class="font-semibold">8</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">On‑time Delivery</span><span
                                class="font-semibold">94%</span></div>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-yellow-50 to-white dark:from-yellow-950/20 dark:to-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 p-5 hover:shadow-lg transition">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                            <Factory class="w-5 h-5 text-yellow-600" />
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Manufacturing</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Active Orders</span><span
                                class="font-semibold">12</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Machines Running</span><span
                                class="font-semibold">7/10</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Defect Rate</span><span
                                class="font-semibold">2.5%</span></div>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-indigo-50 to-white dark:from-indigo-950/20 dark:to-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 p-5 hover:shadow-lg transition">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl">
                            <Package class="w-5 h-5 text-indigo-600" />
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Inventory</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Total SKUs</span><span
                                class="font-semibold">245</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Low Stock Alerts</span><span
                                class="font-semibold text-red-500">6</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Inventory Value</span><span
                                class="font-semibold">₱2.4M</span></div>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-red-50 to-white dark:from-red-950/20 dark:to-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 p-5 hover:shadow-lg transition">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-xl">
                            <Activity class="w-5 h-5 text-red-600" />
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white">CRM</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Active Leads</span><span
                                class="font-semibold">{{ stats.pendingLeads }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Conversion Rate</span><span
                                class="font-semibold">22%</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Customers</span><span
                                class="font-semibold">18</span></div>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-teal-50 to-white dark:from-teal-950/20 dark:to-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 p-5 hover:shadow-lg transition">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-teal-100 dark:bg-teal-900/30 rounded-xl">
                            <ShoppingCart class="w-5 h-5 text-teal-600" />
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white">E‑Commerce</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Active Products</span><span
                                class="font-semibold">127</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Monthly Sales</span><span
                                class="font-semibold">₱850K</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Cart Abandonment</span><span
                                class="font-semibold">34%</span></div>
                    </div>
                </div>
            </div>

            <!-- Charts Row (Real CSS Charts) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Revenue Trend Bar Chart -->
                <div
                    class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <BarChart3 class="w-4 h-4 text-blue-500" />
                            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">Revenue Trend (Last 6 Months)
                            </h3>
                        </div>
                        <TrendingUp class="w-4 h-4 text-gray-400" />
                    </div>
                    <div class="h-48 flex items-end gap-2">
                        <div v-for="(value, idx) in revenueTrend.values" :key="idx"
                            class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-gradient-to-t from-blue-500 to-blue-600 rounded-t-lg transition-all duration-500"
                                :style="{ height: `${(value / maxRevenue) * 100}%`, minHeight: '4px' }"></div>
                            <span class="text-[10px] sm:text-xs mt-2 text-gray-500">{{ revenueTrend.months[idx]
                                }}</span>
                            <span class="text-[9px] font-bold text-gray-600 dark:text-gray-400">₱{{ (value /
                                1000).toFixed(0) }}K</span>
                        </div>
                    </div>
                </div>

                <!-- Order Status Distribution (Stacked Bars) -->
                <div
                    class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center gap-2 mb-4">
                        <PieChart class="w-4 h-4 text-emerald-500" />
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">Order Status Distribution</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="w-20 text-xs font-medium text-gray-600">Pending</span>
                            <div class="flex-1 h-6 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500 rounded-full transition-all duration-700"
                                    :style="{ width: `${(orderStatuses.pending / totalOrders) * 100}%` }"></div>
                            </div>
                            <span class="text-sm font-bold">{{ orderStatuses.pending }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-20 text-xs font-medium text-gray-600">Processing</span>
                            <div class="flex-1 h-6 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full transition-all duration-700"
                                    :style="{ width: `${(orderStatuses.processing / totalOrders) * 100}%` }"></div>
                            </div>
                            <span class="text-sm font-bold">{{ orderStatuses.processing }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-20 text-xs font-medium text-gray-600">Shipped</span>
                            <div class="flex-1 h-6 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-500 rounded-full transition-all duration-700"
                                    :style="{ width: `${(orderStatuses.shipped / totalOrders) * 100}%` }"></div>
                            </div>
                            <span class="text-sm font-bold">{{ orderStatuses.shipped }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-20 text-xs font-medium text-gray-600">Delivered</span>
                            <div class="flex-1 h-6 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 rounded-full transition-all duration-700"
                                    :style="{ width: `${(orderStatuses.delivered / totalOrders) * 100}%` }"></div>
                            </div>
                            <span class="text-sm font-bold">{{ orderStatuses.delivered }}</span>
                        </div>
                    </div>
                    <div
                        class="mt-4 pt-4 border-t border-gray-100 dark:border-zinc-800 flex justify-between text-xs text-gray-500">
                        <span>Total Orders: {{ totalOrders }}</span>
                        <span>Delivered: {{ ((orderStatuses.delivered / totalOrders) * 100).toFixed(1) }}%</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity (Enhanced) -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800">
                <div class="flex items-center gap-2 mb-4">
                    <Clock class="w-4 h-4 text-gray-500" />
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">Recent Activity</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 pb-3 border-b border-gray-100 dark:border-zinc-800">
                        <div class="p-1.5 bg-green-100 dark:bg-green-900/20 rounded-lg">
                            <ShoppingCart class="w-3 h-3 text-green-600" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">New order #PO-2026-00045 placed
                            </p>
                            <p class="text-xs text-gray-500">2 minutes ago</p>
                        </div>
                        <CheckCircle2 class="w-4 h-4 text-green-500" />
                    </div>
                    <div class="flex items-start gap-3 pb-3 border-b border-gray-100 dark:border-zinc-800">
                        <div class="p-1.5 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                            <Users class="w-3 h-3 text-blue-600" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">New employee hired: Sarah
                                Johnson</p>
                            <p class="text-xs text-gray-500">1 hour ago</p>
                        </div>
                        <CheckCircle2 class="w-4 h-4 text-blue-500" />
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="p-1.5 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                            <Package class="w-3 h-3 text-amber-600" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Low stock alert: Yarn type
                                'Polyester'</p>
                            <p class="text-xs text-gray-500">3 hours ago</p>
                        </div>
                        <AlertTriangle class="w-4 h-4 text-amber-500" />
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <button class="text-xs text-blue-600 dark:text-blue-400 font-medium hover:underline">View all
                        activity →</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>