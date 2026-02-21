<script setup>
import { onMounted, ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Join Monti ERP - B2B Registration" />

    <div class="min-h-screen flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-950 font-sans selection:bg-blue-100">

        <div class="relative hidden md:flex md:w-[40%] bg-[#1E40AF] overflow-hidden items-center justify-center p-12">
            <div
                class="absolute top-[-10%] left-[-10%] w-72 h-72 bg-blue-400 rounded-full mix-blend-screen filter blur-[100px] opacity-20 animate-pulse">
            </div>
            <div
                class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-indigo-600 rounded-full mix-blend-screen filter blur-[120px] opacity-30">
            </div>

            <div class="relative z-10 transition-all duration-1000 transform"
                :class="isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                <Link href="/">
                    <div
                        class="size-16 mb-4 p-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-inner transition hover:scale-105">
                        <img src="/images/applogo.png" alt="Monti Logo" class="h-full w-full object-contain" />
                    </div>
                </Link>

                <h1 class="text-6xl font-black text-white leading-tight mb-6">
                    Elevate Your <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-indigo-100">Textile
                        Business.</span>
                </h1>

                <p class="text-blue-100 text-lg mb-10 max-w-md leading-relaxed opacity-90">
                    The ultimate ERP solution for garment manufacturers and distributors in the Philippines.
                </p>

                <div class="space-y-6">
                    <div v-for="(feature, index) in ['Wholesale Rates', 'Live Inventory', 'Automated Invoicing']"
                        :key="feature" class="flex items-center space-x-4 text-white/90"
                        :style="{ transitionDelay: `${index * 200 + 500}ms` }"
                        :class="isLoaded ? 'translate-x-0 opacity-100' : '-translate-x-10 opacity-0'">
                        <div
                            class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-400/20 flex items-center justify-center border border-blue-400/30">
                            <svg class="w-3.5 h-3.5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                            </svg>
                        </div>
                        <span class="font-medium tracking-wide">{{ feature }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6 lg:p-16">
            <div class="w-full max-w-2xl transition-all duration-700 delay-300"
                :class="isLoaded ? 'scale-100 opacity-100' : 'scale-95 opacity-0'">
                <div class="mb-12">
                    <h2 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">Create Partner
                        Account</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-3 text-lg">Join the network of top-tier textile
                        partners.</p>
                </div>

                <form @submit.prevent="submit" class="grid gap-y-7">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="group">
                            <InputLabel for="company_name" value="Company Legal Name"
                                class="text-xs font-bold text-slate-400 uppercase ml-1" />
                            <TextInput id="company_name" type="text"
                                class="mt-1.5 block w-full border-slate-200 focus:ring-4 focus:ring-blue-50/50 transition-all duration-300"
                                v-model="form.company_name" required autofocus />
                            <InputError class="mt-2" :message="form.errors.company_name" />
                        </div>
                        <div>
                            <InputLabel for="tin_number" value="TIN Number"
                                class="text-xs font-bold text-slate-400 uppercase ml-1" />
                            <TextInput id="tin_number" type="text"
                                class="mt-1.5 block w-full border-slate-200 focus:ring-4 focus:ring-blue-50/50"
                                v-model="form.tin_number" placeholder="000-000-000-000" />
                            <InputError class="mt-2" :message="form.errors.tin_number" />
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="business_type" value="Business Type"
                                class="text-xs font-bold text-slate-400 uppercase ml-1" />
                            <select v-model="form.business_type" required
                                class="mt-1.5 block w-full rounded-xl border-slate-200 shadow-sm focus:border-[#1E40AF] focus:ring-4 focus:ring-blue-50/50 dark:bg-slate-900 dark:text-white transition-all">
                                <option value="" disabled>Select category</option>
                                <option value="retailer">Retailer</option>
                                <option value="wholesaler">Wholesaler</option>
                                <option value="garment_factory">Garment Factory</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.business_type" />
                        </div>
                        <div>
                            <InputLabel for="contact_person" value="Authorized Representative"
                                class="text-xs font-bold text-slate-400 uppercase ml-1" />
                            <TextInput id="contact_person" type="text" class="mt-1.5 block w-full border-slate-200"
                                v-model="form.contact_person" required />
                            <InputError class="mt-2" :message="form.errors.contact_person" />
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="email" value="Business Email"
                                class="text-xs font-bold text-slate-400 uppercase ml-1" />
                            <TextInput id="email" type="email" class="mt-1.5 block w-full border-slate-200"
                                v-model="form.email" required />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>
                        <div>
                            <InputLabel for="phone" value="Contact Number"
                                class="text-xs font-bold text-slate-400 uppercase ml-1" />
                            <TextInput id="phone" type="text" class="mt-1.5 block w-full border-slate-200"
                                v-model="form.phone" required />
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="company_address" value="Business Address"
                            class="text-xs font-bold text-slate-400 uppercase ml-1" />
                        <textarea id="company_address"
                            class="mt-1.5 block w-full rounded-xl border-slate-200 shadow-sm focus:border-[#1E40AF] focus:ring-4 focus:ring-blue-50/50 dark:bg-slate-900 dark:text-white"
                            v-model="form.company_address" required rows="2"></textarea>
                        <InputError class="mt-2" :message="form.errors.company_address" />
                    </div>

                    <div
                        class="grid md:grid-cols-2 gap-6 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div>
                            <InputLabel for="password" value="Password"
                                class="text-xs font-bold text-slate-400 uppercase ml-1" />
                            <div class="relative group">
                                <TextInput id="password" :type="showPassword ? 'text' : 'password'"
                                    class="mt-1.5 block w-full border-slate-200 pr-12 focus:ring-4 focus:ring-blue-50/50 transition-all duration-300"
                                    v-model="form.password" required autocomplete="new-password" />
                                <button type="button" @click="togglePassword"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 transition-colors focus:outline-none">
                                    <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>
                        <div>
                            <InputLabel for="password_confirmation" value="Confirm Password"
                                class="text-xs font-bold text-slate-400 uppercase ml-1" />
                            <div class="relative group">
                                <TextInput id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'"
                                    class="mt-1.5 block w-full border-slate-200 pr-12 focus:ring-4 focus:ring-blue-50/50 transition-all duration-300"
                                    v-model="form.password_confirmation" required autocomplete="new-password" />
                                <button type="button" @click="toggleConfirmPassword"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 transition-colors focus:outline-none">
                                    <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
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