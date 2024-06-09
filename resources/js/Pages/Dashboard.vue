<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import {nextTick, onMounted, ref} from "vue";
import {Column, Pie} from "@antv/g2plot";
import useState from "ant-design-vue/es/_util/hooks/useState.js";
import {notification} from "ant-design-vue";

const [visibleProgressCrawlLaws, setVisibleProgressCrawlLaws] = useState(false);
const [disabledButtonCrawlLaws, setDisabledButtonCrawlLaws] = useState(false);
const [percentProgressCrawlLaws, setPercentProgressCrawlLaws] = useState(0);
const [dateNearestLawCrawled, setDateNearestLawCrawled] = useState('');

const [visibleProgressCrawlQuestions, setVisibleProgressCrawlQuestions] = useState(false);
const [disabledButtonCrawlQuestions, setDisabledButtonCrawlQuestions] = useState(false);
const [percentProgressCrawlQuestions, setPercentProgressCrawlQuestions] = useState(0);
const [dateNearestQuestionCrawled, setDateNearestQuestionCrawled]= useState('');

const activeKey = ref('1');

let countLawsCorpus = [];
usePage().props.countLawsCorpus.forEach((item) => {
    countLawsCorpus.push({
        name: item.name,
        count: item.count
    });
});

let countQuestionsCorpus = [];
usePage().props.countQuestionsCorpus.forEach((item) => {
    countQuestionsCorpus.push({
        field: item.field,
        count: item.count
    });
});

let column, pie;
onMounted(() => {
    nextTick(() =>
    {
        column = new Column('column-laws-corpus', {
            data: countLawsCorpus,
            xField: 'name',
            yField: 'count',
            maxColumnWidth: 60,
            label: {
                position: 'top',
                style: {
                    fill: '#000000',
                    opacity: 0.8,
                },
            },
            columnStyle: {
                fill: '#1677ff',
            },
        });
        column.render();

        pie = new Pie('pie-questions-corpus', {
            data: countQuestionsCorpus,
            angleField: 'count',
            colorField: 'field',
            radius: 1,
            innerRadius: 0.4,
            label: {
                type: 'inner',
                offset: '-30%',
                content: '{value}',
                style: {
                    fontSize: 12,
                    textAlign: 'center',
                    fill: '#000000',
                },
            },
            interactions: [{ type: 'element-active'  }],
            legend: { position: 'left' },
        });
        pie.render()
    });
});

const showColumnNewLawsCrawled = async () => fetch('http://127.0.0.1:8000/api/crawl/documents/done', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
}).then(response => response.json())
    .then(data => {
        setDateNearestLawCrawled(data[0]['dateTime'].split(" ")[0]);
        const columnNewLawsCrawled = new Column('column-new-laws-crawled', {
            data: data,
            xField: 'type',
            yField: 'count',
            maxColumnWidth: 60,
            label: {
                position: 'top',
                style: {
                    fill: '#000000',
                    opacity: 0.8,
                },
            },
            columnStyle: {
                fill: '#1677ff',
            },
        });
        columnNewLawsCrawled.render();
    })
    .catch((error) => {
        notification.error({
            message: 'Lỗi lấy dữ liệu và hiển thị biểu đồ văn bản luật mới thu thập' ,
            description: 'Chi tiết lỗi: ' + error,
        });

})
showColumnNewLawsCrawled()

const crawlLawsStart = () => {
    fetch('http://127.0.0.1:8000/api/crawl/documents/start', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    }).then(response => response.json())
        .then(data => {
            if (data.status === 'STARTED') {
                setVisibleProgressCrawlLaws(true);
                setDisabledButtonCrawlLaws(true);
                setPercentProgressCrawlLaws(5);
                notification.info({
                    message: 'Đã bắt đầu thu thập văn bản luật',
                    description: 'Hệ thống đang tiến hành thu thập văn bản luật, vui lòng chờ trong giây lát. Theo dõi thêm ở thanh tiến trình.',
                });
                inProgressCrawlLaws();
            }
        })
        .catch((error) => {
            notification.error({
                message: 'Lỗi trong quá trình thu thập văn bản luật' ,
                description: 'Chi tiết lỗi: ' + error,
            });
        });
}

const getDataInProgressCrawlLaws = async () => {
    try {
        const response = await fetch("http://127.0.0.1:8000/api/crawl/documents/in_progress", {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })

        const data = await response.json();
        return data;
    } catch (error) {
        notification.error({
            message: 'Lỗi lấy dữ liệu tiến trình thu thập văn bản luật',
            description: 'Chi tiết lỗi: ' + error,
        });
    }
}

const inProgressCrawlLaws = async () => {
    let isDone = false;
    await new Promise(resolve => setTimeout(resolve, 5000));
    while (!isDone) {
        try {
            const responseData = await getDataInProgressCrawlLaws();

            if (responseData.status === 'DONE') {
                isDone = true;
                setVisibleProgressCrawlLaws(false);
                setDisabledButtonCrawlLaws(false);
                setPercentProgressCrawlLaws(100);
                notification.success({
                    message: 'Thu thập văn bản luật hoàn tất!',
                    description: 'Hệ thống đã thu thập xong văn bản luật, bạn có thể xem kết quả ở biểu đồ.',
                });

                const element = document.getElementById('column-new-laws-crawled');
                while (element.firstChild) {
                    element.removeChild(element.firstChild);
                }

                await showColumnNewLawsCrawled();
            } else if (responseData.status === 'IN_PROGRESS'){
                setPercentProgressCrawlLaws(5 + 30*responseData.numberTypeCrawled);
                await new Promise(resolve => setTimeout(resolve, 3000));
            }
        }catch (error) {
            notification.error({
                message: 'Lỗi cập nhật tiến trình thu thập văn bản luật' ,
                description: 'Chi tiết lỗi: ' + error,
            });
        }
    }
}

const showPieNewQuestionsCrawled =  async () => fetch('http://127.0.0.1:8000/api/crawl/questions/done', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
}).then(response => response.json())
    .then(data => {
        setDateNearestQuestionCrawled(data[0]['dateTime'].split(" ")[0]);
        const pieNewQuestions = new Pie('pie-new-questions-crawled', {
            data: data,
            angleField: 'numberQuestions',
            colorField: 'field',
            radius: 1,
            innerRadius: 0.4,
            label: {
                type: 'inner',
                offset: '-30%',
                content: '{value}',
                style: {
                    fontSize: 12,
                    textAlign: 'center',
                    fill: '#000000',
                },
            },
            interactions: [{ type: 'element-active'  }],
            legend: { position: 'right' },
        });
        pieNewQuestions.render()
    })
    .catch((error) => {
        notification.error({
            message: 'Lỗi lấy dữ liệu và hiển thị biểu đồ câu hỏi mới thu thập' ,
            description: 'Chi tiết lỗi: ' + error,
        });
    })
showPieNewQuestionsCrawled();

const crawlQuestionsStart = () => {
    fetch('http://127.0.0.1:8000/api/crawl/questions/start', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    }).then(response => response.json())
        .then(data => {
            if (data.status === 'STARTED') {
                setVisibleProgressCrawlQuestions(true);
                setDisabledButtonCrawlQuestions(true);
                setPercentProgressCrawlQuestions(4);
                notification.info({
                    message: 'Đã bắt đầu thu thập câu hỏi',
                    description: 'Hệ thống đang tiến hành thu thập câu hỏi, vui lòng chờ trong giây lát. Theo dõi thêm ở thanh tiến trình.',
                });
                inProgressCrawlQuestions();
            }
        })
        .catch((error) => {
            notification.error({
                message: 'Lỗi trong quá trình thu thập câu hỏi' ,
                description: 'Chi tiết lỗi: ' + error,
            });
        });
}

const getDataInProgressCrawlQuestions = async () => {
    try {
        const response = await fetch("http://127.0.0.1:8000/api/crawl/questions/in_progress", {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })

        const data = await response.json();
        return data;
    } catch (error) {
        notification.error({
            message: 'Lỗi lấy dữ liệu tiến trình thu thập câu hỏi' ,
            description: 'Chi tiết lỗi: ' + error,
        });
    }
}

const inProgressCrawlQuestions = async () => {
    let isDone = false;
    await new Promise(resolve => setTimeout(resolve, 15000));
    while (!isDone) {
        try {
            const responseData = await getDataInProgressCrawlQuestions();

            if (responseData.status === 'DONE') {
                isDone = true;
                setVisibleProgressCrawlQuestions(false);
                setDisabledButtonCrawlQuestions(false);
                setPercentProgressCrawlLaws(100);
                notification.success({
                    message: 'Thu thập câu hỏi hoàn tất!',
                    description: 'Hệ thống đã thu thập xong câu hỏi, bạn có thể xem kết quả ở biểu đồ.',
                });

                const element = document.getElementById('pie-new-questions-crawled');
                while (element.firstChild) {
                    element.removeChild(element.firstChild);
                }

                await showPieNewQuestionsCrawled();
            } else if (responseData.status === 'IN_PROGRESS'){
                setPercentProgressCrawlQuestions(4 + 3.5*responseData.numberFieldCrawled);
                await new Promise(resolve => setTimeout(resolve, 5000));
            }
        }catch (e) {
            notification.error({
                message: 'Lỗi cập nhật tiến trình thu thập câu hỏi' ,
                description: 'Chi tiết lỗi: ' + e,
            });
        }
    }
}
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
                    <a-tab-pane key="1" tab="Thống kê văn bản Luật" :force-render="true">
                        <div class="flex items-center space-x-16">
                            <a-button @click="crawlLawsStart" :disabled="disabledButtonCrawlLaws" type="primary"> Thu thập văn bản luật mới </a-button>
                            <a-progress v-if="visibleProgressCrawlLaws === true" :percent="percentProgressCrawlLaws" status="active" :size="[200, 15]" style="width: 400px"></a-progress>
                        </div>
                        <div class="flex items-start justify-between mt-12">
                            <div class="w-2/5">
                                <h3 class="text-xl text-center">Thống kê toàn bộ tập Luật  </h3>
                                <div class="mt-8" id="column-laws-corpus"></div>
                            </div>
                            <div class="w-2/5">
                                <h3 class="text-xl text-center">Thống kê Luật mới thu thập ngày {{ dateNearestLawCrawled }}</h3>
                                <div class="mt-8" id="column-new-laws-crawled"></div>
                            </div>
                        </div>
                    </a-tab-pane>
                    <a-tab-pane key="2" tab="Thống kê câu hỏi Luật" :force-render="true">
                        <div class="flex items-center space-x-16">
                            <a-button @click="crawlQuestionsStart" :disabled="disabledButtonCrawlQuestions" type="primary"> Thu thập câu hỏi luật mới </a-button>
                            <a-progress v-if="visibleProgressCrawlQuestions === true" :percent="percentProgressCrawlQuestions" status="active" :size="[200, 15]" style="width: 400px"></a-progress>
                        </div>
                        <div class="flex items-start justify-between mt-12">
                            <div class="w-1/2">
                                <h3 class="text-xl text-center">Thống kê không gian câu hỏi  </h3>
                                <div class="mt-8" id="pie-questions-corpus"/>
                            </div>
                            <div class="w-1/2">
                                <h3 class="text-xl text-center">Thống kê câu hỏi mới thu thập ngày {{ dateNearestQuestionCrawled }} </h3>
                                <div class="mt-8" id="pie-new-questions-crawled"></div>
                            </div>
                        </div>
                    </a-tab-pane>
                </a-tabs>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
