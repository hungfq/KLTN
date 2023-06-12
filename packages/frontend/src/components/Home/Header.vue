<template>
  <div class="flex h-[60px] shadow-2xl rounded">
    <img
      class="h-[40px] my-auto ml-5 w-[50px]"
      :src="imageUrl"
    >
    <div class="grow flex">
      <div class="grow" />
      <HeaderButton :name-button="'Tin tức'" />
      <HeaderButton :name-button="'Đào tạo'" />
      <HeaderButton :name-button="'Bộ môn'" />
      <HeaderButton :name-button="'Sinh viên'" />
      <HeaderButton :name-button="'Nghiên cứu'" />
      <HeaderButton :name-button="'Hoạt động'" />
      <HeaderButton :name-button="'Tư vấn'" />
      <HeaderButton :name-button="'TT-Tin học'" />
      <HeaderButton :name-button="'Giới thiệu'" />
      <LoginButton @click="showLoginModal= true" />
    </div>
  </div>
  <LoginModalVue
    v-model="showLoginModal"
    @login="login"
  />
</template>

<script>
import { decodeCredential, googleLogout, googleTokenLogin } from 'vue3-google-login';
import { signInWithGoogle } from '../../utils/api/auth';
import HeaderButton from './HeaderButton.vue';
import LoginButton from './LoginButton.vue';
import LoginModalVue from '../Modal/LoginModal.vue';

export default {
  name: 'HeaderBar',
  components: {
    HeaderButton,
    LoginButton,
    LoginModalVue,
  },
  emits: ['begin-login', 'end-login'],
  data () {
    return {
      showLoginModal: false,
    };
  },
  computed: {
    imageUrl () {
      const imageUrl = new URL('/src/assets/images/fit.png', import.meta.url);
      return imageUrl;
    },
  },
  methods: {
    async login (close, typeLogin) {
      close();
      try {
        this.$emit('begin-login');
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
        this.$emit('end-login');
      } catch (err) {
        this.$emit('end-login');
        console.log(err.message);
      }
    },
    logout () {
      googleLogout();
    },
  },
};
</script>

<style lang="scss" scoped>

</style>
