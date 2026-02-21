<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import {
    User, Home, Briefcase, FileCheck, Upload,
    Trash2, ShieldCheck, ChevronLeft, Save, CheckCircle2
} from 'lucide-vue-next';

const isLoaded = ref(false);
const showSuccessModal = ref(false);

const form = useForm({
    first_name: '',
    middle_name: '',
    last_name: '',
    email: '',
    phone_number: '',
    street_address: '',
    street_address_line2: '',
    city: '',
    state_province: '',
    postal_zip_code: '',
    position_applied: '',
    expected_salary: '',
    notice_period: 'immediate',
    textile_experience: '',
    sss_file: null,
    philhealth_file: null,
    pagibig_file: null,
    status: 'pending'
});

onMounted(() => {
    isLoaded.value = true;
});

const handleFileUpload = (e, type) => {
    const file = e.target.files[0];
    if (file) {
        form[type + '_file'] = file;
    }
};

const removeFile = (type) => {
    form[type + '_file'] = null;
};

const submitForm = () => {
    form.post(route('applicants.public.store'), {
        forceFormData: true,
        onSuccess: () => {
            showSuccessModal.value = true;
            // Delay redirect to allow the user to see the success message
            setTimeout(() => {
                router.visit('/');
            }, 3000);
        },
        onError: (errors) => {
            console.log(errors);
        }
    });
};
</script>

<template>

    <Head title="Join Our Team | Monti Textile Career" />

    <div
        class="relative min-h-screen flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-950 font-sans selection:bg-blue-100">

        <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <div v-if="showSuccessModal"
                class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-950/60 backdrop-blur-sm">
                <div
                    class="bg-white dark:bg-slate-900 rounded-[3rem] p-10 max-w-sm w-full shadow-2xl text-center border border-slate-100 dark:border-slate-800">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-500/10 mb-6">
                        <CheckCircle2 class="w-10 h-10 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">Application Received</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">Your professional profile has
                        been encrypted and sent to our HR node. Redirecting you shortly...</p>
                    <Link href="/"
                        class="inline-flex items-center justify-center w-full py-4 bg-slate-900 dark:bg-blue-600 text-white font-bold rounded-2xl hover:opacity-90 transition-all active:scale-95">
                        Return Home Now
                    </Link>
                </div>
            </div>
        </Transition>

        <div class="relative hidden md:flex md:w-[35%] bg-blue-700 overflow-hidden items-center justify-center p-12">
            <div class="absolute inset-0 opacity-10 pointer-events-none"
                style="background-image: radial-gradient(#ffffff 0.5px, transparent 0.5px); background-size: 30px 30px;">
            </div>

            <div class="relative z-10 transition-all duration-1000 transform"
                :class="isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">

                <Link href="/">
                    <div class="size-16 mb-6 p-2 bg-white rounded-xl shadow-2xl">
                        <img src="/images/applogo.png" alt="Monti Logo" class="h-full w-full object-contain" />
                    </div>
                </Link>

                <h1 class="text-5xl font-black text-white leading-tight mb-6">
                    Start Your <br />
                    <span class="text-blue-200">Journey</span> With Us.
                </h1>

                <p class="text-blue-100 text-lg mb-10 max-w-md leading-relaxed opacity-90">
                    Apply now to become part of the Philippines' leading textile manufacturing innovation team.
                </p>

                <div class="space-y-4">
                    <div class="flex items-center space-x-4 text-white/80 font-mono text-sm uppercase tracking-widest">
                        <span class="h-px w-8 bg-blue-400"></span>
                        <span>Career Node v1.0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <div
                class="p-6 lg:px-12 border-b border-slate-100 dark:border-slate-800 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md z-20 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Application Portal
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">
                        Personnel Onboarding System</p>
                </div>
                <Link href="/"
                    class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors text-slate-400">
                    <ChevronLeft class="size-6" />
                </Link>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/30 dark:bg-transparent">
                <div class="max-w-4xl mx-auto p-6 lg:p-12 transition-all duration-700 delay-300"
                    :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">

                    <form @submit.prevent="submitForm" class="space-y-10">
                        <div
                            class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                            <div class="flex items-center mb-8">
                                <div
                                    class="h-12 w-12 bg-blue-600 rounded-2xl flex items-center justify-center mr-5 shadow-lg shadow-blue-500/20">
                                    <User class="text-white h-6 w-6" />
                                </div>
                                <h4 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">
                                    Personal Identity</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">First
                                        Name *</label>
                                    <input v-model="form.first_name" type="text" required
                                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-blue-600 transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Middle
                                        Name</label>
                                    <input v-model="form.middle_name" type="text"
                                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-blue-600 transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Last
                                        Name *</label>
                                    <input v-model="form.last_name" type="text" required
                                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-blue-600 transition-all">
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                            <div class="flex items-center mb-8">
                                <div
                                    class="h-12 w-12 bg-indigo-500 rounded-2xl flex items-center justify-center mr-5 shadow-lg shadow-indigo-500/20">
                                    <Home class="text-white h-6 w-6" />
                                </div>
                                <h4 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">
                                    Residential Details</h4>
                            </div>
                            <div class="space-y-6">
                                <div class="space-y-1.5">
                                    <label
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Street
                                        Address *</label>
                                    <input v-model="form.street_address" type="text" required
                                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">City
                                            *</label>
                                        <input v-model="form.city" type="text" required
                                            class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">State
                                            / Province *</label>
                                        <input v-model="form.state_province" type="text" required
                                            class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Postal
                                            Code *</label>
                                        <input v-model="form.postal_zip_code" type="text" required
                                            class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                            <div class="flex items-center mb-8">
                                <div
                                    class="h-12 w-12 bg-blue-600 rounded-2xl flex items-center justify-center mr-5 shadow-lg shadow-blue-500/20">
                                    <Briefcase class="text-white h-6 w-6" />
                                </div>
                                <h4 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">
                                    Professional Profile</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Email
                                        Address *</label>
                                    <input v-model="form.email" type="email" required
                                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Phone
                                        Number *</label>
                                    <input v-model="form.phone_number" type="tel" required
                                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm">
                                </div>
                                <div class="space-y-1.5">
                                    <label
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Position
                                        Applied *</label>
                                    <input v-model="form.position_applied" type="text" required
                                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm">
                                </div>
                                <div class="space-y-1.5">
                                    <label
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Expected
                                        Monthly Salary *</label>
                                    <input v-model="form.expected_salary" type="number" step="0.01" required
                                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm">
                                </div>
                                <div class="space-y-1.5">
                                    <label
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Notice
                                        Period *</label>
                                    <select v-model="form.notice_period"
                                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm">
                                        <option value="immediate">Immediate</option>
                                        <option value="15_days">15 Days</option>
                                        <option value="30_days">30 Days</option>
                                        <option value="60_days">60 Days</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Textile
                                        Experience *</label>
                                    <select v-model="form.textile_experience" required
                                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm">
                                        <option value="" disabled>Select Option</option>
                                        <option value="yes">Yes, I have experience</option>
                                        <option value="no">No experience yet</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                            <div class="flex items-center mb-8">
                                <div
                                    class="h-12 w-12 bg-emerald-600 rounded-2xl flex items-center justify-center mr-5 shadow-lg shadow-emerald-500/20">
                                    <FileCheck class="text-white h-6 w-6" />
                                </div>
                                <h4 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">
                                    Compliance Documents</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div v-for="type in ['sss', 'philhealth', 'pagibig']" :key="type" class="space-y-3">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">{{
                                        type }} ID Upload</p>
                                    <div :class="form[type + '_file'] ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800'"
                                        class="relative h-40 rounded-[2rem] border-2 border-dashed flex flex-col items-center justify-center p-6 transition-all group overflow-hidden">

                                        <template v-if="!form[type + '_file']">
                                            <Upload
                                                class="h-6 w-6 text-slate-300 group-hover:text-blue-500 transition-colors mb-3" />
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                                Select Image</p>
                                            <input type="file" @change="(e) => handleFileUpload(e, type)"
                                                class="absolute inset-0 opacity-0 cursor-pointer"
                                                accept=".jpg,.jpeg,.png">
                                        </template>

                                        <template v-else>
                                            <div class="flex flex-col items-center text-center">
                                                <div
                                                    class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-full mb-2">
                                                    <FileCheck class="h-6 w-6 text-emerald-600" />
                                                </div>
                                                <p
                                                    class="text-[10px] font-black text-emerald-800 dark:text-emerald-400 truncate w-24">
                                                    {{ form[type + '_file'].name }}</p>
                                                <button @click="removeFile(type)" type="button"
                                                    class="mt-3 p-2 bg-rose-100 text-rose-600 rounded-xl hover:bg-rose-200 transition-colors">
                                                    <Trash2 class="h-4 w-4" />
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between items-center py-10 gap-6">
                            <div
                                class="flex items-center text-[10px] font-black uppercase tracking-widest text-slate-400">
                                <ShieldCheck class="h-5 w-5 text-emerald-500 mr-2" /> Data Encryption Active
                            </div>
                            <div class="flex gap-4 w-full sm:w-auto">
                                <button type="submit" :disabled="form.processing"
                                    class="w-full sm:w-auto flex items-center justify-center gap-3 px-12 py-5 bg-blue-600 hover:bg-blue-700 text-white rounded-[1.5rem] font-bold text-sm shadow-2xl shadow-blue-500/30 active:scale-95 transition-all disabled:opacity-50">
                                    <Save class="h-5 w-5" />
                                    <span>{{ form.processing ? 'PROCESSING...' : 'SUBMIT APPLICATION' }}</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-200 dark:bg-slate-800 rounded-full;
}

input,
select,
textarea {
    @apply transition-all duration-300;
}
</style>