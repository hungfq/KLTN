<!-- eslint-disable max-len -->
<template>
  <div class="flex flex-col">
    <img
      class="w-fit h-fit"
      :src="imageUrlBanner"
    >
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import moment from 'moment';
import 'moment/dist/locale/vi';

export default {
  name: 'HeaderBar',
  components: {
  },
  props: {
    username: {
      type: String,
      default: '',
    },
    showSearch: {
      type: Boolean,
      default: true,
    },
  },
  data () {
    return {
      miniAvatarShow: false,
      notificationShow: false,
      imageUrl: '',
    };
  },
  computed: {
    imageUrlBanner () {
      const imageUrlBanner = new URL('/src/assets/images/banner_UTE.png', import.meta.url).href;
      return imageUrlBanner;
    },
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token', 'userInfo',
    ]),
    ...mapGetters('notification', [
      'listNotifications',
    ]),
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    defaultAvatarUrl () {
      const imageUrlAvatar = new URL('/src/assets/images/default_avatar.png', import.meta.url);
      return imageUrlAvatar;
    },
  },
  methods: {
    handleSearch (value) {
      this.$emit('search-header', { value, type: '' });
    },
    async signOut () {
      const { _id } = this.$store.state.auth.userInfo;
      await this.$store.dispatch('url/clearUrls');
      await this.$store.$socket.emit('logout', _id);
      this.$store.dispatch('auth/signOut');
    },
    clickNotifyOrInfo (input) {
      if (input === 'notify') {
        this.miniAvatarShow = false;
        this.notificationShow = !this.notificationShow;
      } else if (input === 'avatar') {
        this.notificationShow = false;
        this.miniAvatarShow = !this.miniAvatarShow;
      }
    },
    updatePage (page) {
      this.$store.dispatch('url/updatePage', page);
    },
    timeAgo (createdAt) {
      moment.updateLocale('vi');
      return moment(createdAt).fromNow();
    },
    unreadCount (listNotifications) {
      return listNotifications.filter((x) => x.isRead === false).length;
    },
    async showNotification () {
      this.notificationShow = !this.notificationShow;
    },
    async readNotification (_id) {
      await this.$store.dispatch('notification/readNotification', { token: this.token, id: _id });
    },
    async deleteNotification (_id) {
      await this.$store.dispatch('notification/deleteNotification', { token: this.token, id: _id });
    },
  },
};
</script>

<style lang="scss" scoped>

</style>
