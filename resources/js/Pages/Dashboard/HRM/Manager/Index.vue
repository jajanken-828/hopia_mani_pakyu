<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {
    Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot
} from '@headlessui/vue'
import {
    Users, UserPlus, Calendar, TrendingUp, UserCheck, ArrowUpCircle,
    Eye, ShieldOff, ShieldCheck, Award, X,
    CheckCircle2, XCircle, Star, Building2, ClipboardList,
    LayoutDashboard, CheckCircle, RotateCcw
} from 'lucide-vue-next'

// ─────────────────────────────────────────────
// Props
// ─────────────────────────────────────────────
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalEmployees: 0,
            activeRecruitment: 0,
            pendingLeaves: 0,
            attendanceRate: 0
        })
    },
    suggestedTrainees: {
        type: Array,
        default: () => []
    },
    allEmployees: {
        type: Array,
        default: () => []
    }
})

// ─────────────────────────────────────────────
// Toast
// ─────────────────────────────────────────────
const showToast = ref(false)
const toastMsg = ref('')
const triggerToast = (msg) => {
    toastMsg.value = msg
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 4000)
}

// ─────────────────────────────────────────────
// Main Tab Toggle
// ─────────────────────────────────────────────
const activeTab = ref('employees')

// ─────────────────────────────────────────────
// Employee Display State
// ─────────────────────────────────────────────
const departments = ['ALL', 'HRM', 'SCM', 'FIN', 'MAN', 'INV', 'ORD', 'WAR', 'CRM', 'ECO']
const activeDept = ref('ALL')

const filteredEmployees = computed(() => {
    if (activeDept.value === 'ALL') return props.allEmployees
    return props.allEmployees.filter(e => e.role === activeDept.value)
})
const filteredManagers = computed(() => filteredEmployees.value.filter(e => e.position === 'manager'))
const filteredStaff = computed(() => filteredEmployees.value.filter(e => e.position === 'staff'))

// View Details Modal
const isViewingEmployee = ref(false)
const selectedEmployee = ref(null)
const openViewEmployee = (emp) => { selectedEmployee.value = emp; isViewingEmployee.value = true }
const closeViewEmployee = () => { isViewingEmployee.value = false; selectedEmployee.value = null }

// Deactivate Modal
const isDeactivating = ref(false)
const deactivateTarget = ref(null)
const deactivateReason = ref('')

const openDeactivateModal = (emp) => {
    deactivateTarget.value = emp
    deactivateReason.value = ''
    isDeactivating.value = true
}
const closeDeactivateModal = () => { isDeactivating.value = false; deactivateTarget.value = null }

const confirmDeactivate = () => {
    router.delete(route('hrm.employees.destroy', deactivateTarget.value.id), {
        data: { reason: deactivateReason.value || 'No reason provided' },
        preserveScroll: true,
        onSuccess: () => {
            triggerToast(`${deactivateTarget.value.name}'s account has been deactivated.`)
            closeDeactivateModal()
        },
    })
}

const reactivateEmployee = (emp) => {
    router.delete(route('hrm.employees.destroy', emp.id), {
        data: { reason: 'Account Reactivation by Manager' },
        preserveScroll: true,
        onSuccess: () => triggerToast(`${emp.name}'s account has been reactivated.`),
    })
}

// ─────────────────────────────────────────────
// Trainee Split
// ─────────────────────────────────────────────
const pendingGrading = computed(() =>
    props.suggestedTrainees.filter(t => t.trainee_grade === null || t.trainee_grade === undefined)
)
const alreadyGraded = computed(() =>
    props.suggestedTrainees.filter(t => t.trainee_grade !== null && t.trainee_grade !== undefined)
)

// ─────────────────────────────────────────────
// Grading Modal
// ─────────────────────────────────────────────
const isGrading = ref(false)
const gradingTrainee = ref(null)
const isSavingGrade = ref(false)

// Replaced useForm with a native reactive payload object for absolute stability
const gradeData = ref({
    skills_performance: 0,
    behaviour: 0,
    technicals: 0,
    safety_awareness: 0,
    productivity: 0,
})

const gradeCriteria = [
    { id: 'skills_performance', label: 'Skills Performance' },
    { id: 'behaviour', label: 'Behaviour' },
    { id: 'technicals', label: 'Technicals' },
    { id: 'safety_awareness', label: 'Safety Awareness' },
    { id: 'productivity', label: 'Productivity' },
]

const gradeIsValid = computed(() =>
    gradeCriteria.every(c => gradeData.value[c.id] >= 1)
)

const gradeTotal = computed(() => {
    if (!gradeIsValid.value) return null
    const sum = gradeCriteria.reduce((acc, c) => acc + Number(gradeData.value[c.id]), 0)
    return ((sum / 25) * 100).toFixed(1)
})

const isPassing = computed(() =>
    gradeTotal.value !== null && Number(gradeTotal.value) >= 80
)

const openGradingModal = (trainee) => {
    gradingTrainee.value = trainee
    if (trainee.trainee_grade) {
        gradeData.value.skills_performance = trainee.trainee_grade.skills_performance || 0
        gradeData.value.behaviour = trainee.trainee_grade.behaviour || 0
        gradeData.value.technicals = trainee.trainee_grade.technicals || 0
        gradeData.value.safety_awareness = trainee.trainee_grade.safety_awareness || 0
        gradeData.value.productivity = trainee.trainee_grade.productivity || 0
    } else {
        gradeData.value = { skills_performance: 0, behaviour: 0, technicals: 0, safety_awareness: 0, productivity: 0 }
    }
    isGrading.value = true
}

const closeGradingModal = () => {
    isGrading.value = false
    gradingTrainee.value = null
    gradeData.value = { skills_performance: 0, behaviour: 0, technicals: 0, safety_awareness: 0, productivity: 0 }
}

const submitGrade = () => {
    if (!gradingTrainee.value) return

    if (!gradeIsValid.value) {
        triggerToast('Please rate all 5 criteria before saving.')
        return
    }

    isSavingGrade.value = true

    // Spread to a plain object with explicit Number() cast.
    // Never pass a Vue reactive Proxy directly to router.post() --
    // Inertia may silently fail to serialize it.
    const payload = {
        skills_performance: Number(gradeData.value.skills_performance),
        behaviour: Number(gradeData.value.behaviour),
        technicals: Number(gradeData.value.technicals),
        safety_awareness: Number(gradeData.value.safety_awareness),
        productivity: Number(gradeData.value.productivity),
    }

    router.post(route('hrm.manager.grade-trainee', gradingTrainee.value.id), payload, {
        preserveScroll: true,
        onSuccess: (page) => {
            isSavingGrade.value = false
            const name = gradingTrainee.value.name
            const pct = gradeTotal.value
            closeGradingModal()

            // Laravel sets flash.message ONLY when the DB write succeeded.
            // No flash = silent middleware redirect with no save -- warn the user.
            const flashMsg = page.props?.flash?.message
            if (flashMsg) {
                triggerToast(flashMsg)
            } else {
                triggerToast('⚠️ Grade may not have saved -- no server confirmation. Check storage/logs/laravel.log.')
            }
        },
        onError: (errors) => {
            isSavingGrade.value = false
            // errors.grade is set by controller's withErrors() when the DB write fails
            const msg = errors?.grade || Object.values(errors)[0] || 'Validation failed.'
            console.error('[submitGrade] Server errors:', errors)
            triggerToast(`Save failed: ${msg}`)
        },
        onFinish: () => {
            // Safety net -- prevents button from staying stuck in loading state
            isSavingGrade.value = false
        },
    })
}

// ─────────────────────────────────────────────
// Promotion Confirmation Modal
// ─────────────────────────────────────────────
const isConfirmingPromotion = ref(false)
const selectedTrainee = ref(null)

const confirmPromotion = (t) => { selectedTrainee.value = t; isConfirmingPromotion.value = true }
const closePromotionModal = () => { isConfirmingPromotion.value = false; selectedTrainee.value = null }

const promoteToStaff = () => {
    if (!selectedTrainee.value) return
    router.post(route('hrm.manager.finalize-promotion', selectedTrainee.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            triggerToast(`${selectedTrainee.value.name} has been promoted to Staff!`)
            closePromotionModal()
        },
    })
}

// ─────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────
const getInitials = (name) =>
    name?.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) ?? '?'

const deptColors = {
    HRM: 'bg-pink-100 text-pink-700',
    SCM: 'bg-blue-100 text-blue-700',
    FIN: 'bg-emerald-100 text-emerald-700',
    MAN: 'bg-purple-100 text-purple-700',
    INV: 'bg-amber-100 text-amber-700',
    ORD: 'bg-green-100 text-green-700',
    WAR: 'bg-orange-100 text-orange-700',
    CRM: 'bg-red-100 text-red-700',
    ECO: 'bg-teal-100 text-teal-700',
}
const getDeptClass = (role) => deptColors[role] ?? 'bg-slate-100 text-slate-600'

const avatarColors = [
    'bg-blue-50 text-blue-600', 'bg-violet-50 text-violet-600',
    'bg-emerald-50 text-emerald-600', 'bg-orange-50 text-orange-600',
    'bg-pink-50 text-pink-600', 'bg-teal-50 text-teal-600',
]
const getAvatarColor = (id) => avatarColors[id % avatarColors.length]

const gradeColor = (score) => {
    if (score == null) return 'text-slate-400'
    if (score >= 80) return 'text-emerald-500'
    if (score >= 60) return 'text-blue-600'
    return 'text-red-500'
}
</script>

<template>

    <Head title="HRM Manager Dashboard" />

    <AuthenticatedLayout>

        <Transition name="toast">
            <div v-if="showToast"
                class="fixed top-6 right-6 z-[200] flex items-center gap-3 px-6 py-4 bg-slate-900 text-white rounded-2xl shadow-2xl border border-white/10">
                <CheckCircle class="h-5 w-5 text-emerald-400 flex-shrink-0" />
                <p class="text-sm font-bold tracking-tight">{{ toastMsg }}</p>
            </div>
        </Transition>

        <div class="mb-8">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                HRM <span class="text-blue-600">Manager</span> Dashboard
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                Manage your workforce, employees, and trainee promotions.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                        <Users class="h-6 w-6 text-blue-600" />
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+4%</span>
                </div>
                <p class="text-sm font-medium text-slate-500">Total Employees</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.totalEmployees }}</p>
            </div>
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
                        <UserPlus class="h-6 w-6 text-indigo-600" />
                    </div>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">{{
                        suggestedTrainees.length }} Active</span>
                </div>
                <p class="text-sm font-medium text-slate-500">Trainees</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ suggestedTrainees.length }}</p>
            </div>
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                        <Calendar class="h-6 w-6 text-amber-600" />
                    </div>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Action
                        Required</span>
                </div>
                <p class="text-sm font-medium text-slate-500">Pending Leaves</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.pendingLeaves }}</p>
            </div>
            <div
                class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                        <TrendingUp class="h-6 w-6 text-emerald-600" />
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Optimal</span>
                </div>
                <p class="text-sm font-medium text-slate-500">Attendance Rate</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.attendanceRate }}%</p>
            </div>
        </div>

        <div
            class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl mb-6 border border-slate-200 dark:border-slate-700">
            <button @click="activeTab = 'employees'" :class="[
                'flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-200',
                activeTab === 'employees'
                    ? 'bg-white dark:bg-slate-700 text-blue-600 shadow-sm'
                    : 'text-slate-500 hover:text-slate-700'
            ]">
                <Building2 class="h-4 w-4" /> Employee Display
            </button>
            <button @click="activeTab = 'trainees'" :class="[
                'flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-200',
                activeTab === 'trainees'
                    ? 'bg-white dark:bg-slate-700 text-indigo-600 shadow-sm'
                    : 'text-slate-500 hover:text-slate-700'
            ]">
                <Award class="h-4 w-4" /> Trainee Display
                <span v-if="suggestedTrainees.length > 0"
                    class="ml-1 bg-indigo-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full leading-none">
                    {{ suggestedTrainees.length }}
                </span>
            </button>
        </div>


        <div v-if="activeTab === 'employees'">

            <div class="flex items-center gap-2 flex-wrap mb-6">
                <span class="text-xs font-black text-slate-400 uppercase tracking-widest mr-1">Filter:</span>
                <button v-for="dept in departments" :key="dept" @click="activeDept = dept" :class="[
                    'px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-widest transition-all',
                    activeDept === dept
                        ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20'
                        : 'bg-white dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700 hover:border-blue-300 hover:text-blue-600'
                ]">{{ dept }}</button>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex items-center gap-3">
                    <div class="p-1.5 bg-blue-50 rounded-lg">
                        <ShieldCheck class="h-4 w-4 text-blue-600" />
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">Managers</h2>
                        <p class="text-xs text-slate-400">{{ filteredManagers.length }} manager{{
                            filteredManagers.length !== 1 ? 's' : '' }}</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/60 dark:bg-slate-900/40">
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Employee</th>
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Department</th>
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                    Account Control</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/60">
                            <tr v-for="emp in filteredManagers" :key="emp.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div v-if="emp.profile_photo_url"
                                            class="h-10 w-10 rounded-xl overflow-hidden flex-shrink-0">
                                            <img :src="emp.profile_photo_url" :alt="emp.name"
                                                class="h-full w-full object-cover" />
                                        </div>
                                        <div v-else
                                            :class="['h-10 w-10 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0', getAvatarColor(emp.id)]">
                                            {{ getInitials(emp.name) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ emp.name }}
                                            </p>
                                            <p class="text-[10px] font-black text-slate-400 uppercase">ID: #{{
                                                emp.employee_id || emp.id }}</p>
                                            <p class="text-[11px] text-slate-400">{{ emp.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="['px-2 py-1 rounded-md text-[10px] font-black uppercase', getDeptClass(emp.role)]">{{
                                            emp.role }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="['inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase', emp.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700']">
                                        <span class="h-1.5 w-1.5 rounded-full"
                                            :class="emp.is_active ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                        {{ emp.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openViewEmployee(emp)"
                                            class="p-2 rounded-xl bg-slate-50 dark:bg-slate-700/50 text-slate-500 hover:text-blue-600 transition-colors">
                                            <Eye class="h-4 w-4" />
                                        </button>
                                        <button v-if="emp.is_active" @click="openDeactivateModal(emp)"
                                            class="p-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                            <ShieldOff class="h-4 w-4" />
                                        </button>
                                        <button v-else @click="reactivateEmployee(emp)"
                                            class="p-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors">
                                            <ShieldCheck class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredManagers.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">
                                    No managers found in {{ activeDept.value }}.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex items-center gap-3">
                    <div class="p-1.5 bg-indigo-50 rounded-lg">
                        <UserCheck class="h-4 w-4 text-indigo-600" />
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">Staff</h2>
                        <p class="text-xs text-slate-400">{{ filteredStaff.length }} staff member{{
                            filteredStaff.length !== 1 ? 's' : '' }}</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/60 dark:bg-slate-900/40">
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Employee</th>
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Department</th>
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                    Account Control</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/60">
                            <tr v-for="emp in filteredStaff" :key="emp.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div v-if="emp.profile_photo_url"
                                            class="h-10 w-10 rounded-xl overflow-hidden flex-shrink-0">
                                            <img :src="emp.profile_photo_url" :alt="emp.name"
                                                class="h-full w-full object-cover" />
                                        </div>
                                        <div v-else
                                            :class="['h-10 w-10 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0', getAvatarColor(emp.id)]">
                                            {{ getInitials(emp.name) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ emp.name }}
                                            </p>
                                            <p class="text-[10px] font-black text-slate-400 uppercase">ID: #{{
                                                emp.employee_id || emp.id }}</p>
                                            <p class="text-[11px] text-slate-400">{{ emp.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="['px-2 py-1 rounded-md text-[10px] font-black uppercase', getDeptClass(emp.role)]">{{
                                            emp.role }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="['inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase', emp.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700']">
                                        <span class="h-1.5 w-1.5 rounded-full"
                                            :class="emp.is_active ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                        {{ emp.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openViewEmployee(emp)"
                                            class="p-2 rounded-xl bg-slate-50 dark:bg-slate-700/50 text-slate-500 hover:text-blue-600 transition-colors">
                                            <Eye class="h-4 w-4" />
                                        </button>
                                        <button v-if="emp.is_active" @click="openDeactivateModal(emp)"
                                            class="p-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                            <ShieldOff class="h-4 w-4" />
                                        </button>
                                        <button v-else @click="reactivateEmployee(emp)"
                                            class="p-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors">
                                            <ShieldCheck class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredStaff.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">
                                    No staff found in {{ activeDept.value }}.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div v-if="activeTab === 'trainees'">

            <div class="space-y-8">

                <!-- Pending Grading Section -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex items-center gap-3">
                        <div class="p-1.5 bg-amber-50 rounded-lg">
                            <ClipboardList class="h-4 w-4 text-amber-600" />
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-slate-900 dark:text-white">Pending Grading</h2>
                            <p class="text-xs text-slate-400">{{ pendingGrading.length }} trainee{{
                                pendingGrading.length !== 1 ? 's' : '' }} awaiting evaluation</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/60 dark:bg-slate-900/40">
                                    <th
                                        class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Trainee</th>
                                    <th
                                        class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Department</th>
                                    <th
                                        class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Suggested</th>
                                    <th
                                        class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/60">
                                <tr v-for="t in pendingGrading" :key="t.id"
                                    class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div v-if="t.profile_photo_url"
                                                class="h-10 w-10 rounded-xl overflow-hidden flex-shrink-0">
                                                <img :src="t.profile_photo_url" :alt="t.name"
                                                    class="h-full w-full object-cover" />
                                            </div>
                                            <div v-else
                                                :class="['h-10 w-10 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0', getAvatarColor(t.id)]">
                                                {{ getInitials(t.name) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{
                                                    t.name }}</p>
                                                <p class="text-[10px] font-black text-slate-400 uppercase">ID: #{{
                                                    t.employee_id || t.id }}</p>
                                                <p class="text-[11px] text-slate-400">{{ t.email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="['px-2 py-1 rounded-md text-[10px] font-black uppercase', getDeptClass(t.role)]">{{
                                                t.role }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span v-if="t.promotion_suggested"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-blue-100 text-blue-700">
                                            <ArrowUpCircle class="h-3.5 w-3.5" />
                                            Suggested
                                        </span>
                                        <span v-else class="text-[10px] font-black text-slate-400 uppercase">Not
                                            Yet</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="openGradingModal(t)"
                                            class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold uppercase shadow-md hover:bg-blue-700 transition-all active:scale-95">
                                            Grade Now
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="pendingGrading.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">
                                        No trainees pending grading.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Graded Trainees Section -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex items-center gap-3">
                        <div class="p-1.5 bg-emerald-50 rounded-lg">
                            <Award class="h-4 w-4 text-emerald-600" />
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-slate-900 dark:text-white">Graded Trainees</h2>
                            <p class="text-xs text-slate-400">{{ alreadyGraded.length }} trainee{{
                                alreadyGraded.length !== 1 ? 's' : '' }} evaluated</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/60 dark:bg-slate-900/40">
                                    <th
                                        class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Trainee</th>
                                    <th
                                        class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Department</th>
                                    <th
                                        class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Score</th>
                                    <th
                                        class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                        Status / Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/60">
                                <tr v-for="t in alreadyGraded" :key="t.id"
                                    class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div v-if="t.profile_photo_url"
                                                class="h-10 w-10 rounded-xl overflow-hidden flex-shrink-0">
                                                <img :src="t.profile_photo_url" :alt="t.name"
                                                    class="h-full w-full object-cover" />
                                            </div>
                                            <div v-else
                                                :class="['h-10 w-10 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0', getAvatarColor(t.id)]">
                                                {{ getInitials(t.name) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{
                                                    t.name }}</p>
                                                <p class="text-[10px] font-black text-slate-400 uppercase">ID: #{{
                                                    t.employee_id || t.id }}</p>
                                                <p class="text-[11px] text-slate-400">{{ t.email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="['px-2 py-1 rounded-md text-[10px] font-black uppercase', getDeptClass(t.role)]">{{
                                                t.role }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="['font-bold', gradeColor(t.trainee_grade.total_percentage)]">{{
                                            t.trainee_grade.total_percentage }}%</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div v-if="t.trainee_grade.total_percentage >= 80"
                                                class="flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-700">
                                                <CheckCircle2 class="h-3.5 w-3.5" />
                                                Qualified
                                            </div>
                                            <div v-else
                                                class="flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-700">
                                                <XCircle class="h-3.5 w-3.5" />
                                                Below Threshold
                                            </div>
                                            <button v-if="t.trainee_grade.total_percentage >= 80"
                                                @click="confirmPromotion(t)"
                                                class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold uppercase shadow-md hover:bg-emerald-700 transition-all active:scale-95">
                                                Confirm Promotion
                                            </button>
                                            <button v-else @click="openGradingModal(t)"
                                                class="px-4 py-2 rounded-xl bg-amber-600 text-white text-xs font-bold uppercase shadow-md hover:bg-amber-700 transition-all active:scale-95 flex items-center gap-1">
                                                <RotateCcw class="h-3.5 w-3.5" />
                                                Re-Evaluate
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="alreadyGraded.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">
                                        No graded trainees yet.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>


        <!-- Grading Modal -->
        <TransitionRoot as="template" :show="isGrading">
            <Dialog as="div" class="relative z-50" @close="closeGradingModal">
                <TransitionChild as="template" enter="ease-out duration-200" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in duration-150" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" />
                </TransitionChild>
                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <TransitionChild as="template" enter="ease-out duration-200" enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100" leave="ease-in duration-150"
                            leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel
                                class="relative w-full max-w-xl transform overflow-hidden rounded-[2rem] bg-white dark:bg-slate-800 p-8 shadow-2xl border border-slate-100 dark:border-slate-700">
                                <div class="flex items-center mb-6">
                                    <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase">Coursework
                                        Grading</h3>
                                    <button @click="closeGradingModal"
                                        class="text-slate-400 hover:text-slate-600 transition-colors">
                                        <X class="h-6 w-6" />
                                    </button>
                                </div>

                                <p class="text-sm text-slate-500 mb-6">
                                    Rate <b class="text-slate-900 dark:text-white">{{ gradingTrainee?.name }}</b> based
                                    on their training
                                    performance.
                                    <span class="block mt-1 text-[11px] text-amber-600 font-bold">80% average (20 of 25
                                        stars) required for
                                        promotion.</span>
                                </p>

                                <div class="space-y-6">
                                    <div v-for="criterion in gradeCriteria" :key="criterion.id">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                            {{ criterion.label }}
                                        </p>
                                        <div class="flex gap-2">
                                            <button v-for="star in 5" :key="star" type="button"
                                                @click="gradeData[criterion.id] = star"
                                                class="focus:outline-none transition-transform active:scale-110">
                                                <Star :class="[
                                                    gradeData[criterion.id] >= star
                                                        ? 'text-amber-400 fill-amber-400'
                                                        : 'text-slate-200 dark:text-slate-600',
                                                    'h-7 w-7 transition-colors cursor-pointer'
                                                ]" />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="gradeIsValid" :class="[
                                    'mt-6 p-4 rounded-2xl border-2 transition-all',
                                    isPassing ? 'border-emerald-300 bg-emerald-50' : 'border-red-200 bg-red-50'
                                ]">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                Total Average</p>
                                            <p
                                                :class="['text-3xl font-black mt-0.5', isPassing ? 'text-emerald-600' : 'text-red-500']">
                                                {{ gradeTotal }}%
                                            </p>
                                        </div>
                                        <div v-if="isPassing"
                                            class="flex items-center gap-2 px-3 py-2 bg-emerald-100 text-emerald-700 rounded-xl">
                                            <CheckCircle2 class="h-4 w-4" />
                                            <span class="text-xs font-black uppercase">Will be promoted</span>
                                        </div>
                                        <div v-else
                                            class="flex items-center gap-2 px-3 py-2 bg-red-100 text-red-600 rounded-xl">
                                            <XCircle class="h-4 w-4" />
                                            <span class="text-xs font-black uppercase">Below threshold</span>
                                        </div>
                                    </div>
                                    <p v-if="isPassing" class="text-xs text-emerald-600 mt-2 font-medium">
                                        ✓ This trainee will automatically be promoted to Staff upon saving.
                                    </p>
                                </div>

                                <p v-else class="mt-4 text-xs text-slate-400 text-center font-bold text-amber-600">
                                    Rate all 5 criteria to see the score preview and enable saving.
                                </p>

                                <div class="mt-8 flex flex-col gap-3">
                                    <button @click="submitGrade" :disabled="isSavingGrade || !gradeIsValid" :class="[
                                        'w-full py-4 rounded-xl font-bold uppercase text-xs tracking-widest transition-all',
                                        (isSavingGrade || !gradeIsValid)
                                            ? 'bg-slate-300 text-slate-500 cursor-not-allowed'
                                            : 'bg-slate-900 text-white hover:bg-blue-600 shadow-lg active:scale-95'
                                    ]">
                                        {{ isSavingGrade ? 'Saving...' : 'Save Trainee Grades' }}
                                    </button>
                                    <button @click="closeGradingModal"
                                        class="w-full py-3 text-slate-400 font-bold hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl transition-all text-xs uppercase">
                                        Cancel
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>


        <TransitionRoot as="template" :show="isViewingEmployee">
            <Dialog as="div" class="relative z-50" @close="closeViewEmployee">
                <TransitionChild as="template" enter="ease-out duration-200" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in duration-150" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" />
                </TransitionChild>
                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <TransitionChild as="template" enter="ease-out duration-200" enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100" leave="ease-in duration-150"
                            leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel
                                class="relative w-full max-w-md transform rounded-2xl bg-white dark:bg-slate-800 shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                                <div
                                    class="px-6 pt-6 pb-4 border-b border-slate-50 dark:border-slate-700 flex items-start justify-between">
                                    <DialogTitle class="text-base font-black text-slate-900 dark:text-white uppercase">
                                        Employee
                                        Details</DialogTitle>
                                    <button @click="closeViewEmployee"
                                        class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 transition-colors">
                                        <X class="h-5 w-5" />
                                    </button>
                                </div>
                                <div v-if="selectedEmployee" class="p-6">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div v-if="selectedEmployee.profile_photo_url"
                                            class="h-16 w-16 rounded-2xl overflow-hidden flex-shrink-0">
                                            <img :src="selectedEmployee.profile_photo_url"
                                                class="h-full w-full object-cover" />
                                        </div>
                                        <div v-else
                                            :class="['h-16 w-16 rounded-2xl flex items-center justify-center font-black text-xl flex-shrink-0', getAvatarColor(selectedEmployee.id)]">
                                            {{ getInitials(selectedEmployee.name) }}
                                        </div>
                                        <div>
                                            <p class="text-lg font-black text-slate-900 dark:text-white">{{
                                                selectedEmployee.name }}</p>
                                            <p class="text-xs font-bold text-slate-400 uppercase">{{
                                                selectedEmployee.position
                                                }} · ID #{{ selectedEmployee.employee_id || selectedEmployee.id }}</p>
                                            <span
                                                :class="['inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase', selectedEmployee.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700']">
                                                {{ selectedEmployee.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-slate-50 dark:bg-slate-900/40 rounded-xl p-3">
                                            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Email</p>
                                            <p class="text-xs font-medium text-slate-700 dark:text-slate-300 break-all">
                                                {{
                                                    selectedEmployee.email }}</p>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-900/40 rounded-xl p-3">
                                            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Department
                                            </p>
                                            <span
                                                :class="['px-2 py-0.5 rounded-md text-[10px] font-black uppercase', getDeptClass(selectedEmployee.role)]">{{
                                                    selectedEmployee.role }}</span>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-900/40 rounded-xl p-3">
                                            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Position</p>
                                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300 capitalize">
                                                {{
                                                    selectedEmployee.position }}</p>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-900/40 rounded-xl p-3">
                                            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Join Date
                                            </p>
                                            <p class="text-xs font-medium text-slate-700 dark:text-slate-300">
                                                {{ selectedEmployee.join_date ? new
                                                    Date(selectedEmployee.join_date).toLocaleDateString('en-US', {
                                                        month:
                                                            'short',
                                                        day: 'numeric', year: 'numeric'
                                                    }) : 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <button @click="closeViewEmployee"
                                        class="mt-5 w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition-all">
                                        Close
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>


        <TransitionRoot as="template" :show="isDeactivating">
            <Dialog as="div" class="relative z-50" @close="closeDeactivateModal">
                <TransitionChild as="template" enter="ease-out duration-200" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in duration-150" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" />
                </TransitionChild>
                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <TransitionChild as="template" enter="ease-out duration-200" enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100" leave="ease-in duration-150"
                            leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel
                                class="relative w-full max-w-md transform rounded-2xl bg-white dark:bg-slate-800 shadow-2xl border border-red-100 dark:border-red-900/40 overflow-hidden">
                                <div class="p-6">
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="flex-shrink-0 h-11 w-11 rounded-xl bg-red-100 flex items-center justify-center">
                                            <ShieldOff class="h-5 w-5 text-red-600" />
                                        </div>
                                        <div class="flex-1">
                                            <DialogTitle
                                                class="text-base font-black text-slate-900 dark:text-white uppercase">
                                                Deactivate Account</DialogTitle>
                                            <p class="text-sm text-slate-500 mt-1">
                                                You are about to deactivate <span
                                                    class="font-bold text-slate-900 dark:text-white">{{
                                                        deactivateTarget?.name
                                                    }}</span>'s account.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-5">
                                        <label
                                            class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">
                                            Reason <span class="text-red-500">*</span>
                                        </label>
                                        <textarea v-model="deactivateReason" rows="3"
                                            placeholder="e.g. Violation of company policy, contract ended..."
                                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/40 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent resize-none">
                                </textarea>
                                    </div>
                                    <div class="mt-5 flex gap-3 sm:flex-row-reverse">
                                        <button @click="confirmDeactivate" :disabled="!deactivateReason.trim()"
                                            :class="['flex-1 sm:flex-none py-2.5 px-5 rounded-xl text-xs font-black uppercase text-white transition-all active:scale-95',
                                                deactivateReason.trim() ? 'bg-red-600 hover:bg-red-700 shadow-md' : 'bg-red-300 cursor-not-allowed']">
                                            Confirm Deactivation
                                        </button>
                                        <button @click="closeDeactivateModal"
                                            class="flex-1 sm:flex-none py-2.5 px-5 rounded-xl text-xs font-black uppercase text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 transition-all">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>


        <TransitionRoot as="template" :show="isConfirmingPromotion">
            <Dialog as="div" class="relative z-50" @close="closePromotionModal">
                <TransitionChild as="template" enter="ease-out duration-200" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in duration-150" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" />
                </TransitionChild>
                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <TransitionChild as="template" enter="ease-out duration-200" enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100" leave="ease-in duration-150"
                            leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel
                                class="relative w-full max-w-sm transform bg-white dark:bg-slate-800 rounded-[2rem] p-8 shadow-2xl text-center border border-blue-100 dark:border-blue-900/40">
                                <div
                                    class="h-20 w-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <UserCheck class="h-10 w-10 text-blue-600" />
                                </div>
                                <DialogTitle class="text-xl font-black text-slate-900 dark:text-white uppercase">Confirm
                                    Promotion</DialogTitle>
                                <p class="text-slate-500 text-sm mt-2 mb-3 leading-relaxed">
                                    Promote <b class="text-slate-900 dark:text-white">{{ selectedTrainee?.name }}</b> to
                                    official <span class="text-blue-600 font-bold">Staff</span>?
                                </p>
                                <div v-if="selectedTrainee?.trainee_grade"
                                    class="flex items-center justify-center gap-2 p-3 bg-emerald-50 rounded-xl mb-6">
                                    <CheckCircle2 class="h-4 w-4 text-emerald-600 flex-shrink-0" />
                                    <p class="text-xs text-emerald-700 font-black">
                                        Final Score: {{ selectedTrainee.trainee_grade.total_percentage }}% — Qualified
                                    </p>
                                </div>
                                <div class="flex flex-col gap-3">
                                    <button @click="promoteToStaff"
                                        class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all uppercase text-xs tracking-widest hover:bg-blue-700">
                                        Confirm Promotion
                                    </button>
                                    <button @click="closePromotionModal"
                                        class="w-full px-6 py-3 text-slate-400 font-bold hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl transition-all text-xs uppercase">
                                        Cancel
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

    </AuthenticatedLayout>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(-12px);
}
</style>