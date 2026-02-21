<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const isLoaded = ref(false);
const showPassword = ref(false);

onMounted(() => {
    isLoaded.value = true;
});

// Single identity state for both email and employee ID
const form = useForm({
    identity: '',
    password: '',
    remember: false,
});

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const submit = () => {
    const isEmail = form.identity.includes('@');
    const routeName = isEmail ? 'login' : 'employee.login.store';

    const data = {
        password: form.password,
        remember: form.remember,
        [isEmail ? 'email' : 'employee_id']: form.identity
    };

    form.transform(() => data).post(route(routeName), {
        onFinish: () => {
            form.reset('password');
        },
    });
};

const identityPlaceholder = computed(() => "user@monticorp.com or EMP-XXXX-X");
</script>

<template>

    <Head title="ERP Secure Authorization | Monti Textile" />

    <div class="min-h-screen flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-950 font-sans selection:bg-blue-100">

        <div class="relative hidden md:flex md:w-[40%] bg-slate-900 overflow-hidden items-center justify-center p-12">
            <div class="absolute inset-0 opacity-20 pointer-events-none"
                style="background-image: radial-gradient(#3b82f6 0.5px, transparent 0.5px); background-size: 30px 30px;">
            </div>

            <div
                class="absolute top-[-10%] left-[-10%] w-72 h-72 bg-blue-600 rounded-full mix-blend-screen filter blur-[100px] opacity-10 animate-pulse">
            </div>

            <div class="relative z-10 transition-all duration-1000 transform text-center md:text-left"
                :class="isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">

                <Link href="/">
                    <div
                        class="size-16 mb-6 p-2 bg-white dark:bg-slate-800 border border-slate-700 rounded-xl shadow-2xl mx-auto md:mx-0">
                        <img src="/images/applogo.png" alt="Monti Logo" class="h-full w-full object-contain" />
                    </div>
                </Link>

                <h1 class="text-5xl font-black text-white leading-tight mb-4 font-mono uppercase tracking-tighter">
                    MONTI<span class="text-blue-500">ERP</span>
                </h1>

                <p
                    class="text-slate-400 text-lg mb-8 max-w-md leading-relaxed font-mono uppercase text-xs tracking-[0.2em]">
                    Enterprise Resource Planning <br /> Central Node v1.0
                </p>

                <div class="space-y-4">
                    <div class="flex items-center space-x-4 text-slate-400 font-mono text-sm">
                        <span class="text-blue-500">>></span>
                        <span>Authorized Personnel Only</span>
                    </div>
                    <div class="flex items-center space-x-4 text-slate-400 font-mono text-sm">
                        <span class="text-blue-500">>></span>
                        <span>Encrypted Session Protocol</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6 lg:p-16 relative">
            <div class="w-full max-w-md transition-all duration-700 delay-300"
                :class="isLoaded ? 'scale-100 opacity-100' : 'scale-95 opacity-0'">

                <div class="mb-10">
                    <h2 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight font-mono">
                        SECURE_LOGIN</h2>
                    <p
                        class="text-slate-500 dark:text-slate-400 mt-3 text-lg font-mono text-sm uppercase tracking-widest">
                        Identify yourself to the system.</p>
                </div>

                <transition enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
                    <div v-if="status"
                        class="mb-6 rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-4 text-xs font-mono text-emerald-500">
                        <div class="flex items-center">
                            <span class="mr-2">>></span> {{ status }}
                        </div>
                    </div>
                </transition>

                <form @submit.prevent="submit" class="space-y-7">
                    <div>
                        <InputLabel for="identity" value="User Identity"
                            class="text-xs font-bold text-slate-400 uppercase font-mono tracking-widest ml-1" />
                        <div class="relative group mt-1.5">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"
                                        stroke-width="2.5" />
                                </svg>
                            </div>
                            <TextInput id="identity" type="text"
                                class="block w-full pl-11 pr-4 py-4 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/10 transition-all font-mono"
                                v-model="form.identity" required autofocus :placeholder="identityPlaceholder" />
                        </div>
                        <InputError class="mt-2 text-xs font-mono"
                            :message="form.errors.email || form.errors.employee_id || form.errors.identity" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center ml-1">
                            <InputLabel for="password" value="Access Key"
                                class="text-xs font-bold text-slate-400 uppercase font-mono tracking-widest" />
                            <Link v-if="canResetPassword" :href="route('password.request')"
                                class="text-xs font-bold text-blue-600 hover:underline dark:text-blue-400 font-mono uppercase tracking-widest">
                                [ RESET ]
                            </Link>
                        </div>
                        <div class="relative group mt-1.5">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                        stroke-width="2.5" />
                                </svg>
                            </div>
                            <TextInput id="password" :type="showPassword ? 'text' : 'password'"
                                class="block w-full pl-11 pr-12 py-4 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/10 transition-all font-mono"
                                v-model="form.password" required placeholder="••••••••" />

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
                        <InputError class="mt-2 text-xs font-mono" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" v-model="form.remember"
                                class="rounded-md border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-slate-900 dark:border-slate-700 transition-all" />
                            <span
                                class="ms-2 text-sm text-slate-500 font-mono uppercase tracking-widest group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Keep
                                Session Active</span>
                        </label>
                    </div>

                    <div class="space-y-6 pt-2">
                        <PrimaryButton
                            class="w-full py-4 justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-2xl shadow-xl shadow-blue-500/20 active:scale-[0.98] transition-all text-sm font-bold font-mono tracking-[0.2em]"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }" :disabled="form.processing">
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                AUTHENTICATING...
                            </span>
                            <span v-else>INITIALIZE_ACCESS</span>
                        </PrimaryButton>


                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&display=swap');

.font-mono {
    font-family: 'JetBrains Mono', monospace;
}

input:focus {
    outline: none;
    box-shadow: none !important;
}
</style>