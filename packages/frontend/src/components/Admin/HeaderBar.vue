<!-- eslint-disable max-len -->
<template>
  <div class="flex flex-col">
    <img
      class="w-fit h-fit"
      :src="imageUrlBanner"
    >
    <div class="flex justify-end bg-white">
      <div class="my-2 flex justify-center items-center">
        <div
          class="dropdown dropdown-bottom dropdown-end cursor-pointer mr-4 rounded-lg shadow-lg w-10 h-10 flex items-center justify-center"
          :class="{'dropdown-open': notificationShow}"
          @click="clickNotifyOrInfo('notify')"
        >
          <div
            v-if="unreadCount(listNotifications)"
            class="relative"
          >
            <div class="inline-flex absolute -top-2 -right-2 justify-center items-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full border-2 border-white dark:border-gray-900">
              {{ unreadCount(listNotifications) }}
            </div>
          </div>
          <font-awesome-icon :icon="['fas', 'bell']" />
          <div class="dropdown-content card card-compact w-[480px] p-2 shadow bg-slate-50 mt-1">
            <div class="card-body">
              <h3 class="card-title">
                Thông báo
              </h3>
              <div
                v-if="!listNotifications.length"
                class="flex flex-col w-full"
              >
                <div class="mt-2 mx-2 px-12 py-28 bg-white rounded-lg shadow">
                  Hiện không có thông báo
                </div>
              </div>
              <div
                v-for="noti in listNotifications"
                :key="`noti-${noti._id}`"
                class="flex flex-col w-full max-h-96 overflow-y-auto"
              >
                <div
                  :class="[noti.isRead ? 'bg-white' : 'bg-green-300']"
                  class="mt-2 mx-2 px-6 py-4 bg-white rounded-lg shadow cursor-pointer"
                  @click="readNotification(noti._id)"
                >
                  <div class="inline-flex items-center justify-between w-full">
                    <div class="inline-flex items-center">
                      <h3 class="font-bold text-base text-gray-800">
                        {{ noti.title }}
                      </h3>
                    </div>
                    <p class="text-xs text-gray-500">
                      {{ timeAgo(noti.createdAt) }}
                    </p>
                  </div>
                  <div class="inline-flex items-center justify-between w-full">
                    <p class="mt-1 text-sm text-left text-gray-900">
                      {{ noti.message }}
                    </p>
                    <a
                      class="text-blue-700"
                      @click="deleteNotification(noti._id)"
                    >Xóa</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Dropdown button logout -->
        <div
          class="dropdown dropdown-end"
          :class="{'dropdown-open': miniAvatarShow}"
        >
          <label
            class="m-1"
            @click="clickNotifyOrInfo('avatar')"
          ><div class="flex w-52 items-center cursor-pointer rounded-lg shadow-lg mr-2 py-1">
            <img
              class="w-8 h-8 rounded-full ml-2"
              :src="userInfo ? userInfo.picture : defaultAvatarUrl"
              alt="Avatar"
            > <span class="mx-2 font-semibold text-blue-800">
              {{ username }}
            </span>
          </div>
          </label>
          <ul
            class="dropdown-content menu shadow font-semibold bg-white w-52 mt-1 mr-2"
          >
            <li
              class="cursor-pointer font-semibold flex items-center justify-center"
              @click="signOut"
            >
              <div class="py-2 w-full">
                <font-awesome-icon :icon="['fas', 'right-to-bracket']" />
                <span>Đăng xuất</span>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
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
