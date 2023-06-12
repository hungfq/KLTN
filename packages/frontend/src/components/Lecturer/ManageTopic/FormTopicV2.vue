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
        <div
          class="w-[400px]"
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
      'token', 'userId',
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
    scheduleSelect () {
      return this.listSchedules.map((s) => ({
        value: s._id,
        label: s.name,
      }));
    },
  },
  async mounted () {
    this.loading = true;
    this.prepareSchedule();
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
    prepareSchedule () {
      const schedules = this.$store.state.schedule.listScheduleApproveLecturer;
      if (!schedules || schedules.length === 0) return;
      this.scheduleId = schedules[0]._id || schedules[0].id;
      this.listSchedules = schedules;
    },
    rollBack () {
      this.$store.dispatch('url/updateSection', `${this.module}-list`);
    },
    async handleAddTopicAdmin () {
      const {
        studentIds,
      } = this;
      try {
        this.loading = true;
        if (this.check()) {
          if (this.isSave) {
            const value = {
              title: this.title,
              description: this.description,
              code: this.code,
              limit: this.limit,
              students: studentIds,
              scheduleId: this.scheduleId,
              lecturerId: this.userId,
            };
            console.log('🚀 ~ file: FormTopicV2.vue:253 ~ handleAddTopicAdmin ~ value.lecturerId:', value.lecturerId);
            await TopicApi.createTopic(this.token, value);
            this.$toast.success('Đã thêm thành công!');
            this.rollBack();
          } else if (this.isUpdate) {
            const value = {
              title: this.title,
              description: this.description,
              code: this.code,
              limit: this.limit,
              students: studentIds,
              scheduleId: this.topicOld.scheduleId._id,
              lecturerId: this.topicOld.lecturerId._id,
              criticalLecturerId: this.topicOld.criticalLecturerId._id,
              advisorLecturerGrade: this.topicOld.advisorLecturerGrade,
              criticalLecturerGrade: this.topicOld.criticalLecturerGrade,
              committeePresidentGrade: this.topicOld.committeePresidentGrade,
              committeeSecretaryGrade: this.topicOld.committeeSecretaryGrade,
            };
            await TopicApi.updateTopicById(this.token, { ...value, _id: this.id });
            this.rollBack();
            this.$toast.success('Đã cập nhật thành công!');
          }
        }
        this.loading = false;
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
      return true;
    },

  },
};
</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
