<template>
  <div class="flex">
    <div
      class=" rounded ml-auto mr-4 my-2 bg-blue-800 text-white font-sans font-semibold py-2 px-4 cursor-pointer"
      @click="$store.dispatch('url/updateSection', 'schedule-import')"
    >
      Thêm đợt đăng ký
    </div>
    <form
      class="flex"
      @submit.prevent="upload"
    >
      <input
        id="upload123"
        ref="uploadBtn"
        class="hidden"
        type="file"
        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        @change="handleNewButtonClick"
      >
      <label
        ref="labelBtn"
        class="hidden"
        for="upload123"
      >ss</label>
      <button
        ref="submitBtn"
        type="submit"
        class="hidden"
      >
        student
      </button>
    </form>
  </div>
  <div class="shadow-md sm:rounded-lg m-4">
    <SearchInput
      v-model="searchVal"
      :search-icon="true"
      @keydown.space.enter="search"
    />
    <EasyDataTable
      v-model:server-options="serverOptions"
      :server-items-length="serverItemsLength"
      show-index
      :headers="headers"
      :items="schedulesShow"
      :loading="loading"
      buttons-pagination
      :rows-items="rowItems"
    >
      <template #header-import-export="header">
        <a
          class="rounded bg-gray-800 text-white font-sans font-semibold cursor-pointer p-2"
          :href="`${BASE_API_URL}/api/v2/template?type=User`"
        >Tải mẫu nhập sinh viên</a>
      </template>
      <template #item-startDate="item">
        {{ formatDate(item.startDate) }}
      </template>
      <template #item-deadline="item">
        {{ formatDate(item.deadline) }}
      </template>
      <template #item-import-export="item">
        <div class-="flex flex-col">
          <a
            class="cursor-pointer font-medium text-blue-600 dark:text-blue-500 hover:underline mx-2"
            @click="handleClickStudent(item._id)"
          >Nhập sinh viên bằng file excel</a>
          <a
            class="cursor-pointer font-medium text-blue-600 dark:text-blue-500 hover:underline mx-2"
            @click="selectStudents(item._id)"
          >Chọn sinh viên</a>
          <a
            class="font-medium text-blue-600 dark:text-blue-500 hover:underline mx-2"
            :href="getLink(item._id)"
          >Xuất báo cáo</a>
        </div>
      </template>
      <template #item-operation="item">
        <div class="flex">
          <font-awesome-icon
            icon="fa-solid fa-eye"
            size="2xl"
            @click="showRow(item)"
          />
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
    <ConfirmModal
      v-model="showConfirmModal.value"
      @confirm="confirmRemove"
      @cancel="showConfirmModal=false"
    >
      <template #title>
        Xác nhận
      </template>
      <div>Bạn có xác nhận xóa đợt đăng ký này không?</div>
    </ConfirmModal>
  </div>
  <SelectStudent
    v-model="showSelectStudent"
    :schedule-id="selectStudentScheduleId"
    @change-students="changeStudents"
  />
</template>

<script>
import { mapState, mapGetters, useStore } from 'vuex';
import SearchInput from 'vue-search-input';
// Optionally import default styling
import 'vue-search-input/dist/styles.css';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { useToast } from 'vue-toast-notification';
import moment from 'moment';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import ScheduleApi from '../../../utils/api/schedule';
import SelectStudent from '../../Modal/SelectStudent.vue';
import 'moment/dist/locale/vi';

export default {
  name: 'ManageScheduleAdmin',
  components: {
    SearchInput,
    ConfirmModal,
    SelectStudent,
  },
  setup () {
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const removeId = ref(0);
    const store = useStore();
    const $toast = useToast();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const schedules = ref([]);
    const showSelectStudent = ref(false);
    const selectStudentScheduleId = ref(null);
    const searchVal = ref('');
    const headers = [
      { text: 'Mã đợt đăng ký', value: 'code', sortable: true },
      { text: 'Tên đợt đăng ký ', value: 'name', sortable: true },
      { text: 'Thời gian bắt đầu', value: 'startDate' },
      { text: 'Thời gian kết thúc', value: 'deadline' },
      { text: 'import-export', value: 'import-export' },
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
      try {
        const response = await ScheduleApi.listAllSchedule(token, options);
        schedules.value = response.data;
        store.state.schedule.listSchedules = schedules.value;
        serverItemsLength.value = response.meta.pagination.total;
        loading.value = false;
      } catch (e) {
        loading.value = false;
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên để xử lý');
      }
    };

    onMounted(async () => {
      loading.value = true;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
      loading.value = false;
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
        await ScheduleApi.removeSchedule(this.token, id);
        showConfirmModal.value = false;
        removeId.value = 0;
        $toast.success('Đã xóa thành công!');
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
      }
    };

    const handleRemoveSchedule = (id) => {
      removeId.value = id;
      showConfirmModal.value = true;
    };

    const schedulesShow = computed(() => {
      if (!schedules.value) return [];
      return schedules.value;
    });

    const selectStudents = (scheduleId) => {
      selectStudentScheduleId.value = scheduleId;
      showSelectStudent.value = true;
    };

    const changeStudents = async (students) => {
      try {
        showSelectStudent.value = false;
        const _id = selectStudentScheduleId.value;
        await ScheduleApi.updateSchedule(token, { _id, students });
        $toast.success('Đã cập nhật  danh sách sinh viên thành công!');
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };

    const formatDate = (datetime) => moment(datetime).format('LL');

    const search = async () => {
      if (!searchVal.value || searchVal.value === '') await loadToServer(serverOptions.value);
      else await loadToServer({ ...serverOptions.value, search: searchVal.value });
    };

    return {
      headers,
      items,
      showRow,
      itemsSelected,
      selectStudents,
      loading,
      serverOptions,
      schedules,
      serverItemsLength,
      rowItems,
      editItem,
      modulePage,
      handleImport,
      showConfirmModal,
      confirmRemove,
      schedulesShow,
      handleRemoveSchedule,
      BASE_API_URL,
      showSelectStudent,
      searchVal,
      selectStudentScheduleId,
      changeStudents,
      formatDate,
      search,
    };
  },
  data () {
    return {
      importType: '',
      importId: '',
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('schedule', [
      'listSchedules',
    ]),
  },
  methods: {
    handleClickStudent (id) {
      this.importId = id;
      this.importType = 'student';
      this.$refs.labelBtn.click();
    },
    handleClickTopic (id) {
      this.importId = id;
      this.importType = 'topic';
      this.$refs.labelBtn.click();
    },
    handleNewButtonClick () {
      this.$refs.submitBtn.click();
    },
    async upload () {
      const { files } = this.$refs.uploadBtn;
      if (files.length > 0) {
        if (this.importType === 'student') {
          await this.$store.dispatch('schedule/importStudent', { token: this.token, id: this.importId, xlsx: files[0] })
            .then((data) => {
              if (data.status === 201) {
                this.$toast.success('Đã nhập thành công!');
              }
            });
        } else if (this.importType === 'topic') {
          await this.$store.dispatch('schedule/importTopic', { token: this.token, id: this.importId, xlsx: files[0] })
            .then((data) => {
              if (data.status === 201) {
                this.$toast.success('Đã nhập thành công!');
              }
            });
        }
        this.importId = '';
        this.importType = '';
        this.$refs.uploadBtn.value = '';
        this.search();
      } else {
        this.$toast.error('File không tồn tại');
      }
    },
    async handleUpdateSchedule (id) {
      await this.$store.dispatch('url/updateSection', 'schedule-update');
      await this.$store.dispatch('url/updateId', id);
    },
    getLink (id) {
      return `${this.BASE_API_URL}/v1/schedule/${id}/export`;
    },
    async handleExportSchedule (id) {
      await this.$store.dispatch('schedule/exportExcel', { token: this.token, id });
    },
    async handleShowSchedule (id) {
      await this.$store.dispatch('url/updateSection', 'schedule-view');
      await this.$store.dispatch('url/updateId', id);
    },
    formatDay (oldDate) {
      try {
        if (!oldDate || oldDate === '') {
          return '';
        }
        const newDate = new Date(oldDate);
        const dateString = new Date(newDate.getTime() - (newDate.getTimezoneOffset() * 60000))
          .toISOString()
          .split('T')[0];
        return dateString;
      } catch (e) {
        return '';
      }
    },
  },
};
</script>
