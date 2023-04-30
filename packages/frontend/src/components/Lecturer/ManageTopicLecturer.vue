<template>
  <div class="flex">
    <div class="inline-block p-2 rounded-md">
      <select
        v-model="selectSchedule"
        class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
        @change="selectHandler"
      >
        <!-- <option
          :key="`key-null`"
          :value="''"
        /> -->
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
      v-if="canEdit"
      class=" rounded ml-auto mr-4 my-2 bg-blue-800 text-white font-sans font-semibold py-2 px-4 cursor-pointer"
      @click="$store.dispatch('url/updateSection', 'topic-import')"
    >
      Thêm đề tài
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
      @expand-row="loadIntroduction"
    >
      <template #expand="item">
        <div
          style="padding: 15px"
        >
          {{ item }}
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
          v-if="checkCanEdit(item.scheduleId)"
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
import SearchInput from 'vue-search-input';
import ConfirmModal from '../Modal/ConfirmModal.vue';
import ScheduleApi from '../../utils/api/schedule';
// Optionally import default styling
import 'vue-search-input/dist/styles.css';

// import 'vue-search-input/dist/styles.css';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';
import { useToast } from 'vue-toast-notification';
import TopicApi from '../../utils/api/topic';

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
    const items = [];
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: headers[0].value,
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    const modulePage = computed(() => store.getters['url/module']);
    const loadToServer = async (options) => {
      loading.value = true;
      const response = await TopicApi.listAllTopics(token, options);
      topics.value = response.data;
      store.state.topic.listTopics = topics.value;
      serverItemsLength.value = response.meta.pagination.total;
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

    const editItem = (item) => {
      store.dispatch('url/updateSection', `${modulePage.value}-update`);
      store.dispatch('url/updateId', item._id);
    };
    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });
    watch(modulePage, async () => { await loadToServer(serverOptions.value); });

    const handleImport = () => {
      store.dispatch('url/updateSection', `${modulePage.value}-import`);
    };

    const selectHandler = () => {
      // this.checkCanEdit(this.selectVal);
      // this.$store.commit('topic/setTopicScheduleId', this.selectVal);
      // await this.$store.dispatch('topic/fetchListTopicByLecturerSchedule', { token: this.token, lecturerId: this.userId, scheduleId: this.selectVal });
    };

    const checkCanEdit = (scheduleId) => {
      if (!scheduleId) return false;
      const schedule = schedules.value.filter((sc) => sc._id === scheduleId)[0];
      if (!schedule) return false;
      const now = Date.now();
      const start = new Date(schedule.startApproveDate);
      const end = new Date(schedule.endApproveDate);
      return (start < now && now < end);
    };

    const showRow = (item) => {
      // if (!checkCanEdit(item.scheduleId)) return;
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

    const selectSchedule = ref('');
    const selectLecturer = ref('');

    const loadIntroduction = async (index) => {
      const expandedItem = topics.value[index];
      console.log('🚀 ~ file: ManageTopicLecturer.vue:216 ~ loadIntroduction ~ expandedItem:', expandedItem);
      if (!expandedItem.data) {
        expandedItem.expandLoading = true;
        expandedItem.data = 'Hihihi';
        expandedItem.expandLoading = false;
      }
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
      checkCanEdit,
      schedules,
      loadIntroduction,
    };
  },
  data () {
    return {
      selectVal: '',
      searchVal: '',
      // topics: [],
      canEdit: false,
      // showConfirmModal: false,
      listSchedules: [],
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
    listTopicsLecturer () {
      const list = this.listTopicsByLecturerSchedule.map((t) => {
        const listStudents = t.students.map((s) => this.listStudents.find((st) => st.code.toString() === s.toString()));
        return {
          ...t, studentInfo: listStudents,
        };
      });
      const newList = list.filter((item) => {
        if (!item.lecturerId) return false;
        return item.lecturerId._id.toString() === this.userId.toString();
      });
      return newList;
    },
  },
  watch: {
    listTopicsByLecturerSchedule: {
      handler () {
        this.topics = this.listTopicsLecturer;
      },
    },
  },
  async mounted () {
    // Fetch list data schedule
    const schedules = await ScheduleApi.listAllSchedule(this.token);
    this.listSchedules = schedules.data;
    // console.log('🚀 ~ file: ManageTopicLecturer.vue:209 ~ mounted ~ listSchedule:', this.listSchedules);
    // await this.$store.dispatch('student/fetchListStudent', this.token);
    // await this.$store.dispatch('schedule/fetchListSchedules', this.token);
    // this.topics = this.listTopicsLecturer;
    // this.selectVal = this.listSchedules[0] ? this.listSchedules[0]._id : null;
    // this.$store.commit('topic/setTopicScheduleId', this.selectVal);
    // if (this.selectVal) { await this.$store.dispatch('topic/fetchListTopicByLecturerSchedule', { token: this.token, lecturerId: this.userId, scheduleId: this.selectVal }); }
    // this.checkCanEdit(this.selectVal);
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
    // async confirmRemove () {
    //   const id = this.removeId;
    //   this.showConfirmModal = false;
    //   try {
    //     const value = {
    //       id,
    //       token: this.token,
    //     };
    //     await this.$store.dispatch('topic/removeTopic', value);
    //     await this.$store.dispatch('topic/fetchListTopicByLecturerSchedule', { token: this.token, lecturerId: this.userId, scheduleId: this.selectVal });
    //     this.$toast.success('Đã xóa thành công!');
    //   } catch (e) {
    //     this.$toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
    //   }

    //   this.removeId = '';
    // },
    async handleRemoveTopic (id) {
      this.removeId = id;
      this.showConfirmModal = true;
    },
    displayLecturer (lecturer) {
      return lecturer ? lecturer.name : '';
    },
    search () {
      if (this.searchVal !== '') {
        const topicFilter = this.listTopicsLecturer.filter((topic) => {
          const re = new RegExp(`\\b${this.searchVal}`, 'gi');
          if (topic.title.match(re)) return true;
          if (topic.code.match(re)) return true;
          for (let i; i < topic.students.length; i += 1) {
            if (topic.students[i]) {
              if (topic.students[i].name.match(re)) return true;
              if (topic.students[i].code.match(re)) return true;
            }
          }
          return false;
        });
        this.topics = topicFilter;
      } else this.topics = this.listTopicsLecturer;
    },
    async selectHandler () {
      this.checkCanEdit(this.selectVal);
      this.$store.commit('topic/setTopicScheduleId', this.selectVal);
      await this.$store.dispatch('topic/fetchListTopicByLecturerSchedule', { token: this.token, lecturerId: this.userId, scheduleId: this.selectVal });
    },
    // checkCanEdit (scheduleId) {
    //   console.log('🚀 ~ file: ManageTopicLecturer.vue:272 ~ checkCanEdit ~ scheduleId:', scheduleId);
    // const schedule = this.listSchedules.filter((sc) => sc._id === scheduleId)[0];
    // if (schedule) {
    //   const now = Date.now();
    //   const start = new Date(schedule.startApproveDate);
    //   const end = new Date(schedule.endApproveDate);
    //   this.canEdit = (start < now && now < end);
    // }
    // },
  },
};
</script>
