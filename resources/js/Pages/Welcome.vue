<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();

const modules = [
    {
        name: 'Inventory Management',
        desc: 'Real-time tracking of raw fibers, dyes, and finished fabric rolls.',
        status: 'Operational',
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
    },
    {
        name: 'Production Control',
        desc: 'Monitor loom efficiency, knitting schedules, and batch processing.',
        status: 'Active',
        icon: 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21'
    },
    {
        name: 'Quality Assurance',
        desc: 'Automated defect detection and textile strength certification logs.',
        status: 'Secure',
        icon: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    },
];

function handleImageError() {
    const container = document.getElementById('screenshot-container');
    if (container) container.classList.add('!hidden');
}
</script>

<template>

    <Head title="Monti ERP | Industrial Textile Management" />

    <div class="bg-slate-50 text-slate-900 dark:bg-[#0b1120] dark:text-slate-100 min-h-screen font-sans relative">
        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.1] pointer-events-none"
            style="background-image: radial-gradient(#1e293b 0.5px, transparent 0.5px); background-size: 24px 24px;">
        </div>

        <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
            <header class="flex items-center justify-between py-6 border-b border-slate-200 dark:border-slate-800/60">
                <div class="flex items-center gap-3">
                    <img src="/images/applogo.png" alt="Logo" class="h-9 w-9 object-contain" />
                    <div class="flex flex-col">
                        <span
                            class="text-xl font-bold tracking-tight leading-none text-slate-900 dark:text-white uppercase">
                            MONTI<span class="text-blue-600">TEXTILE</span>
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Manufacturing
                            ERP</span>
                    </div>
                </div>

                <nav v-if="canLogin" class="flex items-center gap-6">
                    <Link v-if="$page.props.auth?.user" :href="route('dashboard')"
                        class="text-sm font-semibold bg-slate-900 dark:bg-blue-600 hover:opacity-90 text-white px-6 py-2.5 rounded-md transition shadow-sm">
                        Enterprise Dashboard
                    </Link>
                    <Link v-else-if="$page.props.auth?.supplier" :href="route('supplier.dashboard')"
                        class="text-sm font-semibold bg-emerald-600 hover:opacity-90 text-white px-6 py-2.5 rounded-md transition shadow-sm">
                        Supplier Hub
                    </Link>

                    <template v-else>
                        <Link :href="route('client.login')" class="text-sm font-medium hover:text-blue-600 transition">
                            Business Login
                        </Link>
                        <Link v-if="canRegister" :href="route('client.register')"
                            class="text-sm font-semibold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-6 py-2.5 rounded-md hover:bg-slate-50 transition shadow-sm">
                            Be our Partner
                        </Link>
                    </template>
                </nav>
            </header>

            <main class="py-16">
                <section class="grid lg:grid-cols-5 gap-16 items-center mb-24">
                    <div class="lg:col-span-2">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 border border-blue-100 text-blue-700 dark:text-blue-400 text-[11px] font-bold uppercase tracking-wider mb-6">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                            </span>
                            System v1.0 Online
                        </div>
                        <h1
                            class="text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.1] mb-6">
                            LET'S <span class="text-blue-600">WEAVE</span><br />
                            The Future.
                        </h1>
                        <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                            Precision management for Monti Textile's manufacturing lifecycle. From raw weaving to global
                            distribution, keep every thread accounted for.
                        </p>
                    </div>

                    <div id="screenshot-container" class="lg:col-span-3 relative">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl blur opacity-20">
                        </div>
                        <div
                            class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?q=80&w=2000&auto=format&fit=crop"
                                alt="Industrial Fabric Supply"
                                class="w-full h-[400px] object-cover opacity-90 dark:brightness-75"
                                @error="handleImageError" />
                        </div>
                    </div>
                </section>

                <div v-if="canRegister && !$page.props.auth?.user && !$page.props.auth?.supplier"
                    class="grid md:grid-cols-2 gap-6 mt-5">
                    <div class="bg-blue-600/5 border border-blue-500/20 rounded-xl p-8 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xl font-bold text-slate-900 dark:text-white">Join our Team</h4>
                            <p class="text-slate-500 dark:text-slate-400 mt-1">Become part of the Monti Textile family
                                and innovate
                                the industry.</p>
                        </div>
                        <Link :href="route('apply')"
                            class="mt-6 text-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold transition shadow-lg shadow-blue-600/20">
                            Apply Now
                        </Link>
                    </div>

                    <div
                        class="bg-emerald-600/5 border border-emerald-500/20 rounded-xl p-8 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xl font-bold text-slate-900 dark:text-white">Become a Supplier</h4>
                            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Supply us with high-quality raw
                                materials and
                                grow your business.</p>
                        </div>
                        <Link :href="route('supplier.register')"
                            class="mt-6 text-center bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-lg font-bold transition shadow-lg shadow-emerald-600/20 uppercase tracking-widest text-xs">
                            Register Business
                        </Link>
                    </div>
                </div>
            </main>

            <footer
                class="py-10 border-t border-slate-200 dark:border-slate-800/60 mt-12 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="text-xs text-slate-500 font-medium">
                    © 2026 Monti Textile Corp <span class="mx-2 text-slate-300 dark:text-slate-700">|</span>
                    <span class="font-mono text-blue-500">Node_Server: [STABLE]</span>
                </div>

                <div class="flex flex-wrap gap-8 justify-center">
                    <template v-if="!$page.props.auth?.user && !$page.props.auth?.supplier">
                        <Link :href="route('login')"
                            class="text-[10px] font-bold text-slate-400 hover:text-blue-500 transition uppercase tracking-widest">
                            Employee Portal
                        </Link>
                        <Link :href="route('supplier.login')"
                            class="text-[10px] font-bold text-slate-400 hover:text-emerald-500 transition uppercase tracking-widest">
                            Supplier Portal
                        </Link>
                    </template>
                </div>
            </footer>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&display=swap');

.font-mono {
    font-family: 'JetBrains Mono', monospace;
}
</style>