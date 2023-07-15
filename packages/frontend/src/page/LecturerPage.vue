<!-- eslint-disable max-len -->
<template>
  <!-- component -->
  <div v-if="(isAuthenticated && userRole === 'LECTURER')">
    <div class="flex h-full">
      <ManageBarLecturerVue
        v-if="page === 'management'"
        :list-items="listItems"
        :list-schedules="listScheduleTopicTask"
      />
      <div class="flex flex-col min-h-screen">
        <HeaderBarVue
          :username="userName"
        />
        <div class="bg-white border rounded-2 overflow-y-auto">
          <template v-if="page === 'management'">
            <BodyTopicPage v-if="module === 'topic'" />
            <BodyTopicProposalPage v-if="module === 'topic_proposal_approve'" />
            <BodyTopicApprovePage v-if="module === 'topic_approve'" />
            <BodyMarkPage v-if="module === 'mark'" />
          </template>
          <BodyTaskPage v-if="isTask" />
          <!-- <TaskDraggableVue /> -->
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
import ManageBarLecturerVue from '../components/common/ManageBar.vue';
import HeaderBarVue from '../components/Admin/HeaderBar.vue';
import TaskBarTopicVue from '../components/Lecturer/TaskBarTopic.vue';

import BodyTopicPage from '../components/Lecturer/ManageTopic/TopicBodyPage.vue';
import BodyTopicProposalPage from '../components/Lecturer/ManageTopicProposal/TopicProposalBodyPage.vue';
import BodyTopicApprovePage from '../components/Lecturer/ManageApprove/TopicApproveBodyPage.vue';
import BodyMarkPage from '../components/Lecturer/ManageMark/MarkBodyPage.vue';
import BodyTaskPage from '../components/Lecturer/ManageTask/TaskBodyPage.vue';

export default {
  name: 'LecturerPage',
  components: {
    ErrorModalVue,
    ManageBarLecturerVue,
    HeaderBarVue,
    BodyTaskPage,
    TaskBarTopicVue,
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
      listScheduleTopicTask: [],
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
    ...mapGetters('task', [
      'listScheduleTopic',
    ]),
    isScheduleApprove () {
      // if (!this.listScheduleApproveLecturer || this.listScheduleApproveLecturer.length < 1) return false;
      // TODO: Update the logic show approve time for topic
      return true;
    },
    isTask () {
      return this.module.includes('task-schedule');
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
    await this.$store.dispatch('task/fetchScheduleTopic', this.token);
    const scheduleTopic = this.listScheduleTopic.map((schedule) => ({ id: `task-schedule-${schedule._id}`, value: schedule.code, icon: 'fa-calendar-days' }));
    this.listScheduleTopicTask = scheduleTopic;
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
