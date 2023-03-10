<!-- eslint-disable max-len -->
<template>
  <!-- component -->
  <div v-if="(isAuthenticated && userRole === 'ADMIN')">
    <div class="flex h-screen antialiased text-gray-900 bg-gray-100">
      <div class="flex flex-shrink-0 transition-all">
        <LeftMiniBarVue />
        <ManageBarVue :list-items="listItems" />
      </div>
      <div class="flex grow flex-col">
        <HeaderBarVue :username="userName" />
        <div class="bg-white mx-4 border rounded overflow-scroll">
          <template v-if="module === 'student'">
            <ManageStudentAdminVueV2 v-if="section === 'student-list'" />
            <FormUserVue
              v-if="section === 'student-update' || section === 'student-import' || section === 'student-view'"
            />
          </template>
          <template v-if="module === 'lecturer'">
            <ManageLecturerAdminVue v-if="section === 'lecturer-list'" />
            <FormUserVue
              v-if="section === 'lecturer-update' || section === 'lecturer-import' || section === 'lecturer-view'"
            />
          </template>
          <template v-if="module === 'admin'">
            <ManageAdminVue v-if="section === 'admin-list'" />
            <FormUserVue v-if="section === 'admin-update' || section === 'admin-import' || section === 'admin-view'" />
          </template>
          <template v-if="module === 'topic'">
            <ManageTopicAdminVue v-if="section === 'topic-list'" />
            <FormTopicVue v-if="section === 'topic-update' || section === 'topic-import' || section === 'topic-view'" />
          </template>
          <template v-if="module === 'schedule'">
            <ManageScheduleAdminVue v-if="section === 'schedule-list'" />
            <FormScheduleVue v-if="section === 'schedule-update' || section === 'schedule-import' || section === 'schedule-view'" />
          </template>
          <template v-if="module === 'topic_proposal'">
            <ManageApproveProposalAdminVue v-if="section === 'topic_proposal-list'" />
            <FormApproveVue v-if="section === 'topic_proposal-update' || section === 'topic_proposal-view'" />
          </template>
          <template v-if="module === 'committee'">
            <ManageCommitteeAdminVue v-if="section === 'committee-list'" />
            <FormCommitteeVue v-if="section === 'committee-update' || section === 'committee-import' || section === 'committee-view'" />
            <FormTopicCommitteeVue v-if="section === 'committee-add-topic'" />
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
import ManageBarVue from '../components/common/ManageBar.vue';
import HeaderBarVue from '../components/Admin/HeaderBar.vue';
import ManageStudentAdminVue from '../components/Admin/ManageStudentAdmin.vue';
import ManageStudentAdminVueV2 from '../components/Admin/ManageStudentAdminV2.vue';
import ManageLecturerAdminVue from '../components/Admin/ManageLecturerAdmin.vue';
import ManageAdminVue from '../components/Admin/ManageAdmin.vue';
import ManageTopicAdminVue from '../components/Admin/ManageTopicAdmin.vue';
import ManageScheduleAdminVue from '../components/Admin/ManageScheduleAdmin.vue';
import ManageApproveProposalAdminVue from '../components/Admin/ManageApproveProposalAdmin.vue';
import ManageCommitteeAdminVue from '../components/Admin/ManageCommitteeAdmin.vue';
import FormUserVue from '../components/Admin/FormUser.vue';
import FormTopicVue from '../components/Admin/FormTopic.vue';
import FormScheduleVue from '../components/Admin/FormSchedule.vue';
import FormApproveVue from '../components/Admin/FormApprove.vue';
import FormCommitteeVue from '../components/Admin/FormCommittee.vue';
import FormTopicCommitteeVue from '../components/Admin/FormTopicCommittee.vue';

export default {
  name: 'AdminPage',
  components: {
    ErrorModalVue,
    LeftMiniBarVue,
    ManageBarVue,
    HeaderBarVue,
    ManageStudentAdminVue,
    FormUserVue,
    ManageLecturerAdminVue,
    ManageAdminVue,
    ManageTopicAdminVue,
    FormTopicVue,
    ManageScheduleAdminVue,
    ManageApproveProposalAdminVue,
    FormScheduleVue,
    FormApproveVue,
    ManageCommitteeAdminVue,
    FormCommitteeVue,
    FormTopicCommitteeVue,
    ManageStudentAdminVueV2,
  },
  props: {
  },
  data () {
    return {
      showErrorModal: false,
      isSidebarOpen: true,
      listItems: [
        {
          id: 'student',
          value: 'Qu???n l?? sinh vi??n',
        },
        {
          id: 'lecturer',
          value: 'Qu???n l?? gi???ng vi??n',
        },
        {
          id: 'admin',
          value: 'Qu???n l?? admin',
        },
        {
          id: 'topic',
          value: 'Qu???n l?? ????? t??i',
        },
        {
          id: 'schedule',
          value: 'Qu???n l?? l???ch ????ng k??',
        },
        // {
        //   id: 'topic_proposal',
        //   value: 'Qu???n l?? ????? xu???t ????? t??i',
        // },
        {
          id: 'committee',
          value: 'Qu???n l?? h???i ?????ng',
        },
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
      'page', 'module', 'section', 'id',
    ]),
  },
  async mounted () {
    if (!this.isAuthenticated || this.userRole !== 'ADMIN') {
      this.showErrorModal = true;
    }
    const { page, module, section } = this.$store.state.url;
    if (!page && !module && !section) {
      this.$store.dispatch('url/updatePage', 'management');
      this.$store.dispatch('url/updateModule', 'student');
      this.$store.dispatch('url/updateSection', 'student-list');
    }
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
