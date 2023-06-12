<template>
  <div class="flex flex-col">
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
          <template #empty-message>
            <div class="text-center text-gray-500">
              Không có dữ liệu
            </div>
          </template>
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
              <div class="m-1 cursor-pointer rounded">
                <button
                  class=" btn btn-primary w-36 py-1"
                  @click="editItem(item._id)"
                >
                  <span class="font-semibold pr-2">Phê duyệt</span>
                  <font-awesome-icon
                    size="xl"
                    :icon="['fas', 'check']"
                  />
                </button>
              </div>
              <div class="m-1 cursor-pointer rounded">
                <button
                  class="btn btn-warning w-36"
                  @click="handleRemoveTopic(item._id)"
                >
                  <span class="font-semibold pr-2 cursor-pointer">Từ chối</span>
                  <font-awesome-icon
                    size="xl"
                    :icon="['fas', 'ban']"
                  />
                </button>
              </div>
            </div>
          </template>
        </EasyDataTable>
      </div>
    </div>
  </div>
  <ConfirmModal
    v-model="showConfirmModal"
    @confirm="confirmRemove"
    @cancel="showConfirmModal=false"
  >
    <template #title>
      Xác nhận
    </template>
    <div>Bạn có xác nhận từ chối đề tài này ra hội đồng không?</div>
  </ConfirmModal>
  <ConfirmModal
    v-model="showConfirmApproveModal"
    @confirm="confirmApprove"
    @cancel="showConfirmApproveModal=false"
  >
    <template #title>
      Xác nhận phê duyệt
    </template>
    <div>Bạn có xác nhận phê duyệt đề tài ra hội đồng không?</div>
  </ConfirmModal>
</template>

<script>
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { useStore } from 'vuex';
import { useToast } from 'vue-toast-notification';
import SearchInput from 'vue-search-input';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import 'vue-search-input/dist/styles.css';
import Multiselect from '@vueform/multiselect';

import ScheduleApi from '../../../utils/api/schedule';
import TopicApi from '../../../utils/api/topic';

export default {
  name: 'ManageTopicLecturer',
  components: {
    SearchInput,
    ConfirmModal,
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
    ];
    const tab = ref('advisor');
    const searchVal = ref('');
    const selectSchedule = ref('all');
    const selectLecturer = ref('');
    const showConfirmApproveModal = ref(false);
    const approveId = ref('');
    const declineId = ref('');
    const items = [];
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: 'updated_at',
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    const modulePage = computed(() => store.getters['url/module']);
    const loadToServer = async (options) => {
      loading.value = true;
      let response;
      if (!selectSchedule.value || selectSchedule.value === 'all') {
        if (tab.value === 'advisor') {
          response = await TopicApi.listTopicAdvisorApprove(token, options);
        } else {
          response = await TopicApi.listTopicCriticalApprove(token, options);
        }
      } else if (tab.value === 'advisor') {
        response = await TopicApi.listTopicAdvisorApprove(token, options, selectSchedule.value);
      } else {
        response = await TopicApi.listTopicCriticalApprove(token, options, selectSchedule.value);
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
        approveId.value = id;
        showConfirmApproveModal.value = true;
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

    const showConfirmModal = ref(false);
    const confirmRemove = async () => {
      showConfirmModal.value = false;
      try {
        if (tab.value === 'advisor') {
          await TopicApi.topicAdvisorDecline(token, declineId.value);
        } else {
          await TopicApi.topicCriticalDecline(token, declineId.value);
        }
        $toast.success('Đã từ chối thành công!');
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
      }
    };
    const confirmApprove = async () => {
      showConfirmApproveModal.value = false;
      try {
        if (tab.value === 'advisor') {
          await TopicApi.topicAdvisorApprove(token, approveId.value);
        } else {
          await TopicApi.topicCriticalApprove(token, approveId.value);
        }
        $toast.success('Đã phê duyệt thành công đề tài ra hội đồng');
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
      }
    };

    const handleRemoveTopic = (id) => {
      try {
        declineId.value = id;
        showConfirmModal.value = true;
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
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
        committeePresidentGrade: topic.committeePresidentGrade || 0,
        committeeSecretaryGrade: topic.committeeSecretaryGrade || 0,
        advisorLecturerGrade: topic.advisorLecturerGrade || 0,
        criticalLecturerGrade: topic.criticalLecturerGrade || 0,
        criticalLecturerApprove: topic.criticalLecturerApprove || false,
        advisorLecturerApprove: topic.advisorLecturerApprove || false,
      }));
    });

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
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
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
      editItem,
      modulePage,
      handleImport,
      showConfirmModal,
      confirmRemove,
      selectSchedule,
      selectLecturer,
      topicShow,
      selectHandler,
      schedules,
      headerTabs,
      tab,
      searchVal,
      search,
      showConfirmApproveModal,
      confirmApprove,
      declineId,
      listScheduleSelect,
      selectHandlerSchedule,
      handleRemoveTopic,
    };
  },
};
</script>
