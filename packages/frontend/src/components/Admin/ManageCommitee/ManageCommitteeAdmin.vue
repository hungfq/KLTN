<template>
  <div class="flex flex-col">
    <div class="flex justify-end mr-4 my-2">
      <div class="w-64 mx-1">
        <Multiselect
          v-model="selectCritical"
          :options="listLecturerSelect"
          :can-deselect="false"
          :searchable="true"
          no-results-text="Không có kết quả"
          no-options-text="Không có lựa lựa chọn"
          :placeholder="'Giảng viên phản biện'"
          :can-clear="false"
          @change="selectHandlerLecturer('CRITICAL', $event)"
        />
      </div>
      <div class="w-64 mx-1">
        <Multiselect
          v-model="selectPresident"
          :options="listLecturerSelect"
          :can-deselect="false"
          :searchable="true"
          :placeholder="'Chủ tịch hội đồng'"
          no-results-text="Không có kết quả"
          no-options-text="Không có lựa lựa chọn"
          :can-clear="false"
          @change="selectHandlerLecturer('PRESIDENT', $event)"
        />
      </div>
      <div class="w-64 mx-1">
        <Multiselect
          v-model="selectSecretary"
          :options="listLecturerSelect"
          :can-deselect="false"
          :searchable="true"
          :placeholder="'Thư ký hội đồng'"
          no-results-text="Không có kết quả"
          no-options-text="Không có lựa lựa chọn"
          :can-clear="false"
          @change="selectHandlerLecturer( 'SECRETARY', $event)"
        />
      </div>
      <div class="w-64 mx-1">
        <Multiselect
          v-model="selectSchedule"
          :options="listScheduleSelect"
          :searchable="true"
          :can-deselect="false"
          no-results-text="Không có kết quả"
          no-options-text="Không có lựa lựa chọn"
          :can-clear="false"
          :placeholder="'Đợt đăng ký'"
          @change="selectHandlerSchedule"
        />
      </div>
      <div class="mx-auto" />
      <div
        class="btn btn-secondary mx-1"
        @click="showInviteModal= true"
      >
        <font-awesome-icon :icon="['fas', 'paper-plane']" />
        <span class="ml-1">Gửi thư mời</span>
      </div>
      <div
        class="btn btn-info mx-1"
        @click="showExportModal= true"
      >
        <font-awesome-icon :icon="['fas', 'list-check']" />
        <span class="ml-1">Xuất điểm</span>
      </div>
      <ButtonAddItem
        :title="'Thêm hội đồng'"
        @handle-import="$store.dispatch('url/updateSection', 'committee-import')"
      />
    </div>
    <div class="mx-4">
      <SearchInput
        v-model="searchVal"
        :search-icon="true"
        @keydown.space.enter="search"
      />
    </div>
    <EasyDataTable
      v-model:items-selected="itemsSelected"
      v-model:server-options="serverOptions"
      :server-items-length="serverItemsLength"
      table-class-name="mx-4"
      show-index
      :headers="headers"
      :items="committeesShow"
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
            class="tooltip tooltip-bottom pr-3"
            data-tip="Xem hội đồng"
          >
            <font-awesome-icon
              class="cursor-pointer"
              icon="fa-solid fa-eye"
              size="xl"
              @click="showRow(item)"
            />
          </div>
          <IconTooltip
            :icon="'fa-solid fa-book'"
            :title="'Nhập đề tài'"
            @click-icon="handleAddTopic(item._id)"
          />

          <div
            class="tooltip tooltip-bottom mr-2"
            data-tip="Chỉnh sửa hội đồng"
          >
            <font-awesome-icon
              class="cursor-pointer"
              :icon="['fas', 'pen-to-square']"
              size="xl"
              @click="editItem(item)"
            />
          </div>
          <div
            class="tooltip tooltip-bottom"
            data-tip="Xóa hội đồng"
          >
            <font-awesome-icon
              icon="fa-solid fa-trash-can"
              size="xl"
              @click="handleRemoveSchedule(item._id)"
            />
          </div>
        </div>
      </template>
    </EasyDataTable>
    <ConfirmModal
      v-model="showConfirmModal"
      @confirm="confirmRemove"
      @cancel="showConfirmModal=false"
    >
      <template #title>
        Xác nhận
      </template>
      <div>Bạn có xác nhận xóa hội đồng này không?</div>
    </ConfirmModal>
    <SendInvite
      v-model="showInviteModal"
      @send-invite="handleSendInvite"
    />
    <ExportGrade
      v-model="showExportModal"
      @export="handleExportGrade"
    />
  </div>
</template>

<script>
import { mapState, mapGetters, useStore } from 'vuex';
import SearchInput from 'vue-search-input';
import 'vue-search-input/dist/styles.css';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { useToast } from 'vue-toast-notification';
import Multiselect from '@vueform/multiselect';
import { saveAs } from 'file-saver';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import CommitteeApi from '../../../utils/api/committee';
import ScheduleApi from '../../../utils/api/schedule';
import ButtonAddItem from '../../common/ButtonAddItem.vue';
import IconTooltip from '../../common/IconTooltip.vue';
import SendInvite from '../../Modal/SendInvite.vue';
import ExportGrade from '../../Modal/ExportGrade.vue';

export default {
  name: 'ManageStudentAdmin',
  components: {
    SearchInput,
    Multiselect,
    ConfirmModal,
    ButtonAddItem,
    IconTooltip,
    SendInvite,
    ExportGrade,
  },
  setup () {
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const removeId = ref(0);
    const store = useStore();
    const $toast = useToast();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const selectPresident = ref(null);
    const selectCritical = ref(null);
    const selectSecretary = ref(null);
    const selectSchedule = ref(null);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const committees = ref([]);
    const headers = [
      { text: 'Mã hội đồng', value: 'code', sortable: true },
      { text: 'Tên hội đồng ', value: 'name', sortable: true },
      { text: 'Giảng viên phản biện', value: 'criticalLecturerId.name' },
      { text: 'Chủ tịch hội đồng', value: 'committeePresidentId.name' },
      { text: 'Thư kí hội đồng', value: 'committeeSecretaryId.name' },
      { text: 'Đợt đăng ký', value: 'schedule_code' },
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
    const errorHandler = (e) => {
      if (e.response.data.error.code === 400) $toast.error(e.response.data.error.message);
      else { $toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    };

    const loadToServer = async (options) => {
      loading.value = true;
      try {
        const response = await CommitteeApi.listAllCommittee(token, options, selectCritical.value, selectPresident.value, selectSecretary.value, selectSchedule.value);
        committees.value = response.data;
        store.state.committee.listCommittee = response.data;
        serverItemsLength.value = response.meta.pagination.total;
        loading.value = false;
      } catch (e) {
        loading.value = false;
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên để xử lý');
      }
    };

    onMounted(async () => {
      loading.value = true;
      await store.dispatch('lecturer/fetchListLecturer', token);
      await store.dispatch('schedule/fetchListSchedules', token);
      const schedulesStore = store.getters['schedule/listSchedules'];
      if (schedulesStore.length > 0) {
        selectSchedule.value = schedulesStore[0]._id;
      }
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        errorHandler(e);
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
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
    const showInviteModal = ref(false);
    const showExportModal = ref(false);
    const confirmRemove = async (id) => {
      try {
        await CommitteeApi.deleteCommittee(token, removeId.value);
        showConfirmModal.value = false;
        removeId.value = 0;
        await loadToServer(serverOptions.value);
        $toast.success('Đã xóa thành công!');
      } catch (e) {
        errorHandler(e);
        // $toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
      }
    };

    const handleRemoveSchedule = (id) => {
      removeId.value = id;
      showConfirmModal.value = true;
    };

    const committeesShow = computed(() => {
      if (!committees.value) return [];
      return committees.value;
    });

    const getLink = (id) => `${BASE_API_URL.value}/v1/schedule/${id}/export`;

    const searchVal = ref('');
    const search = async () => {
      serverOptions.value.page = 1;
      if (!searchVal.value || searchVal.value === '') await loadToServer(serverOptions.value);
      else await loadToServer({ ...serverOptions.value, search: searchVal.value });
    };

    const selectHandlerLecturer = async (type, event) => {
      if (type === 'CRITICAL') selectCritical.value = event;
      else if (type === 'PRESIDENT') selectPresident.value = event;
      else if (type === 'SECRETARY') selectSecretary.value = event;
      await loadToServer(serverOptions.value);
    };

    const selectHandlerSchedule = async (value) => {
      selectSchedule.value = value;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
        errorHandler(e);
      }
    };
    const handleSendInvite = async (scheduleId) => {
      try {
        showInviteModal.value = false;
        await ScheduleApi.sendMailToCommitteeBySchedule(token, scheduleId);
        $toast.success('Đã gửi mail!');
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };
    const handleExportGrade = async (scheduleId) => {
      try {
        const response = await ScheduleApi.exportGradeExcel(token, scheduleId);
        console.log('🚀 ~ file: ManageCommitteeAdmin.vue:340 ~ handleExportGrade ~ response:', response);
        const schedulesStore = store.getters['schedule/listSchedules'];
        const sc = schedulesStore.find((s) => s._id === scheduleId);
        if (sc) {
          saveAs(response.data, `${sc.code}.xlsx`);
        } else {
          saveAs(response.data, `grade-${scheduleId}.xlsx`);
        }
        showExportModal.value = false;
      } catch (e) {
        console.log('🚀 ~ file: ManageCommitteeAdmin.vue:350 ~ handleExportGrade ~ e:', e);
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };

    return {
      headers,
      items,
      showRow,
      itemsSelected,
      loading,
      serverOptions,
      committees,
      serverItemsLength,
      rowItems,
      editItem,
      modulePage,
      handleImport,
      showConfirmModal,
      confirmRemove,
      committeesShow,
      handleRemoveSchedule,
      getLink,
      BASE_API_URL,
      searchVal,
      selectCritical,
      selectSecretary,
      selectPresident,
      selectHandlerLecturer,
      selectSchedule,
      search,
      selectHandlerSchedule,
      showInviteModal,
      showExportModal,
      handleSendInvite,
      handleExportGrade,
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('committee', [
      'listCommittee',
    ]),
    ...mapGetters('lecturer', [
      'listLecturer',
    ]),
    ...mapGetters('schedule', [
      'listSchedules',
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
        arr.push({ value: schedule._id, label: schedule.code });
      });
      return arr;
    },
  },
  async mounted () {
    // await this.$store.dispatch('lecturer/fetchListLecturer', this.token);
    // await this.$store.dispatch('schedule/fetchListSchedules', this.token);
    // if (this.listScheduleSelect.length > 2) {
    //   // eslint-disable-next-line prefer-destructuring
    //   this.selectSchedule = this.listScheduleSelect[1].value;
    // }
  },
  methods: {
    handleAddTopic (id) {
      this.$store.dispatch('url/updateSection', 'committee-add-topic');
      this.$store.dispatch('url/updateId', id);
    },
    handleUpdateStudent (id) {
      this.$store.dispatch('url/updateSection', 'committee-update');
      this.$store.dispatch('url/updateId', id);
    },
    handleShowStudent (id) {
      this.$store.dispatch('url/updateSection', 'committee-view');
      this.$store.dispatch('url/updateId', id);
    },
    async handleRemoveStudent (id) {
      this.removeId = id;
      this.showConfirmModal = true;
    },
    async upload (files) {
      if (files.length > 0) {
        await this.$store.dispatch('committee/importCommittee', { token: this.token, xlsx: files[0] })
          .then((data) => {
            if (data.status === 201) {
              this.$toast.success('Đã nhập thành công!');
            }
          });
        this.search();
      } else {
        this.$toast.error('File không tồn tại');
      }
    },
  },
};
</script>
