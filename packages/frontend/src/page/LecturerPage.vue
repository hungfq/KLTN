<!-- eslint-disable max-len -->
<template>
  <!-- component -->
  <div v-if="true || (isAuthenticated && userRole === 'LECTURER')">
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
          v-if="page !== 'task'"
          :username="userName"
        />
        <MiniHeaderBarVue
          v-if="page === 'task'"
          :username="userName"
        />
        <div class="bg-white mx-4 border rounded overflow-scroll">
          <template v-if="page === 'management'">
            <template v-if="module === 'topic'">
              <ManageTopicLecturerVue v-if="section === 'topic-list'" />
              <FormTopicVue
                v-if="section === 'topic-update' || section === 'topic-import' || section === 'topic-view'"
              />
            </template>
            <template v-if="module === 'topic_proposal_approve'">
              <ManageApproveProposalLecturerVue
                v-if="section === 'topic_proposal_approve-list'"
                :open="isScheduleApprove"
              />
              <FormApproveProposalVue v-if="section === 'topic_proposal_approve-view' || section === 'topic_proposal_approve-update'" />
            </template>
            <template v-if="module === 'topic_advisor_approve'">
              <ManageTopicAdvisorLecturerVue
                v-if="section === 'topic_advisor_approve-list'"
              />
            </template>
            <template v-if="module === 'topic_critical_approve'">
              <ManageTopicCriticalLecturerVue
                v-if="section === 'topic_critical_approve-list'"
              />
            </template>
            <template v-if="module === 'advisor_mark'">
              <AdvisorMarkPage />
            </template>
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
    :message="'B???n kh??ng c?? quy???n truy c???p, vui l??ng ????ng nh???p l???i!'"
    @close-error="closeErrorModal"
  />
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import ErrorModalVue from '../components/Modal/ErrorModal.vue';
import LeftMiniBarVue from '../components/common/LeftMiniBar.vue';
import ManageBarLecturerVue from '../components/common/ManageBar.vue';
import HeaderBarVue from '../components/Admin/HeaderBar.vue';
import MiniHeaderBarVue from '../components/Lecturer/MiniHeaderBar.vue';
import ManageTopicLecturerVue from '../components/Lecturer/ManageTopicLecturer.vue';
import ManageApproveProposalLecturerVue from '../components/Lecturer/ManageApproveProposalLecturer.vue';
import ManageTopicAdvisorLecturerVue from '../components/Lecturer/ManageTopicAdvisorLecturer.vue';
import ManageTopicCriticalLecturerVue from '../components/Lecturer/ManageTopicCriticalLecturer.vue';
import FormTopicVue from '../components/Lecturer/FormTopic.vue';
import FormApproveProposalVue from '../components/Lecturer/FormApproveProposal.vue';
import TaskBarScheduleVue from '../components/Lecturer/TaskBarSchedule.vue';
import TaskBarTopicVue from '../components/Lecturer/TaskBarTopic.vue';
import TaskDraggableVue from '../components/Lecturer/TaskDraggable.vue';
import AdvisorMarkPage from '../lecturer_page/AdvisorMark/AdvisorMarkPage.vue';

export default {
  name: 'LecturerPage',
  components: {
    ErrorModalVue,
    LeftMiniBarVue,
    ManageBarLecturerVue,
    HeaderBarVue,
    MiniHeaderBarVue,
    ManageTopicLecturerVue,
    FormTopicVue,
    ManageApproveProposalLecturerVue,
    FormApproveProposalVue,
    TaskBarScheduleVue,
    TaskBarTopicVue,
    TaskDraggableVue,
    ManageTopicAdvisorLecturerVue,
    ManageTopicCriticalLecturerVue,
    AdvisorMarkPage,
  },
  props: {
  },
  data () {
    return {
      showErrorModal: false,
      isSidebarOpen: true,
      listItems: [
        { id: 'topic', value: 'Qu???n l?? ????? t??i h?????ng d???n' },
        // { id: 'topic_proposal', value: 'Y??u c???u h?????ng d???n' },
        { id: 'topic_proposal_approve', value: 'Ph?? duy???t y??u c???u h?????ng d???n' },
        { id: 'topic_advisor_approve', value: 'Ph?? duy???t ????? t??i h?????ng d???n ra h???i ?????ng' },
        { id: 'topic_critical_approve', value: 'Ph?? duy???t ????? t??i ph???n bi???n ra h???i ?????ng' },
        { id: 'advisor_mark', value: 'Gi??o vi??n h?????ng d???n cho ??i???m' },
        { id: 'president_mark', value: 'Ch??? t???ch h???i ?????ng cho ??i???m' },
        { id: 'critical_mark', value: 'Gi??o vi??n ph???n bi???n cho ??i???m' },
        { id: 'secretary_mark', value: 'Th?? k?? h???i ?????ng cho ??i???m' },
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
      if (!this.listScheduleApproveLecturer || this.listScheduleApproveLecturer.length < 1) return false;
      return true;
    },
  },
  async mounted () {
    // if (!this.isAuthenticated || this.userRole !== 'LECTURER') {
    //   this.showErrorModal = true;
    // }
    const { page, module, section } = this.$store.state.url;
    if (!page && !module && !section) {
      this.$store.dispatch('url/updatePage', 'management');
      this.$store.dispatch('url/updateModule', 'topic');
      this.$store.dispatch('url/updateSubModule', 'topic');
      this.$store.dispatch('url/updateSection', 'topic-list');
    }
    await this.$store.dispatch('schedule/fetchListScheduleApproveLecturer', this.token);
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
