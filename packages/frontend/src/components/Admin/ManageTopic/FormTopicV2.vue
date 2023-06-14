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
      <div
        v-if="!loading"
        class="flex flex-col mt-4 justify-center items-center"
      >
        <div
          v-if="page === 1 || isView"
          class="flex flex-col"
          :class="{'min-h-[200px]' : !isView}"
        >
          <div class="flex space-x-4">
            <div class="w-[400px]">
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
            </div>
            <div class="w-[400px]">
              <span class="font-bold text-sm py-4 my-4">
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
                  class="w-[400px]"
                />
              </div>
            </div>
          </div>
          <div class="flex space-x-4">
            <div class="w-[400px]">
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
            </div>
            <div class="w-[400px]">
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
          </div>
          <div class="flex space-x-4">
            <!-- Grade -->
            <div class="w-[400px]">
              <FormKit
                v-if="!isSave"
                v-model="advisorLecturerGrade"
                type="number"
                label="Điểm của giảng viên hướng dẫn"
                :disabled="isView"
              />
            </div>
            <div class="w-[400px]">
              <FormKit
                v-if="!isSave"
                v-model="criticalLecturerGrade"
                type="number"
                label="Điểm của giảng viên phản biện"
                :disabled="isView"
              />
            </div>
          </div>
          <div class="flex space-x-4">
            <div class="w-[400px]">
              <FormKit
                v-if="!isSave"
                v-model="committeePresidentGrade"
                type="number"
                label="Điểm của chủ tịch hội đồng"
                :disabled="isView"
              />
            </div>
            <div class="w-[400px]">
              <FormKit
                v-if="!isSave"
                v-model="committeeSecretaryGrade"
                name="limit"
                type="number"
                label="Điểm của thư ký"
                :disabled="isView"
              />
            </div>
          </div>
        </div>
        <!-- Select lecturer -->
        <div
          v-if="page === 2 || isView"
          class="flex space-x-4"
          :class="{'min-h-[200px]' : !isView}"
        >
          <div class="w-[400px]">
            <span class="font-bold text-sm">
              Giảng viên hướng dẫn
            </span>
            <div class="mt-1">
              <Multiselect
                v-model="lecturerId"
                :can-deselect="false"
                no-results-text="Không có kết quả"
                no-options-text="Không có lựa lựa chọn"
                :options="listLecturers"
                :can-clear="false"
                :searchable="true"
                :disabled="isView"
              />
            </div>
          </div>
          <div class="w-[400px]">
            <span class="font-bold text-sm">
              Giảng viên phản biện
            </span>
            <div class="mt-1">
              <Multiselect
                v-model="criticalLecturerId"
                :options="listLecturers"
                :can-deselect="false"
                no-results-text="Không có kết quả"
                no-options-text="Không có lựa lựa chọn"
                :can-clear="false"
                :searchable="true"
                :disabled="isView"
              />
            </div>
          </div>
        </div>
        <!-- Danh sách sinh viên đã chọn -->
        <div
          v-if="page === 3 || isView"
          class=""
        >
          <div class="flex flex-col">
            <div class="font-bold my-4">
              Danh sách sinh viên đã chọn
            </div>
            <div class="overflow-x-auto w-[600px]">
              <EasyDataTable
                show-index
                :headers="headers"
                :items="listStudentsSelected"
                :loading="loading"
                :buttons-pagination="false"
                hide-footer
              />
            </div>
          </div>
          <div
            v-if="!isView"
            class="flex mt-4"
          >
            <FormKit
              v-model.number="limit"
              name="limit"
              type="number"
              label="Số thành viên"
              outer-class="!mx-8 w-[300px]"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này', min: 'Số lượng không nhỏ hơn 1', max: 'Số lượng không lớn hơn 3' }"
              validation="required|min:1|max:3"
              :disabled="isView"
            />
            <button
              v-if="!isView"
              class="btn btn-primary mt-5 !mx-8 w-[250px]"
              @click="chooseStudent"
            >
              Chọn danh sách sinh viên
            </button>
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
              @click="page=step.page"
            >
              {{ step.label }}
            </li>
          </ul>
        </div>
      </div>
      <LoadingProcess v-else />
      <!-- Modal footer -->
      <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200 justify-end">
        <button
          v-if="!isView && page > 1&&!loading"
          type="button"
          class="btn btn-secondary mx-2"
          @click="page=1"
        >
          Quay lại
        </button>
        <button
          v-if="!isView && page === 3 &&!loading"
          type="button"
          class="btn btn-primary mx-2"
          @click="handleAddTopicAdmin"
        >
          {{ isSave ? 'Lưu' : 'Cập nhật' }}
        </button>
        <button
          v-if="!isView && page !== 3&&!loading"
          type="button"
          class="btn btn-primary mx-2"
          @click="page=page+1"
        >
          Tiếp theo
        </button>
      </div>
    </div>
  </div>
  <SelectStudent
    v-model="showSelectStudent"
    :schedule-id="scheduleId"
    :selected="listStudentsSelected"
    :enabled-excel="true"
    :type="'TOPIC-FORM-UPDATE'"
    @change-students="changeStudents"
  />
</template>

<script>
import Multiselect from '@vueform/multiselect';
import { mapGetters } from 'vuex';
import TopicApi from '../../../utils/api/topic';
import UserApi from '../../../utils/api/user';
import ScheduleApi from '../../../utils/api/schedule';
import LoadingProcess from '../../common/Loading.vue';
import SelectStudent from '../../Modal/SelectStudent.vue';

export default {
  name: 'FormTopic',
  components: {
    Multiselect,
    LoadingProcess,
    SelectStudent,
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
      scheduleId: null,
      thesisDefenseDate: '',
      advisorLecturerGrade: 0,
      committeePresidentGrade: 0,
      committeeSecretaryGrade: 0,
      criticalLecturerGrade: 0,
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
      loading: true,
      listStudentsSelected: [],
      page: 1,
      steps: [
        { label: 'Thông tin cho đề tài', page: 1 },
        { label: 'Chọn giáo viên', page: 2 },
        { label: 'Chọn sinh viên', page: 3 },
      ],
      showSelectStudent: false,
      selectStudentScheduleId: null,
      headers: [
        { text: 'Mã số', value: 'code', sortable: true },
        { text: 'Tên ', value: 'name', sortable: true },
        { text: 'Email', value: 'email' },
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
    this.loading = true;
    const students = await UserApi.listUser(this.token, 'STUDENT', null);
    const lecturers = await UserApi.listUser(this.token, 'LECTURER', null);
    const schedules = await ScheduleApi.listAllSchedule(this.token);
    this.listLecturers = lecturers.data.map((lecturer) => {
      let l = {
        value: lecturer._id,
        label: lecturer.name,
      };
      if (this.isView) {
        l = { ...l, disabled: true };
      }
      return l;
    });

    this.listStudents = students.data.map((student) => {
      let st = {
        value: student.code,
        label: `${student.code} - ${student.name}`,
      };
      if (this.isView) {
        st = { ...st, disabled: true };
      }
      return st;
    });
    this.listSchedules = schedules.data.map((schedule) => {
      let st = {
        value: schedule._id,
        label: `${schedule.code} : ${schedule.name}`,
      };
      if (this.isView) {
        st = { ...st, disabled: true };
      }
      return st;
    });
    // auto select when create
    if (this.listSchedules.length > 0) {
      this.scheduleId = this.listSchedules[0].value;
    }
    if (this.listLecturers.length > 1) {
      this.lecturerId = this.listLecturers[0].value;
      this.criticalLecturerId = this.listLecturers[1].value;
    }
    if (this.isUpdate || this.isView) {
      const { id } = this.$store.state.url;
      if (!id) {
        this.loading = false;
        return;
      }
      const topic = await TopicApi.listTopicById(this.token, id);
      if (topic) {
        this.title = topic.title;
        this.code = topic.code;
        this.description = topic.description;
        this.limit = topic.limit;
        if (topic.lecturerId) this.lecturerId = topic.lecturerId._id;
        if (topic.criticalLecturerId) this.criticalLecturerId = topic.criticalLecturerId._id;
        if (topic.scheduleId) this.scheduleId = topic.scheduleId._id;
        this.studentIds = topic.students;
        // if (topic.thesisDefenseDate) {
        //   const date = new Date(topic.thesisDefenseDate);
        //   const dateString = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
        //     .toISOString()
        //     .split('T')[0];
        //   this.thesisDefenseDate = dateString;
        // }
        this.listStudentsSelected = topic.list_students;
        this.advisorLecturerGrade = topic.advisorLecturerGrade;
        this.committeePresidentGrade = topic.committeePresidentGrade;
        this.committeeSecretaryGrade = topic.committeeSecretaryGrade;
        this.criticalLecturerGrade = topic.criticalLecturerGrade;
      }
      this.loading = false;
    }
    this.loading = false;
  },
  methods: {
    chooseStudent () {
      this.showSelectStudent = true;
    },
    errorHandler (e) {
      if (e.response.data.error.code === 400) this.$toast.error(e.response.data.error.message);
      else { this.$toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    },
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
            await TopicApi.updateTopicById(this.token, { ...value, _id: this.id });
            this.rollBack();
            this.$toast.success('Đã cập nhật thành công!');
          }
        }
        this.loading = false;
      } catch (e) {
        this.errorHandler(e);
      }
      this.loading = false;
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
      if (this.lecturerId === this.criticalLecturerId) {
        this.$toast.error('Giảng viên hướng dẫn phải khác giảng viên phản biện');
        return false;
      }
      if (this.studentIds.length > this.limit) {
        this.$toast.error('Số lượng sinh viên được chọn không được quá số lượng giới hạn');
        return false;
      }
      if (!this.scheduleId) {
        this.$toast.error('Vui lòng chọn đợt đăng ký');
        return false;
      }
      return true;
    },
    changeStudents (students) {
      // console.log('🚀 ~ file: FormTopicV2.vue:511 ~ changeStudents ~ students:', students);
      this.showSelectStudent = false;
      if (students.length > 3 || students.length < 1) {
        this.$toast.error('Số lượng thành viên không quá 3 thành viên và không nhỏ hơn 1');
        return;
      }
      this.limit = students.length;
      this.listStudentsSelected = students;
      this.studentIds = this.listStudentsSelected.map((st) => st.code);
    },
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css">
.multiselect-tags-search-wrapper{
  height: 30px;
}

</style>
