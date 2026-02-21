<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    MagnifyingGlassIcon,
    BuildingOfficeIcon,
    CheckIcon,
    XMarkIcon,
    ClockIcon,
    ShieldCheckIcon,
    UserGroupIcon,
    BanknotesIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    pendingCompanies: Array,
    verifiedCompanies: Array
});

// --- Modal State ---
const showModal = ref(false);
const modalConfig = ref({
    title: '',
    message: '',
    confirmText: '',
    confirmColor: '',
    action: null
});

const openConfirmation = (title, message, confirmText, color, action) => {
    modalConfig.value = { title, message, confirmText, confirmColor: color, action };
    showModal.value = true;
};

// --- Computed Lists ---
const pendingList = computed(() => props.pendingCompanies || []);
const verifiedList = computed(() => props.verifiedCompanies || []);
const activeTab = ref('approvals');

// --- Database Operations ---
const executeUpdate = (id, status) => {
    router.patch(route('eco.manager.clients.status.update', id), {
        status: status
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
        }
    });
};

// --- Action Handlers (Triggers Modals) ---
const handleApprove = (company) => {
    openConfirmation(
        'Approve Business',
        `Are you sure you want to activate ${company.company_name}? They will gain full access to the B2B portal.`,
        'Confirm Approval',
        'bg-green-600',
        () => executeUpdate(company.id, 'active')
    );
};

const handleReject = (company) => {
    openConfirmation(
        'Reject Application',
        `Are you sure you want to reject ${company.company_name}? This will deny their portal access.`,
        'Reject Business',
        'bg-red-600',
        () => executeUpdate(company.id, 'rejected')
    );
};

const handleToggleStatus = (company) => {
    const isCurrentlyActive = company.status === 'active';
    const nextStatus = isCurrentlyActive ? 'suspended' : 'active';
    const actionLabel = isCurrentlyActive ? 'Suspend' : 'Activate';
    const color = isCurrentlyActive ? 'bg-red-600' : 'bg-green-600';

    openConfirmation(
        `${actionLabel} Account`,
        `Are you sure you want to ${actionLabel.toLowerCase()} ${company.company_name}?`,
        `Confirm ${actionLabel}`,
        color,
        () => executeUpdate(company.id, nextStatus)
    );
};

const stats = computed(() => [
    { label: 'Pending Approval', value: pendingList.value.length, icon: ClockIcon, color: 'text-orange-500' },
    { label: 'Verified Partners', value: verifiedList.value.length, icon: ShieldCheckIcon, color: 'text-green-600' },
    { label: 'Total B2B Clients', value: pendingList.value.length + verifiedList.value.length, icon: UserGroupIcon, color: 'text-blue-600' },
    { label: 'System Credit', value: '₱4.2M', icon: BanknotesIcon, color: 'text-purple-600' },
]);
</script>

<template>

    <Head title="Partner Verification" />

    <AuthenticatedLayout>
        <div class="max-w-[1600px] mx-auto space-y-8 p-4 lg:p-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <div
                        class="flex items-center gap-2 text-indigo-600 font-black text-[10px] uppercase tracking-[0.2em]">
                        <ShieldCheckIcon class="h-3.5 w-3.5" />
                        ECO Manager Console
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter uppercase">
                        Partner <span class="text-indigo-600">Verification</span>
                    </h1>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div v-for="stat in stats" :key="stat.label"
                    class="p-7 rounded-[2.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ stat.label }}</p>
                    <div class="flex items-center justify-between">
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{
                            stat.value }}</h3>
                        <div :class="stat.color" class="p-2 bg-gray-50 dark:bg-gray-800 rounded-xl">
                            <component :is="stat.icon" class="h-6 w-6" />
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div
                    class="p-8 border-b border-gray-50 dark:border-gray-800 flex flex-col lg:flex-row justify-between items-center gap-6">
                    <div class="flex p-1.5 bg-gray-50 dark:bg-gray-950 rounded-2xl">
                        <button @click="activeTab = 'approvals'"
                            :class="activeTab === 'approvals' ? 'bg-white dark:bg-gray-800 shadow-sm text-indigo-600' : 'text-gray-400'"
                            class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                            Pending Reviews ({{ pendingList.length }})
                        </button>
                        <button @click="activeTab = 'verified'"
                            :class="activeTab === 'verified' ? 'bg-white dark:bg-gray-800 shadow-sm text-indigo-600' : 'text-gray-400'"
                            class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                            Verified Directory ({{ verifiedList.length }})
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-gray-800/30 text-[10px] font-black uppercase text-gray-400 tracking-[0.15em]">
                                <th class="px-8 py-5">Corporate Entity</th>
                                <th class="px-8 py-5">Liaison</th>
                                <th class="px-8 py-5">Status</th>
                                <th class="px-8 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <tr v-for="company in (activeTab === 'approvals' ? pendingList : verifiedList)"
                                :key="company.id" class="group hover:bg-gray-50/30 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 border border-indigo-100 dark:border-indigo-800/50 shadow-sm">
                                            <BuildingOfficeIcon class="h-5 w-5" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                                                {{ company.company_name }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                TIN: {{ company.tin_number || 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p
                                        class="text-xs font-black uppercase tracking-tight text-gray-800 dark:text-gray-200">
                                        {{ company.contact_person }}</p>
                                    <p class="text-[10px] font-bold text-indigo-500 uppercase">{{ company.email }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <span :class="{
                                        'bg-orange-50 text-orange-600 dark:bg-orange-950/30': company.status === 'pending',
                                        'bg-green-50 text-green-600 dark:bg-green-950/30': company.status === 'active',
                                        'bg-red-50 text-red-600 dark:bg-red-950/30': company.status === 'suspended'
                                    }"
                                        class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border border-current opacity-80">
                                        {{ company.status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <template v-if="activeTab === 'approvals'">
                                            <button @click="handleApprove(company)"
                                                class="p-2.5 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-600 hover:scale-110 transition-transform">
                                                <CheckIcon class="h-4 w-4" />
                                            </button>
                                            <button @click="handleReject(company)"
                                                class="p-2.5 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 hover:scale-110 transition-transform">
                                                <XMarkIcon class="h-4 w-4" />
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button @click="handleToggleStatus(company)"
                                                :class="company.status === 'active' ? 'bg-red-600 shadow-red-500/20' : 'bg-green-600 shadow-green-500/20'"
                                                class="px-6 py-2.5 text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg hover:brightness-110 transition-all">
                                                {{ company.status === 'active' ? 'Suspend' : 'Activate' }}
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="showModal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
            <div
                class="bg-white dark:bg-gray-900 w-full max-w-md rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden transform transition-all scale-100">
                <div class="p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="h-12 w-12 rounded-2xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-500">
                            <ExclamationTriangleIcon class="h-6 w-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{
                                modalConfig.title }}</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">System Confirmation
                                Required</p>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-8 leading-relaxed font-medium">
                        {{ modalConfig.message }}
                    </p>

                    <div class="flex gap-3">
                        <button @click="showModal = false"
                            class="flex-1 px-6 py-4 rounded-2xl bg-gray-50 dark:bg-gray-800 text-[10px] font-black uppercase tracking-widest text-gray-500 hover:bg-gray-100 transition-colors">
                            Cancel
                        </button>
                        <button @click="modalConfig.action" :class="modalConfig.confirmColor"
                            class="flex-1 px-6 py-4 rounded-2xl text-white text-[10px] font-black uppercase tracking-widest shadow-lg hover:brightness-110 transition-all">
                            {{ modalConfig.confirmText }}
                        </button>
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