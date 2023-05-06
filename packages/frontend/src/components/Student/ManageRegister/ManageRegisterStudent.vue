<template>
  <div class="flex flex-col">
    <div>
      <div class="flex">
        <div class="inline-block p-2 rounded-md">
          <select
            v-model="selectSchedule"
            class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm sm:text-sm"
            @change="selectHandler"
          >
            <option
              :key="`key-null`"
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
          <template #expand="item">
            <div
              class="flex"
            >
              <div class="flex flex-col grow">
                <div>Danh sach sinh vien</div>
                <ul>
                  <li
                    v-for="student in item.students"
                    :key="`${student}`"
                  >
                    {{ student }}
                  </li>
                </ul>
              </div>
              <div class="flex flex-col grow">
                <div>Diem so</div>
                <div class="flex">
                  <div> Giao vien huong dan: </div>
                  <div>{{ item.advisorLecturerGrade }}</div>
                </div>
                <div class="flex">
                  <div> Giao vien phan vien: </div>
                  <div>{{ item.criticalLecturerGrade }}</div>
                </div>
                <div class="flex">
                  <div>Chu tich hoi dong: </div>
                  <div>{{ item.committeePresidentGrade }}</div>
                </div>
                <div class="flex">
                  <div> Thu ky hoi dong: </div>
                  <div>{{ item.advisorLecturerGrade }}</div>
                </div>
              </div>
              <div class="flex flex-col grow">
                <div>Chap thuan ra hoi dong</div>
                <div class="flex">
                  <div> Giao vien huong dan: </div>
                  <div>{{ item.advisorLecturerApprove ? 'Dong y' : 'Khong dong y' }}</div>
                </div>
                <div class="flex">
                  <div> Giao vien phan bien: </div>
                  <div>{{ item.criticalLecturerApprove ? 'Dong y' : 'Khong dong y' }}</div>
                </div>
              </div>
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
            <div class="m-1 cursor-pointer rounded-xl">
              <button
                class=" text-white bg-indigo-600 p-1 w-24"
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
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import 'vue-search-input/dist/styles.css';

import ScheduleApi from '../../../utils/api/schedule';
import TopicApi from '../../../utils/api/topic';

export default {
  name: 'ManageTopicLecturer',
  components: {
    SearchInput,
    ConfirmModal,
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
      // { text: 'Sinh vien', value: 'students' },
      { text: 'Giảng viên hướng dẫn', value: 'lecturer' },
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
    const currentCodeStudent = store.getters['auth/code'];
    const modulePage = computed(() => store.getters['url/module']);
    const loadToServer = async (options) => {
      loading.value = true;
      let response;
      if (!selectSchedule.value || selectSchedule.value === 'all') {
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

    const fetchListScheduleRegisterByStudentId = async () => {
      const listAllScheduleToday = await ScheduleApi.listScheduleToday(token);
      const scheduleToday = listAllScheduleToday.register;
      const schedulesTodayRegister = scheduleToday.filter((schedule) => schedule.students.includes(currentCodeStudent));
      return schedulesTodayRegister;
    };

    onMounted(async () => {
      schedules.value = await fetchListScheduleRegisterByStudentId();
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    });

    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });
    watch(tab, async () => {
      await loadToServer(serverOptions.value);
    }, { deep: true });
    watch(modulePage, async () => { await loadToServer(serverOptions.value); });

    const selectHandler = async () => {
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
        if (e.response.data.error.message === 'You are already register!') {
          $toast.error('Không thể đăng ký. Bạn đã tồn tại đăng ký trong đợt này!');
        } else {
          $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên ');
        }
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
      headerTabs,
      tab,
      searchVal,
      search,
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