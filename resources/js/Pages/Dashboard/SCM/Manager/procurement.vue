<script setup>
import { ref, computed, reactive, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import {
    ClipboardList, FileText, Package, CreditCard, Receipt,
    CheckCircle, XCircle, ChevronDown, ChevronRight, Plus,
    Send, Eye, DollarSign, Truck, AlertTriangle, Users,
    ShoppingCart, Boxes, RefreshCw, Search, X, Building2,
    Star, Clock, BadgeCheck, Ban, Printer, Download,
    ArrowRight, MoreHorizontal, CircleDollarSign, Warehouse
} from 'lucide-vue-next';

// ─────────────────────────────────────────────────────────
// PROPS (from Inertia controller)
// ─────────────────────────────────────────────────────────
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            pendingMaterialRequests: 5,
            activeRFQs: 8,
            pendingPOs: 3,
            unpaidInvoices: 2
        })
    },
    materialRequests: {
        type: Array,
        default: () => [
            { id: 1, req_number: 'MR-2026-001', material_id: 1, material_name: '200GSM Combed Cotton', category: 'Fabric', unit: 'm', current_stock: 850, reorder_point: 1200, required_qty: 1750, urgency: 'High', requested_by: 'Inventory System', requested_at: '2026-03-08', status: 'pending', notes: 'Stock critically low, urgent restock needed.' },
            { id: 2, req_number: 'MR-2026-002', material_id: 2, material_name: 'Reactive Dye (Navy)', category: 'Chemicals', unit: 'L', current_stock: 15, reorder_point: 50, required_qty: 100, urgency: 'High', requested_by: 'Inventory System', requested_at: '2026-03-08', status: 'rfq_sent', notes: 'Dye house running low.' },
            { id: 3, req_number: 'MR-2026-003', material_id: 3, material_name: 'Branded Resin Buttons', category: 'Trim', unit: 'pcs', current_stock: 12000, reorder_point: 15000, required_qty: 25000, urgency: 'Medium', requested_by: 'Inventory System', requested_at: '2026-03-09', status: 'pending', notes: '' },
            { id: 4, req_number: 'MR-2026-004', material_id: 4, material_name: 'Poly Bag (12x16)', category: 'Packaging', unit: 'pcs', current_stock: 2200, reorder_point: 5000, required_qty: 8000, urgency: 'Low', requested_by: 'Inventory System', requested_at: '2026-03-10', status: 'pending', notes: '' },
        ]
    },
    suppliers: {
        type: Array,
        default: () => [
            { id: 1, business_name: 'Heritage Textile Mills', representative_name: 'James Reyes', email: 'james@heritagetextile.com', phone_number: '+63-912-555-0101', address: 'Pampanga, PH', rating: 4.9, status: 'Preferred', categories: ['Fabric', 'Trim'] },
            { id: 2, business_name: 'Eastern Yarn Co.', representative_name: 'Maria Santos', email: 'maria@easternyarn.com', phone_number: '+63-917-555-0202', address: 'Bulacan, PH', rating: 4.5, status: 'Verified', categories: ['Fabric'] },
            { id: 3, business_name: 'Skyline Fabric Ltd', representative_name: 'Carlo Tan', email: 'carlo@skylinefabric.com', phone_number: '+63-918-555-0303', address: 'Quezon City, PH', rating: 4.7, status: 'Approved', categories: ['Fabric', 'Chemicals'] },
            { id: 4, business_name: 'ChemPro Supplies', representative_name: 'Ana Cruz', email: 'ana@chempro.com', phone_number: '+63-920-555-0404', address: 'Manila, PH', rating: 4.6, status: 'Verified', categories: ['Chemicals'] },
            { id: 5, business_name: 'Trim World Int\'l', representative_name: 'Bob Lim', email: 'bob@trimworld.com', phone_number: '+63-921-555-0505', address: 'Cavite, PH', rating: 4.3, status: 'Approved', categories: ['Trim', 'Packaging'] },
        ]
    },
    rfqs: {
        type: Array,
        default: () => [
            {
                id: 1, rfq_number: 'RFQ-2026-001', mr_ref: 'MR-2026-001', material_name: '200GSM Combed Cotton', category: 'Fabric', unit: 'm', required_qty: 1750, deadline: '2026-03-15', sent_at: '2026-03-09', status: 'partial_response', notes: 'Urgent – prioritize lead time.', responses: [
                    { id: 1, supplier_id: 1, supplier_name: 'Heritage Textile Mills', unit_price: 8.45, total_price: 14787.50, lead_time: '10 days', validity_date: '2026-03-31', payment_terms: 'Net 30', notes: 'Can deliver in batch.', status: 'pending_review', submitted_at: '2026-03-10' },
                    { id: 2, supplier_id: 2, supplier_name: 'Eastern Yarn Co.', unit_price: 7.90, total_price: 13825.00, lead_time: '18 days', validity_date: '2026-03-28', payment_terms: 'Net 45', notes: 'Best price guaranteed.', status: 'pending_review', submitted_at: '2026-03-10' },
                ]
            },
            {
                id: 2, rfq_number: 'RFQ-2026-002', mr_ref: 'MR-2026-002', material_name: 'Reactive Dye (Navy)', category: 'Chemicals', unit: 'L', required_qty: 100, deadline: '2026-03-14', sent_at: '2026-03-09', status: 'responded', notes: '', responses: [
                    { id: 3, supplier_id: 4, supplier_name: 'ChemPro Supplies', unit_price: 42.00, total_price: 4200.00, lead_time: '7 days', validity_date: '2026-03-25', payment_terms: 'Net 30', notes: '', status: 'accepted', submitted_at: '2026-03-10' },
                ]
            },
        ]
    },
    purchaseOrders: {
        type: Array,
        default: () => [
            {
                id: 1, po_number: 'SCMPO-2026-0041', supplier_id: 1, supplier_name: 'Heritage Textile Mills', rfq_ref: 'RFQ-2026-001', issued_date: '2026-03-10', expected_delivery: '2026-03-20', status: 'confirmed', subtotal: 14787.50, tax_rate: 10, tax_amount: 1478.75, grand_total: 16266.25, notes: 'Deliver to Warehouse B.', items: [
                    { id: 1, material_name: '200GSM Combed Cotton', qty: 1750, unit: 'm', unit_price: 8.45, total: 14787.50 }
                ], received: false
            },
            {
                id: 2, po_number: 'SCMPO-2026-0042', supplier_id: 4, supplier_name: 'ChemPro Supplies', rfq_ref: 'RFQ-2026-002', issued_date: '2026-03-10', expected_delivery: '2026-03-17', status: 'sent', subtotal: 4200.00, tax_rate: 10, tax_amount: 420.00, grand_total: 4620.00, notes: '', items: [
                    { id: 2, material_name: 'Reactive Dye (Navy)', qty: 100, unit: 'L', unit_price: 42.00, total: 4200.00 }
                ], received: false
            },
        ]
    },
    invoices: {
        type: Array,
        default: () => [
            { id: 1, invoice_number: 'INV-HTM-8821', po_number: 'SCMPO-2026-0041', supplier_name: 'Heritage Textile Mills', invoice_date: '2026-03-14', due_date: '2026-04-13', amount: 16266.25, payment_terms: 'Net 30', status: 'unpaid', received_at: '2026-03-14' },
            { id: 2, invoice_number: 'INV-CPS-4410', po_number: 'SCMPO-2026-0042', supplier_name: 'ChemPro Supplies', invoice_date: '2026-03-11', due_date: '2026-04-10', amount: 4620.00, payment_terms: 'Net 30', status: 'paid', received_at: '2026-03-11' },
        ]
    },
    payments: {
        type: Array,
        default: () => [
            { id: 1, payment_number: 'PAY-2026-051', invoice_number: 'INV-CPS-4410', supplier_name: 'ChemPro Supplies', paid_date: '2026-03-18', amount: 4620.00, method: 'Bank Transfer', bank_reference: 'TXN-00448812', remarks: '', status: 'cleared' },
        ]
    }
});

// ─────────────────────────────────────────────────────────
// NAVIGATION STATE
// ─────────────────────────────────────────────────────────
const activeSection = ref('material_request');
const orderingExpanded = ref(false);
const paymentExpanded = ref(false);

const setSection = (section) => {
    activeSection.value = section;
    if (['purchase_order', 'invoice', 'make_payment'].includes(section)) {
        orderingExpanded.value = true;
    }
    if (['invoice', 'make_payment'].includes(section)) {
        paymentExpanded.value = true;
    }
};

const toggleOrdering = () => {
    orderingExpanded.value = !orderingExpanded.value;
    if (!orderingExpanded.value) paymentExpanded.value = false;
};

const togglePayment = () => {
    paymentExpanded.value = !paymentExpanded.value;
    if (paymentExpanded.value && activeSection.value === 'ordering') {
        activeSection.value = 'invoice';
    }
};

// ─────────────────────────────────────────────────────────
// LOCAL REACTIVE DATA (mirrors props, allows optimistic UI)
// ─────────────────────────────────────────────────────────
const localMaterialRequests = ref([...props.materialRequests]);
const localRFQs = ref([...props.rfqs]);
const localPOs = ref([...props.purchaseOrders]);
const localInvoices = ref([...props.invoices]);
const localPayments = ref([...props.payments]);

// ─────────────────────────────────────────────────────────
// MODALS STATE
// ─────────────────────────────────────────────────────────
// RFQ Creation Modal
const showRFQModal = ref(false);
const rfqTargetRequest = ref(null);
const rfqForm = reactive({
    mr_id: null,
    material_name: '',
    required_qty: 0,
    unit: '',
    deadline: '',
    delivery_address: 'Main Warehouse, Imus, Cavite',
    payment_terms: 'Net 30',
    notes: '',
    selected_suppliers: []
});

// Supplier Selection Modal
const showSupplierModal = ref(false);
const supplierSelectionStep = ref(false);
const filteredSuppliers = computed(() => {
    if (!rfqTargetRequest.value) return props.suppliers;
    return props.suppliers.filter(s => s.categories.includes(rfqTargetRequest.value.category));
});

// View RFQ Detail Modal
const showRFQDetailModal = ref(false);
const selectedRFQ = ref(null);

// Accept Quotation Modal
const showAcceptModal = ref(false);
const selectedQuotationToAccept = ref(null);
const acceptingRFQ = ref(null);

// Decline Quotation Modal
const showDeclineModal = ref(false);
const selectedQuotationToDecline = ref(null);
const declineReason = ref('');

// PO Detail Modal
const showPODetailModal = ref(false);
const selectedPO = ref(null);

// Invoice Detail Modal
const showInvoiceModal = ref(false);
const selectedInvoice = ref(null);

// Payment Modal
const showPaymentModal = ref(false);
const paymentTargetInvoice = ref(null);
const paymentForm = reactive({
    invoice_id: null,
    invoice_number: '',
    supplier_name: '',
    amount: 0,
    method: 'Bank Transfer',
    bank_reference: '',
    payment_date: new Date().toISOString().split('T')[0],
    remarks: ''
});
const paymentMethods = ['Bank Transfer', 'Check', 'Online Payment', 'Cash'];

// Notification
const notification = reactive({ show: false, type: 'success', message: '' });

// ─────────────────────────────────────────────────────────
// LOADING STATE
// ─────────────────────────────────────────────────────────
const isLoading = ref(false);

// ─────────────────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────────────────
const formatCurrency = (val) => {
    if (!val && val !== 0) return '₱0.00';
    return '₱' + Number(val).toLocaleString('en-PH', { minimumFractionDigits: 2 });
};

const statusBadge = (status) => {
    const map = {
        pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
        rfq_sent: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        po_created: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
        fulfilled: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        partial_response: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
        responded: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        pending_review: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
        accepted: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        declined: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        draft: 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
        sent: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        confirmed: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
        received: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        cancelled: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        unpaid: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        paid: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        overdue: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-700',
        cleared: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        High: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        Medium: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
        Low: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
    };
    return map[status] || 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
};

const statusLabel = (status) => {
    const map = {
        pending: 'Pending',
        rfq_sent: 'RFQ Sent',
        po_created: 'PO Created',
        fulfilled: 'Fulfilled',
        partial_response: 'Partial Response',
        responded: 'Responded',
        pending_review: 'Pending Review',
        accepted: 'Accepted',
        declined: 'Declined',
        draft: 'Draft',
        sent: 'Sent',
        confirmed: 'Confirmed',
        received: 'Received',
        cancelled: 'Cancelled',
        unpaid: 'Unpaid',
        paid: 'Paid',
        overdue: 'Overdue',
        cleared: 'Cleared',
    };
    return map[status] || status;
};

const showNotif = (type, message) => {
    notification.show = true;
    notification.type = type;
    notification.message = message;
    setTimeout(() => { notification.show = false; }, 3500);
};

const rfqResponseCount = computed(() =>
    localRFQs.value.reduce((acc, rfq) => acc + (rfq.responses?.filter(r => r.status === 'pending_review').length || 0), 0)
);

const pendingInvoiceCount = computed(() =>
    localInvoices.value.filter(inv => inv.status === 'unpaid').length
);

// ─────────────────────────────────────────────────────────
// ACTIONS — RFQ FLOW
// ─────────────────────────────────────────────────────────
const openCreateRFQ = (request) => {
    rfqTargetRequest.value = request;
    rfqForm.mr_id = request.id;
    rfqForm.material_name = request.material_name;
    rfqForm.required_qty = request.required_qty;
    rfqForm.unit = request.unit;
    rfqForm.deadline = '';
    rfqForm.delivery_address = 'Main Warehouse, Imus, Cavite';
    rfqForm.payment_terms = 'Net 30';
    rfqForm.notes = request.notes || '';
    rfqForm.selected_suppliers = [];
    showRFQModal.value = true;
    supplierSelectionStep.value = false;
};

const proceedToSupplierSelection = () => {
    if (!rfqForm.deadline) {
        showNotif('error', 'Please set a response deadline.');
        return;
    }
    supplierSelectionStep.value = true;
};

const toggleSupplier = (supplierId) => {
    const idx = rfqForm.selected_suppliers.indexOf(supplierId);
    if (idx === -1) rfqForm.selected_suppliers.push(supplierId);
    else rfqForm.selected_suppliers.splice(idx, 1);
};

const submitRFQ = async () => {
    if (rfqForm.selected_suppliers.length === 0) {
        showNotif('error', 'Please select at least one supplier.');
        return;
    }
    isLoading.value = true;
    try {
        const response = await axios.post('/dashboard/scm/procurement/rfq', rfqForm);
        const newRFQ = response.data.rfq;
        localRFQs.value.push(newRFQ);
        const mr = localMaterialRequests.value.find(r => r.id === rfqForm.mr_id);
        if (mr) mr.status = 'rfq_sent';
        showRFQModal.value = false;
        showNotif('success', `RFQ ${newRFQ.rfq_number} sent to ${rfqForm.selected_suppliers.length} supplier(s) successfully.`);
    } catch (e) {
        // Optimistic UI for demo
        const newRFQ = {
            id: Date.now(),
            rfq_number: 'RFQ-2026-' + String(localRFQs.value.length + 1).padStart(3, '0'),
            mr_ref: rfqTargetRequest.value.req_number,
            material_name: rfqForm.material_name,
            category: rfqTargetRequest.value.category,
            unit: rfqForm.unit,
            required_qty: rfqForm.required_qty,
            deadline: rfqForm.deadline,
            sent_at: new Date().toISOString().split('T')[0],
            status: 'sent',
            notes: rfqForm.notes,
            responses: []
        };
        localRFQs.value.push(newRFQ);
        const mr = localMaterialRequests.value.find(r => r.id === rfqForm.mr_id);
        if (mr) mr.status = 'rfq_sent';
        showRFQModal.value = false;
        showNotif('success', `RFQ sent to ${rfqForm.selected_suppliers.length} supplier(s). (Demo mode)`);
    } finally {
        isLoading.value = false;
    }
};

// ─────────────────────────────────────────────────────────
// ACTIONS — QUOTATION RESPONSE HANDLING
// ─────────────────────────────────────────────────────────
const openRFQDetail = (rfq) => {
    selectedRFQ.value = rfq;
    showRFQDetailModal.value = true;
};

const openAcceptModal = (rfq, response) => {
    acceptingRFQ.value = rfq;
    selectedQuotationToAccept.value = response;
    showRFQDetailModal.value = false;
    showAcceptModal.value = true;
};

const openDeclineModal = (rfq, response) => {
    acceptingRFQ.value = rfq;
    selectedQuotationToDecline.value = response;
    declineReason.value = '';
    showDeclineModal.value = true;
};

const acceptQuotation = async () => {
    isLoading.value = true;
    try {
        const response = await axios.post(`/dashboard/scm/procurement/quotations/${selectedQuotationToAccept.value.id}/accept`, {
            rfq_id: acceptingRFQ.value.id
        });
        const newPO = response.data.purchase_order;
        localPOs.value.push(newPO);
        selectedQuotationToAccept.value.status = 'accepted';
        const rfq = localRFQs.value.find(r => r.id === acceptingRFQ.value.id);
        if (rfq) rfq.status = 'responded';
        showAcceptModal.value = false;
        showNotif('success', `Purchase Order created successfully from ${selectedQuotationToAccept.value.supplier_name}'s quotation.`);
        setSection('purchase_order');
    } catch (e) {
        // Optimistic UI
        const q = selectedQuotationToAccept.value;
        const rfq = acceptingRFQ.value;
        const newPO = {
            id: Date.now(),
            po_number: 'SCMPO-2026-' + String(localPOs.value.length + 43).padStart(4, '0'),
            supplier_id: q.supplier_id,
            supplier_name: q.supplier_name,
            rfq_ref: rfq.rfq_number,
            issued_date: new Date().toISOString().split('T')[0],
            expected_delivery: q.lead_time,
            status: 'draft',
            subtotal: q.total_price,
            tax_rate: 10,
            tax_amount: q.total_price * 0.10,
            grand_total: q.total_price * 1.10,
            notes: '',
            items: [{ id: Date.now(), material_name: rfq.material_name, qty: rfq.required_qty, unit: rfq.unit, unit_price: q.unit_price, total: q.total_price }],
            received: false
        };
        localPOs.value.push(newPO);
        q.status = 'accepted';
        const rfqRecord = localRFQs.value.find(r => r.id === rfq.id);
        if (rfqRecord) rfqRecord.status = 'responded';
        showAcceptModal.value = false;
        showNotif('success', `PO ${newPO.po_number} created! (Demo mode)`);
        setSection('purchase_order');
    } finally {
        isLoading.value = false;
    }
};

const declineQuotation = async () => {
    isLoading.value = true;
    try {
        await axios.post(`/dashboard/scm/procurement/quotations/${selectedQuotationToDecline.value.id}/decline`, {
            reason: declineReason.value
        });
        selectedQuotationToDecline.value.status = 'declined';
        showDeclineModal.value = false;
        showNotif('success', 'Quotation declined and supplier notified.');
    } catch {
        selectedQuotationToDecline.value.status = 'declined';
        showDeclineModal.value = false;
        showNotif('success', 'Quotation declined. (Demo mode)');
    } finally {
        isLoading.value = false;
    }
};

// ─────────────────────────────────────────────────────────
// ACTIONS — PURCHASE ORDER
// ─────────────────────────────────────────────────────────
const openPODetail = (po) => {
    selectedPO.value = po;
    showPODetailModal.value = true;
};

const sendPO = async (po) => {
    isLoading.value = true;
    try {
        await axios.post(`/dashboard/scm/procurement/purchase-orders/${po.id}/send`);
        po.status = 'sent';
        showNotif('success', `PO ${po.po_number} sent to ${po.supplier_name}.`);
    } catch {
        po.status = 'sent';
        showNotif('success', `PO ${po.po_number} sent. (Demo mode)`);
    } finally {
        isLoading.value = false;
    }
};

// ─────────────────────────────────────────────────────────
// ACTIONS — PAYMENT FLOW
// ─────────────────────────────────────────────────────────
const openPaymentModal = (invoice) => {
    paymentTargetInvoice.value = invoice;
    paymentForm.invoice_id = invoice.id;
    paymentForm.invoice_number = invoice.invoice_number;
    paymentForm.supplier_name = invoice.supplier_name;
    paymentForm.amount = invoice.amount;
    paymentForm.method = 'Bank Transfer';
    paymentForm.bank_reference = '';
    paymentForm.payment_date = new Date().toISOString().split('T')[0];
    paymentForm.remarks = '';
    showPaymentModal.value = true;
};

const submitPayment = async () => {
    if (!paymentForm.bank_reference) {
        showNotif('error', 'Please provide a reference number.');
        return;
    }
    isLoading.value = true;
    try {
        const response = await axios.post('/dashboard/scm/procurement/payments', paymentForm);
        const newPayment = response.data.payment;
        localPayments.value.push(newPayment);
        const inv = localInvoices.value.find(i => i.id === paymentForm.invoice_id);
        if (inv) inv.status = 'paid';
        showPaymentModal.value = false;
        showNotif('success', `Payment of ${formatCurrency(paymentForm.amount)} processed for ${paymentForm.supplier_name}.`);
    } catch {
        const newPayment = {
            id: Date.now(),
            payment_number: 'PAY-2026-' + String(localPayments.value.length + 52).padStart(3, '0'),
            invoice_number: paymentForm.invoice_number,
            supplier_name: paymentForm.supplier_name,
            paid_date: paymentForm.payment_date,
            amount: paymentForm.amount,
            method: paymentForm.method,
            bank_reference: paymentForm.bank_reference,
            remarks: paymentForm.remarks,
            status: 'cleared'
        };
        localPayments.value.push(newPayment);
        const inv = localInvoices.value.find(i => i.id === paymentForm.invoice_id);
        if (inv) inv.status = 'paid';
        showPaymentModal.value = false;
        showNotif('success', `Payment of ${formatCurrency(paymentForm.amount)} processed. (Demo mode)`);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <!-- ── Notification Toast ─────────────────────────────────── -->
        <transition name="slide-toast">
            <div v-if="notification.show" :class="[
                'fixed top-5 right-5 z-[9999] flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-xl border text-sm font-medium',
                notification.type === 'success' ? 'bg-white dark:bg-gray-800 border-green-300 text-green-700 dark:text-green-400' : 'bg-white dark:bg-gray-800 border-red-300 text-red-700 dark:text-red-400'
            ]">
                <component :is="notification.type === 'success' ? CheckCircle : XCircle"
                    class="w-5 h-5 flex-shrink-0" />
                {{ notification.message }}
                <button @click="notification.show = false" class="ml-2 opacity-60 hover:opacity-100">
                    <X class="w-4 h-4" />
                </button>
            </div>
        </transition>

        <!-- ── Page Header ─────────────────────────────────────────── -->
        <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Procurement Management</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">SCM · Full Procurement Workflow</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span
                    class="flex items-center gap-1 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 px-3 py-1.5 rounded-full font-medium">
                    <AlertTriangle class="w-3.5 h-3.5" /> {{ props.stats.pendingMaterialRequests }} Pending Requests
                </span>
                <span v-if="rfqResponseCount > 0"
                    class="flex items-center gap-1 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 px-3 py-1.5 rounded-full font-medium">
                    <FileText class="w-3.5 h-3.5" /> {{ rfqResponseCount }} Quotations to Review
                </span>
                <span v-if="pendingInvoiceCount > 0"
                    class="flex items-center gap-1 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 px-3 py-1.5 rounded-full font-medium">
                    <Receipt class="w-3.5 h-3.5" /> {{ pendingInvoiceCount }} Unpaid Invoices
                </span>
            </div>
        </div>

        <!-- ── Stats Row ─────────────────────────────────────────────── -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center flex-shrink-0">
                    <ClipboardList class="w-5 h-5 text-orange-500" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{localMaterialRequests.filter(r =>
                        r.status === 'pending').length }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Material Requests</p>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center flex-shrink-0">
                    <Send class="w-5 h-5 text-blue-500" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ localRFQs.length }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Active RFQs</p>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center flex-shrink-0">
                    <ShoppingCart class="w-5 h-5 text-indigo-500" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ localPOs.length }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Purchase Orders</p>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0">
                    <CircleDollarSign class="w-5 h-5 text-red-500" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{localInvoices.filter(i => i.status
                        === 'unpaid').length }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Unpaid Invoices</p>
                </div>
            </div>
        </div>

        <!-- ── Main Layout ───────────────────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

            <!-- LEFT NAV -->
            <div class="lg:col-span-3">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm sticky top-4">
                    <div class="px-4 pt-4 pb-2">
                        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                            Workflow</p>
                    </div>
                    <nav class="px-2 pb-3 space-y-0.5">
                        <!-- Material Request -->
                        <button @click="setSection('material_request')" :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150',
                            activeSection === 'material_request' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                        ]">
                            <div class="flex items-center gap-2.5">
                                <ClipboardList class="w-4 h-4 flex-shrink-0" />
                                <span>Material Request</span>
                            </div>
                            <span v-if="localMaterialRequests.filter(r => r.status === 'pending').length > 0" :class="[
                                'text-[10px] font-bold px-1.5 py-0.5 rounded-full',
                                activeSection === 'material_request' ? 'bg-white/20 text-white' : 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400'
                            ]">{{localMaterialRequests.filter(r => r.status === 'pending').length}}</span>
                        </button>

                        <!-- Supplier Quotation -->
                        <button @click="setSection('supplier_quotation')" :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150',
                            activeSection === 'supplier_quotation' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                        ]">
                            <div class="flex items-center gap-2.5">
                                <FileText class="w-4 h-4 flex-shrink-0" />
                                <span>Supplier Quotation</span>
                            </div>
                            <span v-if="rfqResponseCount > 0" :class="[
                                'text-[10px] font-bold px-1.5 py-0.5 rounded-full',
                                activeSection === 'supplier_quotation' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400'
                            ]">{{ rfqResponseCount }}</span>
                        </button>

                        <!-- Ordering & Receipt (accordion) -->
                        <div>
                            <button @click="toggleOrdering" :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150',
                                ['purchase_order', 'invoice', 'make_payment'].includes(activeSection) ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/10' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                            ]">
                                <div class="flex items-center gap-2.5">
                                    <Package class="w-4 h-4 flex-shrink-0" />
                                    <span>Ordering &amp; Receipt</span>
                                </div>
                                <component :is="orderingExpanded ? ChevronDown : ChevronRight"
                                    class="w-4 h-4 flex-shrink-0 transition-transform duration-200" />
                            </button>

                            <!-- Sub nav -->
                            <transition name="accordion">
                                <div v-show="orderingExpanded"
                                    class="mt-0.5 ml-4 space-y-0.5 pl-3 border-l-2 border-gray-200 dark:border-gray-600">
                                    <!-- Purchase Order -->
                                    <button @click="setSection('purchase_order')" :class="[
                                        'w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150',
                                        activeSection === 'purchase_order' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                                    ]">
                                        <div class="flex items-center gap-2">
                                            <ShoppingCart class="w-3.5 h-3.5 flex-shrink-0" />
                                            <span>Purchase Order</span>
                                        </div>
                                    </button>

                                    <!-- Payment (sub-accordion) -->
                                    <div>
                                        <button @click="togglePayment" :class="[
                                            'w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150',
                                            ['invoice', 'make_payment'].includes(activeSection) ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/10' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                                        ]">
                                            <div class="flex items-center gap-2">
                                                <CreditCard class="w-3.5 h-3.5 flex-shrink-0" />
                                                <span>Payment</span>
                                            </div>
                                            <component :is="paymentExpanded ? ChevronDown : ChevronRight"
                                                class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200" />
                                        </button>

                                        <transition name="accordion">
                                            <div v-show="paymentExpanded"
                                                class="mt-0.5 ml-3 space-y-0.5 pl-3 border-l-2 border-gray-200 dark:border-gray-600">
                                                <!-- Invoice -->
                                                <button @click="setSection('invoice')" :class="[
                                                    'w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150',
                                                    activeSection === 'invoice' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                                                ]">
                                                    <div class="flex items-center gap-2">
                                                        <Receipt class="w-3.5 h-3.5 flex-shrink-0" />
                                                        <span>Invoice</span>
                                                    </div>
                                                    <span v-if="pendingInvoiceCount > 0" :class="[
                                                        'text-[10px] font-bold px-1.5 py-0.5 rounded-full',
                                                        activeSection === 'invoice' ? 'bg-white/20 text-white' : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'
                                                    ]">{{ pendingInvoiceCount }}</span>
                                                </button>

                                                <!-- Make Payment -->
                                                <button @click="setSection('make_payment')" :class="[
                                                    'w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150',
                                                    activeSection === 'make_payment' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                                                ]">
                                                    <DollarSign class="w-3.5 h-3.5 flex-shrink-0" />
                                                    <span>Make Payment</span>
                                                </button>
                                            </div>
                                        </transition>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </nav>

                    <!-- Workflow indicator -->
                    <div class="border-t border-gray-100 dark:border-gray-700 px-4 py-3 mt-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Flow Status</p>
                        <div class="flex flex-col gap-1.5">
                            <div v-for="(step, i) in [
                                { label: 'Material Request', key: 'material_request', done: localMaterialRequests.some(r => r.status !== 'pending') },
                                { label: 'Supplier Quotation', key: 'supplier_quotation', done: localRFQs.some(r => r.responses?.some(q => q.status === 'accepted')) },
                                { label: 'Purchase Order', key: 'purchase_order', done: localPOs.length > 0 },
                                { label: 'Invoice & Payment', key: 'invoice', done: localPayments.length > 0 }
                            ]" :key="i" class="flex items-center gap-2 text-xs">
                                <div
                                    :class="['w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0', step.done ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600']">
                                    <CheckCircle v-if="step.done" class="w-3 h-3 text-white" />
                                </div>
                                <span
                                    :class="step.done ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">{{
                                    step.label }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT CONTENT PANEL -->
            <div class="lg:col-span-9">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm min-h-[500px]">

                    <!-- ══ MATERIAL REQUEST ══════════════════════════════════════ -->
                    <div v-if="activeSection === 'material_request'">
                        <div
                            class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                    <ClipboardList class="w-5 h-5 text-blue-500" />
                                    Material Requests
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Materials flagged for restock
                                    by the Inventory module.</p>
                            </div>
                        </div>
                        <div class="p-5 space-y-3">
                            <div v-if="localMaterialRequests.length === 0" class="text-center py-16 text-gray-400">
                                <Boxes class="w-12 h-12 mx-auto mb-3 opacity-30" />
                                <p class="text-sm">No material requests from Inventory.</p>
                            </div>
                            <div v-for="req in localMaterialRequests" :key="req.id"
                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 transition-all">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{
                                            req.req_number }}</span>
                                        <span :class="statusBadge(req.urgency)"
                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full">{{ req.urgency
                                            }}</span>
                                        <span :class="statusBadge(req.status)"
                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full">{{
                                            statusLabel(req.status) }}</span>
                                    </div>
                                    <p class="font-semibold text-gray-800 dark:text-white text-sm">{{ req.material_name
                                        }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ req.category }}
                                        &nbsp;·&nbsp; Requested: <strong>{{ Number(req.required_qty).toLocaleString() }}
                                            {{ req.unit }}</strong> &nbsp;·&nbsp; In Stock: <strong>{{
                                            Number(req.current_stock).toLocaleString() }} {{ req.unit }}</strong></p>
                                    <div class="mt-2 bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 w-full max-w-xs">
                                        <div :style="{ width: Math.min((req.current_stock / req.reorder_point) * 100, 100) + '%' }"
                                            :class="['h-1.5 rounded-full', req.current_stock < req.reorder_point ? 'bg-red-500' : 'bg-green-500']">
                                        </div>
                                    </div>
                                    <p v-if="req.notes" class="text-xs text-gray-400 dark:text-gray-500 mt-1 italic">{{
                                        req.notes }}</p>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <div class="text-right mr-2">
                                        <p class="text-xs text-gray-400">Gap</p>
                                        <p
                                            :class="['text-sm font-bold', req.current_stock < req.required_qty ? 'text-red-600' : 'text-green-600']">
                                            {{ req.current_stock < req.required_qty ? '-' + (req.required_qty -
                                                req.current_stock).toLocaleString() + ' ' + req.unit : 'Sufficient' }}
                                                </p>
                                    </div>
                                    <button v-if="req.status === 'pending'" @click="openCreateRFQ(req)"
                                        class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition-all shadow-sm whitespace-nowrap">
                                        <Send class="w-3.5 h-3.5" /> Create RFQ
                                    </button>
                                    <span v-else-if="req.status === 'rfq_sent'"
                                        class="text-xs text-blue-600 dark:text-blue-400 font-medium flex items-center gap-1">
                                        <CheckCircle class="w-3.5 h-3.5" /> RFQ Sent
                                    </span>
                                    <span v-else
                                        class="text-xs text-green-600 dark:text-green-400 font-medium flex items-center gap-1">
                                        <BadgeCheck class="w-3.5 h-3.5" /> {{ statusLabel(req.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══ SUPPLIER QUOTATION ════════════════════════════════════ -->
                    <div v-else-if="activeSection === 'supplier_quotation'">
                        <div
                            class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                    <FileText class="w-5 h-5 text-blue-500" />
                                    Supplier Quotations
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Review quotation responses
                                    from suppliers. Accept to generate a Purchase Order.</p>
                            </div>
                        </div>
                        <div class="p-5 space-y-4">
                            <div v-if="localRFQs.length === 0" class="text-center py-16 text-gray-400">
                                <FileText class="w-12 h-12 mx-auto mb-3 opacity-30" />
                                <p class="text-sm">No RFQs sent yet. Create one from Material Requests.</p>
                            </div>
                            <div v-for="rfq in localRFQs" :key="rfq.id"
                                class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                                <!-- RFQ Header -->
                                <div
                                    class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/50">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="text-xs font-mono font-bold text-gray-700 dark:text-gray-200">{{
                                                    rfq.rfq_number }}</span>
                                                <span class="text-[10px] text-gray-400 dark:text-gray-500">← {{
                                                    rfq.mr_ref }}</span>
                                                <span :class="statusBadge(rfq.status)"
                                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full">{{
                                                    statusLabel(rfq.status) }}</span>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white mt-0.5">{{
                                                rfq.material_name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{
                                                Number(rfq.required_qty).toLocaleString() }} {{ rfq.unit }}
                                                &nbsp;·&nbsp; Deadline: {{ rfq.deadline }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ rfq.responses?.length
                                            || 0 }} response(s)</span>
                                        <button @click="openRFQDetail(rfq)"
                                            class="flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                            <Eye class="w-3.5 h-3.5" /> View RFQ
                                        </button>
                                    </div>
                                </div>

                                <!-- Supplier Responses -->
                                <div v-if="rfq.responses && rfq.responses.length > 0"
                                    class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <div v-for="res in rfq.responses" :key="res.id"
                                        :class="['flex flex-wrap items-center justify-between gap-3 px-4 py-3 transition-all', res.status === 'accepted' ? 'bg-green-50 dark:bg-green-900/10' : res.status === 'declined' ? 'bg-red-50 dark:bg-red-900/10 opacity-60' : 'bg-white dark:bg-gray-800']">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                                                <span class="text-white text-xs font-bold">{{
                                                    res.supplier_name.charAt(0) }}</span>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-sm text-gray-800 dark:text-white">{{
                                                    res.supplier_name }}</p>
                                                <div
                                                    class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex-wrap">
                                                    <span>Unit: <strong class="text-gray-700 dark:text-gray-200">{{
                                                            formatCurrency(res.unit_price) }}/{{ rfq.unit
                                                            }}</strong></span>
                                                    <span>Total: <strong class="text-gray-700 dark:text-gray-200">{{
                                                            formatCurrency(res.total_price) }}</strong></span>
                                                    <span>
                                                        <Clock class="w-3 h-3 inline mr-0.5" />{{ res.lead_time }}
                                                    </span>
                                                    <span>Terms: {{ res.payment_terms }}</span>
                                                    <span>Valid: {{ res.validity_date }}</span>
                                                </div>
                                                <p v-if="res.notes"
                                                    class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 italic">{{
                                                    res.notes }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span :class="statusBadge(res.status)"
                                                class="text-[10px] font-bold px-2 py-0.5 rounded-full">{{
                                                statusLabel(res.status) }}</span>
                                            <template v-if="res.status === 'pending_review'">
                                                <button @click="openAcceptModal(rfq, res)"
                                                    class="flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                                                    <CheckCircle class="w-3.5 h-3.5" /> Accept
                                                </button>
                                                <button @click="openDeclineModal(rfq, res)"
                                                    class="flex items-center gap-1 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all border border-red-200 dark:border-red-800">
                                                    <XCircle class="w-3.5 h-3.5" /> Decline
                                                </button>
                                            </template>
                                            <span v-else-if="res.status === 'accepted'"
                                                class="text-xs text-green-600 dark:text-green-400 font-medium flex items-center gap-1">
                                                <BadgeCheck class="w-3.5 h-3.5" /> PO Created
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="px-4 py-3 bg-white dark:bg-gray-800 text-xs text-gray-400 italic">
                                    Awaiting supplier responses...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══ PURCHASE ORDER ═════════════════════════════════════════ -->
                    <div v-else-if="activeSection === 'purchase_order'">
                        <div
                            class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                    <ShoppingCart class="w-5 h-5 text-blue-500" />
                                    Purchase Orders
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">SCM-issued POs sent to
                                    suppliers. Click a row to
                                    expand details.</p>
                            </div>
                        </div>
                        <div class="p-5 space-y-3">
                            <div v-if="localPOs.length === 0" class="text-center py-16 text-gray-400">
                                <ShoppingCart class="w-12 h-12 mx-auto mb-3 opacity-30" />
                                <p class="text-sm">No purchase orders yet. Accept a supplier quotation first.</p>
                            </div>
                            <div v-for="po in localPOs" :key="po.id"
                                class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                                    @click="openPODetail(po)">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span
                                                class="text-xs font-mono font-bold text-gray-700 dark:text-gray-200">{{
                                                po.po_number
                                                }}</span>
                                            <span :class="statusBadge(po.status)"
                                                class="text-[10px] font-bold px-2 py-0.5 rounded-full">{{
                                                statusLabel(po.status) }}</span>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white mt-0.5">{{
                                            po.supplier_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            Issued: {{ po.issued_date }} &nbsp;·&nbsp; Expected Delivery: {{
                                            po.expected_delivery }}
                                            <template v-if="po.rfq_ref"> &nbsp;·&nbsp; Ref: {{ po.rfq_ref }}</template>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <p class="text-xs text-gray-400">Grand Total</p>
                                            <p class="font-bold text-gray-800 dark:text-white">{{
                                                formatCurrency(po.grand_total) }}</p>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <button v-if="po.status === 'draft'" @click.stop="sendPO(po)"
                                                class="flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                                                <Send class="w-3 h-3" /> Send PO
                                            </button>
                                            <button @click.stop="openPODetail(po)"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                <Eye class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Inline item preview -->
                                <div
                                    class="px-4 py-2 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span v-for="item in po.items" :key="item.id" class="flex items-center gap-1">
                                            <Package class="w-3 h-3" /> {{ item.material_name }}: {{ item.qty }} {{
                                            item.unit }} @ {{
                                            formatCurrency(item.unit_price) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══ INVOICE ════════════════════════════════════════════════ -->
                    <div v-else-if="activeSection === 'invoice'">
                        <div
                            class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                    <Receipt class="w-5 h-5 text-blue-500" />
                                    Purchase Invoices
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Invoices received from
                                    suppliers. Process payment
                                    on unpaid invoices.</p>
                            </div>
                        </div>
                        <div class="p-5">
                            <div v-if="localInvoices.length === 0" class="text-center py-16 text-gray-400">
                                <Receipt class="w-12 h-12 mx-auto mb-3 opacity-30" />
                                <p class="text-sm">No invoices yet.</p>
                            </div>
                            <div class="space-y-3">
                                <div v-for="inv in localInvoices" :key="inv.id"
                                    :class="['flex flex-wrap items-center justify-between gap-3 p-4 rounded-xl border transition-all', inv.status === 'unpaid' ? 'border-red-200 dark:border-red-800 bg-red-50/40 dark:bg-red-900/5' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50']">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span
                                                class="text-xs font-mono font-bold text-gray-700 dark:text-gray-200">{{
                                                inv.invoice_number
                                                }}</span>
                                            <span class="text-xs text-gray-400 dark:text-gray-500">← {{ inv.po_number
                                                }}</span>
                                            <span :class="statusBadge(inv.status)"
                                                class="text-[10px] font-bold px-2 py-0.5 rounded-full">{{
                                                statusLabel(inv.status) }}</span>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white mt-0.5">{{
                                            inv.supplier_name }}</p>
                                        <div
                                            class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex-wrap">
                                            <span>Invoice Date: {{ inv.invoice_date }}</span>
                                            <span
                                                :class="inv.status === 'unpaid' ? 'text-red-600 dark:text-red-400 font-semibold' : ''">Due:
                                                {{ inv.due_date }}</span>
                                            <span>Terms: {{ inv.payment_terms }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <p class="text-xs text-gray-400">Amount</p>
                                            <p
                                                :class="['font-bold text-lg', inv.status === 'unpaid' ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-white']">
                                                {{ formatCurrency(inv.amount) }}
                                            </p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button @click="selectedInvoice = inv; showInvoiceModal = true"
                                                class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors border border-gray-200 dark:border-gray-600 rounded-lg">
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <button v-if="inv.status === 'unpaid'" @click="openPaymentModal(inv)"
                                                class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-sm">
                                                <DollarSign class="w-3.5 h-3.5" /> Pay Now
                                            </button>
                                            <span v-else
                                                class="flex items-center gap-1 text-xs text-green-600 dark:text-green-400 font-medium px-2">
                                                <BadgeCheck class="w-4 h-4" /> Paid
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══ MAKE PAYMENT ═══════════════════════════════════════════ -->
                    <div v-else-if="activeSection === 'make_payment'">
                        <div
                            class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                    <DollarSign class="w-5 h-5 text-blue-500" />
                                    Payment History
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">All processed supplier
                                    payments and transaction
                                    records.</p>
                            </div>
                        </div>
                        <div class="p-5">
                            <!-- Unpaid Queue -->
                            <div v-if="localInvoices.some(i => i.status === 'unpaid')" class="mb-5">
                                <h4
                                    class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <AlertTriangle class="w-3.5 h-3.5 text-red-500" /> Outstanding Payments
                                </h4>
                                <div class="space-y-2">
                                    <div v-for="inv in localInvoices.filter(i => i.status === 'unpaid')" :key="inv.id"
                                        class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-lg">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{
                                                inv.invoice_number }} — {{
                                                inv.supplier_name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Due: {{ inv.due_date }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <p class="font-bold text-red-600 dark:text-red-400">{{
                                                formatCurrency(inv.amount) }}</p>
                                            <button @click="openPaymentModal(inv)"
                                                class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                                <DollarSign class="w-3 h-3" /> Pay
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Log -->
                            <h4
                                class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                                Completed Payments
                            </h4>
                            <div v-if="localPayments.length === 0" class="text-center py-10 text-gray-400">
                                <CircleDollarSign class="w-10 h-10 mx-auto mb-2 opacity-30" />
                                <p class="text-sm">No payments processed yet.</p>
                            </div>
                            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700" v-else>
                                <table class="w-full text-sm text-left">
                                    <thead
                                        class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th class="px-4 py-3">Payment #</th>
                                            <th class="px-4 py-3">Invoice</th>
                                            <th class="px-4 py-3">Supplier</th>
                                            <th class="px-4 py-3">Date</th>
                                            <th class="px-4 py-3">Method</th>
                                            <th class="px-4 py-3">Reference</th>
                                            <th class="px-4 py-3 text-right">Amount</th>
                                            <th class="px-4 py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <tr v-for="pay in localPayments" :key="pay.id"
                                            class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{
                                                pay.payment_number
                                                }}</td>
                                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">{{
                                                pay.invoice_number }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{
                                                pay.supplier_name }}</td>
                                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">{{
                                                pay.paid_date }}</td>
                                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">{{ pay.method
                                                }}</td>
                                            <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{
                                                pay.bank_reference
                                                }}</td>
                                            <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">{{
                                                formatCurrency(pay.amount) }}</td>
                                            <td class="px-4 py-3">
                                                <span :class="statusBadge(pay.status)"
                                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full capitalize">{{
                                                    pay.status
                                                    }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-gray-50 dark:bg-gray-700/30">
                                        <tr>
                                            <td colspan="6"
                                                class="px-4 py-3 text-xs font-bold text-gray-500 dark:text-gray-400 text-right">
                                                Total Paid:</td>
                                            <td
                                                class="px-4 py-3 text-right font-bold text-green-600 dark:text-green-400">
                                                {{formatCurrency(localPayments.reduce((s, p) => s + Number(p.amount),
                                                0)) }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!--  MODAL: CREATE RFQ                                              -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <transition name="modal-fade">
            <div v-if="showRFQModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                @click.self="showRFQModal = false">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700">
                    <!-- Header -->
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <FileText class="w-5 h-5 text-blue-500" />
                                <span>{{ supplierSelectionStep ? 'Select Suppliers' : 'Create Request for Quotation'
                                    }}</span>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ rfqTargetRequest?.req_number
                                }} — {{
                                rfqTargetRequest?.material_name }}</p>
                        </div>
                        <button @click="showRFQModal = false"
                            class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Step 1: RFQ Form -->
                    <div v-if="!supplierSelectionStep" class="p-6 space-y-5">
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 text-sm">
                            <p class="font-bold text-blue-800 dark:text-blue-300 mb-1">Request for Quotation</p>
                            <p class="text-blue-600 dark:text-blue-400 text-xs">Formal procurement inquiry to be sent to
                                qualified suppliers.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Material</label>
                                <input type="text" :value="rfqForm.material_name" readonly
                                    class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-800 dark:text-white" />
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Required
                                    Quantity</label>
                                <div class="flex">
                                    <input type="number" v-model="rfqForm.required_qty"
                                        class="flex-1 px-3 py-2.5 rounded-l-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                    <span
                                        class="px-3 py-2.5 bg-gray-100 dark:bg-gray-600 border border-l-0 border-gray-200 dark:border-gray-600 rounded-r-lg text-sm text-gray-500 dark:text-gray-400">{{
                                        rfqForm.unit }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Response
                                    Deadline <span class="text-red-500">*</span></label>
                                <input type="date" v-model="rfqForm.deadline"
                                    :min="new Date().toISOString().split('T')[0]"
                                    class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Payment
                                    Terms</label>
                                <select v-model="rfqForm.payment_terms"
                                    class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                    <option>Net 30</option>
                                    <option>Net 45</option>
                                    <option>Net 60</option>
                                    <option>Cash on Delivery</option>
                                    <option>50% DP, 50% on Delivery</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Delivery
                                Address</label>
                            <input type="text" v-model="rfqForm.delivery_address"
                                class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Additional
                                Notes / Special Requirements</label>
                            <textarea v-model="rfqForm.notes" rows="3"
                                placeholder="e.g., Material specs, certifications required, packing instructions..."
                                class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button @click="showRFQModal = false"
                                class="px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all">Cancel</button>
                            <button @click="proceedToSupplierSelection"
                                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-all shadow-sm">
                                Next: Select Suppliers
                                <ArrowRight class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Supplier Selection -->
                    <div v-else class="p-6 space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Select which suppliers to send this RFQ to.
                            Only
                            suppliers that carry <strong>{{ rfqTargetRequest?.category }}</strong> are shown.</p>
                        <div class="space-y-2">
                            <div v-for="sup in filteredSuppliers" :key="sup.id" @click="toggleSupplier(sup.id)"
                                :class="['flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition-all', rfqForm.selected_suppliers.includes(sup.id) ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-700']">
                                <div class="flex items-center gap-3">
                                    <div
                                        :class="['w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0 transition-all', rfqForm.selected_suppliers.includes(sup.id) ? 'bg-blue-600 border-blue-600' : 'border-gray-300 dark:border-gray-500']">
                                        <CheckCircle v-if="rfqForm.selected_suppliers.includes(sup.id)"
                                            class="w-3.5 h-3.5 text-white" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-gray-800 dark:text-white">{{
                                            sup.business_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ sup.representative_name
                                            }} · {{
                                            sup.email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-0.5 text-xs text-yellow-500 font-bold">
                                        <Star class="w-3 h-3 fill-yellow-400 text-yellow-400" /> {{ sup.rating }}
                                    </div>
                                    <span
                                        :class="['text-[10px] font-bold px-2 py-0.5 rounded-full', sup.status === 'Preferred' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : sup.status === 'Verified' ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400']">{{
                                        sup.status }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <button @click="supplierSelectionStep = false"
                                class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 flex items-center gap-1">
                                <ChevronRight class="w-4 h-4 rotate-180" /> Back
                            </button>
                            <div class="flex gap-3">
                                <span class="text-xs text-gray-500 dark:text-gray-400 self-center">{{
                                    rfqForm.selected_suppliers.length }} selected</span>
                                <button @click="submitRFQ"
                                    :disabled="isLoading || rfqForm.selected_suppliers.length === 0"
                                    :class="['flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm', rfqForm.selected_suppliers.length > 0 ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-400 cursor-not-allowed']">
                                    <Send class="w-4 h-4" />
                                    <span v-if="isLoading">Sending...</span>
                                    <span v-else>Send RFQ to {{ rfqForm.selected_suppliers.length }} Supplier(s)</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!--  MODAL: RFQ DETAIL VIEW                                         -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <transition name="modal-fade">
            <div v-if="showRFQDetailModal && selectedRFQ"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                @click.self="showRFQDetailModal = false">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-200 dark:border-gray-700">
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-800 dark:text-white">{{ selectedRFQ.rfq_number }}</h3>
                        <button @click="showRFQDetailModal = false">
                            <X class="w-5 h-5 text-gray-400" />
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold">Material</p>
                                <p class="text-gray-800 dark:text-white font-medium mt-0.5">{{ selectedRFQ.material_name
                                    }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold">Required Qty</p>
                                <p class="text-gray-800 dark:text-white font-medium mt-0.5">{{
                                    Number(selectedRFQ.required_qty).toLocaleString() }} {{ selectedRFQ.unit }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold">Sent Date</p>
                                <p class="text-gray-800 dark:text-white font-medium mt-0.5">{{ selectedRFQ.sent_at }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold">Response Deadline</p>
                                <p class="text-gray-800 dark:text-white font-medium mt-0.5">{{ selectedRFQ.deadline }}
                                </p>
                            </div>
                        </div>
                        <div v-if="selectedRFQ.notes" class="text-sm">
                            <p class="text-xs text-gray-400 uppercase font-semibold">Notes</p>
                            <p class="text-gray-600 dark:text-gray-300 mt-0.5 italic">{{ selectedRFQ.notes }}</p>
                        </div>
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-400 uppercase font-semibold mb-2">Supplier Responses ({{
                                selectedRFQ.responses?.length || 0 }})</p>
                            <div v-if="!selectedRFQ.responses?.length" class="text-sm text-gray-400 italic">Awaiting
                                responses...</div>
                            <div v-for="res in selectedRFQ.responses" :key="res.id"
                                class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0 text-sm">
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ res.supplier_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{
                                        formatCurrency(res.unit_price) }}/{{
                                        selectedRFQ.unit }} · {{ res.lead_time }} · {{ res.payment_terms }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <p class="font-bold text-gray-800 dark:text-white">{{
                                        formatCurrency(res.total_price) }}</p>
                                    <span :class="statusBadge(res.status)"
                                        class="text-[10px] font-bold px-2 py-0.5 rounded-full">{{
                                        statusLabel(res.status)
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!--  MODAL: ACCEPT QUOTATION → CREATE PO                           -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <transition name="modal-fade">
            <div v-if="showAcceptModal && selectedQuotationToAccept"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                @click.self="showAcceptModal = false">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700">
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <CheckCircle class="w-5 h-5 text-green-500" /> Accept Quotation
                        </h3>
                        <button @click="showAcceptModal = false">
                            <X class="w-5 h-5 text-gray-400" />
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div
                            class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-sm">
                            <p class="font-bold text-green-800 dark:text-green-300 mb-2">A Purchase Order will be
                                generated for:
                            </p>
                            <div class="space-y-1 text-green-700 dark:text-green-400">
                                <p><strong>Supplier:</strong> {{ selectedQuotationToAccept.supplier_name }}</p>
                                <p><strong>Material:</strong> {{ acceptingRFQ?.material_name }}</p>
                                <p><strong>Quantity:</strong> {{ Number(acceptingRFQ?.required_qty).toLocaleString() }}
                                    {{
                                    acceptingRFQ?.unit }}</p>
                                <p><strong>Unit Price:</strong> {{ formatCurrency(selectedQuotationToAccept.unit_price)
                                    }}</p>
                                <p
                                    class="text-base font-bold border-t border-green-200 dark:border-green-700 pt-2 mt-2">
                                    Grand
                                    Total (incl. 10% tax): {{ formatCurrency(selectedQuotationToAccept.total_price *
                                    1.1) }}</p>
                                <p><strong>Lead Time:</strong> {{ selectedQuotationToAccept.lead_time }}</p>
                                <p><strong>Payment Terms:</strong> {{ selectedQuotationToAccept.payment_terms }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">This will mark the other quotations for this
                            RFQ and
                            create a confirmed purchase order.</p>
                        <div class="flex gap-3 justify-end pt-2">
                            <button @click="showAcceptModal = false"
                                class="px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all">Cancel</button>
                            <button @click="acceptQuotation" :disabled="isLoading"
                                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm">
                                <CheckCircle class="w-4 h-4" />
                                <span>{{ isLoading ? 'Processing...' : 'Confirm & Create PO' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!--  MODAL: DECLINE QUOTATION                                       -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <transition name="modal-fade">
            <div v-if="showDeclineModal && selectedQuotationToDecline"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                @click.self="showDeclineModal = false">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700">
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <XCircle class="w-5 h-5 text-red-500" /> Decline Quotation
                        </h3>
                        <button @click="showDeclineModal = false">
                            <X class="w-5 h-5 text-gray-400" />
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Declining the quotation from <strong>{{
                                selectedQuotationToDecline.supplier_name }}</strong>. The supplier will be notified.</p>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Reason
                                for Declining</label>
                            <textarea v-model="declineReason" rows="3"
                                placeholder="e.g., Price too high, lead time too long, found better supplier..."
                                class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none resize-none"></textarea>
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button @click="showDeclineModal = false"
                                class="px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all">Cancel</button>
                            <button @click="declineQuotation" :disabled="isLoading"
                                class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all">
                                <Ban class="w-4 h-4" /> {{ isLoading ? 'Processing...' : 'Decline Quotation' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!--  MODAL: PO DETAIL                                               -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <transition name="modal-fade">
            <div v-if="showPODetailModal && selectedPO"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                @click.self="showPODetailModal = false">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-200 dark:border-gray-700">
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white">{{ selectedPO.po_number }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Purchase Order Details</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span :class="statusBadge(selectedPO.status)"
                                class="text-[10px] font-bold px-2 py-1 rounded-full">{{ statusLabel(selectedPO.status)
                                }}</span>
                            <button @click="showPODetailModal = false">
                                <X class="w-5 h-5 text-gray-400" />
                            </button>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase">Supplier</p>
                                <p class="text-gray-800 dark:text-white font-medium mt-0.5">{{ selectedPO.supplier_name
                                    }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase">Issue Date</p>
                                <p class="text-gray-800 dark:text-white font-medium mt-0.5">{{ selectedPO.issued_date }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase">Expected Delivery</p>
                                <p class="text-gray-800 dark:text-white font-medium mt-0.5">{{
                                    selectedPO.expected_delivery }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase">RFQ Reference</p>
                                <p class="text-gray-800 dark:text-white font-medium mt-0.5">{{ selectedPO.rfq_ref || '—'
                                    }}</p>
                            </div>
                        </div>
                        <div class="border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                            <table class="w-full text-sm">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left">Item</th>
                                        <th class="px-4 py-2.5 text-right">Qty</th>
                                        <th class="px-4 py-2.5 text-right">Unit Price</th>
                                        <th class="px-4 py-2.5 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <tr v-for="item in selectedPO.items" :key="item.id"
                                        class="bg-white dark:bg-gray-800">
                                        <td class="px-4 py-3 text-gray-800 dark:text-white font-medium">{{
                                            item.material_name }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ item.qty }}
                                            {{
                                            item.unit }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{
                                            formatCurrency(item.unit_price) }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white">{{
                                            formatCurrency(item.total) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700/30 text-sm">
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-gray-500">Subtotal</td>
                                        <td class="px-4 py-2 text-right font-medium text-gray-800 dark:text-white">{{
                                            formatCurrency(selectedPO.subtotal) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-gray-500">Tax ({{
                                            selectedPO.tax_rate
                                            }}%)</td>
                                        <td class="px-4 py-2 text-right font-medium text-gray-800 dark:text-white">{{
                                            formatCurrency(selectedPO.tax_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"
                                            class="px-4 py-2 text-right font-bold text-gray-700 dark:text-gray-200">
                                            Grand Total</td>
                                        <td
                                            class="px-4 py-2 text-right font-bold text-blue-600 dark:text-blue-400 text-base">
                                            {{
                                            formatCurrency(selectedPO.grand_total) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <p v-if="selectedPO.notes" class="text-xs text-gray-500 dark:text-gray-400 italic">Notes: {{
                            selectedPO.notes }}</p>
                        <div class="flex gap-3 justify-end">
                            <button
                                class="flex items-center gap-1.5 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                <Printer class="w-4 h-4" /> Print PO
                            </button>
                            <button v-if="selectedPO.status === 'draft'"
                                @click="sendPO(selectedPO); showPODetailModal = false"
                                class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                                <Send class="w-4 h-4" /> Send to Supplier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!--  MODAL: PROCESS PAYMENT                                         -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <transition name="modal-fade">
            <div v-if="showPaymentModal && paymentTargetInvoice"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                @click.self="showPaymentModal = false">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700">
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <DollarSign class="w-5 h-5 text-blue-500" /> Process Payment
                        </h3>
                        <button @click="showPaymentModal = false">
                            <X class="w-5 h-5 text-gray-400" />
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-blue-800 dark:text-blue-300 text-sm">{{
                                        paymentForm.invoice_number
                                        }}</p>
                                    <p class="text-blue-600 dark:text-blue-400 text-xs mt-0.5">{{
                                        paymentForm.supplier_name }}
                                    </p>
                                </div>
                                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{
                                    formatCurrency(paymentForm.amount) }}</p>
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Payment
                                Method</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button v-for="method in paymentMethods" :key="method"
                                    @click="paymentForm.method = method"
                                    :class="['px-3 py-2 rounded-lg text-xs font-medium border-2 transition-all', paymentForm.method === method ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-blue-300 dark:hover:border-blue-700']">
                                    {{ method }}
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Payment
                                    Date</label>
                                <input type="date" v-model="paymentForm.payment_date"
                                    class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Reference
                                    / TXN # <span class="text-red-500">*</span></label>
                                <input type="text" v-model="paymentForm.bank_reference" placeholder="e.g., TXN-00123456"
                                    class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Remarks</label>
                            <input type="text" v-model="paymentForm.remarks" placeholder="Optional remarks..."
                                class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                        </div>

                        <div class="flex gap-3 justify-end pt-2">
                            <button @click="showPaymentModal = false"
                                class="px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all">Cancel</button>
                            <button @click="submitPayment" :disabled="isLoading"
                                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm">
                                <DollarSign class="w-4 h-4" />
                                <span>{{ isLoading ? 'Processing...' : 'Confirm Payment' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

    </AuthenticatedLayout>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-fade-enter-active .bg-white,
.modal-fade-leave-active .bg-white {
    transition: transform 0.2s ease;
}

.modal-fade-enter-from .bg-white,
.modal-fade-leave-to .bg-white {
    transform: scale(0.96) translateY(8px);
}

.accordion-enter-active,
.accordion-leave-active {
    transition: all 0.25s ease;
    overflow: hidden;
}

.accordion-enter-from,
.accordion-leave-to {
    max-height: 0;
    opacity: 0;
}

.accordion-enter-to,
.accordion-leave-from {
    max-height: 500px;
    opacity: 1;
}

.slide-toast-enter-active,
.slide-toast-leave-active {
    transition: all 0.3s ease;
}

.slide-toast-enter-from,
.slide-toast-leave-to {
    opacity: 0;
    transform: translateX(20px);
}
</style>