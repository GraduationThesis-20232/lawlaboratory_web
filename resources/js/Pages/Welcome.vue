<script setup>
import {Head, Link, router} from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import NavBar from "@/Components/NavBar.vue";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome" />

    <nav-bar :can-login="canLogin" :can-register="canRegister" />
    <div class="h-screen flex items-center justify-center bg-gradient-to-r from-teal-100 to-blue-100">
        <div class="text-center ">
            <img class="block w-[200px] h-auto mx-auto" src="logo.png" alt="LawLaboratory">
            <h2 class="text-3xl lg:text-3xl font-sans">Chào mừng đến với</h2>
            <h1 class="text-3xl lg:text-5xl font-bold">LawLaboratory</h1>
            <p class="py-4 lg:text-lg text-sm">
                Nền tảng tư vấn và tham khảo thông tin về Luật Việt Nam
            </p>
            <a-button @click="router.get(route('laws.index'))" class="btn btn-info bg-yellow-500 shadow-lg" size="large" >Bắt đầu ngay</a-button>
        </div>
    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
