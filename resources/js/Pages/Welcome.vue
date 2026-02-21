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
        // Box/Package icon
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
    },
    {
        name: 'Production Control',
        desc: 'Monitor loom efficiency, knitting schedules, and batch processing.',
        status: 'Active',
        // Factory/Machine icon
        icon: 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21'
    },
    {
        name: 'Quality Assurance',
        desc: 'Automated defect detection and textile strength certification logs.',
        status: 'Secure',
        // Clipboard/Check icon
        icon: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    },
];

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
}
</script>

<template>

    <Head title="Monti ERP | Industrial Textile Management" />

    <div class="bg-slate-50 text-slate-900 dark:bg-[#0b1120] dark:text-slate-100 min-h-screen font-sans">
        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.1] pointer-events-none"
            style="background-image: radial-gradient(#1e293b 0.5px, transparent 0.5px); background-size: 24px 24px;">
        </div>

        <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
            <header class="flex items-center justify-between py-6 border-b border-slate-200 dark:border-slate-800/60">
                <div class="flex items-center gap-3">
                    <img src="/images/applogo.png" alt="Logo" class="h-9 w-9 object-contain" />
                    <div class="flex flex-col">
                        <span class="text-xl font-bold tracking-tight leading-none text-slate-900 dark:text-white">
                            MONTI<span class="text-blue-600">TEXTILE</span>
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Manufacturing
                            ERP</span>
                    </div>
                </div>

                <nav v-if="canLogin" class="flex items-center gap-6">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                        class="text-sm font-semibold bg-slate-900 dark:bg-blue-600 hover:opacity-90 text-white px-6 py-2.5 rounded-md transition shadow-sm">
                        Enterprise Dashboard
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
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 text-blue-700 dark:text-blue-400 text-[11px] font-bold uppercase tracking-wider mb-6">
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
                            <div
                                class="h-8 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 flex items-center px-4 gap-1.5">
                                <div class="size-2.5 rounded-full bg-red-400"></div>
                                <div class="size-2.5 rounded-full bg-amber-400"></div>
                                <div class="size-2.5 rounded-full bg-emerald-400"></div>
                                <div
                                    class="ml-4 h-4 w-40 bg-slate-200 dark:bg-slate-700 rounded shadow-inner text-[8px] flex items-center px-2 text-slate-400 font-mono">
                                    MONTI_TEXTILE_ERP</div>
                            </div>
                            <img src="https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?q=80&w=2000&auto=format&fit=crop"
                                alt="Industrial Fabric Supply"
                                class="w-full h-[400px] object-cover opacity-90 dark:brightness-75"
                                @error="handleImageError" />
                        </div>
                    </div>
                </section>

                <section class="mb-24">
                    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white font-mono">
                                CORE_MODULES</h2>
                            <p class="text-slate-500 mt-2">Enterprise Resource Planning integrated functionality.</p>
                        </div>
                        <div class="h-px flex-1 bg-slate-200 dark:bg-slate-800 mx-8 mb-3 hidden md:block"></div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6 mb-12">
                        <div v-for="module in modules" :key="module.name"
                            class="group p-8 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800/50 rounded-xl hover:border-blue-500/50 transition-all duration-300">
                            <div class="flex justify-between items-start mb-6">
                                <div
                                    class="p-3 bg-blue-50 dark:bg-blue-500/10 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            :d="module.icon" />
                                    </svg>
                                </div>
                                <span
                                    class="text-[10px] font-bold text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded uppercase tracking-wider">{{
                                        module.status }}</span>
                            </div>
                            <h3 class="text-lg font-bold mb-2">{{ module.name }}</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">{{ module.desc }}</p>
                        </div>
                    </div>


                </section>

                <section
                    class="bg-slate-900 dark:bg-blue-600/10 border border-white/5 rounded-2xl p-8 text-white relative overflow-hidden">
                    <div
                        class="grid md:grid-cols-4 gap-8 divide-y md:divide-y-0 md:divide-x divide-white/10 text-center">
                        <div class="flex flex-col items-center justify-center p-4">
                            <span class="text-3xl font-mono font-bold tracking-tighter text-blue-400">14.2k m</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold mt-2 tracking-widest">Daily
                                Yield</span>
                        </div>
                        <div class="flex flex-col items-center justify-center p-4">
                            <span class="text-3xl font-mono font-bold tracking-tighter text-blue-400">0.02%</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold mt-2 tracking-widest">Defect
                                Rate</span>
                        </div>
                        <div class="flex flex-col items-center justify-center p-4">
                            <span class="text-3xl font-mono font-bold tracking-tighter text-blue-400">52</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold mt-2 tracking-widest">Active
                                Looms</span>
                        </div>
                        <div class="flex flex-col items-center justify-center p-4">
                            <span class="text-3xl font-mono font-bold tracking-tighter text-blue-400">100%</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold mt-2 tracking-widest">Sync
                                Status</span>
                        </div>
                    </div>

                </section>
                <div v-if="canRegister && !$page.props.auth.user"
                    class="bg-blue-600/5 mt-5 border border-blue-500/20 rounded-xl p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="text-center md:text-left">
                        <h4 class="text-xl font-bold text-slate-900 dark:text-white">Do you want to be part of our
                            team?</h4>
                        <p class="text-slate-500 dark:text-slate-400 mt-1">Join the Monti Textile family and help us
                            innovate
                            the industry.</p>
                    </div>
                    <Link :href="route('apply')"
                        class="whitespace-nowrap bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold transition shadow-lg shadow-blue-600/20">
                        Apply Now
                    </Link>
                </div>
            </main>

            <footer class="py-10 border-t border-slate-200 dark:border-slate-800/60 mt-12">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="flex items-center gap-4">
                        <div class="text-xs text-slate-500 font-medium">
                            © 2026 Monti Textile Corp <span class="mx-2 text-slate-300 dark:text-slate-700">|</span>
                            <span class="font-mono text-blue-500">Node_Server: [STABLE]</span>
                        </div>
                    </div>
                    <div class="flex gap-8">
                        <Link v-if="canLogin && !$page.props.auth.user" :href="route('login')"
                            class="text-[10px] font-bold text-slate-400 hover:text-blue-500 transition uppercase tracking-widest">
                            Employee Login
                        </Link>
                        <a href="#"
                            class="text-[10px] font-bold text-slate-400 hover:text-white transition uppercase tracking-widest">Security</a>
                        <a href="#"
                            class="text-[10px] font-bold text-slate-400 hover:text-white transition uppercase tracking-widest">Support</a>
                    </div>

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