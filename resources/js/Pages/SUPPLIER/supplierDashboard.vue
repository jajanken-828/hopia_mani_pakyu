<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    auth: Object,
    stats: {
        type: Object,
        default: () => ({
            activeOrders: 12,
            pendingInvoices: 4,
            totalShipments: 158,
            qualityRating: '98%'
        })
    }
});

const isLoaded = ref(false);

onMounted(() => {
    isLoaded.value = true;
});
</script>

<template>

    <Head title="Supplier Hub | Monti Textile" />

    <AuthenticatedLayout>
        <!-- Page Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Vendor Overview</h1>
                <p class="text-slate-500 text-sm mt-0.5">Welcome back, {{ auth.user.representative_name }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ auth.user.business_name }}</p>
                    <p class="text-xs text-emerald-600 font-mono">ID: VND-{{ auth.user.id.toString().padStart(4, '0') }}
                    </p>
                </div>
                <div
                    class="size-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold border border-emerald-200">
                    {{ auth.user.representative_name.charAt(0) }}
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8 transition-all duration-700"
            :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="(val, label) in stats" :key="label"
                    class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <p class="text-slate-500 text-sm font-medium uppercase tracking-wider mb-1">{{
                        label.replace(/([A-Z])/g, ' $1') }}</p>
                    <p class="text-3xl font-black text-slate-900 dark:text-white">{{ val }}</p>
                </div>
            </div>

            <!-- Purchase Orders Table -->
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Active Purchase Orders</h3>
                    <button class="text-emerald-600 font-bold text-sm hover:underline">View All</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead
                            class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase font-bold tracking-widest">
                            <tr>
                                <th class="px-6 py-4">PO Number</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Expected Date</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="i in 3" :key="i"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-slate-700 dark:text-slate-300">
                                    PO-2026-00{{ i }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-700 border border-amber-200">
                                        Processing
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">March 24, 2026</td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        class="text-emerald-600 hover:text-emerald-700 font-bold text-sm">Review</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>