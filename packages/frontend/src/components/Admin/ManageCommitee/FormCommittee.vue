<!-- eslint-disable max-len -->
<template>
  <div
    class="mx-4 my-4 rounded px-2 py-2 bg-slate-500 w-fit text-white font-semibold cursor-pointer"
    @click="rollBack"
  >
    Quay về
  </div>
  <div class="p-4  mx-auto mt-8">
    <!-- Modal content -->
    <div class="bg-white rounded-lg shadow">
      <!-- Modal header -->
      <div class="flex justify-between items-start p-4 rounded-t border-b">
        <!-- TODO: Rename for form committee -->
        <h3 class="text-xl font-semibold text-gray-900">
          Thông tin hội đồng
        </h3>
      </div>
      <template v-if="!loading">
        <div
          class="flex flex-col my-2"
        >
          <div
            v-if="page===1"
            class="ml-5 flex space-x-8  justify-center mt-4"
          >
            <div class="flex flex-col">
              <div class="w-[400px]">
                <FormKit
                  v-model="name"
                  type="text"
                  name="name"
                  label="Tên hội đồng"
                  validation="required"
                  :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
                  :disabled="isView"
                />
              </div>
              <div class="w-[400px]">
                <FormKit
                  v-if="isView"
                  v-model="code"
                  name="code"
                  type="text"
                  label="Mã hội đồng"
                  :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
                  validation="required"
                  :disabled="isView"
                />
              </div>
              <div class="w-[400px]">
                <FormKit
                  v-model="address"
                  name="address"
                  type="text"
                  label="Địa điểm"
                  :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
                  validation="required"
                  :disabled="isView"
                />
              </div>
              <div class="w-[400px]">
                <FormKit
                  v-model="defense_date"
                  name="defense_date"
                  type="datetime-local"
                  label="Thời gian"
                  :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
                  validation="required"
                  :disabled="isView"
                />
              </div>
              <div class="w-[400px]">
                <span class="font-bold text-sm">
                  Đợt đăng ký
                </span>
                <div class="mt-1">
                  <Multiselect
                    v-model="scheduleId"
                    :options="listSchedules"
                    :searchable="true"
                    :can-deselect="false"
                    no-results-text="Không có kết quả"
                    no-options-text="Không có lựa lựa chọn"
                    :can-clear="false"
                    :disabled="isView"
                  />
                </div>
              </div>
            </div>
            <div class="flex flex-col">
              <div class="w-[400px] mt-2">
                <span class="font-bold text-sm">
                  Giảng viên phản biện
                </span>
                <div class="mt-1">
                  <Multiselect
                    v-model="criticalLecturerId"
                    :options="listLecturers"
                    :searchable="true"
                    :can-deselect="false"
                    no-results-text="Không có kết quả"
                    no-options-text="Không có lựa lựa chọn"
                    :can-clear="false"
                    :disabled="isView"
                  />
                </div>
              </div>
              <div class="w-[400px] mt-2">
                <span class="font-bold text-sm">
                  Chủ tịch hội đồng
                </span>
                <div class="mt-1">
                  <Multiselect
                    v-model="committeePresidentId"
                    :options="listLecturers"
                    :searchable="true"
                    :can-deselect="false"
                    no-results-text="Không có kết quả"
                    no-options-text="Không có lựa lựa chọn"
                    :can-clear="false"
                    :disabled="isView"
                  />
                </div>
              </div>
              <div class="w-[400px] mt-2">
                <span class="font-bold text-sm">
                  Thư kí hội đồng
                </span>
                <div class="mt-1">
                  <Multiselect
                    v-model="committeeSecretaryId"
                    :options="listLecturers"
                    :searchable="true"
                    :can-clear="false"
                    :can-deselect="false"
                    no-results-text="Không có kết quả"
                    no-options-text="Không có lựa lựa chọn"
                    :disabled="isView"
                  />
                </div>
              </div>
            </div>
          </div>
          <div
            v-else
            class=" mx-4 my-4"
          >
            <div class="flex justify-between">
              <span class="font-bold text-sm">
                Danh đề tài được GVHD và GVPB duyệt
              </span>
              <div class="mx-auto" />
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
              class="py-4 mb-8"
            >
              <LoadingProcess />
            </div>
          </div>
          <div
            v-if="!isView"
            class="my-4 w-[300px] mx-auto mt-8"
          >
            <ul class="steps">
              <li
                v-for="(step, index) in steps"
                :key="index"
                class="step"
                :class="{ 'step-primary': page >= step.page }"
                @click="handleClickToPage(step.page)"
              >
                {{ step.label }}
              </li>
            </ul>
          </div>
          <div class="flex justify-center mt-4">
            <div
              v-if="isView"
              class="btn btn-primary w-[400px]"
              @click="showListTopics =true"
            >
              Xem danh sách đề tài
            </div>
          </div>
        </div>
        <!-- Modal footer -->
        <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200 mt-2 justify-end">
          <button
            v-if="!isView && page === 2"
            type="button"
            class="btn btn-secondary mx-2"
            @click="handleClickToPage(1)"
          >
            Quay lại
          </button>
          <button
            v-if="!isView && !loading && page !== 1"
            type="button"
            class="btn btn-primary"
            @click="handleAddCommitteeAdmin"
          >
            {{ isSave ? 'Lưu' : 'Cập nhật' }}
          </button>
          <button
            v-if="!isView && page === 1"
            type="button"
            class="btn btn-primary mx-2"
            @click="handleClickToPage(2)"
          >
            Tiếp theo
          </button>
        </div>
      </template>
      <div
        v-else
        class="py-4 mb-8"
      >
        <Loading />
      </div>
    </div>
  </div>
  <ShowTopicCommittee
    v-model="showListTopics"
    :topics="listTopicsView"
  />
</template>

<script>
import Multiselect from '@vueform/multiselect';
import { mapGetters, useStore } from 'vuex';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { useToast } from 'vue-toast-notification';
import moment from 'moment';
import UserApi from '../../../utils/api/user';
import CommitteeApi from '../../../utils/api/committee';
import ScheduleApi from '../../../utils/api/schedule';
import Loading from '../../common/Loading.vue';
import TopicApi from '../../../utils/api/topic';
import 'moment/dist/locale/vi';
import ShowTopicCommittee from '../../Modal/ShowTopicCommitee.vue';

export default {
  name: 'FormTopic',
  components: {
    Multiselect,
    Loading,
    ShowTopicCommittee,
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
    const showListTopics = ref(false);
    const selectStudentScheduleId = ref(null);
    const listTopicsSelected = ref([]);
    const criticalId = ref(0);
    const listTopicsView = ref([]);
    const currentCommittee = ref(null);
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
    // Committee form new
    const code = ref('');
    const name = ref('');
    const defense_date = ref('');
    const address = ref('');
    const committeePresidentId = ref('');
    const committeeSecretaryId = ref('');
    const criticalLecturerId = ref('');
    const listLecturers = ref([]);
    const scheduleId = ref('');
    const schedules = ref([]);
    const messages = ref('');
    const page = ref(1);
    const steps = ref([
      { label: 'Thông tin hội đồng', page: 1 },
      { label: 'Chọn đề tài', page: 2 },
    ]);

    const section = computed(() => store.getters['url/section']);
    const isSave = computed(() => section.value === 'committee-import');
    const isUpdate = computed(() => section.value === 'committee-update');
    const isView = computed(() => section.value === 'committee-view');

    const $toast = useToast();
    const errorHandler = (e) => {
      if (e.response?.data?.error?.code === 400) $toast.error(e.response.data.error.message);
      else { $toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    };

    const loadToServer = async (options) => {
      try {
        loading.value = true;
        // fetch topic approve and have same critical
        const response = await TopicApi.listAllTopicsByCriticalAndApproved(token, options, criticalLecturerId.value, scheduleId.value);
        // TODO: Add schedule when select
        // const response = await TopicApi.listAllTopicsByCriticalAndApproved(token, options, criticalLecturerId.value);
        topics.value = response.data;
        store.state.topic.listTopics = topics.value;
        serverItemsLength.value = response.meta.pagination.total;
      } catch (e) {
        console.log(e);
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

    const formatDate = (rawDate) => {
      try {
        if (!rawDate || rawDate === '') return '';
        const date = new Date(rawDate);
        const dateString = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
          .toISOString();
        const localDate = moment(dateString).local();
        return localDate.format('YYYY-MM-DD HH:mm:ss');
      } catch (e) {
        return '';
      }
    };

    const convertToUTC = (dateStr) => {
      const date = moment(dateStr); // parse the date string
      const utc = date.utc(); // convert to UTC
      return utc;
    };

    onMounted(async () => {
      loading.value = true;

      try {
        const lecturers = await UserApi.listUser(token, 'LECTURER');
        const listSchedules = await ScheduleApi.listAllSchedule(token);
        schedules.value = listSchedules.data;
        listLecturers.value = lecturers.data.map((lecturer) => {
          let l = {
            value: lecturer._id,
            label: lecturer.name,
          };
          if (isView.value) {
            l = { ...l, disabled: true };
          }
          return l;
        });
        // Choose first select
        if (schedules.value.length > 0) {
          scheduleId.value = schedules.value[0]._id;
        }

        if (isUpdate.value || isView.value) {
          const committeeId = store.getters['url/id'];
          const committee = await CommitteeApi.getCommittee(token, committeeId);
          // currentCommittee.value = committee;
          if (committee) {
            name.value = committee.name;
            code.value = committee.code;
            committeePresidentId.value = committee.president_id;
            committeeSecretaryId.value = committee.secretary_id;
            criticalLecturerId.value = committee.critical_id;
            defense_date.value = formatDate(committee.defense_date);
            address.value = committee.address;
            scheduleId.value = committee.schedule_id;
            listTopicsView.value = committee.list_topics || [];
          }
        }
      } catch (e) {
        console.log(e);
        errorHandler(e);
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
    const confirmRemove = async () => {
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
        $toast.success('Đã cập nhật  danh sách sinh viên thành công!');
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

    const check = () => {
      if (!name.value) {
        $toast.error('Vui lòng nhập tên hội đồng');
        return false;
      }
      if (!scheduleId.value) {
        $toast.error('Vui lòng nhập mã hội đồng');
        return false;
      }
      if (!committeePresidentId.value) {
        $toast.error('Vui lòng chọn chủ tịch hội đồng');
        return false;
      }
      if (!committeeSecretaryId.value) {
        $toast.error('Vui lòng chọn thư ký hội đồng');
        return false;
      }
      if (!criticalLecturerId.value) {
        $toast.error('Vui lòng chọn giảng viên phản biện');
        return false;
      }
      if (criticalLecturerId.value === committeeSecretaryId.value
          || criticalLecturerId.value === committeePresidentId.value
          || committeeSecretaryId.value === committeePresidentId.value) {
        $toast.error('Các giáo viên trong hội đồng phải khác nhau');
        return false;
      }
      return true;
    };

    const handleAddCommitteeAdmin = async () => {
      const committeeId = store.getters['url/id'];
      const valueTopics = listTopicsSelected.value.map((item) => item._id);
      const value = {
        name: name.value,
        code: code.value,
        committeePresidentId: committeePresidentId.value,
        committeeSecretaryId: committeeSecretaryId.value,
        criticalLecturerId: criticalLecturerId.value,
        schedule_id: scheduleId.value,
        topics: valueTopics,
        defense_date: convertToUTC(defense_date.value).format(),
        address: address.value,
      };
      console.log('🚀 ~ file: FormCommittee.vue:527 ~ handleAddCommitteeAdmin ~ value:', value);
      try {
        if (check()) {
          if (isSave.value) {
            loading.value = true;
            await CommitteeApi.addCommittee(token, value);
            $toast.success('Đã thêm thành công!');
            loading.value = false;
            rollBack();
          } else if (isUpdate.value) {
            loading.value = true;
            await CommitteeApi.updateCommittee(token, committeeId, value);
            $toast.success('Đã cập nhật thành công!');
            loading.value = false;
            rollBack();
          }
        }
      } catch (e) {
        loading.value = false;
        errorHandler(e);
        rollBack();
      }
    };

    const listSchedules = computed(() => {
      const listSchedulesValue = schedules.value.map((st) => ({
        value: st._id,
        label: st.code,
      }));
      return listSchedulesValue;
    });

    const handleClickToPage = async (pageNext) => {
      page.value = pageNext;
      if (page.value === 2) {
        loading.value = true;
        try {
          // load topic to selected
          if (isUpdate.value || isView.value) {
            const committeeId = store.getters['url/id'];
            const listTopic = await TopicApi.listAllTopicsByCommittee(token, committeeId);
            listTopicsSelected.value = [...listTopic];
          } else listTopicsSelected.value = [];
          await loadToServer(serverOptions.value);
        } catch (e) {
          errorHandler(e);
        }
        loading.value = false;
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
      handleAddCommitteeAdmin,
      check,
      code,
      name,
      committeePresidentId,
      committeeSecretaryId,
      criticalLecturerId,
      listLecturers,
      scheduleId,
      schedules,
      messages,
      page,
      steps,
      isSave,
      isUpdate,
      isView,
      listSchedules,
      handleClickToPage,
      showListTopics,
      currentCommittee,
      listTopicsView,
      defense_date,
      address,
    };
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
