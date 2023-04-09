<template>
  <div class="flex">
    <div
      class=" rounded ml-auto mr-4 my-2 bg-blue-800 text-white font-sans font-semibold py-2 px-4 cursor-pointer"
      @click="$store.dispatch('url/updateSection', 'committee-import')"
    >
      Thêm hội đồng
    </div>
    <!-- <UploadButtonVue @uploadFileExcel="upload" /> -->
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
        :items="committeesShow"
        :loading="loading"
        buttons-pagination
        :rows-items="rowItems"
        @click-row="showRow"
      >
        <template #header-import-export="header">
          <a
            class="rounded bg-gray-800 text-white font-sans font-semibold cursor-pointer p-2"
            href="http://localhost:8001/api/v2/template?type=User"
          >Tải mẫu nhập đề tài</a>
        </template>
        <template #item-import-export="item">
          <div class-="flex">
            <a
              class="cursor-pointer font-medium text-blue-600 dark:text-blue-500 hover:underline mx-2"
              @click="handleAddTopic(item._id)"
            >Nhập đề tài bằng file excel</a>
            <a
              class="font-medium text-blue-600 dark:text-blue-500 hover:underline mx-2"
              :href="getLink(item._id)"
            >Xuất báo cáo</a>
          </div>
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
              @click="handleRemoveSchedule(item._id)"
            />
          </div>
        </template>
      </EasyDataTable>
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
    <div>Bạn có xác nhận xóa hội đồng này không?</div>
  </ConfirmModal>
</template>

<script>
import { mapState, mapGetters, useStore } from 'vuex';
import SearchInput from 'vue-search-input';
import 'vue-search-input/dist/styles.css';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { useToast } from 'vue-toast-notification';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import CommitteeApi from '../../../utils/api/committee';

export default {
  name: 'ManageStudentAdmin',
  components: {
    SearchInput,
    ConfirmModal,
  },
  setup () {
    const removeId = ref(0);
    const store = useStore();
    const $toast = useToast();
    const loading = ref(false);
    const itemsSelected = ref([]);
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
      sortBy: headers[0].value,
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    const modulePage = computed(() => store.getters['url/module']);
    const loadToServer = async (options) => {
      loading.value = true;
      try {
        const response = await CommitteeApi.listAllCommittee(token, options);
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
      // store.dispatch('url/updateId', item._id);
      // store.dispatch('url/updateSection', `${modulePage.value}-view`);
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
      await CommitteeApi.deleteCommittee(this.token, id);
      showConfirmModal.value = false;
      removeId.value = 0;

      try {
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

    const getLink = (id) => `http://localhost:5000/v1/schedule/${id}/export`;

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
    };
  },
  data () {
    return {
      // showConfirmModal: false,
      removeId: '',
      searchVal: '',
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
    search () {
      if (this.searchVal !== '') {
        const committeeFilters = this.listCommittee.filter((st) => {
          const re = new RegExp(`\\b${this.searchVal}`, 'gi');
          if (st.name.match(re)) return true;
          if (st.code.match(re)) return true;
          if (st.committeePresidentId && st.committeePresidentId.name.match(re)) return true;
          if (st.committeeSecretaryId && st.committeeSecretaryId.name.match(re)) return true;
          if (st.criticalLecturerId && st.criticalLecturerId.name.match(re)) return true;
          return false;
        });
        this.committees = committeeFilters;
      } else this.committees = this.listCommittee;
    },
  },
};
</script>
