<template>
    <nav class="flex items-center justify-between p-4 bg-gradient-to-r from-teal-100 to-blue-100 shadow">
        <div class="flex items-center">
            <application-logo class="h-9 w-auto fill-current text-gray-800" />
            <a href="/" class="text-2xl font-bold text-gray-800 no-underline ml-2">
                LawLaboratory
            </a>
        </div>

        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-end">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900"
            >Dashboard</Link
            >

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900"
                >Log in</Link
                >

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ms-4 font-semibold text-gray-600 hover:text-gray-900"
                >Register</Link
                >
            </template>
        </div>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>
