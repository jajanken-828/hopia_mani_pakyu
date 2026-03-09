<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const isLoaded = ref(false);
const showPassword = ref(false);

onMounted(() => { isLoaded.value = true; });

const submit = () => {
    form.post(route('supplier.login.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head title="Supplier Portal Login" />
    <div class="min-h-screen flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-950 font-sans">
        <div class="relative hidden md:flex md:w-[40%] bg-emerald-900 overflow-hidden items-center justify-center p-12">
            <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,...');"></div>
            <div class="relative z-10">
                <Link href="/">
                    <div class="size-16 mb-6 p-2 bg-white rounded-xl shadow-xl">
                        <img src="/images/applogo.png" alt="Logo" class="h-full w-full object-contain" />
                    </div>
                </Link>
                <h1 class="text-5xl font-black text-white leading-tight mb-4">SUPPLY<br /><span
                        class="text-emerald-400">CHAIN</span></h1>
                <p class="text-emerald-100/80 text-lg max-w-md">Secure portal for Monti Textile official vendors and raw
                    material suppliers.</p>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6 lg:p-16">
            <div class="w-full max-w-md transition-all duration-700"
                :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Supplier Sign In</h2>
                    <p class="text-slate-500 mt-2">Access your vendor dashboard and purchase orders.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Registered Email" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                            autofocus />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Password" />
                        <TextInput id="password" :type="showPassword ? 'text' : 'password'" class="mt-1 block w-full"
                            v-model="form.password" required />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" v-model="form.remember"
                                class="rounded border-gray-300 text-emerald-600 shadow-sm" />
                            <span class="ms-2 text-sm text-gray-600">Remember account</span>
                        </label>
                        <Link :href="route('password.request')" class="text-sm text-emerald-600 hover:underline">Forgot
                            password?</Link>
                    </div>

                    <PrimaryButton class="w-full justify-center py-4 bg-emerald-600 hover:bg-emerald-700"
                        :disabled="form.processing">
                        Login to Supplier Hub
                    </PrimaryButton>

                    <div class="text-center text-sm text-slate-500">
                        Interested in becoming a supplier?
                        <Link :href="route('supplier.register')" class="font-bold text-emerald-600 hover:underline">
                            Register Here</Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>