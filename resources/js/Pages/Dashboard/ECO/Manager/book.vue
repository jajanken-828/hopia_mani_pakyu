<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus,
    Search,
    Pencil,
    Trash2,
    Tag,
    Users,
    Percent,
    X,
    Check,
    Filter,
    ArrowDownToLine,
    Ticket,
    Building2,
    ExternalLink,
    ShieldCheck,
    ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
    priceBooks: {
        type: Object,
        default: () => ({ data: [], links: [], meta: {} }),
    },
    customerRates: {
        type: Array,
        default: () => [
            { id: 101, company: 'TechLogistics Corp', rate: '12%', status: 'active', type: 'Fixed' },
            { id: 102, company: 'Manila Build Supplies', rate: 'Tier: Gold', status: 'active', type: 'Tiered' },
        ]
    },
    filters: { type: Object, default: () => ({ search: '' }) },
});

// Tab State
const activeTab = ref('tiers');

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedPriceBook = ref(null);
const search = ref(props.filters.search);

let searchTimeout;
const updateSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('eco.manager.book'), { search: search.value }, { preserveState: true, replace: true });
    }, 300);
};

const createForm = useForm({ name: '', discount: '', target: '', status: 'active', members: 0 });
const editForm = useForm({ name: '', discount: '', target: '', status: 'active', members: 0 });

const toggleStatus = (priceBook) => {
    router.patch(route('eco.manager.book.toggle-status', priceBook.id), {}, { preserveState: true, preserveScroll: true });
};

const stats = computed(() => [
    { label: 'Active Price Books', value: props.priceBooks.data?.filter(pb => pb.status === 'active').length || 0, icon: Tag, color: 'text-indigo-600', bg: 'bg-indigo-50' },
    { label: 'Total Members', value: props.priceBooks.data?.reduce((acc, pb) => acc + (pb.members || 0), 0) || 0, icon: Users, color: 'text-blue-600', bg: 'bg-blue-50' },
    { label: 'Discounted Sales', value: '₱1.1M', icon: Ticket, color: 'text-emerald-600', bg: 'bg-emerald-50' },
    { label: 'Avg. Discount', value: props.priceBooks.data?.length ? (props.priceBooks.data.reduce((acc, pb) => acc + (parseFloat(pb.discount) || 0), 0) / props.priceBooks.data.length).toFixed(1) + '%' : '0%', icon: Percent, color: 'text-amber-600', bg: 'bg-amber-50' },
]);
</script>

<template>

    <Head title="Price Management - Partner Portal" />

    <AuthenticatedLayout>
        <div class="max-w-[1600px] mx-auto space-y-8 p-4 lg:p-10">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <div
                        class="flex items-center gap-2 text-indigo-600 font-black text-[10px] uppercase tracking-[0.2em]">
                        <ShieldCheck class="h-3.5 w-3.5" />
                        Revenue & Margin Control
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter uppercase">
                        Pricing <span class="text-indigo-600">Architect</span>
                    </h1>
                    <p class="text-sm font-medium text-gray-500 italic">
                        Define tiered structures and individual corporate rates
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        class="flex items-center gap-2 px-6 py-3 rounded-[1.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition-all">
                        <ArrowDownToLine class="h-4 w-4" />
                        Export Table
                    </button>
                    <button @click="showCreateModal = true"
                        class="flex items-center gap-2 px-6 py-3 rounded-[1.5rem] bg-indigo-600 text-white shadow-lg shadow-indigo-500/20 text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all">
                        <Plus class="h-4 w-4" />
                        {{ activeTab === 'tiers' ? 'Create Tier' : 'Assign Rate' }}
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div v-for="stat in stats" :key="stat.label"
                    class="p-7 rounded-[2.5rem] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ stat.label }}</p>
                    <div class="flex items-center justify-between">
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{
                            stat.value }}</h3>
                        <div :class="stat.bg" class="p-2.5 rounded-xl transition-colors">
                            <component :is="stat.icon" :class="stat.color" class="h-6 w-6" />
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div
                    class="p-8 border-b border-gray-50 dark:border-gray-800 flex flex-col lg:flex-row justify-between items-center gap-6">
                    <div class="flex p-1.5 bg-gray-50 dark:bg-gray-950 rounded-2xl w-full lg:w-auto">
                        <button @click="activeTab = 'tiers'"
                            :class="activeTab === 'tiers' ? 'bg-white dark:bg-gray-800 shadow-sm text-indigo-600' : 'text-gray-400'"
                            class="flex-1 lg:flex-none px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                            Pricing Tiers
                        </button>
                        <button @click="activeTab = 'rates'"
                            :class="activeTab === 'rates' ? 'bg-white dark:bg-gray-800 shadow-sm text-indigo-600' : 'text-gray-400'"
                            class="flex-1 lg:flex-none px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                            Individual Rates
                        </button>
                    </div>

                    <div class="flex items-center gap-4 w-full lg:w-auto">
                        <div class="relative flex-1 lg:w-80 group">
                            <Search
                                class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors" />
                            <input v-model="search" @input="updateSearch" type="text" placeholder="Search entries..."
                                class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-gray-100 dark:border-gray-800 dark:bg-gray-950 text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder:text-gray-400">
                        </div>
                        <button
                            class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-indigo-600 transition-colors">
                            <Filter class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table v-if="activeTab === 'tiers'" class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-gray-800/30 text-[10px] font-black uppercase text-gray-400 tracking-[0.15em]">
                                <th class="px-8 py-5">Tier Configuration</th>
                                <th class="px-8 py-5">Discount Rate</th>
                                <th class="px-8 py-5">Target Eligibility</th>
                                <th class="px-8 py-5 text-center">Partners</th>
                                <th class="px-8 py-5 text-center">Status</th>
                                <th class="px-8 py-5 text-right px-10">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <tr v-for="book in priceBooks.data" :key="book.id"
                                class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-12 w-12 rounded-[1rem] bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 border border-indigo-100 dark:border-indigo-800/50 shadow-sm">
                                            <Tag class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                                                {{ book.name }}</p>
                                            <p
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">
                                                Tier Index #{{ book.id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-lg font-black text-indigo-600 italic">-{{ book.discount }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{
                                        book.target }}</p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <Users class="h-3.5 w-3.5 text-gray-400" />
                                        <span class="text-xs font-black text-gray-900 dark:text-white">{{ book.members
                                            || 0 }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-center">
                                        <button @click="toggleStatus(book)"
                                            :class="book.status === 'active' ? 'bg-green-50 text-green-600 border-green-100 dark:bg-green-900/20' : 'bg-red-50 text-red-400 border-red-100 dark:bg-red-900/20'"
                                            class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border transition-all active:scale-95">
                                            {{ book.status === 'active' ? 'Active' : 'Disabled' }}
                                        </button>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-2 px-2">
                                        <button @click="openEditModal(book)"
                                            class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-indigo-600 transition-all">
                                            <Pencil class="h-4 w-4" />
                                        </button>
                                        <button @click="confirmDelete(book)"
                                            class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-red-500 transition-all">
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                        <Link :href="route('eco.manager.book.show', book.id)"
                                            class="flex items-center gap-2 px-5 py-2.5 bg-gray-950 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-md">
                                            Manage
                                            <ChevronRight class="h-3.5 w-3.5" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table v-if="activeTab === 'rates'" class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-gray-800/30 text-[10px] font-black uppercase text-gray-400 tracking-[0.15em]">
                                <th class="px-8 py-5">Corporate Partner</th>
                                <th class="px-8 py-5">Pricing Model</th>
                                <th class="px-8 py-5">Applied Rate</th>
                                <th class="px-8 py-5 text-center">Status</th>
                                <th class="px-8 py-5 text-right px-10">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <tr v-for="rate in customerRates" :key="rate.id"
                                class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-12 w-12 rounded-[1rem] bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 border border-blue-100 dark:border-blue-800/50 shadow-sm">
                                            <Building2 class="h-6 w-6" />
                                        </div>
                                        <p
                                            class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                                            {{ rate.company }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{
                                        rate.type }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-lg font-black text-indigo-600 italic tracking-tighter">{{ rate.rate
                                        }}</p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span
                                        class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 text-[9px] font-black uppercase tracking-widest">
                                        {{ rate.status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end px-2">
                                        <button
                                            class="flex items-center gap-2 px-6 py-3 bg-gray-950 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-md">
                                            Adjust Rate
                                            <ExternalLink class="h-3.5 w-3.5 ml-1" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="flex flex-col md:flex-row items-center justify-between p-8 rounded-[2.5rem] bg-indigo-50 dark:bg-indigo-900/10 border border-dashed border-indigo-200 dark:border-indigo-800">
                <div class="flex items-center gap-5 text-center md:text-left mb-4 md:mb-0">
                    <div
                        class="h-14 w-14 rounded-[1.5rem] bg-white dark:bg-gray-900 flex items-center justify-center text-indigo-600 shadow-sm">
                        <Percent class="h-7 w-7" />
                    </div>
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-tight text-gray-900 dark:text-white">Margin
                            Safeguard Active</h4>
                        <p class="text-xs font-medium text-gray-500">Tier discounts exceeding 25% require senior
                            financial auditor approval.</p>
                    </div>
                </div>
                <button
                    class="flex items-center gap-2 text-xs font-black text-indigo-600 uppercase tracking-widest hover:underline px-6 py-3 rounded-2xl bg-white dark:bg-gray-900 shadow-sm">
                    Pricing Policy
                    <ChevronRight class="h-4 w-4" />
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.tracking-tighter {
    letter-spacing: -0.05em;
}

.tracking-tight {
    letter-spacing: -0.02em;
}

::-webkit-scrollbar {
    width: 5px;
    height: 5px;
}

::-webkit-scrollbar-thumb {
    background: #E5E7EB;
    border-radius: 10px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>