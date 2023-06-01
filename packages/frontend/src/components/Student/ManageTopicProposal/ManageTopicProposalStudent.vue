<template>
  <template v-if="!open">
    <div class="relative">
      <img
        class="w-fit h-fit"
        :src="imageUrl"
      >
      <button
        v-if="!checkIsRegister"
        class="btn btn-primary absolute top-[75%] left-[46%] !py-0"
        @click="$store.dispatch('url/updateSection', 'topic_result-list')"
      >
        Xem kết quả
      </button>
      <button
        v-else
        class="btn btn-primary absolute top-[75%] left-[46%] !py-0"
        @click="$store.dispatch('url/updateSection', 'topic_register-list')"
      >
        Đăng ký đề tài
      </button>
    </div>
  </template>
  <template v-if="open">
    <template v-if="!loading">
      <div class="flex">
        <div class="w-[300px] mt-2 ml-4">
          <Multiselect
            v-if="scheduleSelectOption.length > 2"
            v-model="selectSchedule"
            :options="scheduleSelectOption"
            :can-clear="false"
            @change="selectHandlerSchedule"
          />
        </div>
        <div
          class=" rounded ml-auto mr-4 my-2 bg-blue-800 text-white font-sans font-semibold py-2 px-4 cursor-pointer"
          @click="$store.dispatch('url/updateSection', 'topic_proposal-import')"
        >
          Thêm đề xuất đề tài
        </div>
      </div>
      <div
        class="shadow-md sm:rounded-lg m-4"
      >
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
          <template #item-import-export="students">
            <div class-="flex flex-col">
              <ul>
                <li
                  v-for="student in students"
                  :key="`${student}`"
                >
                  {{ student }}
                </li>
              </ul>
            </div>
          </template>
          <template #item-operation="item">
            <div class="flex">
              <div
                class="tooltip tooltip-bottom pr-3"
                data-tip="Xem đề xuất"
              >
                <font-awesome-icon
                  class="cursor-pointer"
                  icon="fa-solid fa-eye"
                  size="2xl"
                  @click="showRow(item)"
                />
              </div>
              <div
                class="tooltip tooltip-bottom"
                data-tip="Chỉnh sửa đề xuất"
              >
                <font-awesome-icon
                  class="cursor-pointer"
                  :icon="['fas', 'pen-to-square']"
                  size="2xl"
                  @click="editItem(item)"
                />
              </div>
              <div
                class="tooltip tooltip-bottom pl-3"
                data-tip="Xóa đợt đề xuất"
              >
                <font-awesome-icon
                  icon="fa-solid fa-trash-can"
                  size="2xl"
                  @click="handleRemoveTopicProposal(item._id)"
                />
              </div>
            </div>
          </template>
        </EasyDataTable>
      </div>
    </template>
    <LoadingProcess v-else />
  </template>
  <ConfirmModal
    v-model="showConfirmModal"
    @confirm="confirmRemove"
    @cancel="showConfirmModal=false"
  >
    <template #title>
      Xác nhận
    </template>
    <div>Bạn có xác nhận xóa đề xuất này?</div>
  </ConfirmModal>
</template>

<script>

import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';
import SearchInput from 'vue-search-input';
import { useToast } from 'vue-toast-notification';
import 'vue-search-input/dist/styles.css';
import Multiselect from '@vueform/multiselect';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import ScheduleApi from '../../../utils/api/schedule';
import TopicProposalApi from '../../../utils/api/topic_proposal';
import LoadingProcess from '../../common/Loading.vue';

export default {
  name: 'ManageTopicProposalStudent',
  components: {
    SearchInput,
    LoadingProcess,
    ConfirmModal,
    Multiselect,
  },
  props: {
    open: {
      type: Boolean,
      default: true,
    },
  },
  setup () {
    const store = useStore();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    // Init value
    const topics = ref([]);
    const schedules = ref([]);
    const imgNotFound = ref('');
    imgNotFound.value = new URL('/src/assets/images/not_proposal.png', import.meta.url).href;
    const headers = [
      { text: 'Mã số', value: 'code', sortable: true },
      { text: 'Tên đề tài ', value: 'title', sortable: true },
      { text: 'Giảng viên hướng dẫn', value: 'lecturer' },
      { text: 'Hành động', value: 'operation' },
    ];

    const headerTabs = [
      { code: 'advisor', text: 'Hướng dẫn' },
      { code: 'critical', text: 'Phản biện' },
    ];
    const tab = ref('advisor');
    const searchVal = ref('');
    const selectSchedule = ref('all');
    const selectLecturer = ref('');
    const items = [];
    const removeId = ref('');
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: 'updated_at',
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    // const currentLecturerId = store.getters['auth/userId'];
    // const currentCodeStudent = store.getters['auth/code'];
    const modulePage = computed(() => store.getters['url/module']);
    const loadToServer = async (options) => {
      loading.value = true;
      if (!selectSchedule.value) {
        loading.value = false;
        return;
      }
      const response = await TopicProposalApi.listAllTopicsByCreated(token, selectSchedule.value, options);
      if (response) {
        topics.value = response.data;
        store.state.topic.listTopicProposalStudent = topics.value;
        if (response.meta) {
          serverItemsLength.value = response.meta.pagination.total;
        } else {
          serverItemsLength.value = 1;
        }
      }
      loading.value = false;
    };

    const $toast = useToast();

    const fetchListScheduleProposalByStudentId = async () => {
      const schedulesToday = store.getters['schedule/listScheduleProposalStudent'];
      return schedulesToday;
    };
    onMounted(async () => {
      loading.value = true;
      schedules.value = await fetchListScheduleProposalByStudentId();
      if (schedules.value.length > 0) {
        store.state.listScheduleProposalStudent = schedules.value;
        selectSchedule.value = schedules.value[0]._id;
      }
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
      loading.value = false;
    });

    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });
    watch(tab, async () => {
      await loadToServer(serverOptions.value);
    }, { deep: true });
    watch(modulePage, async () => { await loadToServer(serverOptions.value); });
    watch(selectSchedule.value, async () => { await loadToServer(serverOptions.value); });

    const selectHandler = async (value) => {
      selectSchedule.value = value;
      await loadToServer(serverOptions.value);
    };
    const selectHandlerSchedule = async (value) => {
      console.log('🚀 ~ file: ManageTopicProposalStudent.vue:229 ~ selectHandler ~ schedule:', value);
    };
    const showRow = (item) => {
      // if (!checkCanEdit(item.scheduleId)) return;
      store.dispatch('url/updateId', item._id);
      store.dispatch('url/updateSection', `${modulePage.value}-view`);
    };

    const showConfirmModal = ref(false);
    const editItem = async (item) => {
      store.dispatch('url/updateSection', `${modulePage.value}-update`);
      store.dispatch('url/updateId', item._id);
    };
    const confirmRemove = async (id) => {
      showConfirmModal.value = false;
      try {
        await TopicProposalApi.removeTopicProposal(token, removeId.value);
        $toast.success('Đã xóa thành công, vui lòng xem kết quả!');
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên ');
      }
    };

    const search = async () => {
      if (!searchVal.value || searchVal.value === '') await loadToServer(serverOptions.value);
      else await loadToServer({ ...serverOptions.value, search: `${searchVal.value}` });
    };
    const handleRemoveTopicProposal = async (id) => {
      removeId.value = id;
      showConfirmModal.value = true;
    };

    const topicShow = computed(() => {
      if (!topics.value) return [];
      return topics.value.map((topic) => ({
        _id: topic._id,
        code: topic.code,
        title: topic.title,
        lecturer: topic.lecturerId?.name || '',
        students: topic.students || [],
        scheduleId: topic.scheduleId?._id || null,
      }));
    });

    const scheduleSelectOption = computed(() => {
      const arr = [{ value: 0, label: 'Chọn đợt' }];
      if (!schedules.value) return arr;
      schedules.value.forEach((schedule) => {
        arr.push({ value: schedule._id, label: schedule.name });
      });
      return arr;
    });

    return {
      headers,
      items,
      itemsSelected,
      loading,
      removeId,
      serverOptions,
      topics,
      scheduleSelectOption,
      serverItemsLength,
      rowItems,
      editItem,
      modulePage,
      showConfirmModal,
      confirmRemove,
      selectSchedule,
      selectLecturer,
      topicShow,
      selectHandler,
      schedules,
      headerTabs,
      tab,
      searchVal,
      search,
      handleRemoveTopicProposal,
      selectHandlerSchedule,
      showRow,
      imgNotFound,
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('student', [
      'listStudents',
    ]),
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    ...mapGetters('topic_proposal', [
      'listTopicProposalCreated', 'topicScheduleId',
    ]),
    ...mapGetters('schedule', [
      'listScheduleProposalStudent',
    ]),
    imageUrl () {
      const imageUrl = new URL('/src/assets/images/not_proposal.png', import.meta.url).href;
      return imageUrl;
    },
    checkIsRegister () {
      const schedules = this.$store.state.schedule.listScheduleRegisterStudent;
      if (schedules && schedules.length > 0) {
        return true;
      }
      return false;
    },
  },
  methods: {
    async handleUpdateTopicProposal (id) {
      await this.$store.dispatch('url/updateSection', `${this.module}-update`);
      await this.$store.dispatch('url/updateId', id);
    },
  },
};
</script>
