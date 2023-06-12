<template>
  <template v-if="loading">
    <LoadingProcessor />
  </template>
  <template v-else>
    <div class="mt-2 bg-slate-100 py-2">
      <div class="tabs ml-4">
        <a
          v-for="option in headerTabs"
          :key="option"
          class="tab tag-lg tab-lifted min-w-[100px] text-blue-900 font-semibold"
          :class="{'tab-active' : option === tab}"
          @click="tab= option"
        >{{ option }}</a>
      </div>
    </div>
    <div v-if="tab">
      <div class="flex flex-col 2xl:min-h-[770px] lg:min-h-[590px]">
        <div class="flex">
          <div class="w-3/5">
            <BodyAndShadow>
              <TitleItem :title="`Đề tài: ${currentTopic.title}`" />
              <div class="flex flex-col 2xl:min-h-[275px] lg:min-h-[250px]">
                <div class="flex">
                  <div class="w-1/2">
                    <LineItem
                      :title="'Mã đề tài: '"
                      :content="currentTopic.code"
                    />
                    <LineItem
                      :title="'Giảng viên phản biện: '"
                      :content="currentTopic.criticalLecturerId?.name || ''"
                    />
                  </div>
                  <div class="w-1/2">
                    <LineItem
                      :title="'Đợt đăng ký: '"
                      :content="`${currentTopic.scheduleId.code}: ${currentTopic.scheduleId.name}`"
                    />
                    <LineItem
                      :title="'Giảng viên hướng dẫn: '"
                      :content="currentTopic.lecturerId?.name || ''"
                    />
                  </div>
                </div>
                <LineItem
                  :title="'Mô tả: '"
                  :content="currentTopic.description"
                />
              </div>
            </BodyAndShadow>
            <BodyAndShadow>
              <TitleItem :title="'Điểm số'" />
              <div class="flex flex-col md:flex-row md:items-center">
                <div class="w-1/2 py-2">
                  <LineItem
                    :title="'Điểm của giáo viên hướng dẫn:'"
                    :content="currentTopic.advisorLecturerGrade || 0"
                  />
                  <LineItem
                    :title="'Điểm của giáo viên phản biện:'"
                    :content="currentTopic.criticalLecturerGrade || 0"
                  />
                </div>
                <div class="w-1/2">
                  <LineItem
                    :title="'Điểm của chủ tịch hội đồng:'"
                    :content="currentTopic.committeePresidentGrade || 0"
                  />
                  <LineItem
                    :title="'Điểm của thư ký hội đồng:'"
                    :content="currentTopic.committeeSecretaryGrade || 0"
                  />
                </div>
              </div>
            </BodyAndShadow>
          </div>

          <div class="w-2/5 flex flex-col">
            <BodyAndShadow>
              <TitleItem :title="'Danh sách thành viên'" />

              <div class="px-4 py-3 h-24">
                <ol class="list-decimal pl-4">
                  <li
                    v-for="student in currentTopic.students"
                    :key="student"
                  >
                    {{ student }}
                  </li>
                </ol>
              </div>
            </BodyAndShadow>

            <BodyAndShadow>
              <TitleItem :title="'Phê duyệt ra hội đồng'" />

              <div class="px-4 py-3 bg-white">
                <div class="flex flex-col">
                  <LineItem
                    :title="'Giảng viên hướng dẫn: '"
                    :content="currentTopic.advisorLecturerApprove ? 'Đồng ý' : 'Chưa đồng ý'"
                  />
                  <LineItem
                    :title="'Giảng viên phản biện: '"
                    :content="currentTopic.criticalLecturerApprove ? 'Đồng ý' : 'Chưa đồng ý'"
                  />
                </div>
              </div>
            </BodyAndShadow>

            <BodyAndShadow>
              <TitleItem :title="'Danh sách hội đồng'" />
              <div class="px-4 py-3 bg-white h-24">
                <div v-if="!currentTopic.committee">
                  Hiện tại chưa có hội đồng cho đề tài này
                </div>
                <ol
                  v-else
                  class="list-decimal pl-4"
                >
                  <li><span class="font-semibold">Chủ tịch  hội đồng:</span> {{ currentTopic.committee.president ? currentTopic.committee.president.name : '' }}</li>
                  <li><span class="font-semibold">Thư ký  hội đồng:</span> {{ currentTopic.committee.secretary ? currentTopic.committee.secretary.name : '' }}</li>
                  <li><span class="font-semibold">Giảng viên phản biện:</span> {{ currentTopic.committee.critical ? currentTopic.committee.critical.name : '' }}</li>
                </ol>
              </div>
            </BodyAndShadow>
            <div class="flex justify-between my-4 mx-4">
              <button
                class="btn btn-secondary"
                @click="showGradeModal=true"
              >
                Xem chi tiết điểm
              </button>
              <button
                class="btn btn-error"
                :disabled="!checkCanCancelTopic"
                @click="removeRegister"
              >
                Hủy đăng ký
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  <ConfirmModal
    v-model="showConfirmModal"
    @confirm="confirmRemove"
    @cancel="showConfirmModal=false"
  >
    <template #title>
      Xác nhận
    </template>
    <div>Bạn có xác nhận xóa đăng ký này?</div>
  </ConfirmModal>
  <ShowGradeStudentModal
    v-model="showGradeModal"
    :topic-id="currentTopicId"
    @confirm="showGradeModal=false"
    @cancel="showGradeModal=false"
  />
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import 'vue-search-input/dist/styles.css';
import TopicApi from '../../../utils/api/topic';
import LineItem from '../LineItem.vue';
import ConfirmModal from '../../Modal/ConfirmModal.vue';
import ShowGradeStudentModal from '../../Modal/ShowGradeStudent.vue';
import TitleItem from './TitleItem.vue';
import BodyAndShadow from './BorderAndShadow.vue';
import LoadingProcessor from '../../common/Loading.vue';

export default {
  name: 'ManageTopicStudent',
  components: {
    LineItem,
    ConfirmModal,
    TitleItem,
    BodyAndShadow,
    LoadingProcessor,
    ShowGradeStudentModal,
  },
  props: {
    open: {
      type: Boolean,
      default: true,
    },
  },
  data () {
    return {
      searchVal: '',
      currentScheduleId: '',
      listSchedules: [],
      topics: [],
      headerTabs: [],
      hashTopics: new Map(),
      tab: '',
      showConfirmModal: false,
      showGradeModal: false,
      loading: false,
      listScheduleCodeRegister: [],

    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    ...mapGetters('student', [
      'studentId', 'studentEmail', 'student', 'listStudents',
    ]),
    ...mapGetters('schedule', [
      'listScheduleRegisterStudent',
    ]),
    currentTopic () {
      if (!this.tab) return null;
      return this.hashTopics.get(this.tab);
    },
    currentTopicId () {
      if (this.currentTopic) {
        return this.currentTopic._id || '';
      }
      return null;
    },
    checkCanCancelTopic () {
      return this.listScheduleCodeRegister.includes(this.tab);
    },
  },
  async mounted () {
    this.fetch();
    // console.log('🚀 ~ file: ManageTopicResult.vue:240 ~ mounted ~ this.listScheduleRegisterStudent:', this.listScheduleRegisterStudent);
    // const listCode = this.listScheduleRegisterStudent.map((schedule) => schedule.code);
    // console.log('🚀 ~ file: ManageTopicResult.vue:241 ~ mounted ~ listCode.includes(this.tab);:', listCode.includes(this.tab));
    // console.log('🚀 ~ file: ManageTopicResult.vue:243 ~ mounted ~ this.headerTabs:', this.headerTabs);
  },
  methods: {
    async fetch () {
      this.loading = true;
      const topicResult = await TopicApi.getResultRegister(this.token);
      topicResult.forEach((topic) => {
        const { scheduleId } = topic;
        if (!scheduleId || !scheduleId.code) return;
        this.hashTopics.set(scheduleId.code, topic);
      });
      this.headerTabs = [...this.hashTopics.keys()];
      this.listScheduleCodeRegister = this.listScheduleRegisterStudent.map((schedule) => schedule.code);
      if (this.headerTabs.length > 0) [this.tab] = this.headerTabs;
      this.topics = this.topicResult;
      this.loading = false;
    },
    async removeRegister () {
      this.showConfirmModal = true;
    },
    async confirmRemove () {
      this.showConfirmModal = false;
      try {
        await TopicApi.removeRegisterTopicStudent(this.token, this.currentTopic._id);
        this.$toast.success('Đã xóa thành công, vui lòng xem kết quả!');
      } catch (e) {
        this.errorHandler(e);
        // if (e.response.data.error.message === 'Schedule is not in register time!') { this.$toast.error('Không trong thời gian đăng ký nên bạn không thể hủy!'); } else { this.$toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên '); }
      }
    },
    errorHandler (e) {
      if (e.response.data.error.code === 400) this.$toast.error(e.response.data.error.message);
      else { this.$toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    },
  },
};
</script>
