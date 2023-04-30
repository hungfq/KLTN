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
          @click-row="showRow"
        >
          <template #item-operation="item">
            <div
              class="flex"
            >
              <img
                src="https://cdn-icons-png.flaticon.com/512/1827/1827951.png"
                class="operation-icon w-6 h-6 mx-2 cursor-pointer"
                @click="editItem(item)"
              >
              <font-awesome-icon
                icon="fa-solid fa-trash-can"
                size="2xl"
                @click="handleRemoveSchedule(item._id)"
              />
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
    <div>Bạn có xác nhận xóa đề tài này không?</div>
  </ConfirmModal>
</template>

<script>
// import { mapState, mapGetters } from 'vuex';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';
import { useToast } from 'vue-toast-notification';
import SearchInput from 'vue-search-input';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
// Optionally import default styling
import 'vue-search-input/dist/styles.css';

// import 'vue-search-input/dist/styles.css';
import ScheduleApi from '../../../utils/api/schedule';
import TopicProposalApi from '../../../utils/api/topic_proposal';

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
      { text: 'Sinh vien', value: 'students' },
      { text: 'Giảng viên phản biện', value: 'critical' },
      { text: 'Hành động', value: 'operation' },
    ];

    const searchVal = ref('');
    const selectSchedule = ref('all');
    const selectLecturer = ref('');
    const items = [];
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: headers[0].value,
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    const modulePage = computed(() => store.getters['url/module']);

    const $toast = useToast();
    const loadToServer = async (options) => {
      try {
        loading.value = true;
        let response;
        if (!selectSchedule.value || selectSchedule.value === 'all') {
          response = await TopicProposalApi.listAllTopicsByLecturer(token, options);
        } else {
          response = await TopicProposalApi.listAllTopicsByLecturer(token, options, selectSchedule.value);
        }
        console.log(topics);
        console.log(topics.value);
        if (response) {
          topics.value = response.data;
          console.log('🚀 ~ file: ManageProposalLecturerV2.vue:141 ~ loadToServer ~ topics.value:', topics.value);
          store.state.topic.listTopics = topics.value;
          if (response.meta) {
            serverItemsLength.value = response.meta.pagination.total;
          } else {
            serverItemsLength.value = 1;
          }
        }
        loading.value = false;
      } catch (e) {
        loading.value = false;
        console.log(e.message);
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };

    onMounted(async () => {
      const listAllSchedule = await ScheduleApi.listScheduleApproveLecturer(token);
      schedules.value = listAllSchedule.data;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    });

    const editItem = (item) => {
      store.dispatch('url/updateSection', `${modulePage.value}-update`);
      store.dispatch('url/updateId', item._id);
    };
    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });
    watch(modulePage, async () => { await loadToServer(serverOptions.value); });

    const handleImport = () => {
      store.dispatch('url/updateSection', `${modulePage.value}-import`);
    };

    const selectHandler = async () => {
      await loadToServer(serverOptions.value);
    };

    const showRow = (item) => {
      store.dispatch('url/updateId', item._id);
      store.dispatch('url/updateSection', `${modulePage.value}-view`);
    };

    const showConfirmModal = ref(false);
    const confirmRemove = async (id) => {
      showConfirmModal.value = false;
      try {
        $toast.success('Đã xóa thành công!');
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
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
      }));
    });

    return {
      headers,
      items,
      showRow,
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
      searchVal,
      search,
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
