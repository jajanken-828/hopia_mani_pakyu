<script setup>
import { usePage, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js';
import { computed, ref } from 'vue'
import {
    LayoutDashboard,
    BarChart3,
    Package,
    LogOut,
    ChevronRight,
    CreditCard,
    UserPlus,
    Spool,
    ClipboardList,
    ChartNoAxesCombined,
    ShoppingBasket,
    HandCoins,
    FileUser,
    DoorOpen,
    BicepsFlexed,
    Truck,
    Wallet,
    Factory,
    Book,
    Boxes,
    ShoppingCart,
    Warehouse,
    Globe,
    Clock,
    CalendarDays,
    History,
    Users,
    Settings,
    Receipt,
    HelpCircle,
    ShieldCheck,
    Building2,
    RefreshCw,       // Added for Inventory Sync
    ClipboardCheck,  // Added for Purchase Orders
} from 'lucide-vue-next'

const page = usePage()
const user = computed(() => page.props.auth.user)
const client = computed(() => page.props.auth.client)
const supplier = computed(() => page.props.auth.supplier) // shared via HandleInertiaRequests
const currentUrl = computed(() => page.url)

// State for the Workforce dropdown
const isWorkforceOpen = ref(false)
const toggleWorkforce = () => {
    isWorkforceOpen.value = !isWorkforceOpen.value
}

const isEmployeePortal = computed(() => currentUrl.value.startsWith('/dashboard/employee-ui'))
const isClient = computed(() => !!client.value)
// Use the shared prop — NOT the URL — so detection survives any redirect
const isSupplier = computed(() => !!supplier.value)

const navItems = computed(() => {
    // --- Supplier Navigation ---
    if (isSupplier.value) {
        return [
            { label: 'Dashboard', href: route('supplier.dashboard'), icon: LayoutDashboard },
            { label: 'Purchase Orders', href: '#', icon: ClipboardCheck },
            { label: 'Inventory Sync', href: '#', icon: RefreshCw },
        ]
    }

    // --- Client Navigation (B2B) ---
    if (isClient.value) {
        return [
            { label: 'Dashboard', href: route('client.dashboard'), icon: LayoutDashboard },
            { label: 'Orders', href: route('client.orders'), icon: ShoppingCart },
            { label: 'Invoices', href: route('client.invoices'), icon: Receipt },
            { label: 'Support', href: route('client.support'), icon: HelpCircle },
        ]
    }

    // --- Employee ID Portal Navigation ---
    if (isEmployeePortal.value) {
        return [
            { label: 'Employee Dashboard', href: route('employee.ui.dashboard'), icon: Clock },
            { label: 'My Attendance', href: route('employee.ui.clock'), icon: CalendarDays },
            { label: 'Leave Request', href: route('employee.ui.leave'), icon: History },
            { label: 'Payslip', href: route('employee.ui.payslip'), icon: HandCoins },
        ]
    }

    // --- Standard ERP Navigation ---
    const items = [
        { label: 'Main Dashboard', href: route('dashboard'), icon: LayoutDashboard },
    ]

    const userRole = user.value?.role?.toUpperCase()
    const userPosition = user.value?.position?.toLowerCase()

    // --- Trainee Navigation ---
    if (userPosition === 'trainee') {
        items.push(
            { label: 'Time In/Out', href: route('trainee.timekeeping'), icon: Clock },
            { label: 'Attendance', href: route('trainee.attendance'), icon: CalendarDays },
            { label: 'Payslips', href: route('trainee.payslip'), icon: HandCoins }
        );
        return items;
    }

    if (userRole === 'HRM') {
        if (userPosition === 'manager') {
            items.push(
                { label: 'Onboarding', href: route('hrm.manager.onboarding'), icon: BarChart3 },
                {
                    label: 'Workforce Management',
                    icon: Users,
                    isDropdown: true,
                    children: [
                        { label: 'Attendance', href: route('hrm.employee.attendance'), icon: FileUser },
                        { label: 'Leave MGMT', href: route('hrm.employee.leave'), icon: DoorOpen },
                    ]
                },
                { label: 'Payroll', href: route('hrm.manager.payroll'), icon: HandCoins },
                { label: 'Analytics', href: route('hrm.manager.analytics'), icon: ChartNoAxesCombined }
            )
        } else if (userPosition === 'staff') {
            items.push(
                { label: 'Recruitment', href: route('hrm.applicants.index'), icon: UserPlus },
                { label: 'Interview', href: route('hrm.employee.interview'), icon: ClipboardList },
                { label: 'Training', href: route('hrm.employee.training'), icon: BicepsFlexed },
                {
                    label: 'Workforce Management',
                    icon: Users,
                    isDropdown: true,
                    children: [
                        { label: 'Attendance', href: route('hrm.employee.attendance'), icon: FileUser },
                        { label: 'Leave MGMT', href: route('hrm.employee.leave'), icon: DoorOpen },
                    ]
                },
                { label: 'Payroll', href: route('hrm.employee.hrmstaffpayroll'), icon: HandCoins }
            )
        }
    }

    if (userRole === 'SCM') {
        if (userPosition === 'manager') {
            items.push(
                { label: 'Procurement', href: route('scm.manager.procurement'), icon: Truck },
                { label: 'Audit', href: route('scm.manager.audit'), icon: ChartNoAxesCombined },
                { label: 'Close', href: route('scm.manager.close'), icon: DoorOpen }
            )
        } else if (userPosition === 'staff') {
            items.push(
                { label: 'Inbound', href: route('scm.employee.inbound'), icon: Truck },
                { label: 'Receiving', href: route('scm.employee.recieving'), icon: Truck },
                { label: 'Inventory', href: route('scm.employee.inventory'), icon: Package },
                { label: 'Verifications', href: route('scm.employee.verification'), icon: HandCoins }
            )
        }
    }

    if (userRole === 'FIN') {
        items.push({ label: 'Finance', href: userPosition === 'manager' ? route('fin.manager.dashboard') : route('fin.employee.dashboard'), icon: Wallet })
    }

    if (userRole === 'MAN') {
        items.push({ label: 'Manufacturing', href: userPosition === 'manager' ? route('man.manager.dashboard') : route('man.employee.dashboard'), icon: Factory })
        items.push({ label: 'Production Orders', href: userPosition === 'manager' ? route('man.manager.dashboard') : route('man.employee.dashboard'), icon: ClipboardList })
        items.push({ label: 'Machine Status', href: userPosition === 'manager' ? route('man.manager.dashboard') : route('man.employee.dashboard'), icon: Settings })
        items.push({ label: 'Maintenance', href: userPosition === 'manager' ? route('man.manager.dashboard') : route('man.employee.dashboard'), icon: Receipt })
    }

    if (userRole === 'INV') {
        items.push({ label: 'Inventory', href: userPosition === 'manager' ? route('inv.manager.inventory') : route('inv.employee.dashboard'), icon: Boxes })
        if (userPosition === 'manager') {
            items.push({ label: 'Master Materials', href: route('inv.manager.material'), icon: Spool })
            items.push({ label: 'Master Products', href: route('inv.manager.product'), icon: Building2 })

        }

    }

    if (userRole === 'ORD') {
        items.push({ label: 'Orders', href: userPosition === 'manager' ? route('ord.manager.dashboard') : route('ord.employee.dashboard'), icon: ShoppingCart })
    }

    if (userRole === 'WAR') {
        items.push({ label: 'Warehouse', href: userPosition === 'manager' ? route('war.manager.dashboard') : route('war.employee.dashboard'), icon: Warehouse })
    }

    if (userRole === 'CRM') {
        if (userPosition === 'manager') {
            items.push(
                { label: 'Quality Oversight', href: route('crm.oversight'), icon: Clock },
                { label: 'Strategic Analytics', href: route('crm.strategy'), icon: ChartNoAxesCombined }
            )
        } else {
            items.push(
                { label: 'Lead & Deals', href: route('crm.lead'), icon: FileUser },
                { label: 'Customer Profiles', href: route('crm.customerprofile'), icon: Users }
            )
        }
    }
    if (userRole === 'ECO') {
        if (userPosition === 'manager') {
            items.push(
                { label: 'Credit MGMT', href: route('eco.manager.credit'), icon: CreditCard },
                { label: 'Book MGMT', href: route('eco.manager.book'), icon: Book },
            )
        } else {
            items.push({ label: 'Online Store', href: route('eco.employee.products'), icon: Globe })
            items.push({ label: 'Order Management', href: route('eco.employee.ordermng'), icon: ShoppingBasket })
        }
    }

    return items
})

const isActive = (href) => {
    if (href === '#') return false
    return currentUrl.value === href || currentUrl.value.startsWith(href + '/')
}

// --- Display helpers that work for all user types ---
const displayName = computed(() => {
    if (isSupplier.value) return supplier.value?.representative_name
    if (isClient.value) return client.value?.company_name
    return user.value?.name
})

const displayInitial = computed(() => displayName.value?.charAt(0) ?? '?')

const displayDepartment = computed(() => {
    if (isSupplier.value) return 'Supplier'
    if (isClient.value) return client.value?.business_type
    return user.value?.role
})

const displayPosition = computed(() => {
    if (isSupplier.value) return supplier.value?.business_name ?? 'Vendor'
    if (isEmployeePortal.value) return user.value?.employee_id ?? 'Staff'
    if (isClient.value) return 'Partner'
    return user.value?.position
})

const sidebarLabel = computed(() => {
    if (isSupplier.value) return 'Vendor'
    if (isClient.value) return 'Partner'
    if (isEmployeePortal.value) return 'Employee'
    return 'System'
})

// Each guard needs its own logout route.
// supplier.logout → clears auth:supplier session → redirects to /
// logout          → clears auth (employee) session → redirects to /login
// Using the wrong one leaves the session alive.
const logoutRoute = computed(() => {
    return isSupplier.value ? route('supplier.logout') : route('logout')
})
</script>

<template>
    <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 z-50 transition-all duration-300">
        <div
            class="flex flex-col flex-grow bg-slate-50 dark:bg-gray-950 border-r border-gray-200/60 dark:border-gray-800/60 shadow-xl">

            <!-- Logo -->
            <div class="flex items-center h-20 flex-shrink-0 px-4">
                <div
                    class="flex items-center gap-2.5 p-2 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 w-full">
                    <div :class="isSupplier ? 'bg-emerald-600 shadow-emerald-500/20' : 'bg-blue-600 shadow-blue-500/20'"
                        class="h-9 w-9 flex-shrink-0 rounded-lg flex items-center justify-center shadow-lg">
                        <img src="/images/applogo.png" alt="Logo" class="h-6 w-6 object-contain brightness-0 invert" />
                    </div>
                    <div class="flex flex-col overflow-hidden">
                        <h2
                            class="text-[13px] font-black text-gray-900 dark:text-white leading-tight tracking-tight uppercase">
                            Monti <span :class="isSupplier ? 'text-emerald-600' : 'text-blue-600'">Textile</span>
                        </h2>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate">
                            {{ sidebarLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex-1 flex flex-col overflow-y-auto px-3 py-4 custom-scrollbar">
                <div class="mb-3 px-2">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.15em]">Main Menu</p>
                </div>
                <nav class="space-y-1">
                    <template v-for="item in navItems" :key="item.label">
                        <div v-if="item.isDropdown" class="space-y-1">
                            <button @click="toggleWorkforce" :class="[
                                isWorkforceOpen ? 'text-blue-600 bg-white/50 dark:bg-gray-900/50' : 'text-gray-500 dark:text-gray-400',
                                'group w-full flex items-center justify-between px-3 py-2.5 text-[13px] font-bold rounded-xl hover:bg-white/50 dark:hover:bg-gray-900/50 transition-all duration-300'
                            ]">
                                <div class="flex items-center">
                                    <div :class="[isWorkforceOpen ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600' : 'text-gray-400']"
                                        class="p-1.5 rounded-lg mr-2.5 transition-colors duration-300">
                                        <component :is="item.icon" class="h-4.5 w-4.5" />
                                    </div>
                                    <span class="truncate tracking-tight">{{ item.label }}</span>
                                </div>
                                <ChevronRight
                                    :class="['h-3.5 w-3.5 transition-transform duration-300', isWorkforceOpen ? 'rotate-90' : 'text-gray-400']" />
                            </button>

                            <div v-show="isWorkforceOpen" class="pl-10 space-y-1 mt-1 transition-all">
                                <Link v-for="subItem in item.children" :key="subItem.label" :href="subItem.href" :class="[
                                    isActive(subItem.href) ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white'
                                ]" class="flex items-center py-2 text-[12px] font-bold transition-colors">
                                    <component :is="subItem.icon" class="h-3.5 w-3.5 mr-2.5" />
                                    {{ subItem.label }}
                                </Link>
                            </div>
                        </div>

                        <Link v-else :href="item.href" :class="[
                            isActive(item.href)
                                ? isSupplier
                                    ? 'bg-white dark:bg-gray-900 text-emerald-600 shadow-sm ring-1 ring-gray-200/50 dark:ring-gray-800'
                                    : 'bg-white dark:bg-gray-900 text-blue-600 shadow-sm ring-1 ring-gray-200/50 dark:ring-gray-800'
                                : 'text-gray-500 dark:text-gray-400 hover:bg-white/50 dark:hover:bg-gray-900/50 hover:text-gray-900 dark:hover:text-white'
                        ]"
                            class="group relative flex items-center justify-between px-3 py-2.5 text-[13px] font-bold rounded-xl transition-all duration-300">
                            <div v-if="isActive(item.href)" :class="isSupplier ? 'bg-emerald-600' : 'bg-blue-600'"
                                class="absolute left-0 top-1/4 bottom-1/4 w-0.5 rounded-r-full"></div>
                            <div class="flex items-center relative z-10">
                                <div :class="[
                                    isActive(item.href)
                                        ? isSupplier
                                            ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600'
                                            : 'bg-blue-50 dark:bg-blue-900/30 text-blue-600'
                                        : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'
                                ]" class="p-1.5 rounded-lg transition-colors duration-300 mr-2.5">
                                    <component :is="item.icon" class="h-4.5 w-4.5 flex-shrink-0" />
                                </div>
                                <span class="truncate tracking-tight">{{ item.label }}</span>
                            </div>
                            <ChevronRight v-if="isActive(item.href)"
                                :class="isSupplier ? 'text-emerald-600/40' : 'text-blue-600/40'" class="h-3.5 w-3.5" />
                        </Link>
                    </template>
                </nav>
            </div>

            <!-- User Profile & Logout -->
            <div class="p-3 mt-auto">
                <div
                    class="bg-white dark:bg-gray-900 rounded-2xl p-2.5 border border-gray-100 dark:border-gray-800 shadow-lg group">
                    <div class="flex items-center gap-2.5 relative z-10">
                        <div class="relative">
                            <div :class="isSupplier
                                ? 'from-emerald-600 to-teal-700 shadow-emerald-500/30'
                                : 'from-blue-600 to-indigo-700 shadow-blue-500/30'"
                                class="h-9 w-9 rounded-xl bg-gradient-to-br flex items-center justify-center text-white text-xs font-black shadow-lg uppercase">
                                {{ displayInitial }}
                            </div>
                            <div
                                class="absolute -bottom-0.5 -right-0.5 h-3 w-3 bg-green-500 border-2 border-white dark:border-gray-900 rounded-full">
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-[11px] font-black text-gray-900 dark:text-white truncate uppercase tracking-tighter">
                                {{ displayName }}
                            </p>
                            <div class="flex items-center gap-1 mb-0.5">
                                <Building2 class="h-2.5 w-2.5 text-gray-400" />
                                <span :class="isSupplier ? 'text-emerald-600' : 'text-blue-600'"
                                    class="text-[8px] font-black uppercase truncate">
                                    {{ displayDepartment }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1">
                                <ShieldCheck :class="isSupplier ? 'text-emerald-500' : 'text-blue-500'"
                                    class="h-2.5 w-2.5" />
                                <span class="text-[8px] font-black text-gray-400 uppercase truncate">
                                    {{ displayPosition }}
                                </span>
                            </div>
                        </div>
                        <Link :href="logoutRoute" method="post" as="button"
                            class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300">
                            <LogOut class="h-3.5 w-3.5" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 3px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.2);
    border-radius: 10px;
}
</style>