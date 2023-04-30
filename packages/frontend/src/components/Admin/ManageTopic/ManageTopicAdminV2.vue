<template>
  <div class="flex">
    <div class="inline-block p-2 rounded-md">
      <select
        v-model="selectSchedule"
        class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
        @change="selectHandler"
      >
        <option
          :key="`key-null`"
          :value="''"
        >
          Tất cả
        </option>
        <option
          v-for="option in listSchedules"
          :key="`key-${option._id}`"
          :value="option._id"
        >
          {{ option.code }} : {{ option.name }}
        </option>
      </select>
    </div>
    <div class="inline-block p-2 rounded-md">
      <select
        v-model="selectLecturer"
        class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
        @change="selectHandler"
      >
        <option
          :key="`key-null`"
          :value="''"
        >
          Tất cả
        </option>
        <option
          v-for="option in listLecturer"
          :key="`key-${option._id}`"
          :value="option._id"
        >
          {{ option.code }} : {{ option.name }}
        </option>
      </select>
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
        <div class="flex">
          <img
            src="https://cdn-icons-png.flaticon.com/512/1827/1827951.png"
            class="operation-icon w-6 h-6 mx-2 cursor-pointer"
            @click="editItem(item)"
          >
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
// import SearchInput from 'vue-search-input';
// Optionally import default styling
import 'vue-search-input/dist/styles.css';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';
import { useToast } from 'vue-toast-notification';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import UploadButtonVue from '../UploadButton.vue';
import TopicApi from '../../../utils/api/topic';

export default {
  name: 'ManageTopicAdmin',
  components: {
    ConfirmModal,
    UploadButtonVue,
  },
  setup () {
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const store = useStore();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const topics = ref([]);
    const headers = [
      { text: 'Mã số', value: 'code', sortable: true },
      { text: 'Tên đề tài ', value: 'title', sortable: true },
      { text: 'Giảng viên hướng dẫn', value: 'lecturer' },
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

    const selectSchedule = ref('');
    const selectLecturer = ref('');

    const topicShow = computed(() => {
      if (!topics.value) return [];
      return topics.value.map((topic) => ({
        _id: topic._id,
        code: topic.code,
        title: topic.title,
        lecturer: topic.lecturerId.name || '',
        critical: topic.criticalLecturerId.name || '',
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
      BASE_API_URL,
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
    async selectHandler () {
      await this.$store.dispatch('topic/fetchListTopicByLecturerSchedule', { token: this.token, lecturerId: this.selectLecturer, scheduleId: this.selectSchedule });
      this.topics = this.listTopicsByLecturerSchedule;
    },
  },
};
</script>
