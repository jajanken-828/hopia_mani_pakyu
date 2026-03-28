<script setup>
import Sidebar from './Sidebar.vue'
import MobileSidebar from './MobileSidebar.vue'
import { usePage, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { computed } from 'vue'

const page = usePage()

// User data from Inertia shared props
const user = computed(() => page.props.auth.user)

// Current URL path
const currentPath = computed(() => page.url)

// Determine the current module from the URL (e.g., 'hrm', 'scm')
const currentModule = computed(() => {
    const match = currentPath.value.match(/\/dashboard\/([^\/]+)/)
    return match ? match[1] : null
})

// Helper to check if a link is active
const isActive = (href) => {
    return currentPath.value === href || currentPath.value.startsWith(href + '/')
}

// Show top navigation only for CEO and when there are submenu items
const showTopNav = computed(() => {
    return user.value?.role === 'CEO' && submenuItems.value.length > 0
})

// Sub‑menu items for the current module
const submenuItems = computed(() => {
    const module = currentModule.value
    if (!module) return []

    const role = user.value?.role
    const position = user.value?.position

    // Define menus for each module (manager and staff)
    const menus = {
        hrm: {
            manager: [

                { label: 'Employee Management', href: route('hrm.manager.employee') },
                { label: 'Onboarding', href: route('hrm.manager.onboarding') },
                { label: 'Payroll', href: route('hrm.manager.payroll') },
                { label: 'Analytics', href: route('hrm.manager.analytics') },
                { label: 'Attendance', href: route('hrm.employee.attendance') },
                { label: 'Leave', href: route('hrm.employee.leave') },
            ],
            staff: [
                { label: 'Dashboard', href: route('hrm.employee.dashboard') },
                { label: 'Recruitment', href: route('hrm.applicants.index') },
                { label: 'Interview', href: route('hrm.employee.interview') },
                { label: 'Training', href: route('hrm.employee.training') },
                { label: 'Attendance', href: route('hrm.employee.attendance') },
                { label: 'Leave', href: route('hrm.employee.leave') },
                { label: 'Payroll', href: route('hrm.employee.hrmstaffpayroll') },
            ]
        },
        scm: {
            manager: [

                { label: 'Operations', href: route('scm.manager.operations') },
                { label: 'Sales Orders', href: route('scm.manager.sales-orders') },
                { label: 'Vendor Management', href: route('scm.manager.vendor') },
                { label: 'Payments', href: route('scm.manager.payments') },
                { label: 'Close', href: route('scm.manager.close') },
                { label: 'Staff Assignment', href: route('scm.manager.assignment') },
            ],
            staff: [
                { label: 'Dashboard', href: route('scm.employee.dashboard') },
                { label: 'Inbound', href: route('scm.employee.inbound') },
                { label: 'Receiving', href: route('scm.employee.recieving') },
                { label: 'Inventory', href: route('scm.employee.inventory') },
                { label: 'Verifications', href: route('scm.employee.verification') },
            ]
        },
        fin: {
            // manager: [{ label: 'Dashboard', href: route('fin.manager.dashboard') }],
            staff: [{ label: 'Dashboard', href: route('fin.employee.dashboard') }]
        },
        man: {
            manager: [
                // { label: 'Dashboard', href: route('man.manager.dashboard') },
                { label: 'Production Orders', href: route('man.manager.production') },
                { label: 'Rejected Items', href: route('man.manager.rejected') },
            ],
            staff: (() => {
                const manufacturingRole = user.value?.manufacturing_role
                if (!manufacturingRole) return []

                const roleRoutes = {
                    knitting_yarn: {
                        dashboard: 'man.staff.knitting-yarn.dashboard',
                        work: 'man.staff.knitting-yarn.page',
                        reports: 'man.staff.knitting-yarn.reports',
                    },
                    dyeing_color: {
                        dashboard: 'man.staff.dyeing-color.dashboard',
                        work: 'man.staff.dyeing-color.page',
                        reports: 'man.staff.dyeing-color.reports',
                    },
                    dyeing_fabric_softener: {
                        dashboard: 'man.staff.dyeing-fabric-softener.dashboard',
                        work: 'man.staff.dyeing-fabric-softener.page',
                        reports: 'man.staff.dyeing-fabric-softener.reports',
                    },
                    dyeing_squeezer: {
                        dashboard: 'man.staff.dyeing-squeezer.dashboard',
                        work: 'man.staff.dyeing-squeezer.page',
                        reports: 'man.staff.dyeing-squeezer.reports',
                    },
                    dyeing_ironing: {
                        dashboard: 'man.staff.dyeing-ironing.dashboard',
                        work: 'man.staff.dyeing-ironing.page',
                        reports: 'man.staff.dyeing-ironing.reports',
                    },
                    dyeing_forming: {
                        dashboard: 'man.staff.dyeing-forming.dashboard',
                        work: 'man.staff.dyeing-forming.page',
                        reports: 'man.staff.dyeing-forming.reports',
                    },
                    dyeing_packaging: {
                        dashboard: 'man.staff.dyeing-packaging.dashboard',
                        work: 'man.staff.dyeing-packaging.page',
                        reports: null,
                    },
                    maintenance_checker: {
                        dashboard: 'man.staff.maintenance-checker.dashboard',
                        work: 'man.staff.maintenance-checker.page',
                        reports: 'man.staff.maintenance-checker.reports',
                    },
                    checker_quality: {
                        dashboard: 'man.staff.checker-quality.dashboard',
                        work: 'man.staff.checker-quality.production',
                        reports: null,
                    },
                }

                const r = roleRoutes[manufacturingRole]
                if (r) {
                    return [
                        { label: 'Dashboard', href: route(r.dashboard) },
                        { label: 'My Work', href: route(r.work) },
                        r.reports ? { label: 'Reports', href: route(r.reports) } : null,
                    ].filter(Boolean)
                }
                return [{ label: 'Dashboard', href: route('man.employee.dashboard') }]
            })()
        },
        inv: {
            manager: [
                // { label: 'Dashboard', href: route('inv.manager.dashboard') },
                { label: 'Production Planning', href: route('inv.manager.production-planning') },
                { label: 'Inventory', href: route('inv.manager.inventory') },
                { label: 'Master Materials', href: route('inv.manager.material') },
                { label: 'Master Products', href: route('inv.manager.product') },
            ],
            staff: [
                { label: 'Dashboard', href: route('inv.employee.dashboard') },
            ]
        },
        ord: {
            // manager: [{ label: 'Dashboard', href: route('ord.manager.dashboard') }],
            staff: [{ label: 'Dashboard', href: route('ord.employee.dashboard') }]
        },
        war: {
            // manager: [{ label: 'Dashboard', href: route('war.manager.dashboard') }],
            staff: [{ label: 'Dashboard', href: route('war.employee.dashboard') }]
        },
        crm: {
            manager: [
                { label: 'Lead', href: route('crm.lead') },
                { label: 'Customer Profile', href: route('crm.customerprofile') },
                { label: 'Approval Queue', href: route('crm.approval.queue') },
            ],
            staff: [
                { label: 'Dashboard', href: route('crm.employee.dashboard') },
                { label: 'Lead & Deals', href: route('crm.lead') },
                { label: 'Customer Profiles', href: route('crm.customerprofile') },
                { label: 'My Day', href: route('crm.staff.day') },
            ]
        },
        eco: {
            manager: [
                // { label: 'Dashboard', href: route('eco.manager.dashboard') },
                { label: 'Store', href: route('eco.manager.store') },
                { label: 'Orders', href: route('eco.manager.orders') },
                { label: 'Quotations', href: route('eco.manager.quotations') },
                { label: 'Credit MGMT', href: route('eco.manager.credit') },
                { label: 'Book MGMT', href: route('eco.manager.book') },
            ],
            staff: [
                { label: 'Dashboard', href: route('eco.employee.dashboard') },
                { label: 'Online Store', href: route('eco.employee.products') },
                { label: 'Order Management', href: route('eco.employee.ordermng') },
            ]
        },
        pro: {
            manager: [
                // { label: 'Dashboard', href: route('pro.manager.dashboard') },
                // { label: 'Material Requests', href: route('pro.manager.material-requests') },
                { label: 'Supplier Quotations', href: route('pro.manager.supplier-quotations') },
                { label: 'Receipt', href: route('pro.manager.receipt') },
            ],
            staff: [
                { label: 'Dashboard', href: route('pro.employee.dashboard') },
            ]
        },
        proj: {
            // manager: [{ label: 'Dashboard', href: route('proj.manager.dashboard') }],
            staff: [{ label: 'Dashboard', href: route('proj.employee.dashboard') }]
        },
        it: {
            // manager: [{ label: 'Dashboard', href: route('it.manager.dashboard') }],
            staff: [{ label: 'Dashboard', href: route('it.employee.dashboard') }]
        },
        ceo: {
            // manager: [{ label: 'Dashboard', href: route('ceo.dashboard') }]
        }
    }

    const moduleMenus = menus[module]
    if (!moduleMenus) return []

    let menu = moduleMenus[position]
    if (!menu && position === 'staff' && module === 'man') {
        // For manufacturing staff with no role, fallback to empty array
        menu = []
    }
    return menu || []
})
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-950 transition-colors duration-300">
        <Sidebar />

        <div
            class="md:hidden bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
            <div class="px-4">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <MobileSidebar />
                        <div class="ml-4">
                            <h1 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">
                                Monti <span class="text-blue-600">Textile</span>
                            </h1>
                        </div>
                    </div>
                    <div
                        class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                        {{ user?.name?.charAt(0) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="md:pl-64 flex flex-col flex-1">
            <!-- Enhanced top navigation bar (only for CEO) -->
            <div v-if="showTopNav"
                class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md border-b border-gray-200/50 dark:border-zinc-800/50 sticky top-0 z-30 shadow-sm">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center space-x-1 overflow-x-auto py-2 scrollbar-hide">
                        <Link v-for="item in submenuItems" :key="item.label" :href="item.href" :class="[
                            isActive(item.href)
                                ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400 -mb-[1px]'
                                : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800/50',
                            'px-4 py-2 rounded-t-lg text-sm font-medium transition-all duration-200 whitespace-nowrap'
                        ]">
                            {{ item.label }}
                        </Link>
                    </div>
                </div>
            </div>

            <main class="py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="animate-in fade-in duration-500">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.animate-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;
    /* IE and Edge */
    scrollbar-width: none;
    /* Firefox */
}
</style>