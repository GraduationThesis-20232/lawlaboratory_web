<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import {ref} from "vue";

const activeKey = ref('1');
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight m-0">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <a-tabs v-model:activeKey="activeKey" size="large">
                    <a-tab-pane key="1" tab="Thống kê văn bản Luật">
                        <a-button @click="router.get(route('crawl.laws'))" type="primary"> Thu thập văn bản luật mới </a-button>
                    </a-tab-pane>
                    <a-tab-pane key="2" tab="Thống kê câu hỏi Luật">
                        <a-button @click="router.get(route('crawl.questions'))" type="primary"> Thu thập câu hỏi luật mới </a-button>
                    </a-tab-pane>
                </a-tabs>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
