<template>
  <div class="flex flex-col">
    <div class="mt-2 bg-slate-100 py-2">
      <div class="tabs ml-4">
        <a
          v-for="option in headerTabs"
          :key="option.code"
          class="tab tag-lg tab-lifted min-w-[100px] text-blue-900 font-semibold"
          :class="{'tab-active' : option.code === tab}"
          @click="tab= option.code"
        >{{ option.text }}</a>
      </div>
    </div>
    <div>
      <div class="flex justify-between">
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
        <ButtonAddItem
          v-if="canShowImport"
          title="Thêm đề tài"
          @handle-import="$store.dispatch('url/updateSection', 'topic-import')"
        />
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
        >
          <template #empty-message>
            <div class="text-center text-gray-500">
              Không có dữ liệu
            </div>
          </template>
          <template #expand="item">
            <div
              class="flex"
            >
              <div class="flex flex-col ml-16">
                <div class="font-semibold">
                  Danh sách sinh viên
                </div>
                <ul class="list-decimal mx-4">
                  <li
                    v-for="student in item.list_students"
                    :key="`st-${student._id}`"
                  >
                    {{ student.code }} : {{ student.name }}
                  </li>
                </ul>
              </div>
            </div>
          </template>
          <template #item-operation="item">
            <div
              class="flex"
            >
              <div>
                <IconTooltip
                  title="Xem đề tài"
                  @click-icon="showRow(item)"
                />
              </div>
              <div v-if="checkCanEdit(item.scheduleId) && tab !== 'critical'">
                <IconTooltip
                  title="Chọn sinh viên"
                  :icon="'fa-solid fa-people-group'"
                  @click-icon="selectStudents(item)"
                />
                <IconTooltip
                  title="Sửa đề tài"
                  :icon="'fa-solid fa-pen-to-square'"
                  @click-icon="editItem(item)"
                />
                <IconTooltip
                  title="Xóa đề tài"
                  :icon="'fa-solid fa-trash-can'"
                  @click-icon="handleRemoveSchedule(item._id)"
                />
              </div>
            </div>
          </template>
        </EasyDataTable>
      </div>
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
// import { mapState, mapGetters } from 'vuex';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';
import { useToast } from 'vue-toast-notification';
import SearchInput from 'vue-search-input';
import Multiselect from '@vueform/multiselect';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
// Optionally import default styling
import 'vue-search-input/dist/styles.css';

// import 'vue-search-input/dist/styles.css';
import ScheduleApi from '../../../utils/api/schedule';
import TopicApi from '../../../utils/api/topic';
import IconTooltip from '../../common/IconTooltip.vue';
import SelectStudent from '../../Modal/SelectStudent.vue';
import ButtonAddItem from '../../common/ButtonAddItem.vue';

export default {
  name: 'ManageTopicLecturer',
  components: {
    SearchInput,
    ConfirmModal,
    IconTooltip,
    SelectStudent,
    ButtonAddItem,
    Multiselect,
  },
  setup () {
    const store = useStore();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const showSelectStudent = ref(false);
    const selectStudentScheduleId = ref(null);
    const listStudentSelected = ref([]);
    const topicStudentId = ref(0);
    const topicStudentLimit = ref(0);
    // Init value
    const topics = ref([]);
    const schedules = ref([]);
    const headers = [
      { text: 'Mã số', value: 'code', sortable: true },
      {
        text: 'Tên đề tài ', value: 'title', sortable: true, width: 300,
      },
      { text: 'Giảng viên hướng dẫn', value: 'lecturer' },
      { text: 'Giảng viên phản biện', value: 'critical' },
      { text: 'Số lượng', value: 'limit' },
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
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: 'updated_at',
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    const currentLecturerId = store.getters['auth/userId'];
    const modulePage = computed(() => store.getters['url/module']);
    const loadToServer = async (options) => {
      loading.value = true;
      let response;
      if (!selectSchedule.value || selectSchedule.value === 'all') {
        if (tab.value === 'advisor') {
          response = await TopicApi.listAllTopicsByLecturerId(token, currentLecturerId, options);
        } else {
          response = await TopicApi.listAllTopicsByCritical(token, currentLecturerId, options);
        }
      } else if (tab.value === 'advisor') {
        response = await TopicApi.listAllTopicsByLecturerIdAndScheduleId(token, currentLecturerId, selectSchedule.value, options);
      } else {
        response = await TopicApi.listAllTopicsByCriticalAndScheduleId(token, currentLecturerId, selectSchedule.value, options);
      }
      if (response) {
        topics.value = response.data;
        store.state.topic.listTopics = topics.value;
        if (response.meta) {
          serverItemsLength.value = response.meta.pagination.total;
        } else {
          serverItemsLength.value = 1;
        }
      }
      loading.value = false;
    };
    const $toast = useToast();
    const errorHandler = (e) => {
      if (e.response.data.error.code === 400) $toast.error(e.response.data.error.message);
      else { $toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    };

    onMounted(async () => {
      const listAllSchedule = await ScheduleApi.listAllSchedule(token);
      schedules.value = listAllSchedule.data;
      if (schedules.value.length > 0) {
        selectSchedule.value = schedules.value[0]._id;
      }
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
    });

    const editItem = (item) => {
      store.dispatch('url/updateSection', `${modulePage.value}-update`);
      store.dispatch('url/updateId', item._id);
    };
    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });
    watch(tab, async () => {
      await loadToServer(serverOptions.value);
    }, { deep: true });
    watch(modulePage, async () => { await loadToServer(serverOptions.value); });

    const handleImport = () => {
      store.dispatch('url/updateSection', `${modulePage.value}-import`);
    };

    const selectHandler = async () => {
      await loadToServer(serverOptions.value);
    };

    const checkCanEdit = (scheduleId) => {
      if (!scheduleId) return false;
      const listSchedules = store.state.schedule.listScheduleApproveLecturer;
      const listScheduleIds = listSchedules.map((s) => parseInt(s._id, 10));
      return listScheduleIds.includes(parseInt(scheduleId, 10));
    };

    const canShowImport = computed(() => {
      const listSchedules = store.state.schedule.listScheduleApproveLecturer;
      return !!listSchedules.length;
    });

    const showRow = (item) => {
      store.dispatch('url/updateId', item._id);
      store.dispatch('url/updateSection', `${modulePage.value}-view`);
    };

    const showConfirmModal = ref(false);
    const confirmRemove = async (id) => {
      showConfirmModal.value = false;
      try {
        $toast.success('Đã xóa thành công!');
      } catch (e) {
        errorHandler(e);
        // $toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
      }
    };

    const search = async () => {
      if (!searchVal.value || searchVal.value === '') await loadToServer(serverOptions.value);
      else await loadToServer({ ...serverOptions.value, search: `${searchVal.value}` });
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
        errorHandler(e);
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };

    const selectStudents = (item) => {
      selectStudentScheduleId.value = item.scheduleId;
      topicStudentId.value = item._id;
      showSelectStudent.value = true;
      listStudentSelected.value = item.list_students;
      topicStudentLimit.value = item.limit;
    };

    const topicShow = computed(() => {
      if (!topics.value) return [];
      return topics.value.map((topic) => ({
        _id: topic._id,
        code: topic.code,
        title: topic.title,
        lecturer: topic.lecturerId.name || '',
        critical: topic.criticalLecturerId.name || '',
        students: topic.students || [],
        scheduleId: topic.scheduleId?._id || null,
        committeePresidentGrade: topic.committeePresidentGrade || 0,
        committeeSecretaryGrade: topic.committeeSecretaryGrade || 0,
        advisorLecturerGrade: topic.advisorLecturerGrade || 0,
        criticalLecturerGrade: topic.criticalLecturerGrade || 0,
        criticalLecturerApprove: topic.criticalLecturerApprove || false,
        advisorLecturerApprove: topic.advisorLecturerApprove || false,
        list_students: topic.list_students,
        limit: topic.limit,
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
        errorHandler(e);
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };

    return {
      headers,
      items,
      showRow,
      itemsSelected,
      loading,
      serverOptions,
      topicStudentLimit,
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
      selectHandler,
      checkCanEdit,
      schedules,
      headerTabs,
      tab,
      searchVal,
      search,
      showSelectStudent,
      selectStudentScheduleId,
      listStudentSelected,
      topicStudentId,
      changeStudents,
      canShowImport,
      selectStudents,
      listScheduleSelect,
      selectHandlerSchedule,
    };
  },
  data () {
    return {
      canEdit: false,
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
      'listSchedules',
    ]),
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
    async handleRemoveTopic (id) {
      this.removeId = id;
      this.showConfirmModal = true;
    },
  },
};
</script>
