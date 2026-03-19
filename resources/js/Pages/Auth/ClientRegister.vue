<script setup>
import { onMounted, ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const form = useForm({
    company_name: '',
    business_type: '',
    tin_number: '',
    contact_person: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    company_address: '',
});

const isLoaded = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);

onMounted(() => {
    isLoaded.value = true;
});

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const toggleConfirmPassword = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

const submit = () => {
    form.post(route('client.register.store'), {
        onSuccess: () => {
            toast.success('Partnership application submitted successfully!');
        },
        onError: (errors) => {
            const errorMsg = Object.values(errors)[0] || 'Registration failed. Please check your inputs.';
            toast.error(errorMsg);
        },
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Join Monti ERP - B2B Registration" />

    <div class="min-h-screen flex flex-col bg-[#f8fafc] dark:bg-slate-950 font-sans selection:bg-blue-100">

        <nav class="absolute top-0 left-0 right-0 p-6 flex justify-between items-center z-20"
            :class="{ 'opacity-0 -translate-y-4': !isLoaded, 'opacity-100 translate-y-0': isLoaded }"
            style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);">
            <Link href="/" class="flex items-center gap-3 group">
                <div class="size-10 p-2 bg-[#1E40AF] rounded-xl shadow-lg group-hover:scale-105 transition-transform">
                    <img src="/images/applogo.png" alt="Monti Logo"
                        class="h-full w-full object-contain filter brightness-0 invert" />
                </div>
                <span class="font-black text-xl tracking-tight text-slate-900 dark:text-white">Monti<span
                        class="text-[#1E40AF] dark:text-blue-400">Textile</span></span>
            </Link>
        </nav>

        <div class="flex-1 flex flex-col items-center justify-center p-6 pt-28 pb-12 relative">
            <div
                class="absolute top-20 left-10 w-72 h-72 rounded-full bg-blue-100 dark:bg-slate-900 blur-3xl opacity-40 pointer-events-none">
            </div>
            <div
                class="absolute bottom-20 right-10 w-96 h-96 rounded-full bg-indigo-50 dark:bg-slate-900 blur-3xl opacity-40 pointer-events-none">
            </div>

            <div class="w-full max-w-3xl relative z-10"
                :class="{ 'opacity-0 translate-y-8': !isLoaded, 'opacity-100 translate-y-0': isLoaded }"
                style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.1s;">

                <div class="text-center mb-10">
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-3">
                        Partner Application</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm md:text-base max-w-xl mx-auto">Fill out the
                        details below to request a B2B account. Once approved, you will gain access to wholesale
                        ordering and order tracking.</p>
                </div>

                <form @submit.prevent="submit"
                    class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-xl border border-slate-100 dark:border-slate-800 p-8 md:p-10 space-y-8">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <h3
                                class="text-sm font-black uppercase tracking-widest text-[#1E40AF] dark:text-blue-400 border-b border-slate-100 dark:border-slate-800 pb-2 mb-4">
                                Company Details</h3>
                        </div>

                        <div>
                            <InputLabel for="company_name" value="Company/Business Name"
                                class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1" />
                            <TextInput id="company_name" type="text"
                                class="mt-1 block w-full py-3 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:ring-[#1E40AF]"
                                v-model="form.company_name" required autofocus placeholder="e.g. Acme Corp" />
                            <InputError class="mt-2" :message="form.errors.company_name" />
                        </div>

                        <div>
                            <InputLabel for="business_type" value="Business Type"
                                class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1" />
                            <select id="business_type" v-model="form.business_type"
                                class="mt-1 block w-full py-3 px-4 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:ring-[#1E40AF] focus:border-[#1E40AF] text-sm text-slate-700 dark:text-slate-300"
                                required>
                                <option value="" disabled>Select Type...</option>
                                <option value="Retailer">Retailer</option>
                                <option value="Wholesaler">Wholesaler</option>
                                <option value="Manufacturer">Manufacturer</option>
                                <option value="Distributor">Distributor</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.business_type" />
                        </div>

                        <div class="md:col-span-2">
                            <InputLabel for="company_address" value="Company Address"
                                class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1" />
                            <TextInput id="company_address" type="text"
                                class="mt-1 block w-full py-3 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:ring-[#1E40AF]"
                                v-model="form.company_address" required placeholder="Full physical address" />
                            <InputError class="mt-2" :message="form.errors.company_address" />
                        </div>

                        <div>
                            <InputLabel for="tin_number" value="TIN Number (Optional)"
                                class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1" />
                            <TextInput id="tin_number" type="text"
                                class="mt-1 block w-full py-3 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:ring-[#1E40AF]"
                                v-model="form.tin_number" placeholder="XXX-XXX-XXX" />
                            <InputError class="mt-2" :message="form.errors.tin_number" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <h3
                                class="text-sm font-black uppercase tracking-widest text-[#1E40AF] dark:text-blue-400 border-b border-slate-100 dark:border-slate-800 pb-2 mb-4 mt-2">
                                Account & Contact Info</h3>
                        </div>

                        <div>
                            <InputLabel for="contact_person" value="Primary Contact Person"
                                class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1" />
                            <TextInput id="contact_person" type="text"
                                class="mt-1 block w-full py-3 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:ring-[#1E40AF]"
                                v-model="form.contact_person" required placeholder="Full Name" />
                            <InputError class="mt-2" :message="form.errors.contact_person" />
                        </div>

                        <div>
                            <InputLabel for="phone" value="Phone Number"
                                class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1" />
                            <TextInput id="phone" type="text"
                                class="mt-1 block w-full py-3 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:ring-[#1E40AF]"
                                v-model="form.phone" required placeholder="+63 9XX XXX XXXX" />
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>

                        <div class="md:col-span-2">
                            <InputLabel for="email" value="Business Email (Used for Login)"
                                class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1" />
                            <TextInput id="email" type="email"
                                class="mt-1 block w-full py-3 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:ring-[#1E40AF]"
                                v-model="form.email" required placeholder="contact@company.com"
                                autocomplete="username" />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="relative">
                            <InputLabel for="password" value="Password"
                                class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1" />
                            <TextInput id="password" :type="showPassword ? 'text' : 'password'"
                                class="mt-1 pr-12 block w-full py-3 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:ring-[#1E40AF]"
                                v-model="form.password" required placeholder="••••••••" autocomplete="new-password" />
                            <button type="button" @click="togglePassword"
                                class="absolute bottom-3 right-4 text-slate-400 hover:text-slate-600 focus:outline-none">
                                <svg v-if="!showPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="relative">
                            <InputLabel for="password_confirmation" value="Confirm Password"
                                class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1" />
                            <TextInput id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'"
                                class="mt-1 pr-12 block w-full py-3 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:ring-[#1E40AF]"
                                v-model="form.password_confirmation" required placeholder="••••••••"
                                autocomplete="new-password" />
                            <button type="button" @click="toggleConfirmPassword"
                                class="absolute bottom-3 right-4 text-slate-400 hover:text-slate-600 focus:outline-none">
                                <svg v-if="!showConfirmPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-4">
                        <Link :href="route('client.login')"
                            class="group text-sm font-semibold text-slate-500 hover:text-[#1E40AF] transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15 19l-7-7 7-7" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            Back to Sign in
                        </Link>

                        <PrimaryButton
                            class="w-full sm:w-auto px-10 py-4 bg-[#1E40AF] hover:bg-blue-700 text-white rounded-2xl shadow-xl shadow-blue-500/20 active:scale-[0.98] transition-all"
                            :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                            Apply for Partnership
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Smooth transition for form focus states */
input,
select,
textarea {
    @apply transition-all duration-300 ease-in-out;
}
</style>