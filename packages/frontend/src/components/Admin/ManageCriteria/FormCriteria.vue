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
          Thong tin tieu chi
        </h3>
      </div>
      <div
        v-if="!loading"
        class="ml-5 grid grid-cols-2"
      >
        <FormKit
          v-model="name"
          type="text"
          name="name"
          label="Tên tieu chi"
          help="TLCN K19 HK1"
          validation="required"
          :disabled="isView"
        />
        <div
          v-if="!isView"
          class="my-2-1 w-3/4"
        >
          <span class="font-bold text-sm py-4 my-4">
            Dot dang ky
          </span>
          <div class="mt-1">
            <Multiselect
              v-model="students"
              mode="tags"
              :close-on-select="false"
              :searchable="true"
              :create-option="true"
              :options="listStudents"
            />
          </div>
        </div>
        <button
          v-if="isView"
          class="rounded bg-slate-500 h-[60px] w-[300px]  text-white font-semibold cursor-pointer "
          @click="showInfoStudent"
        >
          Xem thông tin sinh viên
        </button>
      </div>
      <Loading v-else />
      <!-- Modal footer -->
      <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200">
        <button
          v-if="!isView && !loading"
          type="button"
          class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4  focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
          @click="handleAddScheduleAdmin"
        >
          {{ isSave ? 'Lưu' : 'Cập nhật' }}
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
import Multiselect from '@vueform/multiselect';
import { mapGetters } from 'vuex';
import InfoStudentVue from '../../Modal/InfoStudent.vue';
import UserApi from '../../../utils/api/user';
import Loading from '../../common/Loading.vue';

export default {
  name: 'FormSchedule',
  components: {
    Multiselect,
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
      topics: [],
      students: [],
      listTopics: [],
      listStudents: [],
      infoUser: [],
      loading: false,
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
    const listStudents = await UserApi.listUser(this.token, 'STUDENT');
    const students = listStudents.data;
    this.listStudents = students.map((student) => {
      let st = {
        value: student.code,
        label: `${student.code} - ${student.name}`,
      };
      if (this.isView) {
        st = { ...st, disabled: true };
      }
      return st;
    });
    if (this.isUpdate || this.isView) {
      const { id } = this.$store.state.url;
      const { listSchedules } = this.$store.state.schedule;
      const schedule = listSchedules.find((s) => s._id.toString() === id.toString());
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
        this.students = schedule.students;
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
        endApproveDate, students, code,
      } = this;
      const value = {
        name,
        description,
        startDate,
        deadline,
        startProposalDate,
        endProposalDate,
        startRegisterDate,
        endRegisterDate,
        startApproveDate,
        endApproveDate,
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
          .toISOString()
          .split('T')[0];
        return dateString;
      } catch (e) {
        return '';
      }
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
      if (this.startProposalDate > this.endProposalDate) {
        this.$toast.error('Ngày bắt đầu đề xuất phải nhỏ hơn ngày kết thúc đề xuất ');
        return false;
      }
      if (this.endProposalDate > this.startApproveDate) {
        this.$toast.error('Ngày kết thúc đề xuất phải nhỏ hơn ngày bắt đầu duyệt đề tài ');
        return false;
      }
      if (this.startApproveDate > this.endApproveDate) {
        this.$toast.error('Ngày bắt đầu duyệt đề tài phải nhỏ hơn ngày kết thúc duyệt đề tài ');
        return false;
      }
      if (this.endApproveDate > this.startRegisterDate) {
        this.$toast.error('Ngày kết thúc duyệt đề tài phải nhỏ hơn ngày bắt đầu đăng ký đề tài ');
        return false;
      }
      if (this.startApproveDate > this.endApproveDate) {
        this.$toast.error('Ngày bắt đầu đăng kí đề tài phải nhỏ hơn ngày kết thúc đăng ký đề tài ');
        return false;
      }
      return true;
    },
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
