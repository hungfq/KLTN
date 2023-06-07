<!-- eslint-disable max-len -->
<template>
  <div
    class="mx-4 my-4 rounded px-2 py-2 bg-slate-500 w-fit text-white font-semibold cursor-pointer"
    @click="rollBack"
  >
    Quay về
  </div>
  <div class="p-4 w-full mx-auto mt-[10px] ">
    <!-- Modal content -->
    <div class="bg-white rounded-lg shadow">
      <!-- Modal header -->
      <div class="flex justify-between items-start p-4 rounded-t border-b">
        <h3 class="text-xl font-semibold text-gray-900">
          Thông tin đề tài
        </h3>
      </div>
      <div class=" mx-4 my-4">
        <div class="flex justify-between">
          <span class="font-bold text-sm">
            Danh đề tài được GVHD và GVPB duyệt
          </span>
          <div class="mx-auto" />
          <!-- <ButtonImport

            :handle-import="handleImport"
            :title="'Nhập bằng file excel'"
          /> -->
        </div>
        <div class="mt-1">
          <div class="max-h-96 overflow-y-auto">
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
            />
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal footer -->
  <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200 my-4">
    <button
      v-if="!isView"
      type="button"
      class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4  focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
      @click="handleAddTopicAdmin"
    >
      {{ 'Cập nhật' }}
    </button>
  </div>
</template>

<script>
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';
import { useToast } from 'vue-toast-notification';
import Multiselect from '@vueform/multiselect';
import ButtonImport from '../../common/ButtonImport.vue';
import TopicApi from '../../../utils/api/topic';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import UploadButtonVue from '../UploadButton.vue';
import CommitteeApi from '../../../utils/api/committee';

export default {
  name: 'FormTopic',
  components: {
    ButtonImport,
  },
  setup () {
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const store = useStore();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const topics = ref([]);
    const selectSchedule = ref(0);
    const selectLecturer = ref(0);
    const showSelectStudent = ref(false);
    const selectStudentScheduleId = ref(null);
    const listTopicsSelected = ref([]);
    const criticalId = ref(0);
    const headers = [
      { text: 'Mã số', value: 'code', sortable: true },
      { text: 'Tên đề tài ', value: 'title', sortable: true },
      { text: 'Giảng viên hướng dẫn', value: 'lecturer' },
      { text: 'Giảng viên phản biện', value: 'critical' },
      { text: 'Đợt đăng ký', value: 'schedule' },
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
    // Get id from store
    const transformTopic = (listTopics) => listTopics.map((topic) => ({
      id: topic._id || topic.id,
      title: topic.title,
      code: topic.code,
    }));
    const loadToServer = async (options) => {
      loading.value = true;
      const response = await TopicApi.listAllTopicsByCritical(token, criticalId.value, options, selectSchedule.value);
      topics.value = response.data;
      store.state.topic.listTopics = topics.value;
      serverItemsLength.value = response.meta.pagination.total;
      loading.value = false;
    };

    const $toast = useToast();

    onMounted(async () => {
      try {
        const committeeId = store.getters['url/id'];
        const committee = await CommitteeApi.getCommittee(token, committeeId);
        listTopicsSelected.value = transformTopic(committee.list_topics);
        criticalId.value = committee.critical_id;
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

    const topicShow = computed(() => {
      if (!topics.value) return [];
      return topics.value.map((topic) => ({
        _id: topic._id,
        code: topic.code,
        title: topic.title,
        lecturer: topic.lecturerId.name || '',
        critical: topic.criticalLecturerId.name || '',
        schedule: topic.scheduleId.name || '',
        scheduleId: topic.scheduleId || '',
        list_students: topic.list_students,
      }));
    });
    const selectHandlerSchedule = async (value) => {
      selectSchedule.value = value;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };
    const handleAddTopicAdmin = async () => {
      try {
        console.log('🚀 ~ file: FormTopicCommittee.vue:195 ~ handleAddTopicAdmin ~ itemsSelected:', itemsSelected.value);
        showSelectStudent.value = false;
        $toast.success('Đã cập nhật  danh sách sinh viên thành công!');
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };
    const selectStudents = (item) => {
      selectStudentScheduleId.value = item.scheduleId._id;
      showSelectStudent.value = true;
      listTopicsSelected.value = item.list_students;
    };

    const rollBack = () => {
      store.dispatch('url/updateSection', `${modulePage.value}-list`);
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
      selectStudentScheduleId,
      rowItems,
      editItem,
      modulePage,
      handleImport,
      showConfirmModal,
      confirmRemove,
      listTopicsSelected,
      selectSchedule,
      selectLecturer,
      topicShow,
      BASE_API_URL,
      showSelectStudent,
      selectStudents,
      rollBack,
      selectHandlerSchedule,
      handleAddTopicAdmin,
    };
  },
  computed: {
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    ...mapGetters('auth', [
      'token',
    ]),
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
