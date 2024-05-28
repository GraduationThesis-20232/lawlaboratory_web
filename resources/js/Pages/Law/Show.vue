<script setup>
import {Head, router, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {reactive, ref, toRaw} from "vue";
import useState from "ant-design-vue/lib/_util/hooks/useState.js";
import {EditOutlined} from "@ant-design/icons-vue";

const [expandChapters, setExpandChapters] = useState([])
const [expandArticles, setExpandArticles] = useState([])
const [editArticle, setEditArticle] = useState({})

const activeKey = ref('1')
const law = usePage().props.law
let textStatus, colorStatus;
let keysArticles = [];
let keysChapters = [];

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

function convertLawToTreeData(law) {
    let treeData = [];

    if (law.articles != null) {
        law.articles.forEach((article, articleIndex) => {
            let articleNode = {
                title: article.name,
                key: `article-${articleIndex}`,
                content : article.content
            };
            keysArticles.push(articleNode.key)

            treeData.push(articleNode);
        })
    }

    if (law.chapters != null) {
        law.chapters.forEach((chapter, chapterIndex) => {
            let chapterNode = {
                title: chapter.name,
                key: `chapter-${chapterIndex}`,
                children: []
            };
            keysChapters.push(chapterNode.key)

            chapter.articles.forEach((article, articleIndex) => {
                let articleNode = {
                    title: article.name,
                    key: `article-${chapterIndex}-${articleIndex}`,
                    content : article.content
                };
                keysArticles.push(articleNode.key)

                chapterNode.children.push(articleNode);
            });

            treeData.push(chapterNode);
        });
    }

    if (law.parts != null) {
        law.parts.forEach((part, partIndex) => {
            let partNode = {
                title: part.name,
                key: `part-${partIndex}`,
                children: []
            };

            if (part.chapters == null) {
                return
            }

            part.chapters.forEach((chapter, chapterIndex) => {
                let chapterNode = {
                    title: chapter.name,
                    key: `chapter-${partIndex}-${chapterIndex}`,
                    children: []
                };
                keysChapters.push(chapterNode.key)

                chapter.articles.forEach((article, articleIndex) => {
                    let articleNode = {
                        title: article.name,
                        key: `article-${partIndex}-${chapterIndex}-${articleIndex}`,
                        content : article.content
                    };
                    keysArticles.push(articleNode.key)

                    chapterNode.children.push(articleNode);
                });

                partNode.children.push(chapterNode);
            });
            treeData.push(partNode)
        })
    }

    return treeData;
}

const treeData = convertLawToTreeData(law)

const expandAll = () => {
    setExpandChapters(keysChapters);
    setExpandArticles(keysArticles);
}

const collapseAll = () => {
    setExpandChapters([]);
    setExpandArticles([]);
}

const onSelect = (selectedKeys, info) => {
    setExpandChapters(selectedKeys)
};

const formState = reactive({
    title: '',
    content: '',
    key: '',
});
const open = ref(false);
const showModal = () => {
    formState.title = editArticle.value.title;
    formState.content = Object.values(editArticle.value.content).join('\n');
    formState.key =  editArticle.value.key
    open.value = true;
};
const submitFormEdit = () => {
    formRef.value
        .validateFields()
        .then(values => {
            open.value = false;
            router.put(route('laws.update', {identifier: law.identifier.replace(/\//g, '_'), ...toRaw(formState) }))
        })
        .catch(info => {
            console.log('Validate Failed:', info);
        });
};

const formRef = ref();
const labelCol = {
    style: {
        width: '80px',
    },
};
const wrapperCol = {
    style: {
        width: '1000px',
    },
};

</script>

<template>
    <Head title="Chi tiết luật" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight m-0">{{ law.name }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <a-page-header
                    title="Danh sách luật"
                    @back="() => router.get(route('laws.index'))"
                />
                <a-tabs v-model:activeKey="activeKey" size="large">
                    <template #rightExtra>
                        <a-button @click="expandAll" >Mở rộng tất cả</a-button>
                        <a-button class="ml-4" @click="collapseAll" >Thu hẹp tất cả</a-button>
                    </template>
                    <a-tab-pane key="1" tab="Toàn văn">
                        <a-tree
                            class="p-2"
                            :tree-data="treeData"
                            v-model:expandedKeys="expandChapters"
                            block-node
                            multiple
                            @select="onSelect"
                        >
                            <template #title="{ dataRef }">
                                <template v-if="dataRef.key.includes('article')">
                                    <a-collapse
                                        :bordered="false"
                                        style="background: rgb(255, 255, 255)"
                                        v-model:activeKey="expandArticles"
                                    >
                                        <a-collapse-panel class="font-semibold text-base" :key="dataRef.key" :header="dataRef.title">
                                            <span class="font-normal  flex-grow" v-for="line in dataRef.content">
                                                <p class="pl-4">{{ line }}</p>
                                            </span>
                                            <template v-if="usePage().props.auth.user.role_id === 1" #extra>
                                                <a-tooltip title="Chỉnh sửa">
                                                    <edit-outlined class="text-blue-600"
                                                                   @click="(e) => {
                                                                       e.stopPropagation();
                                                                       setEditArticle(dataRef)
                                                                       showModal()
                                                                   }"
                                                    />
                                                </a-tooltip>
                                            </template>
                                        </a-collapse-panel>
                                    </a-collapse>
                                </template>
                                <template v-else>
                                    <span class="font-bold text-lg" >{{ dataRef.title }}</span>
                                </template>
                            </template>
                        </a-tree>
                        <a-modal v-model:open="open"
                                 title="Chỉnh sửa điều luật"
                                 width="1000px"
                                 ok-text="Lưu" cancel-text="Huỷ"
                                 @ok="submitFormEdit">
                            <a-form
                                ref="formRef"
                                :model="formState"
                                layout="vertical"
                                :label-col="labelCol"
                                :wrapper-col="wrapperCol"
                            >
                                <a-form-item label="Tiêu đề" name="title"
                                             :rules="[{ required: true, message: 'Tiêu đề điều luật không được bỏ trống!' }]">
                                    <a-input v-model:value="formState.title"/>
                                </a-form-item>
                                <a-form-item label="Nội dung" name="content"
                                             :rules="[{ required: true, message: 'Nội dung điều luật không được bỏ trống!' }]">
                                    <a-textarea v-model:value="formState.content" auto-size/>
                                </a-form-item>
                            </a-form>
                        </a-modal>
                    </a-tab-pane>
                    <a-tab-pane key="2" tab="Thông tin">
                        <a-descriptions title="Thông tin cơ bản" bordered size="default" >
                            <a-descriptions-item label="Số ký hiệu" class="font-semibold">{{law.identifier}}</a-descriptions-item>
                            <a-descriptions-item label="Ngày ban hành" :span="2">{{law.issued_date}}</a-descriptions-item>
                            <a-descriptions-item label="Loại văn bản">{{law.legislation}}</a-descriptions-item>
                            <a-descriptions-item label="Ngày có hiệu lực" :span="2">{{law.effective_date}}</a-descriptions-item>
                            <a-descriptions-item label="Nguồn thu thập"> {{law.source_collection}} </a-descriptions-item>
                            <a-descriptions-item label="Đường dẫn thu thập" :span="2" >
                                <a class="underline text-blue-600" :href="law.source_url"> Link </a>
                            </a-descriptions-item>
                            <a-descriptions-item label="Ngành - Lĩnh vực">{{ law.ministries }} - {{law.field}}</a-descriptions-item>
                            <a-descriptions-item label="Ngày đăng công báo" :span="2">{{law.gazette_date}}</a-descriptions-item>
                            <a-descriptions-item label="Cơ quan ban hành">{{law.issuing_body}} </a-descriptions-item>
                            <a-descriptions-item label="Người ký" :span="2">{{law.chairwoman + ' ' + law.signer}}</a-descriptions-item>
                            <a-descriptions-item label="Phạm vi" :span="3">{{law.effective_area}}</a-descriptions-item>
                        </a-descriptions>
                        <a-descriptions class="mt-4" title="Thông tin áp dụng" bordered>
                            <a-descriptions-item label="Tình trạng hiệu lực" class="font-semibold" :span="3">
                                <span :class="colorStatus" >{{ textStatus }}</span>
                            </a-descriptions-item>
                            <a-descriptions-item v-if="law.effect_status === 0 || law.effect_status===2" label="Ngày hết hiệu lực" :span="3">{{law.expiry_date}}</a-descriptions-item>
                            <a-descriptions-item v-if="law.effect_status === 0 || law.effect_status===2" label="Lí do hết hiệu lực" :span="3">{{law.reason_expiration}}</a-descriptions-item>
                        </a-descriptions>
                    </a-tab-pane>
                    <a-tab-pane key="3" tab="Văn bản gốc">

                    </a-tab-pane>
                    <a-tab-pane key="4" tab="Tải văn bản">

                    </a-tab-pane>
                </a-tabs>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
