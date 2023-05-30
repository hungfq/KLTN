<template>
  <div class="flex">
    <div class="w-64 m-4">
      <Multiselect
        v-model="selectSchedule"
        :options="listScheduleSelect"
        :searchable="true"
        :can-clear="false"
        @change="selectHandlerSchedule"
      />
    </div>
    <div class="w-64 m-4">
      <Multiselect
        v-model="selectLecturer"
        :options="listLecturerSelect"
        :searchable="true"
        :can-clear="false"
        @change="selectHandlerLecturer"
      />
    </div>
    <div class="mx-auto" />
    <div class="inline-block p-2 rounded-md">
      <div
        class=" rounded ml-auto mr-4 my-2 bg-blue-800 text-white font-sans font-semibold py-2 px-4 cursor-pointer"
        @click="$store.dispatch('url/updateSection', 'topic-import')"
      >
        Thêm đề tài
      </div>
    </div>
    <div class="inline-block p-2 rounded-md">
      <UploadButtonVue @uploadFileExcel="upload" />
    </div>
    <div class="inline-block p-2 rounded-md mt-4">
      <a
        class=" rounded ml-auto mr-4 my-2 bg-gray-800 text-white font-sans font-semibold py-2 px-4 cursor-pointer"
        href="http://localhost:8001/api/v2/template?type=Topic"
      >Tải mẫu tệp excel</a>
    </div>
  </div>
  <div class="shadow-md sm:rounded-lg m-4">
    <EasyDataTable
      v-model:server-options="serverOptions"
      :server-items-length="serverItemsLength"
      show-index
      :headers="headers"
      :items="topicShow"
      :loading="loading"
      buttons-pagination
      :rows-items="rowItems"
    >
      <template #item-operation="item">
        <div class="flex">
          <div
            class="tooltip tooltip-bottom px-3"
            data-tip="Chọn sinh viên"
          >
            <font-awesome-icon
              class="cursor-pointer"
              :icon="['fas', 'people-group']"
              size="xl"
              @click="selectStudents(item)"
            />
          </div>
          <div
            class="tooltip tooltip-bottom pr-3"
            data-tip="Xem đề tài"
          >
            <font-awesome-icon
              class="cursor-pointer"
              icon="fa-solid fa-eye"
              size="xl"
              @click="showRow(item)"
            />
          </div>
          <div
            class="tooltip tooltip-bottom"
            data-tip="Chỉnh sửa đề tài"
          >
            <font-awesome-icon
              class="cursor-pointer"
              :icon="['fas', 'pen-to-square']"
              size="xl"
              @click="editItem(item)"
            />
          </div>
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
  <SelectStudent
    v-model="showSelectStudent"
    :schedule-id="selectStudentScheduleId"
    :type="'TOPIC'"
    :selected="listStudentSelected"
    :enabled-excel="true"
    @change-students="changeStudents"
  />
</template>

<script>
// import SearchInput from 'vue-search-input';
// Optionally import default styling
import 'vue-search-input/dist/styles.css';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';
import { useToast } from 'vue-toast-notification';
import Multiselect from '@vueform/multiselect';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import UploadButtonVue from '../UploadButton.vue';
import TopicApi from '../../../utils/api/topic';
import SelectStudent from '../../Modal/SelectStudent.vue';

export default {
  name: 'ManageTopicAdmin',
  components: {
    ConfirmModal,
    SelectStudent,
    UploadButtonVue,
    Multiselect,
  },
  setup () {
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const store = useStore();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const topics = ref([]);
    const selectSchedule = ref(0);
    const selectLecturer = ref(0);
    const showSelectStudent = ref(false);
    const selectStudentScheduleId = ref(null);
    const listStudentSelected = ref([]);
    const headers = [
      { text: 'Mã số', value: 'code', sortable: true },
      { text: 'Tên đề tài ', value: 'title', sortable: true },
      { text: 'Giảng viên hướng dẫn', value: 'lecturer' },
      { text: 'Giảng viên phản biện', value: 'critical' },
      { text: 'Đợt đăng ký', value: 'schedule' },
      { text: 'Hành động', value: 'operation' },
    ];
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
      const response = await TopicApi.listAllTopics(token, options, selectLecturer.value, selectSchedule.value);
      topics.value = response.data;
      store.state.topic.listTopics = topics.value;
      serverItemsLength.value = response.meta.pagination.total;
      loading.value = false;
    };

    const $toast = useToast();

    onMounted(async () => {
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    });

    const showRow = (item) => {
      store.dispatch('url/updateId', item._id);
      store.dispatch('url/updateSection', `${modulePage.value}-view`);
    };

    const editItem = (item) => {
      store.dispatch('url/updateSection', `${modulePage.value}-update`);
      store.dispatch('url/updateId', item._id);
    };
    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });
    watch(modulePage, async () => { await loadToServer(serverOptions.value); });

    const handleImport = () => {
      store.dispatch('url/updateSection', `${modulePage.value}-import`);
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

    const topicShow = computed(() => {
      if (!topics.value) return [];
      return topics.value.map((topic) => ({
        _id: topic._id,
        code: topic.code,
        title: topic.title,
        lecturer: topic.lecturerId.name || '',
        critical: topic.criticalLecturerId.name || '',
        schedule: topic.scheduleId.name || '',
        scheduleId: topic.scheduleId || '',
        list_students: topic.list_students,
      }));
    });
    const selectHandlerSchedule = async (value) => {
      selectSchedule.value = value;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };
    const selectHandlerLecturer = async (value) => {
      selectLecturer.value = value;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };
    const changeStudents = async (students) => {
      //  TODO: Add api update student for topic
      console.log('🚀 ~ file: ManageTopicAdminV2.vue:239 ~ changeStudents ~ students:', students);
      try {
        showSelectStudent.value = false;
        $toast.success('Đã cập nhật  danh sách sinh viên thành công!');
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };
    const selectStudents = (item) => {
      selectStudentScheduleId.value = item.scheduleId._id;
      showSelectStudent.value = true;
      listStudentSelected.value = item.list_students;
    };

    return {
      headers,
      items,
      showRow,
      itemsSelected,
      loading,
      serverOptions,
      topics,
      serverItemsLength,
      selectStudentScheduleId,
      rowItems,
      editItem,
      modulePage,
      handleImport,
      showConfirmModal,
      confirmRemove,
      listStudentSelected,
      selectSchedule,
      selectLecturer,
      topicShow,
      BASE_API_URL,
      showSelectStudent,
      selectStudents,

      selectHandlerSchedule,
      selectHandlerLecturer,
      changeStudents,
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
    ...mapGetters('schedule', [
      'listSchedules',
    ]),
    ...mapGetters('lecturer', [
      'listLecturer',
    ]),
    listLecturerSelect () {
      const arr = [{ value: 0, label: 'Chọn giảng viên' }];
      this.listLecturer.forEach((lecturer) => {
        arr.push({ value: lecturer._id, label: lecturer.name });
      });
      return arr;
    },
    listScheduleSelect () {
      const arr = [{ value: 0, label: 'Chọn đợt' }];
      this.listSchedules.forEach((schedule) => {
        arr.push({ value: schedule._id, label: schedule.name });
      });
      return arr;
    },
  },
  methods: {
    handleUpdateTopic (id) {
      this.$store.dispatch('url/updateSection', `${this.module}-update`);
      this.$store.dispatch('url/updateId', id);
    },
    handleShowTopic (id) {
      this.$store.dispatch('url/updateSection', `${this.module}-view`);
      this.$store.dispatch('url/updateId', id);
    },
    async handleRemoveTopic (id) {
      this.removeId = id;
      this.showConfirmModal = true;
    },
    displayLecturer (lecturer) {
      return lecturer ? lecturer.name : '';
    },
    async upload (files) {
      if (files.length > 0) {
        await this.$store.dispatch('topic/importTopic', { token: this.token, xlsx: files[0] })
          .then((data) => {
            if (data.status === 201) {
              this.$toast.success('Đã nhập thành công!');
            }
          });
        await this.$store.dispatch('topic/fetchListTopicByLecturerSchedule', { token: this.token, lecturerId: this.selectLecturer, scheduleId: this.selectSchedule });
        this.search();
      } else {
        this.$toast.error('File không tồn tại');
      }
    },
    search () {
      if (this.searchVal !== '') {
        const topicFilter = this.listTopicsByLecturerSchedule.filter((topic) => {
          const re = new RegExp(`\\b${this.searchVal}`, 'gi');
          if (topic.title.match(re)) return true;
          if (topic.code.match(re)) return true;
          if (!topic.lecturerId) return false;
          if (topic.lecturerId.name.match(re)) return true;
          return false;
        });

        this.topics = topicFilter;
      } else this.topics = this.listTopicsByLecturerSchedule;
    },
    handleNewButtonClick () {
      this.$refs.submitBtn.click();
    },
  },
};
</script>
