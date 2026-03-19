<script setup>
import { onMounted, ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

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
        onSuccess: () => {
            toast.success('Successfully signed in to Client Portal!');
        },
        onError: (errors) => {
            const errorMsg = Object.values(errors)[0] || 'Login failed. Please check your credentials.';
            toast.error(errorMsg);
        },
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head title="Monti Textile - Partner Login" />

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

            <div class="w-full max-w-md relative z-10"
                :class="{ 'opacity-0 translate-y-8': !isLoaded, 'opacity-100 translate-y-0': isLoaded }"
                style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.1s;">

                <div class="text-center mb-10">
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-3">
                        Partner Login</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm md:text-base max-w-sm mx-auto">Welcome back!
                        Please enter your registered B2B credentials to access your dashboard.</p>
                </div>

                <div v-if="$page.props.status"
                    class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                    {{ $page.props.status }}
                </div>

                <form @submit.prevent="submit"
                    class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-xl border border-slate-100 dark:border-slate-800 p-8 md:p-10 space-y-6">

                    <div>
                        <InputLabel for="email" value="Business Email"
                            class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1" />
                        <TextInput id="email" type="email"
                            class="mt-1 block w-full py-3 px-4 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:ring-[#1E40AF]"
                            v-model="form.email" required autofocus autocomplete="username"
                            placeholder="partner@company.com" />
                        <InputError class="mt-2 ml-1" :message="form.errors.email" />
                    </div>

                    <div>
                        <div class="flex justify-between items-end mb-1 ml-1">
                            <InputLabel for="password" value="Password"
                                class="text-xs font-bold uppercase tracking-wider text-slate-500" />
                            <Link v-if="$page.props.canResetPassword" :href="route('password.request')"
                                class="text-xs font-semibold text-[#1E40AF] hover:text-blue-700 dark:text-blue-400 hover:underline transition-all">
                                Forgot password?
                            </Link>
                        </div>
                        <div class="relative">
                            <TextInput id="password" :type="showPassword ? 'text' : 'password'"
                                class="mt-1 pr-12 block w-full py-3 px-4 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-xl focus:ring-[#1E40AF]"
                                v-model="form.password" required autocomplete="current-password"
                                placeholder="••••••••" />

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
                        </div>
                        <InputError class="mt-2 ml-1" :message="form.errors.password" />
                    </div>

                    <div class="block pt-2">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" name="remember" v-model="form.remember" class="peer sr-only" />
                                <div
                                    class="w-5 h-5 border-2 border-slate-300 dark:border-slate-700 rounded transition-all peer-checked:bg-[#1E40AF] peer-checked:border-[#1E40AF]">
                                </div>
                                <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <span
                                class="ml-3 text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Remember
                                my device</span>
                        </label>
                    </div>

                    <div
                        class="flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <div class="text-center sm:text-left text-sm text-slate-500">
                            New partner?
                            <Link :href="route('client.register')"
                                class="font-bold text-[#1E40AF] hover:underline dark:text-blue-400 ml-1">
                                Apply Here
                            </Link>
                        </div>

                        <PrimaryButton
                            class="w-full sm:w-auto px-10 py-4 bg-[#1E40AF] hover:bg-[#1E3A8A] focus:bg-[#1E3A8A] text-white rounded-2xl shadow-xl shadow-blue-500/20 active:scale-[0.98] transition-all"
                            :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                            <span v-if="form.processing">Authenticating...</span>
                            <span v-else>Sign In</span>
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
input {
    @apply transition-all duration-300 ease-in-out;
}

input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 30px white inset !important;
}
</style>