<!-- eslint-disable max-len -->
<template>
  <!-- grid have 3 col and mx=8 my=4 -->
  <div
    v-show="!loading"
    class="flex flex-col"
  >
    <HeaderPage
      class="my-10"
      @begin-login="loading=true"
      @end-login="loading=false"
    />
    <BannerFrame class="my-10" />
    <BannerInfo class="my-10" />
  </div>
  <div class="flex flex-col items-center content-center">
    <Loading v-show="loading" />
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import HeaderPage from '../components/Home/Header.vue';
import BannerFrame from '../components/Home/BannerFrame.vue';
import BannerInfo from '../components/Home/BannerInfo.vue';
import Loading from '../components/common/LoadingCircle.vue';

export default {
  name: 'HomePage',
  components: {
    HeaderPage,
    BannerFrame,
    BannerInfo,
    Loading,
  },
  props: {
  },
  data () {
    return {
      loading: false,
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: (state) => state.auth.isAuthenticated,
      userInfo: (state) => state.ui.auth.userinfo,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('topic', [
      'listTopics',
    ]),
  },
  watch: {
    // Note: only simple paths. Expressions are not supported.
  },
  methods: {
  },
};
</script>

<style lang="scss" scoped>

</style>
