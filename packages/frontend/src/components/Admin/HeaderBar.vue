<!-- eslint-disable max-len -->
<template>
  <div class="flex flex-col">
    <img
      class="max-w-full max-h-full block"
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
    formatDate (rawDate) {
      try {
        if (!rawDate || rawDate === '') return '';
        const date = new Date(rawDate);
        const dateString = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
          .toISOString();
        const localDate = moment(dateString).local();
        return localDate.format('YYYY-MM-DD HH:mm:ss');
      } catch (e) {
        this.errorHandler(e);
        return '';
      }
    },
    errorHandler (e) {
      if (e.response.data.error.code === 400) this.$toast.error(e.response.data.error.message);
      else { this.$toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    },
    timeAgo (createdAt) {
      const date = this.formatDate(createdAt);
      moment.updateLocale('vi');
      return moment(date).fromNow();
    },
    unreadCount (listNotifications) {
      return listNotifications.filter((x) => x.isRead === false).length;
    },
    async showNotification () {
      this.notificationShow = !this.notificationShow;
    },
    async readNotification (_id) {
      try {
        await this.$store.dispatch('notification/readNotification', { token: this.token, id: _id });
      } catch (e) {
        this.errorHandler(e);
      }
    },
    async deleteNotification (_id) {
      try {
        await this.$store.dispatch('notification/deleteNotification', { token: this.token, id: _id });
      } catch (e) {
        this.errorHandler(e);
      }
    },
  },
};
</script>

<style lang="scss" scoped>

</style>
