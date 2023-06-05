<template>
  <div class="flex flex-col">
    <div class="tabs tabs-boxed bg-white">
      <a
        v-for="option in headerTabs"
        :key="option.code"
        class="tab rounded-md"
        :class="{'tab-active' : option.code === tab}"
        @click="tab= option.code"
      >{{ option.text }}</a>
    </div>
    <div>
      <div class="flex">
        <div class="inline-block p-2 rounded-md">
          <select
            v-model="selectSchedule"
            class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm sm:text-sm"
            @change="selectHandler"
          >
            <option
              :value="'all'"
            >
              Tất cả
            </option>
            <option
              v-for="option in schedules"
              :key="`key-${option._id}`"
              :value="option._id"
            >
              {{ option.code }} : {{ option.name }}
            </option>
          </select>
        </div>
      </div>
      <div class="shadow-md sm:rounded-lg m-4">
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
        </EasyDataTable>
      </div>
    </div>
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

import ScheduleApi from '../../../utils/api/schedule';
import TopicApi from '../../../utils/api/topic';

export default {
  name: 'ManageTopicLecturer',
  components: {
    SearchInput,
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
      { text: 'Sinh vien', value: 'students' },
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

    onMounted(async () => {
      const listAllSchedule = await ScheduleApi.listAllSchedule(token);
      schedules.value = listAllSchedule.data;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    });

    const editItem = async (id) => {
      try {
        store.dispatch('url/updateType', mapValue[tab.value]);
        store.dispatch('url/updateSection', `${modulePage.value}-update`);
        store.dispatch('url/updateId', id);
      } catch (e) {
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

    return {
      headers,
      items,
      itemsSelected,
      loading,
      serverOptions,
      topics,
      serverItemsLength,
      rowItems,
      editItem,
      modulePage,
      handleImport,
      selectSchedule,
      selectLecturer,
      topicShow,
      selectHandler,
      schedules,
      headerTabs,
      tab,
      searchVal,
      search,
    };
  },
};
</script>
