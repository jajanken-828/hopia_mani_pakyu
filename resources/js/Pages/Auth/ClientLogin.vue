<script setup>
import { onMounted, ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const isLoaded = ref(false);
const showPassword = ref(false);

onMounted(() => {
    isLoaded.value = true;
});

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const submit = () => {
    form.post(route('client.login.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head title="Monti Textile - Partner Login" />

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
                    Welcome <br />
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-indigo-100">Back.</span>
                </h1>

                <p class="text-blue-100 text-lg mb-10 max-w-md leading-relaxed opacity-90">
                    Access your B2B partner dashboard to manage orders, check live inventory, and view invoices.
                </p>

                <div class="space-y-4">
                    <div class="flex items-center space-x-4 text-white/90">
                        <div
                            class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-400/20 flex items-center justify-center border border-blue-400/30">
                            <svg class="w-3.5 h-3.5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                        </div>
                        <span class="font-medium tracking-wide">Secure Partner Access</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6 lg:p-16">
            <div class="w-full max-w-md transition-all duration-700 delay-300"
                :class="isLoaded ? 'scale-100 opacity-100' : 'scale-95 opacity-0'">

                <div class="mb-10">
                    <h2 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">Partner Sign In
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-3 text-lg">Enter your business credentials to
                        continue.</p>
                </div>

                <div v-if="$page.props.flash && $page.props.flash.message"
                    class="mb-6 p-4 rounded-2xl bg-green-50 text-sm text-green-700 border border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800">
                    {{ $page.props.flash.message }}
                </div>

                <form @submit.prevent="submit" class="space-y-7">
                    <div>
                        <InputLabel for="email" value="Business Email"
                            class="text-xs font-bold text-slate-400 uppercase ml-1" />
                        <TextInput id="email" type="email"
                            class="mt-1.5 block w-full border-slate-200 focus:ring-4 focus:ring-blue-50/50 transition-all duration-300"
                            v-model="form.email" required autofocus autocomplete="username"
                            placeholder="partner@company.com" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center ml-1">
                            <InputLabel for="password" value="Password"
                                class="text-xs font-bold text-slate-400 uppercase" />
                            <Link v-if="route().has('password.request')" :href="route('password.request')"
                                class="text-xs font-bold text-[#1E40AF] hover:underline dark:text-blue-400 uppercase">
                                Forgot?
                            </Link>
                        </div>
                        <div class="relative group">
                            <TextInput :id="'password'" :type="showPassword ? 'text' : 'password'"
                                class="mt-1.5 block w-full border-slate-200 focus:ring-4 focus:ring-blue-50/50 transition-all duration-300 pr-12"
                                v-model="form.password" required autocomplete="current-password"
                                placeholder="••••••••" />

                            <button type="button" @click="togglePassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 transition-colors focus:outline-none">
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
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

                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" v-model="form.remember"
                                class="rounded-md border-slate-300 text-[#1E40AF] shadow-sm focus:ring-[#1E40AF] dark:bg-slate-900 dark:border-slate-700 transition-all" />
                            <span
                                class="ms-2 text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Remember
                                my business</span>
                        </label>
                    </div>

                    <div class="space-y-6 pt-2">
                        <PrimaryButton
                            class="w-full justify-center py-4 bg-[#1E40AF] hover:bg-blue-700 text-white rounded-2xl shadow-xl shadow-blue-500/20 active:scale-[0.98] transition-all text-lg font-bold"
                            :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                            <span v-if="form.processing">Authenticating...</span>
                            <span v-else>Sign In to Portal</span>
                        </PrimaryButton>

                        <div class="text-center text-sm text-slate-500 dark:text-slate-400">
                            New partner?
                            <Link :href="route('client.register')"
                                class="font-bold text-[#1E40AF] hover:underline dark:text-blue-400 ml-1">
                                Register your business
                            </Link>
                        </div>
                    </div>
                </form>

                <div class="mt-12 pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Need technical assistance? Contact B2B Support <br>
                        <span class="font-bold text-slate-600 dark:text-slate-300">sales@montitextile.com</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
input {
    @apply transition-all duration-300 ease-in-out;
}
</style>