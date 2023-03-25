<!-- eslint-disable max-len -->
<template>
  <!-- grid have 3 col and mx=8 my=4 -->
  <div class="flex flex-col">
    <HeaderPage class="my-10" />
    <BannerFrame class="my-10" />
    <BannerInfo class="my-10" />
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import HeaderPage from '../components/Home/Header.vue';
import BannerFrame from '../components/Home/BannerFrame.vue';
import BannerInfo from '../components/Home/BannerInfo.vue';

export default {
  name: 'HomePage',
  components: {
    HeaderPage,
    BannerFrame,
    BannerInfo,
  },
  props: {
  },
  data () {
    return {
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
  async mounted () {
    await this.$store.dispatch('topic/fetchListTopics', 'huhu');
  },
  methods: {
  },
};
</script>

<style lang="scss" scoped>

</style>
