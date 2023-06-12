<template>
  <div class="flex flex-col">
    <template v-if="!open">
      <div class="relative">
        <img
          class="w-fit h-fit"
          :src="imageUrl"
        >
        <button
          class="btn btn-primary absolute bottom-0 left-0 !py-0"
          @click="updateModules('topic_result')"
        >
          Xem kết quả
        </button>
        <button
          class="btn btn-primary absolute bottom-0 right-0 !py-0"
          @click="updateModules('topic_proposal')"
        >
          Đề xuất đề tài
        </button>
      </div>
    </template>
    <template v-if="open">
      <div>
        <div class="flex">
          <div class="w-[300px] mt-2 ml-4">
            <Multiselect
              v-if="scheduleSelectOption.length > 2"
              v-model="selectSchedule"
              :options="scheduleSelectOption"
              :can-deselect="false"
              no-results-text="Không có kết quả"
              no-options-text="Không có lựa lựa chọn"
              :allow-empty="false"
              :clear-on-select="false"
              placeholder="Chọn đợt đăng ký"
              :can-clear="false"
              :preselect-first="true"
              @change="selectHandler"
            />
          </div>
        </div>
        <div class="2xl:min-h-[770px] lg:min-h-[590px] m-4">
          <SearchInput
            v-model="searchVal"
            :search-icon="true"
            @keydown.space.enter="search"
          />
          <EasyDataTable
            v-model:server-options="serverOptions"
            header-text-direction="center"
            body-text-direction="center"
            :server-items-length="serverItemsLength"
            show-index
            :headers="headers"
            :items="topicShow"
            :loading="loading"
            buttons-pagination
          >
            <template #expand="item">
              <div
                class="flex items-start my-4"
              >
                <div class="font-bold">
                  Danh sách sinh viên
                </div>
                <div class="flex ml-12">
                  <div
                    v-for="student in item.list_students"
                    :key="`${student.code}`"
                    class="mt-2 flex flex-col mr-8"
                  >
                    <span class="font-semibold">Mã sinh viên: {{ student.code }} </span>
                    <span class="font-semibold">Tên: {{ student.name }}</span>
                    <span class="font-semibold">Email: {{ student.name }}</span>
                  </div>
                </div>
              </div>
            </template>
            <template #item-operation="item">
              <div
                class="m-1 tooltip tooltip-bottom cursor-pointer rounded-xl"
                data-tip="Đã đủ số lượng đăng ký"
              >
                <button
                  v-if="item.current_register >= item.limit"
                  class="btn btn-primary"
                  :disabled="true"
                >
                  <span class="font-semibold px-1">Đăng ký</span>
                  <font-awesome-icon
                    size="xl"
                    :icon="['fas', 'check']"
                  />
                </button>
                <button
                  v-else
                  class="btn btn-primary"
                  @click="editItem(item)"
                >
                  <span class="font-semibold px-1">Đăng ký</span>
                  <font-awesome-icon
                    size="xl"
                    :icon="['fas', 'check']"
                  />
                </button>
              </div>
            </template>
            <template #item-limit="item">
              <div class="m-1 flex items-center  justify-center ml-2">
                {{ `${item.current_register} / ${item.limit}` }}
              </div>
            </template>
            <template #empty-message>
              <div class="text-center text-gray-500">
                Không có dữ liệu
              </div>
            </template>
          </EasyDataTable>
        </div>
      </div>
    </template>
  </div>
  <ConfirmModal
    v-model="showConfirmModal"
    @confirm="confirmRemove"
    @cancel="showConfirmModal=false"
  >
    <template #title>
      Xác nhận
    </template>
    <div>Bạn có xác nhận đăng ký đề này không?</div>
  </ConfirmModal>
</template>

<script>
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';
import { useToast } from 'vue-toast-notification';
import SearchInput from 'vue-search-input';
import Multiselect from '@vueform/multiselect';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import 'vue-search-input/dist/styles.css';
import IconTooltip from '../../common/IconTooltip.vue';

import TopicApi from '../../../utils/api/topic';

export default {
  name: 'ManageTopicLecturer',
  components: {
    SearchInput,
    ConfirmModal,
    Multiselect,
    IconTooltip,
  },
  props: {
    open: {
      type: Boolean,
      default: false,
    },
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
      { text: 'Giảng viên hướng dẫn', value: 'lecturer' },
      { text: 'Giảng viên phản biện', value: 'critical' },
      { text: 'Số lượng đăng ký', value: 'limit' },
      { text: 'Hành động', value: 'operation' },
    ];

    const tab = ref('advisor');
    const searchVal = ref('');
    const selectSchedule = ref(0);
    const selectLecturer = ref('');
    const items = [];
    const registerId = ref('');
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: 'updated_at',
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    // const currentLecturerId = store.getters['auth/userId'];
    // const currentCodeStudent = store.getters['auth/code'];
    const modulePage = computed(() => store.getters['url/module']);
    const loadToServer = async (options) => {
      loading.value = true;
      let response;
      if (!selectSchedule.value || selectSchedule.value === 0) {
        const scheduleIds = schedules.value.map((s) => s._id);
        response = await TopicApi.listAllTopicsByScheduleIds(token, scheduleIds, options);
      } else {
        response = await TopicApi.listAllTopicsByScheduleId(token, selectSchedule.value, options);
      }
      if (response) {
        topics.value = response.data;
        store.state.topic.listTopics = topics.value;
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

    const fetchListScheduleRegisterByStudentId = async () => {
      const schedulesToday = store.getters['schedule/listScheduleRegisterStudent'];
      return schedulesToday;
    };

    onMounted(async () => {
      schedules.value = await fetchListScheduleRegisterByStudentId();
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
        errorHandler(e);
      }
    });

    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });
    watch(tab, async () => {
      await loadToServer(serverOptions.value);
    }, { deep: true });
    watch(modulePage, async () => { await loadToServer(serverOptions.value); });
    watch(selectSchedule.value, async () => { await loadToServer(serverOptions.value); });

    const selectHandler = async (value) => {
      if (!value) selectSchedule.value = 0;
      else selectSchedule.value = value;
      await loadToServer(serverOptions.value);
    };

    const showConfirmModal = ref(false);
    const editItem = (item) => {
      registerId.value = item._id;
      showConfirmModal.value = true;
    };
    const confirmRemove = async (id) => {
      showConfirmModal.value = false;
      try {
        await TopicApi.addRegisterTopicNew(token, registerId.value);
        $toast.success('Đã đăng ký thành công, vui lòng xem kết quả!');
      } catch (e) {
        errorHandler(e);
        // if (e.response.data.error.message === 'You are already register!') {
        //   $toast.error('Không thể đăng ký. Bạn đã tồn tại đăng ký trong đợt này!');
        // } else {
        //   $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên ');
        // }
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
        list_students: topic.list_students,
        limit: topic.limit,
        current_register: topic.current,
      }));
    });

    const scheduleSelectOption = computed(() => {
      const arr = [{ value: 0, label: 'Tất cả' }];
      if (!schedules.value) return arr;
      schedules.value.forEach((schedule) => {
        arr.push({ value: schedule._id, label: schedule.name });
      });
      return arr;
    });

    const updateModules = (module) => {
      store.dispatch('url/updateModule', module);
      store.dispatch('url/updateSection', `${module}-list`);
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
      showConfirmModal,
      confirmRemove,
      selectSchedule,
      selectLecturer,
      topicShow,
      selectHandler,
      schedules,
      tab,
      searchVal,
      search,
      scheduleSelectOption,
      updateModules,
    };
  },
  data () {
    return {
      // selectVal: '',
      // searchVal: '',
      // topics: [],
      canEdit: false,
      // showConfirmModal: false,
      // listSchedules: [],
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('topic', [
      'listTopicsByLecturerSchedule',
    ]),
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    ...mapGetters('student', [
      'studentId', 'studentEmail', 'student', 'listStudents',
    ]),
    ...mapGetters('schedule', [
      'listSchedules',
    ]),
    imageUrl () {
      const imageUrl = new URL('/src/assets/images/not_register.png', import.meta.url).href;
      return imageUrl;
    },
  },
  methods: {
    async handleUpdateTopic (id) {
      await this.$store.dispatch('url/updateSection', `${this.module}-update`);
      await this.$store.dispatch('url/updateId', id);
    },
    async handleShowTopic (id) {
      await this.$store.dispatch('url/updateSection', `${this.module}-view`);
      await this.$store.dispatch('url/updateId', id);
    },
    async handleRemoveTopic (id) {
      this.removeId = id;
      this.showConfirmModal = true;
    },
  },
};
</script>
<style>
--easy-table-header-item-padding: 10px 15px;
</style>
