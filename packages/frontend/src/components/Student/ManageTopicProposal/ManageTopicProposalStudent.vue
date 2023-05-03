<template>
  <template v-if="!open">
    <div class="py-2 mx-2 font-medium text-red-600 ">
      Hiện tại đang không có đợt đề xuất, vui lòng chọn mục khác!
    </div>
  </template>
  <template v-if="open">
    <div class="flex">
      <div class="inline-block p-2 rounded-md">
        <select
          v-model="selectSchedule"
          class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm sm:text-sm"
          @change="selectHandler"
        >
          <option
            v-for="option in schedules"
            :key="`key-${option._id}`"
            :value="option._id"
          >
            {{ option.code }} : {{ option.name }}
          </option>
        </select>
      </div>
      <div
        class=" rounded ml-auto mr-4 my-2 bg-blue-800 text-white font-sans font-semibold py-2 px-4 cursor-pointer"
        @click="$store.dispatch('url/updateSection', 'topic_proposal-import')"
      >
        Thêm đề xuất đề tài
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
          <div class="flex">
            <img
              src="https://cdn-icons-png.flaticon.com/512/1827/1827951.png"
              class="operation-icon w-6 h-6 mx-2 cursor-pointer"
              @click="editItem(item._id)"
            >
            <font-awesome-icon
              icon="fa-solid fa-trash-can"
              size="2xl"
              @click="handleRemoveTopicProposal(item._id)"
            />
          </div>
        </template>
      </EasyDataTable>
    </div>
  </template>
  <ConfirmModal
    v-model="showConfirmModal"
    @confirm="confirmRemove"
    @cancel="showConfirmModal=false"
  >
    <template #title>
      Xác nhận
    </template>
    <div>Bạn có xác nhận xóa đề xuất này?</div>
  </ConfirmModal>
</template>

<script>

import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';
import SearchInput from 'vue-search-input';
import { useToast } from 'vue-toast-notification';
import 'vue-search-input/dist/styles.css';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import ScheduleApi from '../../../utils/api/schedule';
import TopicProposalApi from '../../../utils/api/topic_proposal';

export default {
  name: 'ManageTopicProposalStudent',
  components: {
    SearchInput,
    ConfirmModal,
  },
  props: {
    open: {
      type: Boolean,
      default: true,
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
    const removeId = ref('');
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
      if (!selectSchedule.value) return;
      const response = await TopicProposalApi.listAllTopicsByCreated(token, selectSchedule.value, options);
      if (response) {
        topics.value = response.data;
        store.state.topic.listTopicProposalStudent = topics.value;
        if (response.meta) {
          serverItemsLength.value = response.meta.pagination.total;
        } else {
          serverItemsLength.value = 1;
        }
      }
      loading.value = false;
    };

    const $toast = useToast();

    const fetchListScheduleProposalByStudentId = async () => {
      const listAllScheduleToday = await ScheduleApi.listScheduleToday(token);
      const scheduleToday = listAllScheduleToday.proposal;
      const schedulesTodayRegister = scheduleToday.filter((schedule) => schedule.students.includes(currentCodeStudent));
      return schedulesTodayRegister;
    };

    onMounted(async () => {
      schedules.value = await fetchListScheduleProposalByStudentId();
      if (schedules.value.length > 0) {
        store.state.listScheduleProposalStudent = schedules.value;
        selectSchedule.value = schedules.value[0]._id;
      }
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
    const showRow = (item) => {
      // if (!checkCanEdit(item.scheduleId)) return;
      store.dispatch('url/updateId', item._id);
      store.dispatch('url/updateSection', `${modulePage.value}-view`);
    };

    const showConfirmModal = ref(false);
    const editItem = async (id) => {
      store.dispatch('url/updateSection', `${modulePage.value}-update`);
      store.dispatch('url/updateId', id);
    };
    const confirmRemove = async (id) => {
      showConfirmModal.value = false;
      try {
        await TopicProposalApi.removeTopicProposal(token, removeId.value);
        $toast.success('Đã xóa thành công, vui lòng xem kết quả!');
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên ');
      }
    };

    const search = async () => {
      if (!searchVal.value || searchVal.value === '') await loadToServer(serverOptions.value);
      else await loadToServer({ ...serverOptions.value, search: `${searchVal.value}` });
    };
    const handleRemoveTopicProposal = async (id) => {
      removeId.value = id;
      console.log('🚀 ~ file: ManageTopicProposalStudent.vue:229 ~ handleRemoveTopicProposal ~ removeId.value:', removeId.value);
      showConfirmModal.value = true;
    };

    const topicShow = computed(() => {
      if (!topics.value) return [];
      return topics.value.map((topic) => ({
        _id: topic._id,
        code: topic.code,
        title: topic.title,
        lecturer: topic.lecturerId?.name || '',
        critical: topic.criticalLecturerId?.name || '',
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
      removeId,
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
      handleRemoveTopicProposal,
      showRow,
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('student', [
      'listStudents',
    ]),
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    ...mapGetters('topic_proposal', [
      'listTopicProposalCreated', 'topicScheduleId',
    ]),
    ...mapGetters('schedule', [
      'listScheduleProposalStudent',
    ]),
  },
  methods: {
    async handleUpdateTopicProposal (id) {
      await this.$store.dispatch('url/updateSection', `${this.module}-update`);
      await this.$store.dispatch('url/updateId', id);
    },
  },
};
</script>
