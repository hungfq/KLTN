<!-- eslint-disable no-undef -->
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
        >
          <div
            v-if="page === 1 || isView"
            class="flex"
            :class="{'min-h-[400px]' : !isView}"
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
            :class="{'min-h-[400px]' : !isView}"
          >
            <div class="flex space-x-4">
              <div
                class="flex flex-col w-[350px]"
              >
                <div class="font-semibold my-2">
                  Thời gian đề xuất đề tài
                </div>
                <litepie-datepicker
                  v-model="proposalTime"
                  placeholder="Khoảng thời gian đề xuất đề tài"
                  separator=" đến "
                  :formatter="formatter"
                  i18n="vi"
                  :auto-apply="true"
                  :options="options"
                />
              </div>
              <div
                class="flex flex-col w-[350px]"
              >
                <div class="font-semibold my-2">
                  Thời gian phê duyệt đề tài
                </div>
                <litepie-datepicker
                  v-model="approveTime"
                  placeholder="Khoảng thời gian phê duyệt đề tài"
                  separator=" đến "
                  :formatter="formatter"
                  i18n="vi"
                  :auto-apply="true"
                  :options="options"
                />
              </div>
            </div>
            <div class="flex space-x-4">
              <div
                class="flex flex-col w-[350px]"
              >
                <div class="font-semibold my-2">
                  Thời gian đăng ký đề tài
                </div>
                <litepie-datepicker
                  v-model="registerTime"
                  placeholder="Khoảng thời gian đăng ký đề tài"
                  separator=" đến "
                  :formatter="formatter"
                  i18n="vi"
                  :auto-apply="true"
                  :options="options"
                />
              </div>
              <div
                class="flex flex-col w-[350px]"
              >
                <div class="font-semibold my-2">
                  Thời gian chấm điểm đề tài
                </div>
                <litepie-datepicker
                  v-model="markTime"
                  placeholder="Khoảng thời gian chấm điểm đề tài"
                  separator=" đến "
                  :formatter="formatter"
                  i18n="vi"
                  :auto-apply="true"
                  :options="options"
                />
              </div>
              <!-- <div
                class="flex flex-col w-[350px]"
              >
                <div class="font-semibold my-2">
                  Thời gian làm đề tài
                </div>
                <litepie-datepicker
                  v-model="workTime"
                  placeholder="Khoảng thời gian làm đề tài"
                  separator=" đến "
                  :formatter="formatter"
                  i18n="vi"
                  :auto-apply="true"
                  :options="options"
                />
              </div> -->
            </div>
            <!-- <div class="flex space-x-4">
              <div
                class="flex flex-col w-[350px]"
              >
                <div class="font-semibold my-2">
                  Thời gian chấm điểm đề tài
                </div>
                <litepie-datepicker
                  v-model="markTime"
                  placeholder="Khoảng thời gian chấm điểm đề tài"
                  separator=" đến "
                  :formatter="formatter"
                  i18n="vi"
                  :auto-apply="true"
                  :options="options"
                />
              </div>
            </div> -->
          </div>
          <div
            v-if="page===3 || isView"
            :class="{'min-h-[400px]' : !isView}"
          >
            <div class="flex flex-col mb-4">
              <div class="font-bold my-4">
                Danh sách sinh viên đã chọn
              </div>
              <div class="mb-4">
                <EasyDataTable
                  show-index
                  :headers="headers"
                  :items="listStudentsSelected"
                  :loading="loading"
                  :rows-per-page="5"
                  buttons-pagination
                />
              </div>
              <button
                v-if="!isView"
                class="btn btn-primary mt-5 !mx-8 "
                @click="chooseStudent"
              >
                Chọn danh sách sinh viên
              </button>
            </div>
          </div>
          <template v-if="page===4">
            <div class="flex flex-col">
              <div class="flex justify-between">
                <div class="flex flex-col pt-8">
                  <FormKit
                    v-model="name"
                    type="text"
                    name="name"
                    label="Tên đợt đăng ký"
                    help="TLCN K19 HK1"
                    :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
                    validation="required"
                    outer-class="w-[350px]"
                    :disabled="isView"
                  />
                  <FormKit
                    v-model="code"
                    name="code"
                    type="text"
                    outer-class="w-[350px]"
                    label="Mã đợt"
                    :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
                    validation="required"
                    :disabled="isView"
                  />
                  <FormKit
                    v-model="description"
                    name="description"
                    type="textarea"
                    label="Mô tả"
                    outer-class="w-[350px]"
                    help="Ghi các thông tin chi tiết tại đây"
                    :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
                    validation="required"
                    :disabled="isView"
                  />
                </div>
                <div class="flex flex-col mb-4">
                  <div class="font-bold my-4">
                    Danh sách sinh viên đã chọn
                  </div>
                  <div class="mb-4">
                    <EasyDataTable
                      show-index
                      :headers="headers"
                      :items="listStudentsSelected"
                      :loading="loading"
                      :rows-per-page="5"
                      buttons-pagination
                    />
                  </div>
                </div>
              </div>
              <div class="w-[1200px]">
                <g-gantt-chart
                  :chart-start="formatToMinute(startProposalDate)"
                  :chart-end="formatToMinute(mark_end)"
                  :precision="calculateType"
                  color-scheme="vue"
                  bar-start="myBeginDate"
                  bar-end="myEndDate"
                  row-height="100"
                >
                  <g-gantt-row
                    :bars="barGantChar"
                  />
                </g-gantt-chart>
              </div>
              <div class="flex mt-4">
                <div class="mr-5">
                  <div class="flex item-center">
                    <div
                      class="w-4 h-4 mt-1 mr-2"
                      style="background-color: #e09b69;"
                    />
                    <div>Thời gian đề xuất</div>
                  </div>
                  <div class="flex item-center">
                    <div
                      class="w-4 h-4 mt-1 mr-2"
                      style="background-color: #FF6666;"
                    />
                    <div>Thời gian phê duyệt</div>
                  </div>
                </div>
                <div class="ml-5">
                  <div class="flex item-center">
                    <div
                      class="w-4 h-4 mt-1 mr-2"
                      style="background-color: #339933;"
                    />
                    <div>Thời gian đăng ký</div>
                  </div>
                  <div class="flex item-center">
                    <div
                      class="w-4 h-4 mt-1 mr-2"
                      style="background-color: #CCCC00;"
                    />
                    <div>Thời gian chấm điểm</div>
                  </div>
                </div>
              </div>
            </div>
          </template>
          <button
            v-if="isView"
            class="rounded bg-slate-500 h-[60px] !mx-8 w-[400px]  text-white font-semibold cursor-pointer "
            @click="showInfoStudent"
          >
            Xem danh sách sinh viên
          </button>
          <div
            v-if="!isView"
            class="my-4 w-[700px] mx-auto"
          >
            <ul class="steps">
              <li
                v-for="(step, index) in steps"
                :key="index"
                class="step"
                :class="{ 'step-primary': page >= step.page }"
                @click="nextPage(step.page)"
              >
                {{ step.label }}
              </li>
            </ul>
          </div>
        </div>
      </div>
      <Loading v-if="loading" />
      <!-- Modal footer -->
      <div class="flex justify-end items-end p-6 rounded-b border-t border-gray-200">
        <button
          v-if="!isView && page > 1&&!loading"
          type="button"
          class="btn btn-secondary mx-2"
          @click="nextPage(page-1)"
        >
          Quay lại
        </button>
        <button
          v-if="!isView && !loading && page === 4"
          type="button"
          class="btn btn-primary"
          @click="handleAddScheduleAdmin"
        >
          {{ isSave ? 'Lưu' : 'Cập nhật' }}
        </button>
        <button
          v-if="page != 4 && !isView"
          type="button"
          class="btn btn-primary"
          @click="nextPage(page+1)"
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
  <SelectStudent
    v-model="showSelectStudent"
    :schedule-id="scheduleId"
    :selected="listStudentsSelected"
    :enabled-excel="true"
    :type="'SCHEDULE-FORM'"
    @change-students="changeStudents"
  />
</template>

<script>
import { mapGetters } from 'vuex';
import dayjs from 'dayjs';
import moment from 'moment';
import InfoStudentVue from '../../Modal/InfoStudent.vue';
import ScheduleApi from '../../../utils/api/schedule';
import Loading from '../../common/Loading.vue';
import 'moment/dist/locale/vi';
import 'dayjs/locale/vi';
import SelectStudent from '../../Modal/SelectStudent.vue';
import LitepieDatepicker from 'litepie-datepicker';

// dayjs().locale('vi');

export default {
  name: 'FormSchedule',
  components: {
    InfoStudentVue,
    Loading,
    SelectStudent,
    LitepieDatepicker,

  },
  data () {
    return {
      headers: [
        {
          text: 'Mã số', value: 'code', sortable: true, width: 200,
        },
        {
          text: 'Tên ', value: 'name', sortable: true, width: 300,
        },
        { text: 'Email', value: 'email', width: 300 },
      ],
      showInfo: false,
      name: '',
      code: '',
      startDate: null,
      deadline: null,
      description: '',
      startProposalDate: null,
      endProposalDate: null,
      startRegisterDate: null,
      endRegisterDate: null,
      startApproveDate: null,
      endApproveDate: null,
      mark_end: null,
      mark_start: null,
      topics: [],
      students: [],
      listTopics: [],
      listStudents: [],
      infoUser: [],
      loading: false,
      page: 1,
      steps: [
        { label: 'Thông tin cơ bản cho lịch đăng ký', page: 1 },
        { label: 'Thời gian', page: 2 },
        { label: 'Chọn sinh viên', page: 3 },
        { label: 'Xem lại', page: 4 },
      ],
      showSelectStudent: false,
      selectStudentScheduleId: null,
      scheduleId: null,
      listStudentsSelected: [],
      dateValue: [],
      formatter: {
        date: 'L LTS',
        month: 'MMMM',
      },
      options: {
        shortcuts: {
          today: 'Hôm nay',
          yesterday: 'Hôm trước',
          past: (period) => `${period} ngày trước`,
          currentMonth: 'Tháng nay',
          pastMonth: 'Tháng trước',
        },
        footer: {
          apply: 'Áp dụng',
          cancel: 'Huỷ',
        },
      },
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
    calculateType () {
      const first = moment(this.startProposalDate, 'L LTS');
      const second = moment(this.mark_end, 'L LTS');
      const days = Math.abs(second.diff(first, 'days'));
      if (days <= 1) return 'hour';
      if (days <= 30) return 'day';
      return 'month';
    },
    proposalTime: {
      get () {
        if (!this.startProposalDate || !this.endProposalDate) return [];
        return [this.startProposalDate, this.endProposalDate];
      },
      // setter
      set (newValue) {
        [this.startProposalDate, this.endProposalDate] = newValue;
      },
    },
    approveTime: {
      get () {
        if (!this.startApproveDate || !this.endApproveDate) return [];
        return [this.startApproveDate, this.endApproveDate];
      },
      // setter
      set (newValue) {
        [this.startApproveDate, this.endApproveDate] = newValue;
      },
    },
    registerTime: {
      get () {
        if (!this.startRegisterDate || !this.endRegisterDate) return [];
        return [this.startRegisterDate, this.endRegisterDate];
      },
      // setter
      set (newValue) {
        [this.startRegisterDate, this.endRegisterDate] = newValue;
      },
    },
    workTime: {
      get () {
        if (!this.startDate || !this.deadline) return [];
        return [this.startDate, this.deadline];
      },
      // setter
      set (newValue) {
        // Note: we are using destructuring assignment syntax here.
        [this.startDate, this.deadline] = newValue;
      },
    },
    markTime: {
      get () {
        if (!this.mark_start || !this.mark_end) return [];
        return [this.mark_start, this.mark_end];
      },
      // setter
      set (newValue) {
        // Note: we are using destructuring assignment syntax here.
        [this.mark_start, this.mark_end] = newValue;
      },
    },
    barGantChar () {
      const barProposal = {
        myBeginDate: this.formatToMinute(this.startProposalDate),
        myEndDate: this.formatToMinute(this.endProposalDate),
        ganttBarConfig: { // each bar must have a nested ganttBarConfig object ...
          id: 'proposal-time', // ... and a unique "id" property
          label: 'Đề xuất',
          style: { // arbitrary CSS styling for your bar
            background: '#e09b69',
            borderRadius: '5px',
            color: 'black',
            height: '100px',
            'overflow-wrap': 'normal',
            'word-break': 'normal',
          },
        },
      };

      const barRegister = {
        myBeginDate: this.formatToMinute(this.startRegisterDate),
        myEndDate: this.formatToMinute(this.endRegisterDate),
        ganttBarConfig: { // each bar must have a nested ganttBarConfig object ...
          id: 'approve-time', // ... and a unique "id" property
          label: 'Đăng ký',
          style: { // arbitrary CSS styling for your bar
            background: '#339933',
            borderRadius: '5px',
            color: 'black',
            height: '100px',
            'overflow-wrap': 'normal',
            'word-break': 'normal',
          },
        },
      };
      const barApprove = {
        myBeginDate: this.formatToMinute(this.startApproveDate),
        myEndDate: this.formatToMinute(this.endApproveDate),
        ganttBarConfig: { // each bar must have a nested ganttBarConfig object ...
          id: 'approve-time', // ... and a unique "id" property
          label: 'Chấp thuận',
          style: { // arbitrary CSS styling for your bar
            background: '#FF6666',
            borderRadius: '5px',
            color: 'black',
            height: '100px',
            'overflow-wrap': 'normal',
            'word-break': 'normal',
          },
        },
      };
      const barMark = {
        myBeginDate: this.formatToMinute(this.mark_start),
        myEndDate: this.formatToMinute(this.mark_end),
        ganttBarConfig: { // each bar must have a nested ganttBarConfig object ...
          id: 'mark-time', // ... and a unique "id" property
          label: 'Chấm điểm',
          style: { // arbitrary CSS styling for your bar
            background: '#CCCC00',
            borderRadius: '5px',
            color: 'black',
            height: '100px',
            'overflow-wrap': 'normal',
            'word-break': 'normal',
          },
        },
      };
      return [barProposal, barRegister, barApprove, barMark];
    },
    date () {
      return new Date(this.startDate);
    },
  },
  async mounted () {
    this.loading = true;
    if (this.isUpdate || this.isView) {
      const { id } = this.$store.state.url;
      // const { listSchedules } = this.$store.state.schedule;
      try {
        this.scheduleId = id;
        const scheduleRaw = await ScheduleApi.fetchSchedule(this.token, id);
        const schedule = scheduleRaw.data;
        if (schedule) {
          this.name = schedule.name;
          this.description = schedule.description;
          this.code = schedule.code;
          // this.startDate = this.formatDate(schedule.startDate);
          // this.deadline = this.formatDate(schedule.deadline);
          this.startProposalDate = this.formatDate(schedule.startProposalDate);
          this.endProposalDate = this.formatDate(schedule.endProposalDate);
          this.startApproveDate = this.formatDate(schedule.startApproveDate);
          this.endApproveDate = this.formatDate(schedule.endApproveDate);
          this.startRegisterDate = this.formatDate(schedule.startRegisterDate);
          this.endRegisterDate = this.formatDate(schedule.endRegisterDate);
          this.mark_end = this.formatDate(schedule.mark_end);
          this.mark_start = this.formatDate(schedule.mark_start);
          this.students = schedule.students;
          this.listStudentsSelected = schedule.list_students;
        }
      } catch (e) {
        this.$toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
      }
    }
    this.loading = false;
  },
  methods: {

    nextPage (pageNumber) {
      if (pageNumber === 2) {
        if (!this.name) {
          this.$toast.error('Tên đợt đăng ký là bắt buộc');
          return;
        }
        if (!this.code) {
          this.$toast.error('Mã đợt đăng ký là bắt buộc');
          return;
        }
      } else if (pageNumber === 3) {
        if (!this.checkDate()) return;
      }
      this.page = pageNumber;
    },
    errorHandler (e) {
      if (e.response.data.error.code === 400) this.$toast.error(e.response.data.error.message);
      else { this.$toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    },
    showInfoStudent () {
      this.showInfo = true;
    },
    rollBack () {
      this.$store.dispatch('url/updateSection', `${this.module}-list`);
    },
    async handleAddScheduleAdmin () {
      const {
        name, description, startProposalDate,
        endProposalDate, startRegisterDate, endRegisterDate, startApproveDate,
        // eslint-disable-next-line camelcase
        endApproveDate, students, code, mark_start, mark_end,
      } = this;
      const value = {
        name,
        description,
        // startDate: this.convertToUTC(startDate).format(),
        // deadline: this.convertToUTC(deadline).format(),
        startProposalDate: this.convertToUTC(startProposalDate).format(),
        endProposalDate: this.convertToUTC(endProposalDate).format(),
        startRegisterDate: this.convertToUTC(startRegisterDate).format(),
        endRegisterDate: this.convertToUTC(endRegisterDate).format(),
        startApproveDate: this.convertToUTC(startApproveDate).format(),
        endApproveDate: this.convertToUTC(endApproveDate).format(),
        // eslint-disable-next-line no-undef
        mark_start: this.convertToUTC(mark_start).format(),
        // eslint-disable-next-line no-undef
        mark_end: this.convertToUTC(mark_end).format(),
        students,
        code,
      };
      try {
        if (this.isSave) {
          this.loading = true;
          if (this.checkDate()) {
            // await this.$store.dispatch('schedule/addSchedule', { token: this.token, value });
            await ScheduleApi.addSchedule(this.token, value);
            this.loading = false;
            this.$toast.success('Đã thêm thành công!');
            this.rollBack();
          }
        } else if (this.isUpdate) {
          if (this.checkDate()) {
            await ScheduleApi.updateSchedule(this.token, { ...value, _id: this.id });
            this.loading = false;
            // await this.$store.dispatch('schedule/updateSchedule', { token: this.token, value: { ...value, _id: this.id } });
            this.$toast.success('Đã cập nhật thành công!');
            this.rollBack();
          }
        }
      } catch (e) {
        console.log('🚀 ~ file: FormScheduleV2.vue:745 ~ handleAddScheduleAdmin ~ e:', e);
        this.loading = false;
        this.errorHandler(e);
        // this.$toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
      }
    },
    formatDate (rawDate) {
      try {
        if (!rawDate || rawDate === '') return null;
        const date = new Date(rawDate);
        const dateString = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
          .toISOString();
        const localDate = moment(dateString).local();
        return localDate.format('L LTS');
      } catch (e) {
        return '';
      }
    },
    formatToMinute (rawDate) {
      const localDate = moment(rawDate, 'L LTS');
      const formatTime = localDate.format('YYYY-MM-DD HH:mm');
      return formatTime;
    },
    convertToUTC (dateStr) {
      const date = moment(dateStr, 'L LTS'); // parse the date string
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
      if (this.compareDate(this.startProposalDate, this.endProposalDate)) {
        this.$toast.error('Ngày bắt đầu đề xuất phải nhỏ hơn ngày kết thúc đề xuất ');
        return false;
      }
      if (this.compareDate(this.endProposalDate, this.startApproveDate)) {
        this.$toast.error('Ngày kết thúc đề xuất phải nhỏ hơn ngày bắt đầu duyệt đề tài ');
        return false;
      }
      if (this.compareDate(this.startApproveDate, this.endApproveDate)) {
        this.$toast.error('Ngày bắt đầu duyệt đề tài phải nhỏ hơn ngày kết thúc duyệt đề tài ');
        return false;
      }
      if (this.compareDate(this.endApproveDate, this.startRegisterDate)) {
        this.$toast.error('Ngày kết thúc duyệt đề tài phải nhỏ hơn ngày bắt đầu đăng ký đề tài ');
        return false;
      }
      if (this.compareDate(this.startRegisterDate, this.endRegisterDate)) {
        this.$toast.error('Ngày bắt đầu đăng kí đề tài phải nhỏ hơn ngày kết thúc đăng ký đề tài ');
        return false;
      }
      if (this.compareDate(this.endRegisterDate, this.mark_start)) {
        this.$toast.error('Ngày kết thúc đăng ký đề tài phải nhỏ hơn ngày bắt đầu chấm điểm đề tài ');
        return false;
      }
      if (this.compareDate(this.mark_start, this.mark_end)) {
        this.$toast.error('Ngày bắt đầu chấm diểm đề tài phải nhỏ hơn ngày kết thúcc chấm điểm đề tài ');
        return false;
      }
      return true;
    },
    changeStudents (students) {
      this.showSelectStudent = false;
      this.listStudentsSelected = students;
      this.students = this.listStudentsSelected.map((st) => st.code);
    },
    chooseStudent () {
      this.showSelectStudent = true;
    },
    compareDate (startDate, endDate) {
      const date1 = moment(startDate, 'L LTS').valueOf();
      const date2 = moment(endDate, 'L LTS').valueOf();

      if (date1 > date2) {
        return true;
      }
      return false;
    },
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
<style>
.g-gantt-row-label {
  height: 0px !important;
  display: none !important;
}
</style>
