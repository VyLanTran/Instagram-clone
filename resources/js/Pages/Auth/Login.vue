<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <!-- Logo -->
        <div class="p-8 pb-12">
            <img class="mx-auto pb-2 z-10" width="50" src="/bird-logo.png" />
            <div class="text-4xl text-[#ff5349] font-bold text-center z-20">
                Social Tap
            </div>
        </div>

        <!-- Login form -->
        <form @submit.prevent="submit">
            <div>
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Email"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="Password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex justify-center">
                <PrimaryButton
                    class="my-8 px-6"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>

            <div class="flex items-center justify-center">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none"
                >
                    Forgot your password?
                </Link>
            </div>

            <div class="flex items-center justify-center my-2 text-[14px]">
                Don't have an account?
                <Link
                    v-if="canResetPassword"
                    :href="route('register')"
                    class="pl-1 font-bold text-[#ff5349] rounded-md focus:outline-none"
                >
                    Sign up
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
