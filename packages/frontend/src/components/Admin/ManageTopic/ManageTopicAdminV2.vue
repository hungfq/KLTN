<template>
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
    <div class="w-64 m-4">
      <Multiselect
        v-model="selectLecturer"
        :options="listLecturerSelect"
        :can-deselect="false"
        :searchable="true"
        no-results-text="Không có kết quả"
        no-options-text="Không có lựa lựa chọn"
        :can-clear="false"
        :placeholder="'Giảng viên hướng dẫn'"
        @change="selectHandlerLecturer"
      />
    </div>
    <div class="mx-auto" />
    <div class="flex mt-4 mr-4">
      <div class="mr-2">
        <ButtonAddItem
          :title="'Thêm đề tài'"
          @handle-import="$store.dispatch('url/updateSection', 'topic-import')"
        />
      </div>
      <UploadButtonVue @uploadFileExcel="upload" />
      <ButtonDownloadTemplate :link="`${BASE_API_URL}/api/v2/template?type=Topic`" />
    </div>
  </div>
  <div class="mx-4 mt-2">
    <SearchInput
      v-model="searchVal"
      :search-icon="true"
      @keydown.space.enter="search"
    />
  </div>
  <EasyDataTable
    v-model:server-options="serverOptions"
    :server-items-length="serverItemsLength"
    show-index
    table-class-name="mx-4"
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
        <div
          class="tooltip tooltip-bottom ml-2"
          data-tip="Xóa đề tài"
        >
          <font-awesome-icon
            icon="fa-solid fa-trash-can"
            size="xl"
            @click="handleRemoveTopic(item._id)"
          />
        </div>
      </div>
    </template>
  </EasyDataTable>

  <ConfirmModal
    v-model="showConfirmModal"
    @confirm="confirmRemove(removeId)"
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
import SearchInput from 'vue-search-input';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import UploadButtonVue from '../UploadButton.vue';
import TopicApi from '../../../utils/api/topic';
import SelectStudent from '../../Modal/SelectStudent.vue';
import ButtonDownloadTemplate from '../../common/ButtonDownloadTemplate.vue';
import ButtonAddItem from '../../common/ButtonAddItem.vue';

export default {
  name: 'ManageTopicAdmin',
  components: {
    ConfirmModal,
    SelectStudent,
    UploadButtonVue,
    Multiselect,
    ButtonDownloadTemplate,
    ButtonAddItem,
    SearchInput,
  },
  setup () {
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const store = useStore();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const topics = ref([]);
    const selectSchedule = ref(null);
    const selectLecturer = ref(null);
    const showSelectStudent = ref(false);
    const selectStudentScheduleId = ref(null);
    const listStudentSelected = ref([]);
    const topicStudentId = ref(0);
    const topicStudentLimit = ref(3);
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
      try {
        await TopicApi.deleteTopicById(token, id);
        showConfirmModal.value = false;
        await loadToServer(serverOptions.value);
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
      try {
        if (students.length !== topicStudentLimit.value) {
          $toast.error('Số lượng sinh viên phải bằng số lượng sinh viên quy định trên đề tài!');
          return;
        }
        showSelectStudent.value = false;
        await TopicApi.importStudentToTopic(token, topicStudentId.value, { students });
        $toast.success('Đã cập nhật  danh sách sinh viên thành công!');
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };
    const selectStudents = (item) => {
      selectStudentScheduleId.value = item.scheduleId._id;
      topicStudentId.value = item._id;
      showSelectStudent.value = true;
      listStudentSelected.value = item.list_students;
      topicStudentLimit.value = item.limit;
    };

    const searchVal = ref('');
    const search = async () => {
      serverOptions.value.page = 1;
      if (!searchVal.value || searchVal.value === '') await loadToServer(serverOptions.value);
      else await loadToServer({ ...serverOptions.value, search: searchVal.value });
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
      search,
      selectHandlerSchedule,
      selectHandlerLecturer,
      changeStudents,
      searchVal,
      topicStudentLimit,
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
      const arr = [{ value: 0, label: 'Tất cả giảng viên' }];
      this.listLecturer.forEach((lecturer) => {
        arr.push({ value: lecturer._id, label: lecturer.name });
      });
      return arr;
    },
    listScheduleSelect () {
      const arr = [{ value: 0, label: 'Tất cả các đợt' }];
      this.listSchedules.forEach((schedule) => {
        arr.push({ value: schedule._id, label: schedule.name });
      });
      return arr;
    },
  },
  async mounted () {
    await this.$store.dispatch('lecturer/fetchListLecturer', this.token);
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
    async upload (files) {
      if (files.length > 0) {
        try {
          await this.$store.dispatch('topic/importTopic', { token: this.token, xlsx: files[0] })
            .then((data) => {
              if (data.status === 200 && data.headers.get('Content-Type') === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                this.$toast.error('Đề tài và mã đã tồn tại hoặc dữ liệu không chính xác');
              } else if (data.status === 200) {
                this.$toast.success('Đã nhập thành công!');
              }
            });
        } catch (e) {
          this.$toast.error('File không đúng chuẩn!');
        }
      } else {
        this.$toast.error('File không tồn tại');
      }
    },
    handleNewButtonClick () {
      this.$refs.submitBtn.click();
    },
  },
};
</script>
