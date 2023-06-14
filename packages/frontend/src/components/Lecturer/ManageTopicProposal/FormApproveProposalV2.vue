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
    <div class="bg-white rounded-lg shadow pb-4">
      <div class="flex justify-between items-start p-4 rounded-t border-b">
        <h3 class="text-xl font-semibold text-gray-900">
          Thông tin đề xuất
        </h3>
      </div>
      <template v-if="!loading">
        <div
          class="ml-5 mt-5 flex flex-col items-center"
        >
          <template v-if="page ===1 || isView">
            <div class="flex">
              <FormKit
                v-model="title"
                type="text"
                name="title"
                label="Tiêu đề"
                help="Vd: Xây dụng web thương mại điện tử e-shop"
                validation="required"
                outer-class="!mx-8 w-[400px]"
                :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"

                :disabled="isView"
              />
              <FormKit
                v-model="description"
                name="description"
                type="textarea"
                label="Mô tả"
                help="Ghi các thông tin chi tiết tại đây"
                validation="required"
                outer-class="!mx-8 w-[400px]"
                :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
                :disabled="isView"
              />
            </div>
            <div class="flex">
              <div
                class="w-[400px] !mx-8"
              >
                <span class="font-bold text-sm">
                  Giảng viên hướng dẫn
                </span>
                <div class="mt-1">
                  <Multiselect
                    v-model="lecturerId"
                    :can-clear="false"
                    :options="listLecturers"
                    :disabled="isView || isUpdate"
                  />
                </div>
              </div>
              <div
                class="w-[400px] !mx-8"
              >
                <span class="font-bold text-sm">
                  Đợt đăng ký
                </span>
                <div class="mt-1">
                  <Multiselect
                    v-model="scheduleId"
                    :options="scheduleSelect"
                    :can-deselect="false"
                    no-results-text="Không có kết quả"
                    no-options-text="Không có lựa lựa chọn"
                    :can-clear="false"
                    :searchable="true"
                    :disabled="isView || isUpdate"
                  />
                </div>
              </div>
            </div>
          </template>
          <template v-if="page===2 || isView">
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
          </template>
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
        <!-- Modal footer -->
        <div class="mt-10 flex items-center p-4 justify-end rounded-b border-t border-gray-200">
          <button
            v-if="!isView && page === 2"
            type="button"
            class="btn btn-secondary mx-2"
            @click="page=1"
          >
            Quay lại
          </button>
          <button
            v-if="!isView && page !== 1"
            type="button"
            class="btn btn-primary mx-2"
            :disabled="!isValid"
            @click="handleAddTopicAdmin"
          >
            {{ isSave ? 'Lưu' : 'Cập nhật' }}
          </button>
          <button
            v-if="!isView && page === 1"
            type="button"
            class="btn btn-primary mx-2"
            @click="page=2"
          >
            Tiếp theo
          </button>
        </div>
      </template>
      <div
        v-else
        class="mb-4"
      >
        <LoadingProcess />
      </div>
    </div>
  </div>
  <SelectStudent
    v-model="showSelectStudent"
    :schedule-id="scheduleId"
    :selected="listStudentsSelected"
    :enabled-excel="true"
    :type="'TOPIC_PROPOSAL'"
    @change-students="changeStudents"
  />
</template>

<script>
import Multiselect from '@vueform/multiselect';
import { getValidationMessages } from '@formkit/validation';
import { mapState, mapGetters } from 'vuex';
import ScheduleApi from '../../../utils/api/schedule';
import TopicProposalApi from '../../../utils/api/topic_proposal';
import SelectStudent from '../../Modal/SelectStudent.vue';

export default {
  name: 'FormApproveProposal',
  components: {
    Multiselect,
    SelectStudent,
  },
  props: {
  },
  data () {
    return {
      title: '',
      code: '',
      description: '',
      limit: 1,
      lecturerId: '',
      scheduleId: '',
      studentIds: [],
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
      messages: '',
      listSchedules: [],
      deadline: '',
      listStudentsSelected: [],
      page: 1,
      steps: [
        { label: 'Thông tin cho đề tài', page: 1 },
        { label: 'Chọn sinh viên', page: 2 },
      ],
      loading: false,
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
      'token', 'userId', 'userInfo',
    ]),
    ...mapGetters('schedule', [
      'currentScheduleId', 'listScheduleApproveLecturer',
    ]),
    isSave () {
      return this.section === 'topic_proposal_approve-import';
    },
    isUpdate () {
      return this.section === 'topic_proposal_approve-update';
    },
    isView () {
      return this.section === 'topic_proposal_approve-view';
    },
    scheduleSelect () {
      return this.listSchedules.map((s) => ({
        value: s._id,
        label: s.name,
      }));
    },
    isValid () {
      if (!this.title) {
        return false;
      }
      if (!this.limit) {
        return false;
      }
      if (Number(this.limit) < 1 || Number(this.limit) > 3) {
        return false;
      }
      if (this.studentIds.length !== this.limit) {
        return false;
      }
      return true;
    },
  },
  async mounted () {
    try {
      await this.$store.dispatch('student/fetchListStudent', this.token);
      // const students = this.$store.state.student.listStudents;
      this.listLecturers = [{
        value: this.userInfo._id,
        label: this.userInfo.name,
        disabled: true,
      }];

      if (this.isUpdate || this.isView) {
        const { id } = this.$store.state.url;
        const topic = await TopicProposalApi.getTopicProposal(this.token, id);
        if (topic) {
          this.title = topic.title;
          this.code = topic.code;
          this.description = topic.description;
          this.limit = topic.limit;
          if (topic.scheduleId) {
            this.scheduleId = topic.scheduleId;
            this.listSchedules = [topic.schedule];
          }
          if (topic.lecturerId) this.lecturerId = topic.lecturerId._id;
          this.studentIds = topic.students;
          this.listStudentsSelected = topic.list_students;
          // if (this.currentScheduleId) this.$store.commit('topic_proposal/setTopicScheduleId', this.currentScheduleId);
        }
      }
    } catch (e) {
      this.errorHandler(e);
      console.error(e);
    }
  },
  methods: {
    errorHandler (e) {
      if (e.response.data.error.code === 400) this.$toast.error(e.response.data.error.message);
      else { this.$toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    },
    chooseStudent () {
      this.showSelectStudent = true;
    },
    rollBack () {
      this.$store.dispatch('url/updateSection', `${this.module}-list`);
    },
    async handleAddTopicAdmin () {
      const { studentIds } = this;
      const value = {
        title: this.title,
        limit: this.limit,
        description: this.description,
        students: studentIds,
        lecturerId: this.userId,
        scheduleId: this.scheduleId,
        status: 'ADMIN',
      };
      try {
        if (this.check() && this.isUpdate) {
          await TopicProposalApi.updateTopicProposal(this.token, { ...value, _id: this.id });
          // await this.$store.dispatch('topic_proposal/updateTopicProposal', { token: this.token, value: { ...value, _id: this.id } });
          await TopicProposalApi.approveTopicProposalByLecturer(this.token, this.id);
        }
        this.$toast.success('Đã phê duyệt đề tài hướng dẫn thành công!');
        this.rollBack();
      } catch (e) {
        this.errorHandler(e);
        // if (e.response.data.error.message === 'Some student already has register in another topic') {
        //   this.$toast.error('Không thể phê duyệt. Sinh viên đã tồn tại trong một đề tài hướng dẫn khác!');
        // } else {
        //   this.$toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên ');
        // }
      }
    },
    check () {
      if (!this.title) {
        this.$toast.error('Vui lòng nhập tên đề tài');
        return false;
      }
      if (!this.limit) {
        this.$toast.error('Vui lòng số lượng thành viên');
        return false;
      }
      if (Number(this.limit) < 1 || Number(this.limit) > 3) {
        this.$toast.error('Số lượng thành viên không quá 3 thành viên và không nhỏ hơn 1');
        return false;
      }
      if (this.studentIds.length !== this.limit) {
        this.$toast.error('Số lượng sinh viên được chọn không được quá số lượng giới hạn');
        return false;
      }
      return true;
    },

    changeStudents (students) {
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

</style>
