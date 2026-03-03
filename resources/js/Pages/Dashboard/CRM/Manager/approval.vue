<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    CheckCircle2,
    XCircle,
    Clock,
    AlertTriangle,
    Eye
} from 'lucide-vue-next';

const props = defineProps({
    approvalItems: Array
});

const processing = ref(false);

const getPriorityClass = (priority) => {
    if (priority === 'High') return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
    return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
};

const determinePriority = (item) => {
    if (item.total_amount > 5000 || item.status === 'credit_review') return 'High';
    return 'Medium';
};

const handleApprove = (id) => {
    if (confirm('Are you sure you want to approve this order?')) {
        processing.value = true;
        router.post(route('crm.approval.process', id), {
            action: 'approve',
            notes: ''
        }, {
            preserveScroll: true,
            onFinish: () => processing.value = false
        });
    }
};

const handleReject = (id) => {
    const reason = prompt('Please enter a reason for rejection:');
    if (reason !== null) {
        processing.value = true;
        router.post(route('crm.approval.process', id), {
            action: 'reject',
            notes: reason
        }, {
            preserveScroll: true,
            onFinish: () => processing.value = false
        });
    }
};
</script>

<template>
    <AuthenticatedLayout title="CRM Approval Queue">
        <div class="p-6 space-y-6 bg-gray-50 dark:bg-zinc-950 min-h-screen">
            <!-- header unchanged -->
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-200 dark:border-zinc-800 pb-6">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white uppercase">
                        CRM <span class="text-blue-600">Approval Queue</span>
                    </h1>
                    <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest mt-1">
                        Reviewing Textile Order Overrides & Credit Limits
                    </p>
                </div>
                <div class="flex gap-2">
                    <div
                        class="flex items-center gap-2 px-3 py-1 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded text-amber-700 dark:text-amber-400 text-sm">
                        <Clock class="w-4 h-4" />
                        <span class="font-bold uppercase text-[10px] tracking-wider">{{ approvalItems?.length || 0 }}
                            Pending Approvals</span>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-zinc-800/50">
                        <tr>
                            <th
                                class="p-4 text-[10px] font-black uppercase text-gray-500 dark:text-zinc-400 tracking-widest">
                                Request ID</th>
                            <th
                                class="p-4 text-[10px] font-black uppercase text-gray-500 dark:text-zinc-400 tracking-widest">
                                Client / Details</th>
                            <th
                                class="p-4 text-[10px] font-black uppercase text-gray-500 dark:text-zinc-400 tracking-widest text-center">
                                Amount</th>
                            <th
                                class="p-4 text-[10px] font-black uppercase text-gray-500 dark:text-zinc-400 tracking-widest">
                                Priority</th>
                            <th
                                class="p-4 text-[10px] font-black uppercase text-gray-500 dark:text-zinc-400 tracking-widest text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-800">
                        <tr v-for="item in approvalItems" :key="item.id"
                            class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="p-4">
                                <span class="font-mono text-sm font-bold text-blue-600 dark:text-blue-400">{{
                                    item.po_number }}</span>
                                <div class="text-[9px] font-black text-gray-400 mt-1 uppercase tracking-tighter">{{
                                    item.status.replace('_', ' ') }}</div>
                            </td>
                            <td class="p-4">
                                <p class="text-sm font-black text-gray-900 dark:text-white uppercase leading-tight">{{
                                    item.client?.company_name }}</p>
                                <p class="text-[10px] text-gray-500 dark:text-zinc-400 truncate max-w-xs mt-0.5">{{
                                    item.notes || 'No specific details provided.' }}</p>
                            </td>
                            <td class="p-4 text-center">
                                <p class="text-sm font-black text-gray-900 dark:text-white">₱{{
                                    Number(item.total_amount).toLocaleString() }}</p>
                            </td>
                            <td class="p-4">
                                <span
                                    :class="['px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest border', getPriorityClass(determinePriority(item))]">
                                    {{ determinePriority(item) }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="p-2 text-gray-400 hover:text-blue-600 transition"
                                        title="View Details">
                                        <Eye class="w-4 h-4" />
                                    </button>
                                    <button @click="handleReject(item.id)" :disabled="processing"
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-zinc-900 border border-red-200 dark:border-red-900/50 text-red-600 rounded-lg text-[10px] font-black uppercase hover:bg-red-50 transition shadow-sm disabled:opacity-50">
                                        <XCircle class="w-3.5 h-3.5" /> REJECT
                                    </button>
                                    <button @click="handleApprove(item.id)" :disabled="processing"
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-[10px] font-black uppercase hover:bg-blue-700 shadow-md shadow-blue-500/20 transition disabled:opacity-50">
                                        <CheckCircle2 class="w-3.5 h-3.5" /> APPROVE
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!approvalItems || approvalItems.length === 0">
                            <td colspan="5" class="p-12 text-center">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">No pending
                                    approvals found in the queue.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="p-4 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl flex items-start gap-4 shadow-sm">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                    <AlertTriangle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-wider">Managerial Tip
                    </h4>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1 leading-relaxed">
                        High Discount requests older than 24 hours will be automatically escalated. Approving a <span
                            class="text-blue-600 font-bold uppercase tracking-tighter">Credit Limit override</span> will
                        notify the Finance module and update the SCM ledger immediately.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>