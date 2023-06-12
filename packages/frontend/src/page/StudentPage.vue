<template>
  <!-- component -->
  <div v-if="(isAuthenticated && userRole === 'STUDENT')">
    <div class="flex bg-white">
      <ManageBarStudentVue
        v-if="page === 'management'"
        :list-items="listItems"
        :list-tasks="listTasks"
      />
      <div class="flex flex-col">
        <HeaderBarVue
          :username="userName"
        />
        <div class="bg-white rounded">
          <template v-if="page === 'management'">
            <TopicRegisterPage v-if="module === 'topic_register'" />
            <TopicProposalPage v-if="module === 'topic_proposal'" />
            <template v-if="module === 'topic_result'">
              <ManageTopicResult
                v-if="section==='topic_result-list'"
              />
            </template>
          </template>
          <template v-if="isTask">
            <TaskDraggableVue />
          </template>
        </div>
      </div>
    </div>
  </div>

  <ErrorModalVue
    v-model="showErrorModal"
    :message="'Bạn không có quyền truy cập, vui lòng đăng nhập lại!'"
    @close-error="closeErrorModal"
  />
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import ErrorModalVue from '../components/Modal/ErrorModal.vue';
import ManageBarStudentVue from '../components/common/ManageBar.vue';
import HeaderBarVue from '../components/Admin/HeaderBar.vue';
import TaskDraggableVue from '../components/Lecturer/TaskDraggable.vue';
import TaskBarTopicVue from '../components/Student/TaskBarTopic.vue';
import LeftMiniBarVue from '../components/common/LeftMiniBar.vue';

import TopicRegisterPage from '../components/Student/ManageRegister/TopicRegisterPage.vue';
import TopicProposalPage from '../components/Student/ManageTopicProposal/TopicProposalBody.vue';
import ManageTopicResult from '../components/Student/ManageResult/ManageTopicResult.vue';
import TopicApi from '../utils/api/topic';

export default {
  name: 'StudentPage',
  components: {
    ErrorModalVue,
    LeftMiniBarVue,
    ManageBarStudentVue,
    HeaderBarVue,
    TaskDraggableVue,
    TaskBarTopicVue,
    TopicRegisterPage,
    TopicProposalPage,
    ManageTopicResult,
  },
  props: {
  },
  data () {
    return {
      showErrorModal: false,
      isSidebarOpen: true,
      listItems: [
        { id: 'topic_proposal', value: 'Đề xuất đề tài', icon: 'fa-solid fa-book' },
        { id: 'topic_register', value: 'Đăng ký đề tài', icon: 'fa-solid fa-user-check' },
        { id: 'topic_result', value: 'Kiểm tra kết quả', icon: 'fa-solid fa-bullseye' },
      ],
      listTasks: [],
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userInfo', 'userRole', 'token', 'userName',
    ]),
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    isScheduleProposal () {
      if (!this.listScheduleProposalStudent || this.listScheduleProposalStudent.length < 1) return false;
      return true;
    },
    isScheduleRegister () {
      // if (!this.listScheduleRegisterStudent || this.listScheduleRegisterStudent.length < 1) return false;
      // return true;
      // TODO: Check permission for date
      return true;
    },
    isTask () {
      return this.module.includes('task');
    },
  },
  async mounted () {
    if (!this.isAuthenticated || this.userRole !== 'STUDENT') {
      this.showErrorModal = true;
    }
    const { page, module, section } = this.$store.state.url;

    if (!page && !module && !section) {
      this.$store.dispatch('url/updatePage', 'management');
      this.$store.dispatch('url/updateModule', 'topic_proposal');
      this.$store.dispatch('url/updateSection', 'topic_proposal-list');
    }
    await this.$store.dispatch('schedule/fetchListScheduleToday', this.token);
    const resultInProcess = await TopicApi.getResultRegisterInProcess(this.token);
    const taskOfTopic = resultInProcess.map((topic) => ({ id: `task-${topic._id}`, value: topic.code, icon: 'fa-solid fa-diagram-predecessor' }));
    this.listTasks = taskOfTopic;
  },
  async created () {
    const { _id } = this.$store.state.auth.userInfo;
    await this.$store.$socket.emit('login', _id);
  },
  methods: {
    closeErrorModal (close) {
      close();
      this.$router.push('/');
    },
  },
};
</script>

<style lang="scss" scoped>

</style>
