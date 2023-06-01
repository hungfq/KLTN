<!-- eslint-disable max-len -->
<template>
  <div
    class="mx-4 my-4 rounded px-2 py-2 bg-slate-500 w-fit text-white font-semibold cursor-pointer"
    @click="rollBack"
  >
    Quay về
  </div>
  <div class="p-4 w-full h-full md:h-auto mx-auto mt-[10px]">
    <!-- Modal content -->
    <div class="bg-white rounded-lg shadow">
      <!-- Modal header -->
      <div class="flex justify-between items-start p-4 rounded-t border-b">
        <h3 class="text-xl font-semibold text-gray-900">
          Thông tin lịch đăng ký
        </h3>
      </div>

      <div v-if="!loading">
        <div
          class="ml-5 mt-5 flex flex-col items-center"
          :class="{'h-72' : !isView}"
        >
          <div
            v-if="page === 1 || isView"
            class="flex"
          >
            <div class="flex flex-col mx-8">
              <FormKit
                v-model="name"
                type="text"
                name="name"
                label="Tên đợt đăng ký"
                help="TLCN K19 HK1"
                :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
                validation="required"
                outer-class="w-[400px]"
                :disabled="isView"
              />
              <FormKit
                v-model="code"
                name="code"
                type="text"
                outer-class="w-[400px]"
                label="Mã đợt"
                :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
                validation="required"
                :disabled="isView"
              />
            </div>
            <FormKit
              v-model="description"
              name="description"
              type="textarea"
              label="Mô tả"
              outer-class="!mx-8 w-[400px]"
              help="Ghi các thông tin chi tiết tại đây"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
              validation="required"
              :disabled="isView"
            />
          </div>
          <div
            v-if="page===2 || isView"
            class="flex"
          >
            <FormKit
              v-model="startProposalDate"
              name="startProposalDate"
              type="datetime-local"
              outer-class="!mx-8 w-[400px]"
              label="Thời gian bắt đầu đề xuất"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
              validation="required"
              :disabled="isView"
            />
            <FormKit
              v-model="endProposalDate"
              name="startProposalDate"
              outer-class="!mx-8 w-[400px]"
              type="datetime-local"
              label="Thời gian kết thúc đề xuất"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
              validation="required"
              :disabled="isView"
            />
          </div>
          <div
            v-if="page===3 || isView"
            class="flex"
          >
            <FormKit
              v-model="startApproveDate"
              name="startApproveDate"
              outer-class="!mx-8 w-[400px]"
              type="datetime-local"
              label="Thời gian bắt đầu chấp thuận"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
              validation="required"
              :disabled="isView"
            />
            <FormKit
              v-model="endApproveDate"
              name="endApproveDate"
              type="datetime-local"
              outer-class="!mx-8 w-[400px]"
              label="Thời gian kết thúc chấp thuận"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
              validation="required"
              :disabled="isView"
            />
          </div>
          <div
            v-if="page===4 || isView"
            class="flex"
          >
            <FormKit
              v-model="startRegisterDate"
              name="startRegisterDate"
              type="datetime-local"
              outer-class="!mx-8 w-[400px]"
              label="Thời gian bắt đầu đăng ký"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
              validation="required"
              :disabled="isView"
            />
            <FormKit
              v-model="endRegisterDate"
              name="endRegisterDate"
              type="datetime-local"
              outer-class="!mx-8 w-[400px]"
              label="Thời gian kết thúc đăng ký"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
              validation="required"
              :disabled="isView"
            />
          </div>
          <div
            v-if="page===5 || isView"
            class="flex"
          >
            <FormKit
              v-model="startDate"
              name="startDate"
              outer-class="!mx-8 w-[400px]"
              type="datetime-local"
              label="Thời gian bắt đầu làm đề tài"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
              validation="required"
              :disabled="isView"
            />
            <FormKit
              v-model="deadline"
              name="deadline"
              outer-class="!mx-8 w-[400px]"
              type="datetime-local"
              label="Thời gian kết thúc làm đề tài"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
              validation="required"
              :disabled="isView"
            />
          </div>
          <div
            v-if="page===6 || isView"
            class="flex"
          >
            <FormKit
              v-model="mark_start"
              name="mark_start"
              outer-class="!mx-8 w-[400px]"
              type="datetime-local"
              label="Thời gian bắt đầu chấm bài"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
              validation="required"
              :disabled="isView"
            />
            <FormKit
              v-model="mark_end"
              name="mark_end"
              type="datetime-local"
              outer-class="!mx-8 w-[400px]"
              label="Thời gian kết thúc chấm bài"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
              validation="required"
              :disabled="isView"
            />
          </div>
          <button
            v-if="isView"
            class="rounded bg-slate-500 h-[60px] !mx-8 w-[400px]  text-white font-semibold cursor-pointer "
            @click="showInfoStudent"
          >
            Xem danh sách sinh viên
          </button>
        </div>
        <div
          v-if="!isView"
          class="my-4 w-[900px] mx-auto"
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
        </div>
      </div>
      <Loading v-if="loading" />
      <!-- Modal footer -->
      <div class="flex items-end p-6 rounded-b border-t border-gray-200">
        <button
          v-if="!isView && !loading && page === 6"
          type="button"
          class="btn btn-primary"
          @click="handleAddScheduleAdmin"
        >
          {{ isSave ? 'Lưu' : 'Cập nhật' }}
        </button>
        <button
          v-if="page != 6 && !isView"
          type="button"
          class="btn btn-primary"
          @click="page+=1"
        >
          Tiếp theo
        </button>
      </div>
    </div>
  </div>
  <InfoStudentVue
    v-model="showInfo"
    :schedule-id="id"
  />
</template>

<script>
import { mapGetters } from 'vuex';
import moment from 'moment';
import InfoStudentVue from '../../Modal/InfoStudent.vue';
import ScheduleApi from '../../../utils/api/schedule';
import Loading from '../../common/Loading.vue';
import 'moment/dist/locale/vi';

export default {
  name: 'FormSchedule',
  components: {
    InfoStudentVue,
    Loading,
  },
  data () {
    return {
      showInfo: false,
      name: '',
      code: '',
      startDate: '',
      deadline: '',
      description: '',
      startProposalDate: '',
      endProposalDate: '',
      startRegisterDate: '',
      endRegisterDate: '',
      startApproveDate: '',
      endApproveDate: '',
      mark_end: '',
      mark_start: '',
      topics: [],
      students: [],
      listTopics: [],
      listStudents: [],
      infoUser: [],
      loading: false,
      page: 1,
      steps: [
        { label: 'Thông tin cơ bản cho lịch đăng ký', page: 1 },
        { label: 'Thời gian sinh viên đề xuất', page: 2 },
        { label: 'Thời gian giảng viên duyệt đề xuất', page: 3 },
        { label: 'Thời gian sinh viên đăng ký đề tài', page: 4 },
        { label: 'Thời gian sinh viên làm đề tài', page: 5 },
        { label: 'Thời gian giảng viên chấm bài', page: 6 },
      ],
    };
  },
  computed: {
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    ...mapGetters('auth', [
      'token',
    ]),
    isSave () {
      return this.section === 'schedule-import';
    },
    isUpdate () {
      return this.section === 'schedule-update';
    },
    isView () {
      return this.section === 'schedule-view';
    },
  },
  async mounted () {
    this.loading = true;
    if (this.isUpdate || this.isView) {
      const { id } = this.$store.state.url;
      // const { listSchedules } = this.$store.state.schedule;
      try {
        const scheduleRaw = await ScheduleApi.fetchSchedule(this.token, id);
        const schedule = scheduleRaw.data;
        if (schedule) {
          this.name = schedule.name;
          this.description = schedule.description;
          this.code = schedule.code;
          this.startDate = this.formatDate(schedule.startDate);
          this.deadline = this.formatDate(schedule.deadline);
          this.startProposalDate = this.formatDate(schedule.startProposalDate);
          this.endProposalDate = this.formatDate(schedule.endProposalDate);
          this.startApproveDate = this.formatDate(schedule.startApproveDate);
          this.endApproveDate = this.formatDate(schedule.endApproveDate);
          this.startRegisterDate = this.formatDate(schedule.startRegisterDate);
          this.endRegisterDate = this.formatDate(schedule.endRegisterDate);
          this.mark_end = this.formatDate(schedule.mark_end);
          this.mark_start = this.formatDate(schedule.mark_start);
          this.students = schedule.students;
        }
      } catch (e) {
        this.$toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    }
    this.loading = false;
  },
  methods: {
    showInfoStudent () {
      this.showInfo = true;
    },
    rollBack () {
      this.$store.dispatch('url/updateSection', `${this.module}-list`);
    },
    async handleAddScheduleAdmin () {
      const {
        name, description, startDate, deadline, startProposalDate,
        endProposalDate, startRegisterDate, endRegisterDate, startApproveDate,
        endApproveDate, students, code, mark_start, mark_end,
      } = this;
      const value = {
        name,
        description,
        startDate: this.convertToUTC(startDate).format(),
        deadline: this.convertToUTC(deadline).format(),
        startProposalDate: this.convertToUTC(startProposalDate).format(),
        endProposalDate: this.convertToUTC(endProposalDate).format(),
        startRegisterDate: this.convertToUTC(startRegisterDate).format(),
        endRegisterDate: this.convertToUTC(endRegisterDate).format(),
        startApproveDate: this.convertToUTC(startApproveDate).format(),
        endApproveDate: this.convertToUTC(endApproveDate).format(),
        mark_start: this.convertToUTC(mark_start).format(),
        mark_end: this.convertToUTC(mark_end).format(),
        students,
        code,
      };
      try {
        if (this.isSave) {
          if (this.checkDate()) {
            await this.$store.dispatch('schedule/addSchedule', { token: this.token, value });
            this.$toast.success('Đã thêm thành công!');
            this.rollBack();
          }
        } else if (this.isUpdate) {
          if (this.checkDate()) {
            await this.$store.dispatch('schedule/updateSchedule', { token: this.token, value: { ...value, _id: this.id } });
            this.$toast.success('Đã cập nhật thành công!');
            this.rollBack();
          }
        }
      } catch (e) {
        this.$toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
      }
    },
    formatDate (rawDate) {
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
    },
    convertToUTC (dateStr) {
      const date = moment(dateStr); // parse the date string
      const utc = date.utc(); // convert to UTC
      return utc;
    },
    checkDate () {
      if (!this.name) {
        this.$toast.error('Tên đợt đăng ký là bắt buộc');
        return false;
      }
      if (!this.code) {
        this.$toast.error('Mã đợt đăng ký là bắt buộc');
        return false;
      }
      if (this.startProposalDate >= this.endProposalDate) {
        this.$toast.error('Ngày bắt đầu đề xuất phải nhỏ hơn ngày kết thúc đề xuất ');
        return false;
      }
      if (this.endProposalDate >= this.startApproveDate) {
        this.$toast.error('Ngày kết thúc đề xuất phải nhỏ hơn ngày bắt đầu duyệt đề tài ');
        return false;
      }
      if (this.startApproveDate >= this.endApproveDate) {
        this.$toast.error('Ngày bắt đầu duyệt đề tài phải nhỏ hơn ngày kết thúc duyệt đề tài ');
        return false;
      }
      if (this.endApproveDate >= this.startRegisterDate) {
        this.$toast.error('Ngày kết thúc duyệt đề tài phải nhỏ hơn ngày bắt đầu đăng ký đề tài ');
        return false;
      }
      if (this.startApproveDate >= this.endApproveDate) {
        this.$toast.error('Ngày bắt đầu đăng kí đề tài phải nhỏ hơn ngày kết thúc đăng ký đề tài ');
        return false;
      }
      if (this.startDate >= this.deadline) {
        this.$toast.error('Ngày bắt đầu làm đề tài phải nhỏ hơn ngày kết thúc làm đề tài ');
        return false;
      }
      if (this.deadline >= this.mark_start) {
        this.$toast.error('Ngày kết thúc làm đề tài phải nhỏ hơn ngày bắt đầu chấm điểm đề tài ');
        return false;
      }
      if (this.mark_start >= this.mark_end) {
        this.$toast.error('Ngày bắt đầu chấm diểm đề tài phải nhỏ hơn ngày kết thúcc chấm điểm đề tài ');
        return false;
      }
      return true;
    },
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
