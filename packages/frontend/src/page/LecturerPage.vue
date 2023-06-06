<!-- eslint-disable max-len -->
<template>
  <!-- component -->
  <div v-if="(isAuthenticated && userRole === 'LECTURER')">
    <div class="flex h-screen antialiased text-gray-900 bg-gray-100">
      <div class="flex flex-shrink-0 transition-all">
        <LeftMiniBarVue />
        <ManageBarLecturerVue
          v-if="page === 'management'"
          :list-items="listItems"
        />
        <TaskBarScheduleVue v-if="page === 'task'" />
        <TaskBarTopicVue v-if="page === 'task'" />
      </div>
      <div class="flex grow flex-col overflow-x-clip">
        <HeaderBarVue
          :username="userName"
        />
        <div class="bg-white mx-4 rounded overflow-auto">
          <template v-if="page === 'management'">
            <BodyTopicPage v-if="module === 'topic'" />
            <BodyTopicProposalPage v-if="module === 'topic_proposal_approve'" />
            <BodyTopicApprovePage v-if="module === 'topic_approve'" />
            <BodyMarkPage v-if="module === 'mark'" />
          </template>
          <template v-if="page === 'task'">
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
import LeftMiniBarVue from '../components/common/LeftMiniBar.vue';
import ManageBarLecturerVue from '../components/common/ManageBar.vue';
import HeaderBarVue from '../components/Admin/HeaderBar.vue';
import TaskBarScheduleVue from '../components/Lecturer/TaskBarSchedule.vue';
import TaskBarTopicVue from '../components/Lecturer/TaskBarTopic.vue';
import TaskDraggableVue from '../components/Lecturer/TaskDraggable.vue';

import BodyTopicPage from '../components/Lecturer/ManageTopic/TopicBodyPage.vue';
import BodyTopicProposalPage from '../components/Lecturer/ManageTopicProposal/TopicProposalBodyPage.vue';
import BodyTopicApprovePage from '../components/Lecturer/ManageApprove/TopicApproveBodyPage.vue';
import BodyMarkPage from '../components/Lecturer/ManageMark/MarkBodyPage.vue';
import ScheduleApi from '../utils/api/schedule';

export default {
  name: 'LecturerPage',
  components: {
    ErrorModalVue,
    LeftMiniBarVue,
    ManageBarLecturerVue,
    HeaderBarVue,
    TaskBarScheduleVue,
    TaskBarTopicVue,
    TaskDraggableVue,
    BodyTopicPage,
    BodyTopicProposalPage,
    BodyTopicApprovePage,
    BodyMarkPage,
  },
  props: {
  },
  data () {
    return {
      showErrorModal: false,
      isSidebarOpen: true,
      listItems: [
        { id: 'topic', value: 'Quản lý đề tài', icon: 'fa-solid fa-book' },
        { id: 'topic_proposal_approve', value: 'Yêu cầu hướng dẫn', icon: 'fa-solid fa-person-chalkboard' },
        { id: 'topic_approve', value: 'Phê duyệt đề tài', icon: 'fa-solid fa-user-check' },
        { id: 'mark', value: 'Chấm điểm', icon: 'fa-solid fa-list-check' },
      ],
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token', 'userName',
    ]),
    ...mapGetters('url', [
      'page', 'module', 'subModule', 'section', 'id',
    ]),
    ...mapGetters('schedule', [
      'listScheduleApproveLecturer',
    ]),
    isScheduleApprove () {
      // if (!this.listScheduleApproveLecturer || this.listScheduleApproveLecturer.length < 1) return false;
      // TODO: Update the logic show approve time for topic
      return true;
    },
  },
  async mounted () {
    if (!this.isAuthenticated || this.userRole !== 'LECTURER') {
      this.showErrorModal = true;
    }
    const { page, module, section } = this.$store.state.url;
    if (!page && !module && !section) {
      this.$store.dispatch('url/updatePage', 'management');
      this.$store.dispatch('url/updateModule', 'topic');
      this.$store.dispatch('url/updateSubModule', 'topic');
      this.$store.dispatch('url/updateSection', 'topic-list');
    }
    await this.$store.dispatch('schedule/fetchListScheduleToday', this.token);
    const scheduleTask = await ScheduleApi.lecturerListScheduleTopicShort(this.token);
    console.log('🚀 ~ file: LecturerPage.vue:118 ~ mounted ~ scheduleTask:', scheduleTask);
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
