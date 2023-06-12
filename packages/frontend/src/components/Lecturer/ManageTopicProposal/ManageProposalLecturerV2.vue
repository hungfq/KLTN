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
          <div class="inline-block p-2 rounded-md">
            <select
              v-model="selectSchedule"
              class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm sm:text-sm"
              @change="selectHandler"
            >
              <option
                :key="`key-null`"
                :value="'all'"
              >
                Tất cả
              </option>
              <option
                v-for="option in schedules"
                :key="`key-${option._id}`"
                :value="option._id"
              >
                {{ option.code }} : {{ option.name }}
              </option>
            </select>
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
          >
            <template #empty-message>
              <div class="text-center text-gray-500">
                Không có dữ liệu
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

export default {
  name: 'ManageTopicLecturer',
  components: {
    SearchInput,
    ConfirmModal,
    LoadingProcess,
  },
  setup () {
    const loading = ref(true);
    const store = useStore();
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    // Init value
    const topics = ref([]);
    const schedules = ref([]);
    const headers = [
      { text: 'Mã số', value: 'code', sortable: true },
      { text: 'Tên đề tài ', value: 'title', sortable: true },
      { text: 'Sinh viên', value: 'students' },
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
    // const listSchedules = store.getters['schedule/listScheduleApproveLecturer'];
    const modulePage = computed(() => store.getters['url/module']);

    const $toast = useToast();
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
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };

    onMounted(async () => {
      const listAllSchedule = await ScheduleApi.listScheduleApproveLecturer(token);
      schedules.value = listAllSchedule.data;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
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
        $toast.error('Đã có lỗi xảy ra, vui lòng báo với quản trị viên!');
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
