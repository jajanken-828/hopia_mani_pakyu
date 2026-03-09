<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    business_name: '',
    representative_name: '',
    address: '',
    email: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
});

const isLoaded = ref(false);

onMounted(() => {
    isLoaded.value = true;
});

const submit = () => {
    form.post(route('supplier.register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Supplier Registration | Monti Textile" />

    <div class="min-h-screen flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-950 font-sans">
        <div class="relative hidden md:flex md:w-[35%] bg-emerald-900 overflow-hidden items-center justify-center p-12">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(#fff 0.5px, transparent 0.5px); background-size: 30px 30px;">
            </div>

            <div class="relative z-10 transition-all duration-1000 transform"
                :class="isLoaded ? 'translate-x-0 opacity-100' : '-translate-x-10 opacity-0'">
                <Link href="/">
                    <div class="size-16 mb-8 p-2 bg-white rounded-2xl shadow-xl flex items-center justify-center">
                        <img src="/images/applogo.png" alt="Monti Logo" class="h-10 w-10 object-contain" />
                    </div>
                </Link>

                <h1 class="text-4xl font-black text-white leading-tight mb-6">
                    Partner with <br />
                    <span class="text-emerald-400">Monti Textile.</span>
                </h1>

                <p class="text-emerald-100/80 text-lg mb-10 max-w-sm leading-relaxed">
                    Join our global supply chain network. Register your business to start receiving purchase orders and
                    managing inventory sync.
                </p>

                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="mt-1 bg-emerald-800 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-bold">Automated POs</h4>
                            <p class="text-emerald-200/60 text-sm">Receive digital orders directly to your dashboard.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6 lg:p-12 overflow-y-auto">
            <div class="w-full max-w-2xl transition-all duration-700 delay-200"
                :class="isLoaded ? 'scale-100 opacity-100' : 'scale-95 opacity-0'">

                <div class="mb-10 text-center md:text-left">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Supplier
                        Onboarding</h2>
                    <p class="text-slate-500 mt-2">Please provide your business details to apply for a vendor account.
                    </p>
                </div>

                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    <div class="md:col-span-2">
                        <InputLabel for="business_name" value="Business / Company Name"
                            class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1" />
                        <TextInput id="business_name" type="text"
                            class="mt-1 block w-full border-slate-200 focus:ring-emerald-500/20"
                            v-model="form.business_name" required autofocus
                            placeholder="e.g. Global Fiber Solutions Inc." />
                        <InputError class="mt-2" :message="form.errors.business_name" />
                    </div>

                    <div>
                        <InputLabel for="representative_name" value="Primary Representative"
                            class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1" />
                        <TextInput id="representative_name" type="text" class="mt-1 block w-full border-slate-200"
                            v-model="form.representative_name" required placeholder="Full Name" />
                        <InputError class="mt-2" :message="form.errors.representative_name" />
                    </div>

                    <div>
                        <InputLabel for="phone_number" value="Contact Phone"
                            class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1" />
                        <TextInput id="phone_number" type="tel" class="mt-1 block w-full border-slate-200"
                            v-model="form.phone_number" required placeholder="+1 (555) 000-0000" />
                        <InputError class="mt-2" :message="form.errors.phone_number" />
                    </div>

                    <div class="md:col-span-2">
                        <InputLabel for="email" value="Business Email Address"
                            class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1" />
                        <TextInput id="email" type="email" class="mt-1 block w-full border-slate-200"
                            v-model="form.email" required placeholder="vendor@company.com" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="md:col-span-2">
                        <InputLabel for="address" value="Office / Warehouse Address"
                            class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1" />
                        <TextInput id="address" type="text" class="mt-1 block w-full border-slate-200"
                            v-model="form.address" required placeholder="Street, City, Province, Country" />
                        <InputError class="mt-2" :message="form.errors.address" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Password"
                            class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1" />
                        <TextInput id="password" type="password" class="mt-1 block w-full border-slate-200"
                            v-model="form.password" required placeholder="••••••••" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirm Password"
                            class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1" />
                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full border-slate-200"
                            v-model="form.password_confirmation" required placeholder="••••••••" />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="md:col-span-2 pt-4">
                        <PrimaryButton
                            class="w-full justify-center py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-lg shadow-emerald-500/20 text-lg font-bold transition-all"
                            :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                            Register Business Profile
                        </PrimaryButton>

                        <div class="mt-6 text-center text-sm text-slate-500">
                            Already a registered supplier?
                            <Link :href="route('supplier.login')"
                                class="font-bold text-emerald-600 hover:underline ml-1">
                                Sign In here
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>