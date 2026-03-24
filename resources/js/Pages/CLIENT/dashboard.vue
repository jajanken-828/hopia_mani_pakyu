<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Calendar, Clock, DollarSign, ShoppingBag, FileText, X, CheckCircle, XCircle, Package } from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
    calendarEvents: Array,
});

// ── Calendar Helpers ─────────────────────────────────────────────
const today = new Date();
const currentYear = ref(today.getFullYear());
const currentMonth = ref(today.getMonth()); // 0-indexed

const daysInMonth = computed(() => {
    return new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
});

const firstDayOfMonth = computed(() => {
    return new Date(currentYear.value, currentMonth.value, 1).getDay();
});

const calendarDays = computed(() => {
    const days = [];
    const totalDays = daysInMonth.value;
    const startDay = firstDayOfMonth.value;

    // Fill leading empty cells
    for (let i = 0; i < startDay; i++) {
        days.push({ date: null, events: [] });
    }

    // Fill actual days
    for (let d = 1; d <= totalDays; d++) {
        const dateStr = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const events = props.calendarEvents?.filter(e => e.date === dateStr) || [];
        days.push({ date: d, fullDate: dateStr, events });
    }

    return days;
});

const goPrevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const goNextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

// ── Modal for Event Details ─────────────────────────────────────
const showModal = ref(false);
const selectedEvents = ref([]);

const openDay = (day) => {
    if (day.events && day.events.length) {
        selectedEvents.value = day.events;
        showModal.value = true;
    }
};

const closeModal = () => {
    showModal.value = false;
    selectedEvents.value = [];
};

// Quotation actions
const handleAccept = (quotationId) => {
    router.post(route('client.quotations.accept', quotationId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        }
    });
};

const handleReject = (quotationId) => {
    if (confirm('Are you sure you want to reject this quotation?')) {
        router.post(route('client.quotations.reject', quotationId), {}, {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            }
        });
    }
};

// Order approval action
const handleApproveOrder = (orderId) => {
    router.post(route('client.orders.accept', orderId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        }
    });
};

// Helper to format currency
const formatCurrency = (val) => '₱' + Number(val).toLocaleString('en-PH', { minimumFractionDigits: 2 });
const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });

// Get event color class for calendar dot or list
const getEventColorClass = (type) => {
    const map = {
        quotation_sent: 'bg-blue-100 text-blue-700 border-blue-200',
        quotation_ready: 'bg-green-100 text-green-700 border-green-200',
        order_ready: 'bg-purple-100 text-purple-700 border-purple-200',
        payment_due: 'bg-red-100 text-red-700 border-red-200'
    };
    return map[type] || 'bg-gray-100 text-gray-700';
};
</script>

<template>

    <Head title="Client Dashboard" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto space-y-8 p-4">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Orders</p>
                            <p class="text-3xl font-bold">{{ stats.total_orders }}</p>
                        </div>
                        <ShoppingBag class="h-8 w-8 text-blue-500" />
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Quotations Sent</p>
                            <p class="text-3xl font-bold">{{ stats.total_quotations_sent }}</p>
                        </div>
                        <FileText class="h-8 w-8 text-green-500" />
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Pending Settlements</p>
                            <p class="text-3xl font-bold">{{ stats.pending_settlements }}</p>
                        </div>
                        <DollarSign class="h-8 w-8 text-red-500" />
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <Calendar class="h-5 w-5" />
                        Activity Calendar
                    </h2>
                    <div class="flex gap-2">
                        <button @click="goPrevMonth"
                            class="px-3 py-1 rounded border hover:bg-gray-100 dark:hover:bg-gray-700">
                            ←
                        </button>
                        <span class="px-4 py-1 font-semibold">{{ monthNames[currentMonth] }} {{ currentYear }}</span>
                        <button @click="goNextMonth"
                            class="px-3 py-1 rounded border hover:bg-gray-100 dark:hover:bg-gray-700">
                            →
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-7 gap-2 text-center mb-2">
                    <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day"
                        class="font-semibold py-2 text-sm">
                        {{ day }}
                    </div>
                </div>
                <div class="grid grid-cols-7 gap-2">
                    <div v-for="(day, idx) in calendarDays" :key="idx" @click="openDay(day)"
                        class="min-h-[80px] p-2 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                        :class="{
                            'bg-blue-50 dark:bg-blue-900/20': day.date === new Date().getDate() && currentMonth === new Date().getMonth() && currentYear === new Date().getFullYear(),
                            'hover:shadow-md': day.events.length
                        }">
                        <span class="text-sm font-medium">{{ day.date || '' }}</span>
                        <div class="mt-1 space-y-1">
                            <div v-for="event in day.events.slice(0, 2)" :key="event.id"
                                class="text-[10px] truncate px-1 py-0.5 rounded"
                                :class="getEventColorClass(event.type)">
                                {{ event.title }}
                            </div>
                            <div v-if="day.events.length > 2" class="text-[9px] text-gray-400 italic">+{{
                                day.events.length - 2 }} more</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Events List (Quick Overview) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <Clock class="h-5 w-5" /> Upcoming Events
                </h2>
                <div v-if="calendarEvents.length === 0" class="text-gray-500">No upcoming events.</div>
                <div v-else class="space-y-2">
                    <div v-for="event in calendarEvents.slice(0, 5)" :key="event.id"
                        @click="openDay({ events: [event] })"
                        class="border-l-4 pl-3 py-1 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                        :class="{
                            'border-blue-500': event.type === 'quotation_sent',
                            'border-green-500': event.type === 'quotation_ready',
                            'border-purple-500': event.type === 'order_ready',
                            'border-red-500': event.type === 'payment_due'
                        }">
                        <p class="font-semibold">{{ event.title }}</p>
                        <p class="text-sm text-gray-500">{{ formatDate(event.date) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Event Details -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
                @click.self="closeModal">
                <div class="bg-white dark:bg-gray-900 rounded-xl max-w-lg w-full max-h-[80vh] overflow-y-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Events on {{ selectedEvents[0] ?
                            formatDate(selectedEvents[0].date) : '' }}</h2>
                        <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                            <X class="h-5 w-5" />
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div v-for="event in selectedEvents" :key="event.id" class="border-b pb-3 last:border-0">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-2 h-2 rounded-full" :class="{
                                    'bg-blue-500': event.type === 'quotation_sent',
                                    'bg-green-500': event.type === 'quotation_ready',
                                    'bg-purple-500': event.type === 'order_ready',
                                    'bg-red-500': event.type === 'payment_due'
                                }"></div>
                                <span class="font-bold">{{ event.title }}</span>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                <p v-if="event.type === 'quotation_ready' || event.type === 'quotation_sent'">
                                    Quotation #: {{ event.details?.quotation_number }}<br>
                                    Grand Total: {{ formatCurrency(event.details?.grand_total) }}<br>
                                    Valid until: {{ formatDate(event.details?.valid_until) }}
                                </p>
                                <p v-if="event.type === 'order_ready'">
                                    Order #: {{ event.details?.po_number }}<br>
                                    Total: {{ formatCurrency(event.details?.total_amount) }}<br>
                                    Tier: {{ event.details?.tier_level || 'Normal' }}
                                </p>
                                <p v-if="event.type === 'payment_due'">
                                    Order #: {{ event.details?.po_number }}<br>
                                    Amount Due: {{ formatCurrency(event.details?.total_amount) }}
                                </p>
                            </div>
                            <div v-if="event.type === 'quotation_ready'" class="mt-3 flex gap-2">
                                <button @click="handleAccept(event.id)"
                                    class="px-3 py-1 bg-green-600 text-white rounded text-sm">
                                    Accept
                                </button>
                                <button @click="handleReject(event.id)"
                                    class="px-3 py-1 bg-red-600 text-white rounded text-sm">
                                    Reject
                                </button>
                            </div>
                            <div v-if="event.type === 'order_ready'" class="mt-3 flex gap-2">
                                <button @click="handleApproveOrder(event.id)"
                                    class="px-3 py-1 bg-purple-600 text-white rounded text-sm">
                                    Approve Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </AuthenticatedLayout>
</template>