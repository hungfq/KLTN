<!-- eslint-disable max-len -->
<template>
  <div
    class="mx-4 my-4 rounded px-2 py-2 bg-slate-500 w-fit text-white font-semibold cursor-pointer"
    @click="rollBack"
  >
    Quay về
  </div>
  <div
    class="p-4 w-full h-full md:h-auto mx-auto mt-[10px]"
  >
    <!-- Modal content -->
    <div class="bg-white rounded-lg shadow">
      <!-- Modal header -->
      <div class="flex justify-between items-start p-4 rounded-t border-b">
        <h3 class="text-xl font-semibold text-gray-900">
          Thông tin đề tài
        </h3>
      </div>
      <div class="ml-5 grid grid-cols-2">
        <FormKit
          v-model="title"
          type="text"
          name="title"
          label="Tiêu đề"
          help="Vd: Xây dụng web thương mại điện tử e-shop"
          validation="required"
          :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
          :disabled="isView"
        />
        <FormKit
          v-if="isView"
          v-model="code"
          name="code"
          type="text"
          label="Mã đề tài"
          validation="required"
          :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
          :disabled="isView"
        />
        <FormKit
          v-model="limit"
          name="limit"
          type="number"
          label="Số thành viên"
          validation="min:1|max:3"
          :disabled="isView"
          :validation-messages="{ min: 'Phải có ít nhất 1 thành viên', max:'Có tối đa 3 thành viên' }"
        />
        <FormKit
          v-model="
            thesisDefenseDate"
          name="thesisDefenseDate"
          type="date"
          label="Thời hạn phản biện"
          validation="required"
          :disabled="isView"
          :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
        />
        <div class="w-3/5">
          <span class="font-bold text-sm">
            Giáo viên hướng dẫn
          </span>
          <div class="mt-1">
            <Multiselect
              v-model="lecturerId"
              :options="listLecturers"
              :searchable="true"
              :disabled="isView"
            />
          </div>
        </div>
        <div class="w-3/5">
          <span class="font-bold text-sm">
            Giáo viên phản biện
          </span>
          <div class="mt-1">
            <Multiselect
              v-model="criticalLecturerId"
              :options="listLecturers"
              :searchable="true"
              :disabled="isView"
            />
          </div>
        </div>
        <div class="my-2-1 w-3/5">
          <span class="font-bold text-sm py-4 my-4">
            Sinh viên đăng kí
          </span>
          <div class="mt-1">
            <Multiselect
              v-model="studentIds"
              mode="tags"
              :close-on-select="false"
              :searchable="true"
              :create-option="true"
              :options="listStudents"
              :disabled="isView"
              class="w-[400px]"
            />
          </div>
        </div>
        <div class="my-2-1 w-3/5">
          <span class="font-bold text-sm py-4 my-4">
            Đợt đăng ký
          </span>
          <div class="mt-1">
            <Multiselect
              v-model="scheduleId"
              :options="listSchedules"
              :searchable="true"
              :disabled="isView"
              class="w-[400px]"
            />
          </div>
        </div>
        <FormKit
          v-model="advisorLecturerGrade"
          type="number"
          label="Điểm của giảng viên hướng dẫn"
          :disabled="isView"
        />
        <FormKit
          v-model="criticalLecturerGrade"
          type="number"
          label="Điểm của giảng viên phản biện"
          :disabled="isView"
        />
        <FormKit
          v-model="committeePresidentGrade"
          type="number"
          label="Điểm của chủ tịch hội đồng"
          :disabled="isView"
        />
        <FormKit
          v-model="committeeSecretaryGrade"
          name="limit"
          type="number"
          label="Điểm của thư ký"
          :disabled="isView"
        />
        <FormKit
          v-model="description"
          name="description"
          type="textarea"
          label="Mô tả"
          help="Ghi các thông tin chi tiết tại đây"
          validation="required"
          :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
          :disabled="isView"
        />
      </div>
      <!-- Modal footer -->
      <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200">
        <button
          v-if="!isView"
          type="button"
          class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4  focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
          @click="handleAddTopicAdmin"
        >
          {{ isSave ? 'Lưu' : 'Cập nhật' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from '@vueform/multiselect';
import { mapGetters } from 'vuex';
import TopicApi from '../../../utils/api/topic';

export default {
  name: 'FormTopic',
  components: {
    Multiselect,
  },
  data () {
    return {
      title: '',
      code: '',
      description: '',
      limit: '',
      lecturerId: '',
      criticalLecturerId: '',
      studentIds: [],
      scheduleId: '',
      thesisDefenseDate: '',
      advisorLecturerGrade: '',
      committeePresidentGrade: '',
      committeeSecretaryGrade: '',
      criticalLecturerGrade: '',
      listStudents: [
        'student1',
        'student2',
        'student3',
        'student4',
      ],
      listLecturers: [
        'lecturer1',
        'lecturer2',
        'lecturer3',
      ],
      listSchedules: [],
      messages: '',
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
      return this.section === 'topic-import';
    },
    isUpdate () {
      return this.section === 'topic-update';
    },
    isView () {
      return this.section === 'topic-view';
    },
  },
  async mounted () {
    console.log('hereeee....');
    try {
      await this.$store.dispatch('lecturer/fetchListLecturer', this.token);
      await this.$store.dispatch('student/fetchListStudent', this.token);
      await this.$store.dispatch('schedule/fetchListSchedules', this.token);
    } catch (e) {
      this.$toast('Đã có lỗi xảy, vui lòng liên hệ quản trị viên để kiểm tra');
    }
    const lecturers = this.$store.state.lecturer.listLecturer;
    const students = this.$store.state.student.listStudents;
    const schedules = this.$store.state.schedule.listSchedules;
    this.listLecturers = lecturers.map((lecturer) => {
      let l = {
        value: lecturer._id,
        label: lecturer.name,
      };
      if (this.isView) {
        l = { ...l, disabled: true };
      }
      return l;
    });
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
    this.listSchedules = schedules.map((schedule) => {
      let st = {
        value: schedule._id,
        label: `${schedule.code} : ${schedule.name}`,
      };
      if (this.isView) {
        st = { ...st, disabled: true };
      }
      return st;
    });
    console.log('🚀 ~ file: FormTopic.vue:267 ~ mounted ~ this.isUpdate || this.isView:', this.isUpdate || this.isView);
    if (this.isUpdate || this.isView) {
      const { id } = this.$store.state.url;
      console.log('🚀 ~ file: FormTopic.vue:268 ~ mounted ~ id:', id);
      const { listTopicsByLecturerSchedule } = this.$store.state.topic;
      const topic = listTopicsByLecturerSchedule.find((s) => s._id.toString() === id.toString());
      if (topic) {
        this.title = topic.title;
        this.code = topic.code;
        this.description = topic.description;
        this.limit = topic.limit;
        if (topic.lecturerId) this.lecturerId = topic.lecturerId._id;
        if (topic.criticalLecturerId) this.criticalLecturerId = topic.criticalLecturerId._id;
        if (topic.scheduleId) this.scheduleId = topic.scheduleId._id;
        this.studentIds = topic.students;
        if (topic.thesisDefenseDate) {
          const date = new Date(topic.thesisDefenseDate);
          const dateString = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
            .toISOString()
            .split('T')[0];
          this.thesisDefenseDate = dateString;
        }
        this.advisorLecturerGrade = topic.advisorLecturerGrade;
        this.committeePresidentGrade = topic.committeePresidentGrade;
        this.committeeSecretaryGrade = topic.committeeSecretaryGrade;
        this.criticalLecturerGrade = topic.criticalLecturerGrade;
      }
    }
  },
  methods: {
    rollBack () {
      this.$store.dispatch('url/updateSection', `${this.module}-list`);
    },
    async handleAddTopicAdmin () {
      const {
        scheduleId, studentIds, lecturerId, criticalLecturerId,
      } = this;
      const value = {
        title: this.title,
        description: this.description,
        code: this.code,
        limit: this.limit,
        students: studentIds,
        lecturerId,
        scheduleId,
        criticalLecturerId,
        thesisDefenseDate: this.thesisDefenseDate,
        advisorLecturerGrade: this.advisorLecturerGrade,
        committeePresidentGrade: this.committeePresidentGrade,
        committeeSecretaryGrade: this.committeeSecretaryGrade,
        criticalLecturerGrade: this.criticalLecturerGrade,
      };
      try {
        this.loading = true;
        if (this.check()) {
          if (this.isSave) {
            await TopicApi.createTopic(this.token, value);
            this.$toast.success('Đã thêm thành công!');
            this.rollBack();
          } else if (this.isUpdate) {
            await TopicApi.createTopic(this.token, { ...value, _id: this.id });
            this.rollBack();
            this.$toast.success('Đã cập nhật thành công!');
          }
        }
      } catch (e) {
        this.$toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
      }
    },
    check () {
      if (!this.title) {
        this.$toast.error('Vui lòng nhập tên đề tài');
        return false;
      }
      if (!this.limit) {
        this.$toast.error('Vui lòng số lượng thành viên mã đề tài');
        return false;
      }
      if (Number(this.limit) < 1 || Number(this.limit) > 3) {
        this.$toast.error('Số lượng thành viên không quá 3 thành viên và không nhỏ hơn 1');
        return false;
      }
      if (!this.lecturerId) {
        this.$toast.error('Vui lòng chọn giảng viên hướng dẫn');
        return false;
      }
      if (!this.lecturerId && this.lecturerId === '') {
        this.$toast.error('Vui lòng chọn giảng viên hướng dẫn');
        return false;
      }
      if (this.studentIds.length > this.limit) {
        this.$toast.error('Số lượng sinh viên được chọn không được quá số lượng giới hạn');
        return false;
      }
      return true;
    },
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
