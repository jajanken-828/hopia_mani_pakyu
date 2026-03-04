<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted, onUnmounted, computed } from 'vue'
import {
    Clock,
    Users,
    ChevronLeft,
    ChevronRight,
    Sunrise,
    Sunset,
    Moon,
    X,
    Calendar as CalendarIcon,
    CheckCircle2,
    Plus,
    ArrowRight,
    Wand2,
    CalendarCheck,
    ListChecks,
    AlertCircle,
    UserX
} from 'lucide-vue-next'

const props = defineProps({
    attendance_status: {
        type: Object,
        default: () => ({ is_clocked_in: false, last_action: '08:45 AM', total_hours_today: '0h 0m' })
    },
    employee_attendance: {
        type: Array,
        default: () => []
    },
    monthly_shifts: {
        type: Array,
        default: () => []
    }
})

// --- UI STATE TOGGLE ---
const activeView = ref('scheduler') // 'scheduler' or 'attendance'

// --- LIVE CLOCK ---
const currentTime = ref(new Date().toLocaleTimeString())
let timerInterval
onMounted(() => {
    timerInterval = setInterval(() => {
        currentTime.value = new Date().toLocaleTimeString()
    }, 1000)
})
onUnmounted(() => clearInterval(timerInterval))

// --- CALENDAR & MODAL LOGIC ---
const viewDate = ref(new Date())
const selectedDate = ref(null)
const isModalOpen = ref(false)
const isAutoScheduleModalOpen = ref(false)
const selectedDept = ref('HRM')
const autoScheduleDept = ref('HRM')
const activeSelectorShift = ref(null)
const activeMonthlySelectorShift = ref(null)
const showSuccessToast = ref(false)
const toastMessage = ref('Staff Assigned to Shift')
const departments = ['HRM', 'SCM', 'FIN', 'MAN', 'INV', 'ORD', 'WAR', 'CRM', 'ECO']

const calendarDays = computed(() => {
    const year = viewDate.value.getFullYear()
    const month = viewDate.value.getMonth()
    const firstDayOfMonth = new Date(year, month, 1).getDay()
    const daysInMonth = new Date(year, month + 1, 0).getDate()
    const days = []
    for (let i = 0; i < firstDayOfMonth; i++) { days.push({ day: null, date: null }) }
    for (let d = 1; d <= daysInMonth; d++) {
        const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
        days.push({ day: d, date: fullDate })
    }
    return days
})

const monthName = computed(() => viewDate.value.toLocaleString('default', { month: 'long', year: 'numeric' }))

// Navigation logic
const fetchMonthData = () => {
    router.get(route('hrm.employee.attendance'), {
        month: viewDate.value.getMonth() + 1,
        year: viewDate.value.getFullYear()
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['monthly_shifts']
    })
}

const nextMonth = () => {
    viewDate.value = new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() + 1, 1)
    fetchMonthData()
}

const prevMonth = () => {
    viewDate.value = new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() - 1, 1)
    fetchMonthData()
}

// --- SHIFT RANGE HELPER ---
const getShiftRange = (type) => {
    switch (type) {
        case 'Morning': return '08:00 AM - 05:00 PM';
        case 'Afternoon': return '04:00 PM - 01:00 AM';
        case 'Graveyard': return '12:00 AM - 09:00 AM';
        default: return '08:00 AM - 05:00 PM';
    }
}

// --- COMPUTED: shifts grouped by date for calendar display ---
const shiftsByDate = computed(() => {
    const map = {}
    props.monthly_shifts.forEach(shift => {
        if (!map[shift.effective_date]) {
            map[shift.effective_date] = new Set()
        }
        map[shift.effective_date].add(shift.shift_type)
    })
    // Convert Set to array for each date
    const result = {}
    Object.keys(map).forEach(date => {
        result[date] = Array.from(map[date])
    })
    return result
})

// --- SHIFT MANAGEMENT ---
const form = useForm({
    user_id: null,
    shift_type: '',
    effective_date: '',
    dept_code: '',
    schedule_range: '',
    is_bulk: false,
    month: null,
    year: null
})

const openDayModal = (date) => {
    if (!date) return
    selectedDate.value = date
    isModalOpen.value = true
    activeSelectorShift.value = null
}

const closeModal = () => {
    isModalOpen.value = false
    isAutoScheduleModalOpen.value = false
    activeSelectorShift.value = null
    activeMonthlySelectorShift.value = null
}

const triggerToast = (msg) => {
    toastMessage.value = msg
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 3000)
}

const updateShift = (employeeId, type) => {
    form.is_bulk = false
    form.user_id = employeeId
    form.shift_type = type
    form.effective_date = selectedDate.value
    form.dept_code = selectedDept.value
    form.schedule_range = getShiftRange(type)

    form.post(route('hrm.employee.attendance.update-shift'), {
        preserveScroll: true,
        onSuccess: () => {
            triggerToast('Staff Assigned to Shift')
            activeSelectorShift.value = null
            router.reload({
                only: ['monthly_shifts'],
                data: { month: viewDate.value.getMonth() + 1, year: viewDate.value.getFullYear() }
            })
        }
    })
}

const setEmployeeMonthlySchedule = (employeeId, shiftType) => {
    form.is_bulk = true
    form.user_id = employeeId
    form.shift_type = shiftType
    form.dept_code = autoScheduleDept.value
    form.month = viewDate.value.getMonth() + 1
    form.year = viewDate.value.getFullYear()
    form.schedule_range = getShiftRange(shiftType)

    form.post(route('hrm.employee.attendance.update-shift'), {
        preserveScroll: true,
        onSuccess: () => {
            triggerToast(`Employee scheduled for the whole month`)
            activeMonthlySelectorShift.value = null
            router.reload({
                only: ['monthly_shifts'],
                data: { month: viewDate.value.getMonth() + 1, year: viewDate.value.getFullYear() }
            })
        }
    })
}

// --- COMPUTED HELPERS ---
const departmentStaff = computed(() => {
    return props.employee_attendance.filter(emp => {
        const empDept = emp.dept || emp.role || ''
        return empDept.toString().toUpperCase() === selectedDept.value.toUpperCase()
    })
})

const monthlyDeptStaff = computed(() => {
    return props.employee_attendance.filter(emp => {
        const empDept = emp.dept || emp.role || ''
        return empDept.toString().toUpperCase() === autoScheduleDept.value.toUpperCase()
    })
})

const getEmployeesInShift = (type) => {
    return props.monthly_shifts
        .filter(s => s.effective_date === selectedDate.value && s.shift_type === type)
        .map(s => props.employee_attendance.find(emp => emp.id === s.user_id))
        .filter(emp => emp && (emp.dept || emp.role || '').toString().toUpperCase() === selectedDept.value.toUpperCase())
}

const getEmployeesInMonthlyShift = (type) => {
    const m = viewDate.value.getMonth() + 1
    const y = viewDate.value.getFullYear()
    const assignedIds = props.monthly_shifts
        .filter(s => {
            const d = new Date(s.effective_date)
            return (d.getMonth() + 1) === m && d.getFullYear() === y && s.shift_type === type
        })
        .map(s => s.user_id)
    const uniqueIds = [...new Set(assignedIds)]
    return uniqueIds.map(id => props.employee_attendance.find(emp => emp.id === id))
        .filter(emp => emp && (emp.dept || emp.role || '').toString().toUpperCase() === autoScheduleDept.value.toUpperCase())
}

// Shift icon mapping
const shiftIcon = (type) => {
    switch (type) {
        case 'Morning': return Sunrise
        case 'Afternoon': return Sunset
        case 'Graveyard': return Moon
        default: return Clock
    }
}
</script>

<template>

    <Head title="Attendance & Scheduling" />

    <AuthenticatedLayout>
        <Transition enter-active-class="transform transition duration-300 ease-out"
            enter-from-class="translate-y-[-100%] opacity-0" enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100"
            leave-to-class="opacity-0">
            <div v-if="showSuccessToast"
                class="fixed top-6 right-6 z-[100] bg-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3">
                <CheckCircle2 class="h-5 w-5" />
                <span class="font-bold text-sm">{{ toastMessage }}</span>
            </div>
        </Transition>

        <div class="flex flex-col lg:flex-row justify-between items-center gap-6 mb-8">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Shift <span
                            class="text-blue-600">Scheduler</span></h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Manage departmental shift
                        pipelines and attendance.</p>
                </div>
                <div
                    class="flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <button @click="isAutoScheduleModalOpen = true"
                        class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                        <Wand2 class="h-3.5 w-3.5" />
                        Set Monthly
                    </button>
                    <button @click="activeView = activeView === 'scheduler' ? 'attendance' : 'scheduler'"
                        :class="activeView === 'attendance' ? 'bg-white dark:bg-slate-700 text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 ml-1">
                        <ListChecks class="h-3.5 w-3.5" />
                        {{ activeView === 'scheduler' ? 'Attendance' : 'Calendar' }}
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div
                    class="flex items-center bg-white dark:bg-slate-800 rounded-2xl p-1 shadow-sm border border-slate-100 dark:border-slate-700">
                    <button @click="prevMonth"
                        class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl text-slate-500 transition-colors">
                        <ChevronLeft class="h-5 w-5" />
                    </button>
                    <span
                        class="px-6 font-black text-[11px] uppercase tracking-widest text-slate-700 dark:text-white">{{
                            monthName }}</span>
                    <button @click="nextMonth"
                        class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl text-slate-500 transition-colors">
                        <ChevronRight class="h-5 w-5" />
                    </button>
                </div>
                <div
                    class="bg-white dark:bg-slate-800 px-6 py-3 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xl shadow-blue-500/5 flex items-center gap-4">
                    <Clock class="h-5 w-5 text-blue-600" />
                    <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tighter">{{ currentTime }}
                    </h2>
                </div>
            </div>
        </div>
        <div class="pb-3 p-2 bg-blue-200 rounded-xl mb-5 text-blue-900 text-center text-sm font-bold">
            <h2 class="">MONTITEXTILE SHIFT SCHEDULE - GRAVEYARD:12AM TO 9AM | MORNING: 8AM TO 5PM |
                AFTERNOON: 4PM
                TO 1AM</h2>
        </div>

        <Transition mode="out-in" enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-4" enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-4">

            <div v-if="activeView === 'scheduler'" key="scheduler">
                <div
                    class="bg-white dark:bg-slate-950 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden p-6">
                    <div class="grid grid-cols-7 gap-3">
                        <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day"
                            class="text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pb-6">{{
                                day }}</div>
                        <div v-for="(item, index) in calendarDays" :key="index" @click="openDayModal(item.date)"
                            :class="[item.date ? 'hover:border-blue-500 cursor-pointer bg-slate-50/50 dark:bg-slate-900/30' : 'bg-transparent border-transparent', item.date === new Date().toISOString().split('T')[0] ? 'ring-2 ring-blue-500 bg-blue-50/30' : '']"
                            class="min-h-[140px] p-4 rounded-[2rem] border border-slate-100 dark:border-slate-800 transition-all group relative overflow-hidden">
                            <span v-if="item.day"
                                class="text-lg font-black text-slate-300 dark:text-slate-600 group-hover:text-blue-600 transition-colors">{{
                                    item.day }}</span>

                            <!-- Dynamic shift icons based on actual assignments -->
                            <div v-if="item.date && shiftsByDate[item.date]" class="mt-4 flex flex-wrap gap-2">
                                <div v-for="shift in shiftsByDate[item.date]" :key="shift"
                                    class="flex items-center gap-1 px-2 py-1 rounded-full text-[8px] font-black uppercase tracking-wider"
                                    :class="{
                                        'bg-amber-100 text-amber-700': shift === 'Morning',
                                        'bg-blue-100 text-blue-700': shift === 'Afternoon',
                                        'bg-indigo-100 text-indigo-700': shift === 'Graveyard'
                                    }" :title="getShiftRange(shift)">
                                    <component :is="shiftIcon(shift)" class="h-3 w-3" />
                                    <span class="hidden sm:inline">{{ shift }}</span>
                                </div>
                            </div>
                            <!-- Fallback dots if no shifts assigned (optional) -->
                            <div v-else-if="item.date" class="mt-4 flex flex-wrap gap-1 opacity-20">
                                <div class="h-1.5 w-1.5 rounded-full bg-amber-400"></div>
                                <div class="h-1.5 w-1.5 rounded-full bg-blue-400"></div>
                                <div class="h-1.5 w-1.5 rounded-full bg-indigo-400"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else key="attendance" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-6">
                        <div
                            class="h-14 w-14 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-2xl flex items-center justify-center">
                            <CheckCircle2 class="h-7 w-7" />
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">On-Time Today</p>
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{
                                employee_attendance.filter(e => e.status === 'On-Time').length}} Staff</h3>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-6">
                        <div
                            class="h-14 w-14 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-2xl flex items-center justify-center">
                            <AlertCircle class="h-7 w-7" />
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Late Arrivals</p>
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{
                                employee_attendance.filter(e => e.status === 'Late').length}} Staff</h3>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-6">
                        <div
                            class="h-14 w-14 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-2xl flex items-center justify-center">
                            <UserX class="h-7 w-7" />
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Currently Absent
                            </p>
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{
                                employee_attendance.filter(e => e.status === 'Absent').length}} Staff</h3>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">
                            Real-Time Attendance Log</h2>
                        <div class="flex gap-2">
                            <button v-for="dept in departments.slice(0, 9)" :key="dept" @click="selectedDept = dept"
                                :class="selectedDept === dept ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'"
                                class="px-4 py-2 rounded-xl text-[10px] font-black transition-all uppercase tracking-tighter">{{
                                    dept }}</button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-slate-50 dark:border-slate-800">
                                    <th
                                        class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Employee</th>
                                    <th
                                        class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Department</th>
                                    <th
                                        class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Current Shift</th>
                                    <th
                                        class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Clock In</th>
                                    <th
                                        class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                <tr v-for="emp in departmentStaff" :key="emp.id"
                                    class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-9 w-9 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-[11px] font-bold text-blue-600">
                                                {{ emp.name.charAt(0) }}</div>
                                            <span
                                                class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight">{{
                                                    emp.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span
                                            class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-[9px] font-black text-slate-500 uppercase tracking-tighter">{{
                                                emp.dept }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{
                                                emp.shift }}</span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">{{
                                                getShiftRange(emp.shift) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-xs font-mono font-bold"
                                            :class="emp.clock_in !== '---' ? 'text-blue-600' : 'text-slate-300'">{{
                                                emp.clock_in }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-2">
                                            <div class="h-1.5 w-1.5 rounded-full"
                                                :class="{ 'bg-emerald-500': emp.status === 'On-Time', 'bg-amber-500': emp.status === 'Late', 'bg-slate-300': emp.status === 'Absent' }">
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-widest"
                                                :class="{ 'text-emerald-600': emp.status === 'On-Time', 'text-amber-600': emp.status === 'Late', 'text-slate-400': emp.status === 'Absent' }">
                                                {{ emp.status }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Monthly Pipeline Modal (unchanged, already shows shift times) -->
        <Transition enter-active-class="duration-300 ease-out" enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100" leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <div v-if="isAutoScheduleModalOpen" class="fixed inset-0 z-[70] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/90 backdrop-blur-md" @click="closeModal"></div>
                <div
                    class="relative bg-white dark:bg-slate-900 w-full max-w-7xl rounded-[3.5rem] shadow-2xl overflow-hidden border border-white/10">
                    <div
                        class="p-10 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-2 bg-blue-600 rounded-xl text-white shadow-lg shadow-blue-500/30">
                                        <CalendarCheck class="h-5 w-5" />
                                    </div>
                                    <h2
                                        class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                                        Monthly Pipeline: {{ monthName }}</h2>
                                </div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Assign an employee
                                    to a shift for the WHOLE month</p>
                            </div>
                            <button @click="closeModal" class="p-3 hover:bg-rose-50 rounded-2xl transition-colors">
                                <X class="h-6 w-6" />
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-8">
                            <button v-for="dept in departments" :key="dept" @click="autoScheduleDept = dept"
                                :class="autoScheduleDept === dept ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/40' : 'bg-white dark:bg-slate-800 text-slate-500'"
                                class="px-6 py-2.5 rounded-xl text-[10px] font-black border border-slate-100 dark:border-slate-700 uppercase tracking-widest transition-all">{{
                                    dept }}</button>
                        </div>
                    </div>

                    <div
                        class="p-10 grid grid-cols-1 lg:grid-cols-3 gap-8 max-h-[55vh] overflow-y-auto custom-scroll relative">
                        <div v-for="shiftType in ['Graveyard', 'Morning', 'Afternoon']" :key="shiftType"
                            class="flex flex-col gap-4 relative">
                            <div :class="{ 'bg-amber-50 border-amber-100 dark:bg-amber-900/10': shiftType === 'Morning', 'bg-blue-50 border-blue-100 dark:bg-blue-900/10': shiftType === 'Afternoon', 'bg-indigo-50 border-indigo-100 dark:bg-indigo-900/10': shiftType === 'Graveyard' }"
                                class="flex items-center justify-between p-5 rounded-[2rem] border mb-4">
                                <div class="flex items-center gap-3">
                                    <div :class="{ 'bg-amber-500': shiftType === 'Morning', 'bg-blue-500': shiftType === 'Afternoon', 'bg-indigo-600': shiftType === 'Graveyard' }"
                                        class="p-2 rounded-xl text-white">
                                        <component
                                            :is="shiftType === 'Morning' ? Sunrise : (shiftType === 'Afternoon' ? Sunset : Moon)"
                                            class="h-4 w-4" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-black text-xs uppercase tracking-widest">{{ shiftType
                                        }}</span>
                                        <span
                                            class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase">{{
                                                getShiftRange(shiftType) }}</span>
                                    </div>
                                </div>
                                <button
                                    @click="activeMonthlySelectorShift = activeMonthlySelectorShift === shiftType ? null : shiftType"
                                    class="p-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border hover:scale-110 transition-transform">
                                    <Plus class="h-4 w-4 text-blue-600" />
                                </button>
                            </div>

                            <div v-if="getEmployeesInMonthlyShift(shiftType).length > 0" class="space-y-3">
                                <div v-for="emp in getEmployeesInMonthlyShift(shiftType)" :key="emp.id"
                                    class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 flex items-center gap-3 shadow-sm">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-[10px] font-bold text-blue-600">
                                        {{ emp.name.charAt(0) }}</div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight">{{
                                                emp.name }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{
                                            getShiftRange(shiftType) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-else
                                class="flex flex-col items-center justify-center py-10 border-2 border-dashed border-slate-100 rounded-[2rem] opacity-40">
                                <Users class="h-8 w-8 text-slate-300 mb-2" />
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Empty
                                    Pipeline</p>
                            </div>

                            <Transition enter-active-class="duration-200 ease-out"
                                enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0">
                                <div v-if="activeMonthlySelectorShift === shiftType"
                                    class="absolute top-20 left-0 right-0 z-[70] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-2xl p-4 max-h-[300px] overflow-y-auto custom-scroll">
                                    <div class="flex items-center justify-between mb-4 px-2">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[9px] font-black uppercase tracking-widest text-blue-600">Assign
                                                Monthly {{ shiftType }}</span>
                                            <span
                                                class="text-[8px] font-bold text-slate-400 uppercase tracking-tight">{{
                                                    getShiftRange(shiftType) }}</span>
                                        </div>
                                        <button @click="activeMonthlySelectorShift = null">
                                            <X class="h-4 w-4 text-slate-400" />
                                        </button>
                                    </div>
                                    <div v-if="monthlyDeptStaff.length > 0" class="space-y-1">
                                        <button v-for="emp in monthlyDeptStaff" :key="emp.id"
                                            @click="setEmployeeMonthlySchedule(emp.id, shiftType)"
                                            class="w-full flex items-center gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-xl transition-colors group">
                                            <div
                                                class="h-7 w-7 rounded-lg bg-slate-100 dark:bg-slate-700 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center text-[9px] font-black">
                                                {{ emp.name.charAt(0) }}</div>
                                            <div class="flex flex-col items-start">
                                                <span
                                                    class="text-xs font-bold text-slate-600 dark:text-slate-300 group-hover:text-blue-600 uppercase">{{
                                                        emp.name }}</span>
                                                <span
                                                    class="text-[8px] font-bold text-slate-400 uppercase group-hover:text-blue-400">{{
                                                        getShiftRange(shiftType) }}</span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>
                    <div
                        class="p-10 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <div class="flex items-center gap-2 text-slate-400">
                            <CheckCircle2 class="h-4 w-4" /><span
                                class="text-[10px] font-bold uppercase tracking-widest">Database Sync Active</span>
                        </div>
                        <button @click="closeModal"
                            class="px-10 py-4 bg-slate-900 dark:bg-white dark:text-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-2xl transition-transform active:scale-95 flex items-center gap-2">Finish
                            Scheduling
                            <ArrowRight class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Daily Shift Modal (unchanged, already shows shift times) -->
        <Transition enter-active-class="duration-300 ease-out" enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100" leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <div v-if="isModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-xl" @click="closeModal"></div>
                <div
                    class="relative bg-white dark:bg-slate-900 w-full max-w-7xl rounded-[3.5rem] shadow-2xl overflow-hidden border border-white/10">
                    <div
                        class="p-10 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-2 bg-blue-600 rounded-xl text-white shadow-lg shadow-blue-500/30">
                                        <CalendarIcon class="h-5 w-5" />
                                    </div>
                                    <h2
                                        class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                                        Scheduled Shift: {{ selectedDate }}</h2>
                                </div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Select Department
                                    to Manage Pipeline Assignments</p>
                            </div>
                            <button @click="closeModal" class="p-3 hover:bg-rose-50 rounded-2xl transition-colors">
                                <X class="h-6 w-6" />
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-8">
                            <button v-for="dept in departments" :key="dept" @click="selectedDept = dept"
                                :class="selectedDept === dept ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/40' : 'bg-white dark:bg-slate-800 text-slate-500'"
                                class="px-6 py-2.5 rounded-xl text-[10px] font-black border border-slate-100 dark:border-slate-700 uppercase tracking-widest transition-all">{{
                                    dept }}</button>
                        </div>
                    </div>

                    <div
                        class="p-10 grid grid-cols-1 lg:grid-cols-3 gap-8 max-h-[55vh] overflow-y-auto custom-scroll relative">
                        <div v-for="shiftType in ['Graveyard', 'Morning', 'Afternoon']" :key="shiftType"
                            class="flex flex-col gap-4 relative">
                            <div :class="{ 'bg-amber-50 border-amber-100 dark:bg-amber-900/10': shiftType === 'Morning', 'bg-blue-50 border-blue-100 dark:bg-blue-900/10': shiftType === 'Afternoon', 'bg-indigo-50 border-indigo-100 dark:bg-indigo-900/10': shiftType === 'Graveyard' }"
                                class="flex items-center justify-between p-5 rounded-[2rem] border mb-4">
                                <div class="flex items-center gap-3">
                                    <div :class="{ 'bg-amber-500': shiftType === 'Morning', 'bg-blue-500': shiftType === 'Afternoon', 'bg-indigo-600': shiftType === 'Graveyard' }"
                                        class="p-2 rounded-xl text-white">
                                        <component
                                            :is="shiftType === 'Morning' ? Sunrise : (shiftType === 'Afternoon' ? Sunset : Moon)"
                                            class="h-4 w-4" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-black text-xs uppercase tracking-widest">{{ shiftType
                                        }}</span>
                                        <span
                                            class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase">{{
                                                getShiftRange(shiftType) }}</span>
                                    </div>
                                </div>
                                <button
                                    @click="activeSelectorShift = activeSelectorShift === shiftType ? null : shiftType"
                                    class="p-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border hover:scale-110 transition-transform">
                                    <Plus class="h-4 w-4 text-blue-600" />
                                </button>
                            </div>
                            <div v-if="getEmployeesInShift(shiftType).length > 0" class="space-y-3">
                                <div v-for="emp in getEmployeesInShift(shiftType)" :key="emp.id"
                                    class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 flex items-center gap-3 shadow-sm hover:border-blue-400 transition-colors">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-[10px] font-bold text-blue-600">
                                        {{ emp.name.charAt(0) }}</div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight">{{
                                                emp.name }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{
                                            getShiftRange(shiftType) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-else
                                class="flex flex-col items-center justify-center py-10 border-2 border-dashed border-slate-100 rounded-[2rem] opacity-40">
                                <Users class="h-8 w-8 text-slate-300 mb-2" />
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Empty
                                    Pipeline</p>
                            </div>
                            <Transition enter-active-class="duration-200 ease-out"
                                enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0">
                                <div v-if="activeSelectorShift === shiftType"
                                    class="absolute top-20 left-0 right-0 z-[70] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-2xl p-4 max-h-[300px] overflow-y-auto custom-scroll">
                                    <div class="flex items-center justify-between mb-4 px-2">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[9px] font-black uppercase tracking-widest text-blue-600">Assign
                                                to {{ shiftType }}</span>
                                            <span
                                                class="text-[8px] font-bold text-slate-400 uppercase tracking-tight">{{
                                                    getShiftRange(shiftType) }}</span>
                                        </div>
                                        <button @click="activeSelectorShift = null">
                                            <X class="h-4 w-4 text-slate-400" />
                                        </button>
                                    </div>
                                    <div v-if="departmentStaff.length > 0" class="space-y-1">
                                        <button v-for="emp in departmentStaff" :key="emp.id"
                                            @click="updateShift(emp.id, shiftType)"
                                            class="w-full flex items-center gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-xl transition-colors group">
                                            <div
                                                class="h-7 w-7 rounded-lg bg-slate-100 dark:bg-slate-700 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center text-[9px] font-black transition-colors">
                                                {{ emp.name.charAt(0) }}</div>
                                            <div class="flex flex-col items-start">
                                                <span
                                                    class="text-xs font-bold text-slate-600 dark:text-slate-300 group-hover:text-blue-600 uppercase">{{
                                                        emp.name }}</span>
                                                <span
                                                    class="text-[8px] font-bold text-slate-400 uppercase group-hover:text-blue-400">{{
                                                        getShiftRange(shiftType) }}</span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>
                    <div
                        class="p-10 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <div class="flex items-center gap-2 text-slate-400">
                            <CheckCircle2 class="h-4 w-4" /><span
                                class="text-[10px] font-bold uppercase tracking-widest">Database Sync Active</span>
                        </div>
                        <button @click="closeModal"
                            class="px-10 py-4 bg-slate-900 dark:bg-white dark:text-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-2xl transition-transform active:scale-95 flex items-center gap-2">Finish
                            Scheduling
                            <ArrowRight class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scroll::-webkit-scrollbar {
    width: 6px;
}

.custom-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scroll::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}

.dark .custom-scroll::-webkit-scrollbar-thumb {
    background: #1e293b;
}
</style>