<template>
  <div class="flex">
    <div
      class=" rounded ml-auto mr-4 my-2 bg-blue-800 text-white font-sans font-semibold py-2 px-4 cursor-pointer"
      @click="$store.dispatch('url/updateSection', 'schedule-import')"
    >
      Thêm đợt đăng ký
    </div>
    <UploadButton @uploadFileExcel="upload" />
  </div>
  <div class="shadow-md sm:rounded-lg m-4">
    <SearchInput
      v-model="searchVal"
      :search-icon="true"
      @keydown.space.enter="search"
    />
    <div class="shadow-md sm:rounded-lg m-4">
      <EasyDataTable
        v-model:items-selected="itemsSelected"
        v-model:server-options="serverOptions"
        :server-items-length="serverItemsLength"
        show-index
        :headers="headers"
        :items="schedulesShow"
        :loading="loading"
        buttons-pagination
        :rows-items="rowItems"
        @click-row="showRow"
      >
        <template #header-import-export="header">
          <a
            class="rounded bg-gray-800 text-white font-sans font-semibold cursor-pointer p-2"
            href="http://localhost:8001/api/v2/template?type=User"
          >Tải mẫu nhập sinh viên</a>
        </template>
        <template #item-operation="item">
          <div class="flex">
            <img
              src="https://cdn-icons-png.flaticon.com/512/1827/1827951.png"
              class="operation-icon w-6 h-6 mx-2 cursor-pointer"
              @click="editItem(item)"
            >
            <font-awesome-icon
              icon="fa-solid fa-trash-can"
              size="2xl"
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
      <div>Bạn có xác nhận xóa đợt đăng ký này không?</div>
    </ConfirmModal>
  </div>
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
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import UploadButton from '../UploadButton.vue';
import ScheduleApi from '../../../utils/api/schedule';

export default {
  name: 'ManageScheduleAdmin',
  components: {
    SearchInput,
    ConfirmModal,
    UploadButton,
  },
  setup () {
    const store = useStore();
    const $toast = useToast();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const schedules = ref([]);
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
      sortBy: headers[0].value,
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
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên để xử lý');
      }
    };

    onMounted(async () => {
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        console.log(e);
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

    const selectSchedule = ref('');
    const selectLecturer = ref('');

    const schedulesShow = computed(() => {
      if (!schedules.value) return [];
      return schedules.value;
      // return schedules.value.map((schedule) => ({
      //   _id: schedule._id,
      //   code: schedule.code,
      //   name: schedule.name,
      //   start_date: schedule.start_date || '',
      //   end_date: schedule.deadline || '',
      // }));
    });

    return {
      headers,
      items,
      showRow,
      itemsSelected,
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
      selectSchedule,
      selectLecturer,
      schedulesShow,
    };
  },
  data () {
    return {
      // showConfirmModal: false,
      removeId: '',
      // schedules: [],
      searchVal: '',
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
  async mounted () {
    // await this.$store.dispatch('schedule/fetchListSchedules', this.token);
    // this.schedules = this.listSchedules;
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
      return `http://localhost:5000/v1/schedule/${id}/export`;
    },
    // async handleExportSchedule (id) {
    //   await this.$store.dispatch('schedule/exportExcel', { token: this.token, id });
    // },
    async handleShowSchedule (id) {
      await this.$store.dispatch('url/updateSection', 'schedule-view');
      await this.$store.dispatch('url/updateId', id);
    },
    async handleRemoveSchedule (id) {
      this.removeId = id;
      this.showConfirmModal = true;
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
    search () {
      if (this.searchVal !== '') {
        const scheduleFilter = this.listSchedules.filter((schedule) => {
          const re = new RegExp(`\\b${this.searchVal}`, 'gi');
          const startDate = this.formatDay(schedule.startDate);
          const endDate = this.formatDay(schedule.endDate);
          if (schedule.name.match(re)) return true;
          if (schedule.code.match(re)) return true;
          if (endDate.match(re)) return true;
          if (startDate.match(re)) return true;
          return false;
        });
        this.schedules = scheduleFilter;
      } else this.schedules = this.listSchedules;
    },
  },
};
</script>
