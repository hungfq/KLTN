<!-- eslint-disable max-len -->
<template>
  <!-- grid have 3 col and mx=8 my=4 -->
  <div
    v-show="!loading"
    class="flex flex-col relative"
  >
    <HeaderPage
      @begin-login="loading=true"
      @end-login="loading=false"
    />
    <div class=" flex mx-10">
      <div class="relative flex">
        <div class="absolute top-32 left-10 w-1/2">
          <div class="font-bold text-7xl w-[650px]">
            Trau dồi hành trình học tập của bạn với chúng tôi
          </div>
          <div class="font-semibold text w-[620px] mt-4">
            Đăng ký vào nền tảng đăng ký luận án của chúng tôi và kết nối với những người cố vấn và tài nguyên để hướng dẫn bạn hướng tới một hành trình nghiên cứu xuất sắc.
          </div>
          <button
            class="btn btn-primary mt-10"
            @click="showLoginModal= true"
          >
            Đăng nhập ngay
          </button>
        </div>
        <img
          class="w-full "
          :src="imageUrlModel"
        >
      </div>
    </div>
    <BannerInfo class="my-10" />
    <BannerFrame class="my-10" />
    <div class="flex flex-col items-center content-center">
      <Loading v-show="loading" />
    </div>
  </div>
  <LoginModalVue
    v-model="showLoginModal"
    @login="login"
  />
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import { decodeCredential, googleLogout, googleTokenLogin } from 'vue3-google-login';
import HeaderPage from '../components/Home/Header.vue';
import BannerFrame from '../components/Home/BannerFrame.vue';
import BannerInfo from '../components/Home/BannerInfo.vue';
import Loading from '../components/common/Loading.vue';
import LoginModalVue from '../components/Modal/LoginModal.vue';

export default {
  name: 'HomePage',
  components: {
    HeaderPage,
    BannerFrame,
    BannerInfo,
    Loading,
    LoginModalVue,
  },
  props: {
  },
  data () {
    return {
      loading: false,
      showLoginModal: false,
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
    imageUrlModel () {
      const imageUrl = new URL('/src/assets/images/model.png', import.meta.url).href;
      return imageUrl;
    },
  },
  watch: {
    // Note: only simple paths. Expressions are not supported.
  },
  methods: {
    async login (close, typeLogin) {
      close();
      try {
        this.loading = true;
        const payload = await googleTokenLogin();
        await this.$store.dispatch('auth/signIn', { ...payload, type: typeLogin });
        const { _id } = this.$store.state.auth.userInfo;
        await this.$store.$socket.emit('id', _id);
        if (typeLogin === 'ADMIN') {
          this.$router.push('/admin');
          this.$store.dispatch('url/updatePage', 'management');
          this.$store.dispatch('url/updateModule', 'schedule');
          this.$store.dispatch('url/updateSection', 'schedule-list');
        } else if (typeLogin === 'STUDENT') {
          this.$router.push('/student');
          this.$store.dispatch('url/updatePage', 'management');
          this.$store.dispatch('url/updateModule', 'topic_proposal');
          this.$store.dispatch('url/updateSection', 'topic_proposal-list');
        } else if (typeLogin === 'LECTURER') {
          this.$router.push('/lecturer');
          this.$store.dispatch('url/updatePage', 'management');
          this.$store.dispatch('url/updateModule', 'topic');
          this.$store.dispatch('url/updateSubModule', 'topic');
          this.$store.dispatch('url/updateSection', 'topic-list');
        }
        this.loading = false;
      } catch (err) {
        this.loading = false;
      }
    },
  },
};
</script>

<style lang="scss" scoped>

</style>
