<template>
  <div class="flex flex-col">
    <div class="flex justify-end mr-4 my-2">
      <div class="w-64 mx-2">
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
      <div class="w-64 mx-2">
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
      <div class="w-64 mx-2">
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
      <div class="mx-auto" />
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
      <template #header-import-export="header">
        <a
          class="rounded bg-gray-800 text-white font-sans font-semibold cursor-pointer p-2"
          :href="`${BASE_API_URL}/api/v2/template?type=User`"
        >Tải mẫu nhập đề tài</a>
      </template>
      <template #item-import-export="item">
        <div class-="flex">
          <a
            class="cursor-pointer font-medium text-blue-600 dark:text-blue-500 hover:underline mx-2"
            @click="handleAddTopic(item._id)"
          >Nhập đề tài</a>
        </div>
      </template>
      <template #item-operation="item">
        <div class="flex">
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
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import CommitteeApi from '../../../utils/api/committee';
import ButtonAddItem from '../../common/ButtonAddItem.vue';

export default {
  name: 'ManageStudentAdmin',
  components: {
    SearchInput,
    Multiselect,
    ConfirmModal,
    ButtonAddItem,
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
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const committees = ref([]);
    const headers = [
      { text: 'Mã hội đồng', value: 'code', sortable: true },
      { text: 'Tên hội đồng ', value: 'name', sortable: true },
      { text: 'Giáo viên phản biện', value: 'criticalLecturerId.name' },
      { text: 'Chủ tịch hội đồng', value: 'committeePresidentId.name' },
      { text: 'Thư kí hội đồng', value: 'committeeSecretaryId.name' },
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
        const response = await CommitteeApi.listAllCommittee(token, options, selectCritical.value, selectPresident.value, selectSecretary.value);
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
        await CommitteeApi.deleteCommittee(token, removeId.value);
        showConfirmModal.value = false;
        removeId.value = 0;
        await loadToServer(serverOptions.value);
        $toast.success('Đã xóa thành công!');
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
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
    ...mapGetters('committee', [
      'listCommittee',
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
  },
  async mounted () {
    await this.$store.dispatch('lecturer/fetchListLecturer', this.token);
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
