<!-- eslint-disable max-len -->
<template>
  <!-- component -->
  <template v-if="(isAuthenticated && userRole === 'ADMIN') && listItems">
    <div class="flex h-screen text-gray-900 bg-gray-100">
      <div class="flex">
        <LeftMiniBarVue />
        <ManageBarVue :list-items="listItems" />
      </div>
      <div class="flex grow flex-col">
        <HeaderBarVue :username="userName" />
        <div class="bg-white mx-4 border rounded-2 overflow-auto h-full mb-1">
          <BodyUserPage
            v-if="['student','lecturer', 'admin'].includes(module)"
            :type-user="module"
            :section="section"
          />
          <BodyTopicPage v-if="module ==='topic'" />
          <BodySchedulePage v-if="module === 'schedule'" />
          <BodyCommitteePage v-if="module === 'committee'" />
          <BodyCriteriaPage v-if="module === 'criteria'" />
        </div>
      </div>
    </div>
  </template>
  <template v-if="showErrorModal">
    <ErrorModalVue
      v-model="showErrorModal"
      :message="'Bạn không có quyền truy cập, vui lòng đăng nhập lại!'"
      @close-error="closeErrorModal"
    />
  </template>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import ErrorModalVue from '../components/Modal/ErrorModal.vue';
import LeftMiniBarVue from '../components/common/LeftMiniBar.vue';
import ManageBarVue from '../components/common/ManageBar.vue';
import HeaderBarVue from '../components/Admin/HeaderBar.vue';
import BodyUserPage from '../components/Admin/ManageUser/UserBodyPage.vue';
import BodyTopicPage from '../components/Admin/ManageTopic/TopicBodyPage.vue';
import BodySchedulePage from '../components/Admin/ManageSchedule/ScheduleBodyPage.vue';
import BodyCommitteePage from '../components/Admin/ManageCommitee/CommitteeBodyPage.vue';
import BodyCriteriaPage from '../components/Admin/ManageCriteria/CriteriaBodyPage.vue';

export default {
  name: 'AdminPage',
  components: {
    ErrorModalVue,
    LeftMiniBarVue,
    ManageBarVue,
    HeaderBarVue,
    BodySchedulePage,
    BodyUserPage,
    BodyTopicPage,
    BodyCommitteePage,
    BodyCriteriaPage,
  },
  props: {
  },
  data () {
    return {
      showErrorModal: false,
      isSidebarOpen: true,
      listItems: [
        {
          id: 'schedule',
          value: 'Quản lý đợt đăng ký',
        },
        {
          id: 'student',
          value: 'Quản lý sinh viên',
        },
        {
          id: 'lecturer',
          value: 'Quản lý giảng viên',
        },
        {
          id: 'admin',
          value: 'Quản lý admin',
        },
        {
          id: 'topic',
          value: 'Quản lý đề tài',
        },
        {
          id: 'committee',
          value: 'Quản lý hội đồng',
        },
        {
          id: 'criteria',
          value: 'Quản lý tieu chi',
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
