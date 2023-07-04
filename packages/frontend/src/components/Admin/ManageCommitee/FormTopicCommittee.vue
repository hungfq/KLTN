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
          <!-- {{ itemsSelected }} -->
        </div>
        <div
          v-if="!loading"
          class="mt-1"
        >
          <div class="max-h-96 overflow-y-auto">
            <EasyDataTable
              v-model:items-selected="listTopicsSelected"
              v-model:server-options="serverOptions"
              :server-items-length="serverItemsLength"
              show-index
              :headers="headers"
              :items="topics"
              :loading="loading"
              :rows-items="rowItems"
            >
              <template #empty-message>
                <div class="text-center text-gray-500">
                  Không có đề tài được giáo viên hướng dẫn và giáo viên phản biện duyệt vào đợt này!
                </div>
              </template>
            </EasyDataTable>
          </div>
        </div>
        <div
          v-else
          class="py-4 mx-4"
        >
          <LoadingProcess />
        </div>
      </div>
    </div>
  </div>
  <!-- Modal footer -->
  <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200 my-4 justify-end">
    <!-- <div class="flex mt-4">
      <div>
        <UploadButton @uploadFileExcel="upload" />
      </div>
      <div>
        <ButtonDownloadTemplate :link="`${BASE_API_URL}/api/v2/template?type=TopicCommittee`" />
      </div>
    </div> -->
    <button
      v-if="!isView"
      class="btn btn-primary"
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
import ButtonImport from '../../common/ButtonImport.vue';
import TopicApi from '../../../utils/api/topic';
import CommitteeApi from '../../../utils/api/committee';
import UploadButton from '../UploadButton.vue';
import ButtonDownloadTemplate from '../../common/ButtonDownloadTemplate.vue';
import LoadingProcess from '../../common/Loading.vue';

export default {
  name: 'FormTopic',
  components: {
    UploadButton,
    ButtonDownloadTemplate,
    LoadingProcess,
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
    const currentCommittee = ref(null);
    const data = ref([]);
    const headers = [
      { text: 'Mã số', value: 'code', sortable: true },
      { text: 'Tên đề tài ', value: 'title', sortable: true },
      { text: 'Giảng viên hướng dẫn', value: 'lecturerId.name' },
      { text: 'Đợt đăng ký', value: 'scheduleId.code' },
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
    const $toast = useToast();
    const errorHandler = (e) => {
      if (e.response.data.error.code === 400) $toast.error(e.response.data.error.message);
      else { $toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    };

    const loadToServer = async (options) => {
      try {
        loading.value = true;
        const response = await TopicApi.listAllTopicsByCriticalAndApproved(token, options, criticalId.value, selectSchedule.value);
        // topics.value = transformTopic(response.data);
        topics.value = response.data;
        store.state.topic.listTopics = topics.value;
        serverItemsLength.value = response.meta.pagination.total;
      } catch (e) {
        errorHandler(e);
      }
      loading.value = false;
    };

    const topicShow = computed(() => {
      if (!topics.value) return [];
      return topics.value.map((topic) => ({
        _id: topic._id,
        code: topic.code,
        title: topic.title,
      }));
    });
    const rollBack = () => {
      store.dispatch('url/updateSection', `${modulePage.value}-list`);
    };

    onMounted(async () => {
      try {
        loading.value = true;
        const committeeId = store.getters['url/id'];
        const committee = await CommitteeApi.getCommittee(token, committeeId);
        const listTopic = await TopicApi.listAllTopicsByCommittee(token, committeeId);
        selectSchedule.value = committee.schedule_id;
        currentCommittee.value = committee;
        listTopicsSelected.value = [...listTopic];
        criticalId.value = committee.critical_id;
        await loadToServer(serverOptions.value);
        loading.value = false;
      } catch (e) {
        errorHandler(e);
        loading.value = false;
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
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

    const selectHandlerSchedule = async (value) => {
      selectSchedule.value = value;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        errorHandler(e);
      }
    };
    const handleAddTopicAdmin = async () => {
      try {
        const committeeId = store.getters['url/id'];
        const valueTopics = listTopicsSelected.value.map((item) => item._id);
        await CommitteeApi.updateTopicsCommittee(token, committeeId, valueTopics);
        $toast.success('Đã cập nhật danh sách đề tài thành công!');
        rollBack();
      } catch (e) {
        errorHandler(e);
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    };
    const selectStudents = (item) => {
      selectStudentScheduleId.value = item.scheduleId._id;
      showSelectStudent.value = true;
      listTopicsSelected.value = item.list_students;
    };

    const upload = async (files) => {
      if (files.length > 0) {
        try {
          const committeeId = store.getters['url/id'];
          loading.value = true;
          const res = await CommitteeApi.importTopicCommittee(token, committeeId, files[0]);
          if (res) {
            if (res.status === 200 && res.headers.get('Content-Type') === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
              $toast.error('Đề tài và mã đã tồn tại hoặc dữ liệu không chính xác');
            } else if (res.status === 200) {
              $toast.success('Đã nhập thành công!');
            }
          }
          loading.value = false;
          rollBack();
        } catch (e) {
          loading.value = false;
          // $toast.error('File không đúng chuẩn!');
          errorHandler(e);
        }
      } else {
        this.$toast.error('File không tồn tại');
      }
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
      upload,
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
