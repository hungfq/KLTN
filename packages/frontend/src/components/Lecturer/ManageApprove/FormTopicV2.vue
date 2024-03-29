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
        class="ml-5 grid grid-cols-2"
      >
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
        <!-- <div class="my-2-1 w-3/5">
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
              :can-clear="false"
              :can-deselect="false"
              no-results-text="Không có kết quả"
              no-options-text="Không có lựa lựa chọn"
              :options="listStudents"
              :disabled="isView"
              class="w-[400px]"
            />
          </div>
        </div> -->
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
      <LoadingProcess v-else />
      <!-- Modal footer -->
      <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200">
        <button
          v-if="!isView && !loading "
          type="button"
          class="btn btn-primary"
          :disabled="!isValid"
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
import UserApi from '../../../utils/api/user';
import ScheduleApi from '../../../utils/api/schedule';
import LoadingProcess from '../../common/Loading.vue';

export default {
  name: 'FormTopic',
  components: {
    Multiselect,
    LoadingProcess,
  },
  data () {
    return {
      topicOld: null,
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
      loading: true,
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
    isValid () {
      if (!this.title) {
        return false;
      }
      if (!this.limit) {
        return false;
      }
      if (!this.description) {
        return false;
      }
      if (Number(this.limit) < 1 || Number(this.limit) > 3) {
        return false;
      }
      return true;
    },
  },
  async mounted () {
    this.loading = true;
    const students = await UserApi.listUser(this.token, 'STUDENT', null);
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
    if (this.isUpdate || this.isView) {
      const { id } = this.$store.state.url;
      if (!id) {
        this.loading = false;
        return;
      }
      const topic = await TopicApi.listTopicById(this.token, id);
      this.topicOld = topic;
      if (topic) {
        this.title = topic.title;
        this.code = topic.code;
        this.description = topic.description;
        this.limit = topic.limit;
        this.studentIds = topic.students;
      }
    }
    this.loading = false;
  },
  methods: {
    errorHandler (e) {
      if (e.response.data.error.code === 400) this.$toast.error(e.response.data.error.message);
      else { this.$toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    },
    rollBack () {
      this.$store.dispatch('url/updateSection', `${this.module}-list`);
    },
    async handleAddTopicAdmin () {
      const {
        studentIds,
      } = this;
      const value = {
        title: this.title,
        description: this.description,
        code: this.code,
        limit: this.limit,
        students: studentIds,
        scheduleId: this.topicOld.scheduleId._id || 1,
        lecturerId: this.topicOld.lecturerId._id,
        criticalLecturerId: this.topicOld.criticalLecturerId._id,
        advisorLecturerGrade: this.topicOld.advisorLecturerGrade,
        criticalLecturerGrade: this.topicOld.criticalLecturerGrade,
        committeePresidentGrade: this.topicOld.committeePresidentGrade,
        committeeSecretaryGrade: this.topicOld.committeeSecretaryGrade,
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
        // this.$toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
        this.errorHandler(e);
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
      return true;
    },

  },
};
</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
