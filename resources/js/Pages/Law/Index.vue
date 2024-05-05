<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import {ref} from "vue";
import useState from "ant-design-vue/lib/_util/hooks/useState.js";

const [laws, setLaws] = useState();
const [codes, setCodes] = useState();
const [constitutions, setConstitutions] = useState();

function handleDataLaw(laws) {
    const maxLength = 80;
    return laws.map(law => {
        let truncatedName = law.name.length > maxLength ? law.name.substring(0, maxLength) + "..." : law.name;
        const subTitle = `Mã định danh: ${law.identifier}`;

        if (law.ministries == null) {
            law.ministries = "";
        }

        let textStatus;
        let colorStatus;
        switch (law.effect_status) {
            case -1:
                textStatus = "Chưa có hiệu lực"
                colorStatus = "text-yellow-600"
                break;
            case 0:
                textStatus = "Hết hiệu lực toàn bộ"
                colorStatus = "text-rose-600"
                break;
            case 1:
                textStatus = "Còn hiệu lực"
                colorStatus = "text-green-600"
                break;
            case 2:
                textStatus = "Hết hiệu lực một phần"
                colorStatus = "text-rose-400"
                break;
            default:
                textStatus = ""
                colorStatus = ""
        }
        colorStatus = colorStatus + " font-bold"
        return { ...law,
            name: truncatedName,
            subTitle: subTitle,
            textStatus: textStatus ,
            colorStatus: colorStatus,
        };
    });
}

const lawsHandled = handleDataLaw(usePage().props.laws)
const codesHandled = handleDataLaw(usePage().props.codes)
const constitutionsHandled = handleDataLaw(usePage().props.constitutions)

setLaws(lawsHandled)
setCodes(codesHandled)
setConstitutions(constitutionsHandled)

const columns = [];
const value = ref("");
const activeKey = ref('1');

function handleFilter(searchValue, lawsHandled) {
    searchValue = searchValue.toLowerCase();
    const lawsSearch = lawsHandled.filter(
        (law) =>
            law.name.toString().toLowerCase().includes(searchValue) ||
            law.identifier.toString().toLowerCase().includes(searchValue) ||
            law.ministries.toString().toLowerCase().includes(searchValue) ||
            new Date(law.issued_date).toLocaleDateString().includes(searchValue) ||
            new Date(law.effective_date).toLocaleDateString().includes(searchValue) ||
            law.textStatus.toString().toLowerCase().includes(searchValue)
    )

    return lawsSearch
}
const onSearch = (searchValue) => {
    if (searchValue === "") {
        setLaws(lawsHandled)
        setCodes(codesHandled)
        setConstitutions(constitutionsHandled)
    } else {
        const lawsSearch = handleFilter(searchValue, lawsHandled);
        const codesSearch = handleFilter(searchValue, codesHandled);
        const constitutionsSearch = handleFilter(searchValue, constitutionsHandled);

        setLaws(lawsSearch);
        setCodes(codesSearch);
        setConstitutions(constitutionsSearch);
    }
}

</script>

<template>
    <Head title="Danh sách các luật và bộ luật" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">DANH SÁCH CÁC LUẬT VÀ BỘ LUẬT</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="pb-4 content-end">
                    <a-space direction="vertical" clearIcon>
                        <a-input-search
                            v-model:value="value"
                            placeholder="Nhập thông tin cần tìm kiếm ..."
                            enter-button="Tìm kiếm"
                            style="width: 1220px"
                            @search="onSearch"
                            allow-clear
                        >
                        </a-input-search>
                    </a-space>
                </div>
                <a-tabs v-model:activeKey="activeKey" size="large">
                    <a-tab-pane key="1" tab="Luật">
                        <a-table :dataSource="laws" :columns="columns" >
                            <template #bodyCell="{column, record}">
                                <a-page-header
                                    class="demo-page-header"
                                    style="border: 1px solid rgb(235, 237, 240)"
                                    :title="record.name"
                                    :sub-title= "record.subTitle"
                                >
                                    <template #extra>
                                        <a-button key="1" type="primary" @click="router.get(route('laws.show' ,{identifier: record.identifier.replace(/\//g, '_')}))">
                                            Chi tiết
                                        </a-button>
                                    </template>
                                    <a-descriptions size="small" :column="3">
                                        <a-descriptions-item label="Ngày ban hành">{{record.issued_date}}</a-descriptions-item>
                                        <a-descriptions-item label="Ngày hiệu lực">{{record.effective_date}}</a-descriptions-item>
                                        <a-descriptions-item label="Trạng thái">
                                            <span :class="record.colorStatus"> {{record.textStatus}}</span>
                                        </a-descriptions-item>
                                        <a-descriptions-item label="Lĩnh vực">
                                            <span class="font-semibold">{{record.ministries}}</span>
                                        </a-descriptions-item>
                                    </a-descriptions>
                                </a-page-header>
                            </template>
                        </a-table>
                    </a-tab-pane>
                    <a-tab-pane key="2" tab="Bộ luật">
                        <a-table :dataSource="codes" :columns="columns" >
                            <template #bodyCell="{column, record}">
                                <a-page-header
                                    class="demo-page-header"
                                    style="border: 1px solid rgb(235, 237, 240)"
                                    :title="record.name"
                                    :sub-title= "record.subTitle"
                                >
                                    <template #extra>
                                        <a-button key="1" type="primary" @click="router.get(route('laws.show' ,{identifier: record.identifier.replace(/\//g, '_')}))">
                                            Chi tiết
                                        </a-button>
                                    </template>
                                    <a-descriptions size="small" :column="3">
                                        <a-descriptions-item label="Ngày ban hành">{{record.issued_date}}</a-descriptions-item>
                                        <a-descriptions-item label="Ngày hiệu lực">{{record.effective_date}}</a-descriptions-item>
                                        <a-descriptions-item label="Trạng thái">
                                            <span :class="record.colorStatus"> {{record.textStatus}}</span>
                                        </a-descriptions-item>
                                        <a-descriptions-item label="Lĩnh vực">
                                            <span class="font-semibold">{{record.ministries}}</span>
                                        </a-descriptions-item>
                                    </a-descriptions>
                                </a-page-header>
                            </template>
                        </a-table>
                    </a-tab-pane>
                    <a-tab-pane key="3" tab="Hiến pháp">
                        <a-table :dataSource="constitutions" :columns="columns" >
                            <template #bodyCell="{column, record}">
                                <a-page-header
                                    class="demo-page-header"
                                    style="border: 1px solid rgb(235, 237, 240)"
                                    :title="record.name"
                                    :sub-title= "record.subTitle"
                                >
                                    <template #extra>
                                        <a-button key="1" type="primary" @click="router.get(route('laws.show' ,{identifier: record.identifier.replace(/\//g, '_')}))">
                                            Chi tiết
                                        </a-button>
                                    </template>
                                    <a-descriptions size="small" :column="3">
                                        <a-descriptions-item label="Ngày ban hành">{{record.issued_date}}</a-descriptions-item>
                                        <a-descriptions-item label="Ngày hiệu lực">{{record.effective_date}}</a-descriptions-item>
                                        <a-descriptions-item label="Trạng thái">
                                            <span :class="record.colorStatus"> {{record.textStatus}}</span>
                                        </a-descriptions-item>
                                        <a-descriptions-item label="Lĩnh vực">
                                            <span class="font-semibold">{{record.ministries}}</span>
                                        </a-descriptions-item>
                                    </a-descriptions>
                                </a-page-header>
                            </template>
                        </a-table>
                    </a-tab-pane >
                </a-tabs>

            </div>

        </div>
    </AuthenticatedLayout>
</template>
