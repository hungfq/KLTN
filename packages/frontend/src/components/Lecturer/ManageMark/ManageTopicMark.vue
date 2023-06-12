<template>
  <div
    class="flex flex-col"
  >
    <template v-if=" !loading &&!isInProcessMark ">
      <div class="relative h-2/3">
        <img
          class="w-fit h-full"
          :src="imageUrl"
        >
        <button
          class="btn btn-primary absolute bottom-0 left-0 !py-0"
          @click="changePage('topic')"
        >
          Xem đề tài
        </button>
        <button
          class="btn btn-primary absolute bottom-0 right-0 !py-0"
          @click="changePage('topic_approve')"
        >
          Đề xuất đề tài
        </button>
      </div>
    </template>
    <template v-else>
      <div class="mt-2 bg-slate-100 py-2">
        <div class="tabs ml-4">
          <a
            v-for="option in headerTabs"
            :key="option.code"
            class="tab tag-lg tab-lifted min-w-[100px] text-blue-900 font-semibold"
            :class="{'tab-active' : option.code === tab}"
            @click="tab= option.code"
          >{{ option.text }}</a>
        </div>
      </div>
      <div>
        <div class="flex">
          <div class="w-64 m-4">
            <Multiselect
              v-model="selectSchedule"
              :options="listScheduleSelect"
              :searchable="true"
              :can-clear="false"
              :can-deselect="false"
              no-results-text="Không có kết quả"
              no-options-text="Không có lựa lựa chọn"
              :placeholder="'Đợt đăng ký'"
              @change="selectHandlerSchedule"
            />
          </div>
        </div>
        <div
          v-if="!loading"
          class="shadow-md sm:rounded-lg m-4"
        >
          <SearchInput
            v-model="searchVal"
            :search-icon="true"
            @keydown.space.enter="search"
          />
          <EasyDataTable
            v-model:items-selected="itemsSelected"
            v-model:server-options="serverOptions"
            :server-items-length="serverItemsLength"
            show-index
            :headers="headers"
            :items="topicShow"
            :loading="loading"
            buttons-pagination
            :rows-items="rowItems"
          >
            <template #item-import-export="students">
              <div class-="flex flex-col">
                <ul>
                  <li
                    v-for="student in students"
                    :key="`${student}`"
                  >
                    {{ student }}
                  </li>
                </ul>
              </div>
            </template>
            <template #item-operation="item">
              <div
                class="flex flex-col"
              >
                <div class="m-1 cursor-pointer rounded-xl">
                  <button
                    class=" btn btn-primary "
                    @click="editItem(item._id)"
                  >
                    <span class="font-semibold px-1">Chấm điểm</span>
                    <font-awesome-icon
                      size="xl"
                      :icon="['fas', 'bullseye']"
                    />
                  </button>
                </div>
              </div>
            </template>
            <template #empty-message>
              <div class="text-center text-gray-500">
                Không có dữ liệu
              </div>
            </template>
          </EasyDataTable>
        </div>
        <loading-process v-else />
      </div>
    </template>
  </div>
</template>

<script>
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { useStore } from 'vuex';
import { useToast } from 'vue-toast-notification';
import SearchInput from 'vue-search-input';
import 'vue-search-input/dist/styles.css';
import Multiselect from '@vueform/multiselect';
import LoadingProcess from '../../common/Loading.vue';

// import ScheduleApi from '../../../utils/api/schedule';
import TopicApi from '../../../utils/api/topic';

export default {
  name: 'ManageTopicLecturer',
  components: {
    SearchInput,
    LoadingProcess,
    Multiselect,
  },
  setup () {
    const store = useStore();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    // Init value
    const topics = ref([]);
    const schedules = ref([]);
    const headers = [
      { text: 'Mã số', value: 'code', sortable: true },
      { text: 'Tên đề tài ', value: 'title', sortable: true },
      { text: 'Sinh viên', value: 'students' },
      { text: 'Hành động', value: 'operation' },
    ];

    const headerTabs = [
      { code: 'advisor', text: 'Hướng dẫn' },
      { code: 'critical', text: 'Phản biện' },
      { code: 'secretary', text: 'Thư ký' },
      { code: 'president', text: 'Chủ tịch' },
    ];

    const mapValue = {
      advisor: 'LT',
      critical: 'CR',
      secretary: 'SE',
      president: 'PD',
    };

    const tab = ref('advisor');
    const searchVal = ref('');
    const selectSchedule = ref('all');
    const selectLecturer = ref('');
    const items = [];
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: 'updated_at',
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    const scheduleMark = store.getters['schedule/listScheduleMarkLecturer'];
    const modulePage = computed(() => store.getters['url/module']);
    store.dispatch('url/updateType', mapValue[tab.value]);
    // TODO: Update API get mark and update mark in committee
    const loadToServer = async (options) => {
      loading.value = true;
      let response;
      // Get all topic need mark grade
      if (!selectSchedule.value || selectSchedule.value === 'all') {
        response = await TopicApi.listAllTopicsMarkGrade(token, options, mapValue[tab.value]);
      } else { // Get topic have scheduleID
        response = await TopicApi.listAllTopicsMarkGrade(token, options, mapValue[tab.value], selectSchedule.value);
      }
      if (response) {
        topics.value = response.data;
        store.state.topic.listTopicPermitRegister = topics.value;
        if (response.meta) {
          serverItemsLength.value = response.meta.pagination.total;
        } else {
          serverItemsLength.value = 1;
        }
      }
      loading.value = false;
    };

    const $toast = useToast();

    const errorHandler = (e) => {
      if (e.response.data.error.code === 400) $toast.error(e.response.data.error.message);
      else { $toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    };

    onMounted(async () => {
      // const listAllSchedule = await ScheduleApi.listAllSchedule(token);
      schedules.value = scheduleMark;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        errorHandler(e);
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    });

    const editItem = async (id) => {
      try {
        store.dispatch('url/updateType', mapValue[tab.value]);
        store.dispatch('url/updateSection', `${modulePage.value}-update`);
        store.dispatch('url/updateId', id);
      } catch (e) {
        errorHandler (e);
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };

    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });
    watch(tab, async () => {
      await loadToServer(serverOptions.value);
    }, { deep: true });
    watch(modulePage, async () => { await loadToServer(serverOptions.value); });

    const handleImport = () => {
      store.dispatch('url/updateSection', `${modulePage.value}-import`);
    };

    const selectHandler = async () => {
      await loadToServer(serverOptions.value);
    };

    const search = async () => {
      if (!searchVal.value || searchVal.value === '') await loadToServer(serverOptions.value);
      else await loadToServer({ ...serverOptions.value, search: `${searchVal.value}` });
    };

    const isInProcessMark = computed(() => schedules.value.length > 0);

    const topicShow = computed(() => {
      if (!topics.value) return [];
      return topics.value.map((topic) => ({
        _id: topic._id,
        code: topic.code,
        title: topic.title,
        lecturer: topic.lecturerId.name || '',
        critical: topic.criticalLecturerId.name || '',
        students: topic.students || [],
        scheduleId: topic.scheduleId?._id || null,
      }));
    });

    const imageUrl = computed(() => {
      const imageUrlBg = new URL('/src/assets/images/not_mark.png', import.meta.url).href;
      return imageUrlBg;
    });

    const changePage = (module) => {
      store.dispatch('url/updateModule', module);
      store.dispatch('url/updateSection', `${module}-list`);
    };

    const listScheduleSelect = computed(() => {
      const arr = [{ value: 0, label: 'Tất cả các đợt' }];
      schedules.value.forEach((schedule) => {
        arr.push({ value: schedule._id, label: `${schedule.code}` });
      });
      return arr;
    });

    const selectHandlerSchedule = async (value) => {
      selectSchedule.value = value;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        errorHandler (e);
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };

    return {
      headers,
      items,
      itemsSelected,
      loading,
      serverOptions,
      topics,
      serverItemsLength,
      rowItems,
      changePage,
      editItem,
      modulePage,
      handleImport,
      selectSchedule,
      selectLecturer,
      topicShow,
      selectHandler,
      schedules,
      imageUrl,
      headerTabs,
      tab,
      searchVal,
      search,
      isInProcessMark,
      listScheduleSelect,
      selectHandlerSchedule,
    };
  },
};
</script>
