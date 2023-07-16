<template>
  <div class="flex flex-col">
    <loading-process v-if="loading" />
    <template v-else>
      <template v-if=" !loading &&!isInApproveTime ">
        <div class="relative grow">
          <div class="">
            <img
              :src="imageUrl"
              class="min-h-full min-w-full block "
            >
          </div>
          <button
            class="btn btn-primary absolute bottom-0 left-0 !py-0"
            @click="$store.dispatch('url/updateSection', 'topic-list')"
          >
            Xem đề tài
          </button>
          <button
            class="btn btn-primary absolute bottom-0 right-0 !py-0"
            @click="$store.dispatch('url/updateSection', 'topic_proposal-list')"
          >
            Đề xuất đề tài
          </button>
        </div>
      </template>
      <div v-else>
        <div
          v-if="schedules.length > 1 "
          class="flex"
        >
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
            <template #item-list_students="item">
              <div class-="flex flex-col">
                <ul>
                  <li
                    v-for="student in item.list_students"
                    :key="`${student._id}`"
                    class="list-decimal"
                  >
                    {{ student.code }} : {{ student.name }}
                  </li>
                </ul>
              </div>
            </template>
            <template #item-operation="item">
              <div
                class="flex flex-col"
              >
                <div class="m-1 cursor-pointer rounded-xl">
                  <button
                    class=" btn btn-primary w-36 py-1"
                    @click="editItem(item)"
                  >
                    <span class="font-semibold px-1">Phê duyệt</span>
                    <font-awesome-icon
                      size="xl"
                      :icon="['fas', 'check']"
                    />
                  </button>
                </div>
                <div class="m-1 cursor-pointer rounded">
                  <button
                    class=" btn btn-warning w-36 py-1"
                    @click="handleRemoveTopic(item._id)"
                  >
                    <span class="font-semibold px-1 cursor-pointer">Từ chối</span>
                    <font-awesome-icon
                      size="xl"
                      :icon="['fas', 'ban']"
                    />
                  </button>
                </div>
              </div>
            </template>
            <template #item-file="item">
              <div
                class="tooltip tooltip-bottom"
                data-tip="Tải báo cáo"
              >
                <font-awesome-icon
                  class="cursor-pointer"
                  :icon="['fas', 'cloud-download']"
                  size="2xl"
                  @click="handleShowDownloadModal(item._id)"
                />
              </div>
            </template>
          </EasyDataTable>
        </div>
      </div>
    </template>
  </div>
  <ConfirmModal
    v-model="showConfirmModal"
    @confirm="confirmRemove"
    @cancel="showConfirmModal=false"
  >
    <template #title>
      Xác nhận
    </template>
    <div>Bạn có xác nhận từ chối hướng dẫn này không?</div>
  </ConfirmModal>
  <ViewFile
    v-model="showDownloadModal"
    :topic-id="viewTopicId"
    :type="'TOPIC_PROPOSAL'"
  />
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
import LoadingProcess from '../../common/Loading.vue';
import Multiselect from '@vueform/multiselect';
import ViewFile from '../../Modal/ViewFile.vue';

export default {
  name: 'ManageTopicLecturer',
  components: {
    SearchInput,
    ConfirmModal,
    Multiselect,
    LoadingProcess,
    ViewFile,
  },
  setup () {
    const loading = ref(true);
    const store = useStore();
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const viewTopicId = ref(0);
    const showDownloadModal = ref(false);
    const rowItems = [10, 20, 50];
    // Init value
    const topics = ref([]);
    const schedules = ref([]);
    const headers = [
      { text: 'Mã số', value: 'code', sortable: true },
      { text: 'Tên đề tài ', value: 'title', sortable: true },
      { text: 'Sinh viên', value: 'list_students' },
      { text: 'Tệp tin', value: 'file' },
      { text: 'Hành động', value: 'operation' },
    ];

    const searchVal = ref('');
    const removeId = ref('');
    const selectSchedule = ref('all');
    const selectLecturer = ref('');
    const items = [];
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: 'updated_at',
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    const modulePage = computed(() => store.getters['url/module']);

    const $toast = useToast();
    const errorHandler = (e) => {
      if (e.response.data.error.code === 400) $toast.error(e.response.data.error.message);
      else { $toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    };
    const loadToServer = async (options) => {
      try {
        loading.value = true;
        let response;
        if (!selectSchedule.value || selectSchedule.value === 'all') {
          response = await TopicProposalApi.listAllTopicsByLecturer(token, options);
        } else {
          response = await TopicProposalApi.listAllTopicsByLecturer(token, options, selectSchedule.value);
        }
        if (response) {
          topics.value = response.data;
          store.state.topic_proposal.listTopicProposalByLecturer = topics.value;
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
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
        errorHandler(e);
      }
    };

    onMounted(async () => {
      const listSchedules = store.getters['schedule/listScheduleApproveLecturer'];
      schedules.value = listSchedules;
      if (schedules.value.length > 0) {
        selectSchedule.value = schedules.value[0]._id;
      }
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
        errorHandler(e);
      }
    });

    const editItem = (item) => {
      store.dispatch('url/updateId', item._id);
      store.dispatch('url/updateSection', `${modulePage.value}-update`);
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

    const handleRemoveTopic = async (id) => {
      removeId.value = id;
      showConfirmModal.value = true;
    };
    const confirmRemove = async (id) => {
      showConfirmModal.value = false;
      try {
        await TopicProposalApi.declineTopicProposal(token, removeId.value);
        await loadToServer(serverOptions.value);
        $toast.success('Đã từ chối thành công!');
      } catch (e) {
        // $toast.error('Đã có lỗi xảy ra, vui lòng báo với quản trị viên!');
        errorHandler(e);
      }
    };

    const search = async () => {
      if (!searchVal.value || searchVal.value === '') await loadToServer(serverOptions.value);
      else await loadToServer({ ...serverOptions.value, search: `${searchVal.value}` });
    };

    const isInApproveTime = computed(() => schedules.value.length !== 0);

    const topicShow = computed(() => {
      if (!topics.value) return [];
      return topics.value.map((topic) => ({
        _id: topic._id,
        code: topic.code,
        title: topic.title,
        lecturer: topic.lecturerId.name || '',
        students: topic.students || [],
        scheduleId: topic.scheduleId?._id || null,
        list_students: topic.list_students || [],
      }));
    });

    const listScheduleSelect = computed(() => {
      const arr = [{ value: 0, label: 'Tất cả các đợt' }];
      schedules.value.forEach((schedule) => {
        arr.push({ value: schedule._id, label: `${schedule.code}` });
      });
      return arr;
    });

    const selectHandlerSchedule = async (value) => {
      selectSchedule.value = value;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
        errorHandler(e);
      }
    };

    const handleShowDownloadModal = (id) => {
      showDownloadModal.value = true;
      viewTopicId.value = id;
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
      rowItems,
      editItem,
      modulePage,
      handleImport,
      handleRemoveTopic,
      showConfirmModal,
      confirmRemove,
      selectSchedule,
      selectLecturer,
      topicShow,
      selectHandler,
      schedules,
      searchVal,
      search,
      isInApproveTime,
      listScheduleSelect,
      selectHandlerSchedule,
      viewTopicId,
      showDownloadModal,
      handleShowDownloadModal,

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
      'listSchedules', 'listScheduleApproveLecturer',
    ]),
    imageUrl () {
      const imageUrl = new URL('/src/assets/images/not_approve.png', import.meta.url).href;
      return imageUrl;
    },
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
    // async handleRemoveTopic (id) {
    // //   this.removeId = id;
    // //   this.showConfirmModal = true;
    // // },
  },
};
</script>
