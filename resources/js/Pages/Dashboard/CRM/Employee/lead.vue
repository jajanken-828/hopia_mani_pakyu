<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Plus, DollarSign, Calendar, X,
    GripVertical, CheckCircle2, AlertCircle, HelpCircle, ArrowRight, UserCheck, Building2
} from 'lucide-vue-next';

const props = defineProps({
    leads: Array
});

// --- State Management ---
const showCreateModal = ref(false);
const showDropConfirm = ref(false);
const showClientConversionModal = ref(false);
const draggingLeadId = ref(null);
const pendingMove = ref({ leadId: null, newStatus: null, companyName: '' });

const form = useForm({
    company_name: '',
    contact_person: '',
    email: '',
    phone: '',
    interest_fabric: 'Cotton',
    estimated_value: '',
});

const conversionForm = useForm({
    lead_id: null,
    company_name: '',
    contact_person: '',
    email: '',
    phone: '',
    business_type: 'wholesaler',
    tin_number: '',
    company_address: '',
    password: 'password123',
});

const columns = [
    { title: 'New Inquiry', status: 'Inquiry' },
    { title: 'Negotiation', status: 'Negotiation' },
    { title: 'Approval Sent', status: 'Approval Sent' },
    { title: 'Closed-Won', status: 'Closed-Won' }
];

const getLeadsByStatus = (status) => {
    return props.leads?.filter(lead => {
        return lead.status === status && lead.status !== 'Converted';
    }) || [];
};

// --- Conversion Logic ---
const openConversionModal = (lead) => {
    conversionForm.lead_id = lead.id;
    conversionForm.company_name = lead.company_name;
    conversionForm.contact_person = lead.contact_person;
    conversionForm.email = lead.email;
    conversionForm.phone = lead.phone;
    showClientConversionModal.value = true;
};

const submitConversion = () => {
    conversionForm.post(route('crm.lead.convert'), {
        preserveScroll: true,
        onSuccess: () => {
            showClientConversionModal.value = false;
            conversionForm.reset();
            router.reload({ only: ['leads'] });
        },
    });
};

// --- Drag and Drop Logic ---
const onDragStart = (lead) => {
    draggingLeadId.value = lead.id;
    pendingMove.value.companyName = lead.company_name;
};

const onDrop = (newStatus) => {
    if (!draggingLeadId.value) return;
    const lead = props.leads.find(l => l.id === draggingLeadId.value);
    const currentIndex = columns.findIndex(c => c.status === lead.status);
    const newIndex = columns.findIndex(c => c.status === newStatus);

    if (newIndex - currentIndex !== 1) {
        draggingLeadId.value = null;
        return;
    }

    pendingMove.value.leadId = draggingLeadId.value;
    pendingMove.value.newStatus = newStatus;
    showDropConfirm.value = true;
};

const confirmMove = () => {
    router.patch(route('crm.lead.status', pendingMove.value.leadId), {
        status: pendingMove.value.newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            closeConfirm();
        }
    });
};

const closeConfirm = () => {
    showDropConfirm.value = false;
    draggingLeadId.value = null;
    pendingMove.value = { leadId: null, newStatus: null, companyName: '' };
};

const submit = () => {
    form.post(route('crm.lead.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        },
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(value || 0);
};
</script>

<template>
    <AuthenticatedLayout title="Lead & Deal Workspace">
        <div class="p-4 md:p-6 space-y-6 bg-slate-50/50 dark:bg-zinc-950 min-h-screen">
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-200 dark:border-zinc-800 pb-6">
                <div>
                    <h1 class="text-xl font-black tracking-tight text-blue-600 uppercase">
                        Linear <span class="text-gray-900 dark:text-white">Deal Pipeline</span>
                    </h1>
                </div>
                <button @click="showCreateModal = true"
                    class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all">
                    <Plus class="w-4 h-4" /> Create Deal
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pb-6">
                <div v-for="(column, index) in columns" :key="column.status" class="flex flex-col group"
                    @dragover.prevent @drop="onDrop(column.status)">

                    <div class="flex items-center justify-between mb-3 px-2">
                        <div class="flex items-center gap-2">
                            <span
                                class="text-[8px] font-black bg-gray-200 dark:bg-zinc-800 text-gray-500 rounded-full h-4 w-4 flex items-center justify-center">
                                {{ index + 1 }}
                            </span>
                            <h3
                                class="text-[9px] font-black uppercase tracking-wider text-gray-500 group-hover:text-blue-600 transition-colors">
                                {{ column.title }}
                            </h3>
                        </div>
                        <span
                            class="text-[9px] font-bold bg-blue-50 text-blue-600 dark:bg-zinc-800 px-2 py-0.5 rounded-md">
                            {{ getLeadsByStatus(column.status).length }}
                        </span>
                    </div>

                    <div
                        class="space-y-3 min-h-[calc(100vh-220px)] p-2 bg-gray-100/50 dark:bg-zinc-900/50 rounded-[1.5rem] border-2 border-transparent transition-all group-hover:border-blue-100 dark:group-hover:border-zinc-800">
                        <div v-for="lead in getLeadsByStatus(column.status)" :key="lead.id" draggable="true"
                            @dragstart="onDragStart(lead)"
                            class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 p-4 rounded-xl shadow-sm hover:shadow-md hover:scale-[1.01] cursor-grab active:cursor-grabbing transition-all group/card relative">

                            <div
                                class="absolute right-3 top-3 opacity-0 group-hover/card:opacity-100 transition-opacity">
                                <GripVertical class="w-3 h-3 text-gray-300" />
                            </div>

                            <p
                                class="text-[10px] font-black uppercase text-gray-900 dark:text-white mb-2 pr-4 italic truncate">
                                {{ lead.company_name }}
                            </p>

                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2 text-[9px] font-bold text-gray-500 uppercase">
                                    <div class="p-1 rounded bg-green-50 text-green-600">
                                        <DollarSign class="w-2.5 h-2.5" />
                                    </div>
                                    {{ formatCurrency(lead.estimated_value) }}
                                </div>
                                <div class="flex items-center gap-2 text-[9px] font-bold text-gray-500 uppercase">
                                    <div class="p-1 rounded bg-blue-50 text-blue-600">
                                        <Calendar class="w-2.5 h-2.5" />
                                    </div>
                                    {{ lead.interest_fabric }}
                                </div>
                            </div>

                            <div
                                class="mt-3 pt-3 border-t border-gray-50 dark:border-zinc-800 flex justify-between items-center">
                                <span class="text-[8px] font-black uppercase text-gray-400 italic">#{{ lead.id }}</span>
                                <div v-if="lead.status === 'Closed-Won'" class="flex items-center gap-1.5">
                                    <button @click.stop="openConversionModal(lead)"
                                        class="flex items-center gap-1 px-2 py-1 bg-green-600 text-white rounded-md text-[8px] font-black uppercase hover:bg-green-700 transition-colors shadow-md">
                                        <UserCheck class="w-2.5 h-2.5" /> client account create
                                    </button>
                                    <CheckCircle2 class="w-3 h-3 text-green-500" />
                                </div>
                            </div>
                        </div>

                        <div v-if="getLeadsByStatus(column.status).length === 0"
                            class="h-24 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 dark:border-zinc-800 rounded-xl">
                            <AlertCircle class="w-4 h-4 text-gray-300 mb-1" />
                            <p class="text-[8px] font-black text-gray-300 uppercase italic">Empty</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="showClientConversionModal"
                class="fixed inset-0 z-[130] flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
                <div
                    class="bg-white dark:bg-zinc-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-200 dark:border-zinc-800">
                    <div
                        class="p-8 border-b border-gray-100 dark:border-zinc-800 flex justify-between items-center bg-green-50/50 dark:bg-green-900/10">
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest italic text-green-700">Promote to
                                Business Client</h3>
                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-1 italic">Finalizing Partnership
                                for {{ conversionForm.company_name }}</p>
                        </div>
                        <button @click="showClientConversionModal = false" class="hover:rotate-90 transition-transform">
                            <X class="w-6 h-6 text-gray-400" />
                        </button>
                    </div>
                    <form @submit.prevent="submitConversion" class="p-8 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-gray-400 uppercase ml-2">Business Type</label>
                                <select v-model="conversionForm.business_type"
                                    class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-xl text-[10px] font-black uppercase p-4">
                                    <option value="wholesaler">Wholesaler</option>
                                    <option value="retailer">Retailer</option>
                                    <option value="manufacturer">Manufacturer</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-gray-400 uppercase ml-2">TIN Number</label>
                                <input v-model="conversionForm.tin_number" placeholder="000-000-000" type="text"
                                    class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-xl text-[10px] font-black p-4"
                                    required />
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-gray-400 uppercase ml-2">Official Company
                                Address</label>
                            <textarea v-model="conversionForm.company_address" rows="3"
                                placeholder="COMPLETE BUSINESS ADDRESS"
                                class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-xl text-[10px] font-black p-4"
                                required></textarea>
                        </div>
                        <button type="submit" :disabled="conversionForm.processing"
                            class="w-full py-4 bg-green-600 text-white rounded-2xl text-[10px] font-black uppercase shadow-xl hover:brightness-110 transition-all flex items-center justify-center gap-2">
                            <Building2 class="w-4 h-4" />
                            {{ conversionForm.processing ? 'Converting...' : 'Finalize Official Client' }}
                        </button>
                    </form>
                </div>
            </div>

            <div v-if="showCreateModal"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
                <div
                    class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-200 dark:border-zinc-800">
                    <div
                        class="p-8 border-b border-gray-100 dark:border-zinc-800 flex justify-between items-center bg-gray-50 dark:bg-zinc-800/50">
                        <h3 class="text-sm font-black uppercase tracking-widest italic">Initiate New Textile Deal</h3>
                        <button @click="showCreateModal = false" class="hover:rotate-90 transition-transform">
                            <X class="w-6 h-6 text-gray-400" />
                        </button>
                    </div>
                    <form @submit.prevent="submit" class="p-8 space-y-5">
                        <div class="space-y-4">
                            <input v-model="form.company_name" placeholder="COMPANY NAME" type="text"
                                class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-xl text-xs font-black uppercase p-4"
                                required />
                            <input v-model="form.contact_person" placeholder="CONTACT PERSON" type="text"
                                class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-xl text-xs font-black uppercase p-4"
                                required />
                            <div class="grid grid-cols-2 gap-4">
                                <input v-model="form.email" placeholder="EMAIL" type="email"
                                    class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-xl text-[10px] font-black uppercase p-4"
                                    required />
                                <input v-model="form.phone" placeholder="PHONE" type="text"
                                    class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-xl text-[10px] font-black uppercase p-4"
                                    required />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <input v-model="form.estimated_value" placeholder="VALUE (₱)" type="number"
                                    class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-xl text-xs font-black uppercase p-4"
                                    required />
                                <select v-model="form.interest_fabric"
                                    class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-xl text-xs font-black uppercase p-4">
                                    <option>Cotton</option>
                                    <option>Wool</option>
                                    <option>Nylon</option>
                                    <option>Polyester</option>
                                    <option>Silk</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" :disabled="form.processing"
                            class="w-full py-4 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase shadow-xl hover:brightness-110 transition-all">
                            {{ form.processing ? 'Syncing Pipeline...' : 'Confirm & Initiate Deal' }}
                        </button>
                    </form>
                </div>
            </div>

            <div v-if="showDropConfirm"
                class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                <div
                    class="bg-white dark:bg-zinc-900 w-full max-w-sm rounded-[2.5rem] shadow-2xl p-8 text-center border border-gray-100 dark:border-zinc-800">
                    <div
                        class="h-16 w-16 rounded-3xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 mx-auto mb-6">
                        <HelpCircle class="h-8 w-8" />
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter mb-2 italic">
                        Confirm Progress?</h3>
                    <p class="text-[11px] text-gray-500 mb-8 font-bold uppercase leading-relaxed">
                        Advance <span class="text-blue-600">{{ pendingMove.companyName }}</span> to <span
                            class="text-blue-600">{{ pendingMove.newStatus }}</span>?
                    </p>
                    <div class="flex gap-3">
                        <button @click="closeConfirm"
                            class="flex-1 py-4 rounded-2xl bg-gray-50 dark:bg-zinc-800 text-[10px] font-black uppercase text-gray-400">Cancel</button>
                        <button @click="confirmMove"
                            class="flex-1 py-4 rounded-2xl bg-blue-600 text-white text-[10px] font-black uppercase shadow-lg shadow-blue-500/20">Verify
                            & Move</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Scrollbar only appears if columns wrap on mobile */
.custom-scrollbar::-webkit-scrollbar {
    height: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
</style>