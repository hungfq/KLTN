<template>
  <vue-final-modal
    v-slot="{ close }"
    v-bind="$attrs"
    @before-open="handleShow()"
  >
    <div class="relative p-4 w-full max-w-4xl mx-auto mt-[5%] ">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow ">
        <!-- Modal header -->
        <div class="flex justify-between items-start p-4 rounded-t border-b">
          <h3 class="text-xl font-semibold text-gray-900 ">
            Chỉnh sửa giáo viên phản biện cho đề tài
          </h3>
          <button
            type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
            data-modal-toggle="defaultModal"
            @click="close"
          >
            <svg
              aria-hidden="true"
              class="w-5 h-5"
              fill="currentColor"
              viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg"
            ><path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            /></svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <div class="flex flex-col">
          <div class="px-2 py-2 flex items-center">
            <div class="w-full px-2">
              <div class="my-1 font-semibold">
                Chọn đợt đăng ký
              </div>
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
            <div class="w-full px-2">
              <div class="my-1 font-semibold">
                Chọn giáo viên phản biện cho đề tài
              </div>
              <Multiselect
                v-model="selectLecturer"
                :options="listLecturerSelect"
                :can-deselect="false"
                :searchable="true"
                no-results-text="Không có kết quả"
                no-options-text="Không có lựa lựa chọn"
                :can-clear="false"
                :placeholder="'Giảng viên hướng dẫn'"
                @change="selectHandlerLecturer"
              />
            </div>
          </div>
          <!-- <div
            class="my-4 w-[300px] mx-auto mt-2"
          >
            <ul class="steps">
              <li
                v-for="(step, index) in steps"
                :key="index"
                class="step"
                :class="{ 'step-primary': page >= step.page }"
                @click="page=step.page"
              >
                {{ step.label }}
              </li>
            </ul>
          </div> -->
          <div class="mx-4">
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
                  Không có đề tài trống giáo viên phản biện duyệt vào đợt này!
                </div>
              </template>
            </EasyDataTable>
          </div>
          <div class="flex justify-end">
            <button
              :disabled="checkSameValue"
              class="btn btn-primary m-2"
              @click="handleImportCriticalTopic"
            >
              Lưu lựa chọn
            </button>
          </div>
        </div>
      </div>
    </div>
  </vue-final-modal>
</template>
<script>
// import { mapGetters } from 'vuex';
import Multiselect from '@vueform/multiselect';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';
import { useToast } from 'vue-toast-notification';
import { cloneDeep } from 'lodash';
import ButtonImport from '../common/ButtonImport.vue';
import TopicApi from '../../utils/api/topic';
import CommitteeApi from '../../utils/api/committee';
// import UploadButton from '../UploadButton.vue';
// import ButtonDownloadTemplate from '../../common/ButtonDownloadTemplate.vue';
import LoadingProcess from '../common/Loading.vue';

export default {
  name: 'InfoModal',
  components: {
    Multiselect,
    LoadingProcess,
  },
  inheritAttrs: false,
  props: {
    listScheduleSelectRaw: [],
    listLecturerSelectRaw: [],
    selectedSchedule: 0,
  },
  setup (props) {
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
    const listScheduleSelect = ref([]);
    const listLecturerSelect = ref([]);
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
      loading.value = true;
      try {
        const response = await TopicApi.listTopicToAddCritical(token, options, selectSchedule.value, selectLecturer.value);
        const topicByCritical = await TopicApi.listAllTopicsByCriticalAndScheduleId(token, selectLecturer.value, selectSchedule.value);
        listTopicsSelected.value = topicByCritical.data;
        itemsSelected.value = cloneDeep(topicByCritical.data);
        topics.value = response.data;
        store.state.topic.listTopics = topics.value;
        serverItemsLength.value = response.meta.pagination.total;
      } catch (e) {
        errorHandler(e);
      }
      loading.value = false;
    };

    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });
    watch(modulePage, async () => { await loadToServer(serverOptions.value); });
    const showConfirmModal = ref(false);

    const selectHandlerSchedule = async (value) => {
      selectSchedule.value = value;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        errorHandler(e);
      }
    };

    const selectHandlerLecturer = async (value) => {
      selectLecturer.value = value;
      try {
        await loadToServer(serverOptions.value);
      } catch (e) {
        errorHandler(e);
      }
    };

    const handleShow = async () => {
      listLecturerSelect.value = [...props.listLecturerSelectRaw];
      listScheduleSelect.value = [...props.listScheduleSelectRaw];
      listScheduleSelect.value.shift();
      listLecturerSelect.value.shift();
      // default value
      selectSchedule.value = props.selectedSchedule;
      if (!selectSchedule.value && listLecturerSelect.value.length > 0) {
        selectSchedule.value = listLecturerSelect.value[0].value;
      }
      if (listLecturerSelect.value.length > 0) {
        selectLecturer.value = listLecturerSelect.value[0].value;
      }
      await loadToServer(serverOptions.value);
    };

    const handleImportCriticalTopic = async () => {
      const topicIds = listTopicsSelected.value.map((t) => t._id);
      try {
        loading.value = true;
        await TopicApi.importCriticalToTopic(token, selectLecturer.value, selectSchedule.value, topicIds);
        $toast.success('Đã thêm giáo viên phản biện cho đề tài thành công!');
        await loadToServer(serverOptions.value);
      } catch (e) {
        errorHandler(e);
      } finally {
        loading.value = false;
      }
    };
    const checkSameValue = computed(() => {
      const preIds = itemsSelected.value.map((item) => item._id);
      const afterIds = listTopicsSelected.value.map((item) => item._id);
      if (preIds.length !== afterIds.length) {
        return false;
      }
      preIds.sort();
      afterIds.sort();
      return JSON.stringify(preIds) === JSON.stringify(afterIds);
    });
    return {
      headers,
      items,
      itemsSelected,
      loading,
      serverOptions,
      topics,
      serverItemsLength,
      selectStudentScheduleId,
      rowItems,
      modulePage,
      showConfirmModal,
      listTopicsSelected,
      selectSchedule,
      selectLecturer,
      BASE_API_URL,
      showSelectStudent,
      selectHandlerSchedule,
      selectHandlerLecturer,
      listScheduleSelect,
      listLecturerSelect,
      handleShow,
      handleImportCriticalTopic,
      checkSameValue,
    };
  },
  computed: {
    ...mapGetters('auth', [
      'token',
    ]),
    ...mapGetters('schedule', [
      'listStudentsOfSchedule',
    ]),
  },
  methods: {
  },
};
</script>
