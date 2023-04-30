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
          v-model="limit"
          name="limit"
          type="number"
          label="Số thành viên"
          validation="min:1|max:3"
          :disabled="isView"
          :validation-messages="{ min: 'Phải có ít nhất 1 thành viên', max:'Có tối đa 3 thành viên' }"
        />
        <div
          v-show="false"
          class="w-3/4"
        >
          <span class="font-bold text-sm">
            Giáo viên hướng dẫn
          </span>
          <div class="mt-1">
            <Multiselect
              v-model="lecturerId"
              :options="listLecturers"
              :close-on-select="false"
              :disabled="isView"
            />
          </div>
        </div>
        <div class="my-2-1 w-3/4">
          <span class="font-bold text-sm py-4 my-4">
            Sinh viên đăng kí
          </span>
          <div class="mt-1">
            <Multiselect
              v-model="studentIds"
              mode="tags"
              :close-on-select="false"
              :searchable="true"
              :options="listStudents"
              :disabled="isView"
              :limit="limit"
            />
          </div>
        </div>
        <FormKit
          v-model="description"
          name="description"
          type="textarea"
          label="Mô tả"
          help="Ghi các thông tin chi tiết tại đây"
          :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này' }"
          validation="required"
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
          {{ isSave ? 'Lưu' : 'Phê duyệt' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from '@vueform/multiselect';
import { getValidationMessages } from '@formkit/validation';
import { mapState, mapGetters } from 'vuex';
import ScheduleApi from '../../../utils/api/schedule';
import TopicProposalApi from '../../../utils/api/topic_proposal';

export default {
  name: 'FormApproveProposal',
  components: {
    Multiselect,
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
  },
  async mounted () {
    await this.$store.dispatch('student/fetchListStudent', this.token);
    const students = this.$store.state.student.listStudents;
    this.listLecturers = [{
      value: this.userInfo._id,
      label: this.userInfo.name,
      disabled: true,
    }];
    this.listStudents = students.map((student) => {
      let st = {
        value: student.code,
        label: `${student.name} - ${student.code}`,
      };
      if (this.isView) {
        st = { ...st, disabled: true };
      }
      return st;
    });

    if (this.isUpdate || this.isView) {
      const { id } = this.$store.state.url;
      const { listTopicProposalByLecturer } = this.$store.state.topic_proposal;
      const topic = listTopicProposalByLecturer.find((s) => s._id.toString() === id.toString());
      if (topic) {
        this.title = topic.title;
        this.code = topic.code;
        this.description = topic.description;
        this.limit = topic.limit;
        if (topic.lecturerId) {
          this.lecturerId = topic.lecturerId;
        }
        this.studentIds = topic.students;
        this.scheduleId = this.currentScheduleId;
        if (this.currentScheduleId) this.$store.commit('topic_proposal/setTopicScheduleId', this.currentScheduleId);
      }
    }
  },
  methods: {
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
          await this.$store.dispatch('topic_proposal/updateTopicProposal', { token: this.token, value: { ...value, _id: this.id } });
          await TopicProposalApi.approveTopicProposalByLecturer(this.token, this.id);
        }
        this.$toast.success('Đã phê duyệt đề tài hướng dẫn thành công!');
        this.rollBack();
      } catch (e) {
        console.log('🚀 ~ file: FormApproveProposalV2.vue:214 ~ handleAddTopicAdmin ~ e:', e);
        console.log('🚀 ~ file: FormApproveProposalV2.vue:216 ~ handleAddTopicAdmin ~ e.response.data.error.message:', e.response.data.error.message);
        if (e.response.data.error.message === 'Some student already has register in another topic') {
          this.$toast.error('Không thể phê duyệt. Sinh viên đã tồn tại trong một đề tài hướng dẫn khác!');
        } else {
          this.$toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên ');
        }
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
