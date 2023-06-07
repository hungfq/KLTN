<!-- eslint-disable max-len -->
<template>
  <div
    class="w-64 bg-white shadow-lg   "
    :class="{ '!w-16' : !open}"
  >
    <nav
      class="flex flex-col h-full"
    >
      <!-- Logo -->
      <div
        class="flex items-center justify-center flex-shrink-0 py-10 relative"
      >
        <a>
          <img
            class="w-2/3 mx-auto"
            :src="imageLogoUrl"
          >
        </a>
        <div
          class="font-bold text-xl absolute -right-6 top-8 px-1 py-1 bg-blue-900  text-white rounded shadow-2xl"
          :class="{'rotate-180': !open}"
          @click="open =!open"
        >
          <font-awesome-icon :icon="['fas', 'arrow-left']" />
        </div>
      </div>
      <div
        v-if="userRole != 'ADMIN'"
        class="flex justify-center font-bold text-stone-400 border-2 py-1 bg-slate-100"
      >
        {{ open ? 'Quản lý đăng ký' : '' }}
      </div>

      <!-- Management -->
      <div
        class="flex flex-col px-4 space-y-2 overflow-hidden hover:overflow-auto mt-2"
      >
        <a
          v-for="item in listItems"
          :key="item.id"
          class="cursor-pointer flex p-2 items-center w-full space-x-2  rounded-lg font-semibold"
          :class="[ item.id == module ? ' text-white bg-blue-900' : 'text-blue-900 transition-colors hover:bg-blue-900 hover:text-white']"
          @click="updateModule(item.id)"
        >
          <font-awesome-icon :icon="item.icon" />
          <span v-if="open"> {{ item.value }} </span>
        </a>
      </div>
      <div class="flex justify-center font-bold text-stone-400 border-2 py-1 bg-slate-100 mt-2">
        {{ open ? 'Quá trình thực hiện' : '' }}
      </div>
      <div
        class="flex flex-col px-4 space-y-2 overflow-hidden hover:overflow-auto mt-2"
      >
        <a
          v-for="item in listTasks"
          :key="item.id"
          class="cursor-pointer flex p-2 items-center w-full space-x-2  rounded-lg font-semibold"
          :class="[ item.id == module ? ' text-white bg-blue-900' : 'text-blue-900 transition-colors hover:bg-blue-900 hover:text-white']"
          @click="updateModuleTask(item.id)"
        >
          <font-awesome-icon :icon="item.icon" />
          <span v-if="open"> {{ item.value }} </span>
        </a>
      </div>
      <div
        class="flex flex-col px-4 space-y-2 overflow-hidden hover:overflow-auto mt-2"
      >
        <a
          v-for="item in listSchedules"
          :key="item.id"
          class="cursor-pointer flex p-2 items-center w-full space-x-2  rounded-lg font-semibold"
          :class="[ item.id == module ? ' text-white bg-blue-900' : 'text-blue-900 transition-colors hover:bg-blue-900 hover:text-white']"
          @click="updateModuleTaskSchedule(item.id)"
        >
          <font-awesome-icon :icon="item.icon" />
          <span v-if="open"> {{ item.value }} </span>
        </a>
      </div>
      <div class="mt-auto" />
      <!-- Notifications -->
      <div class="flex flex-col px-2 my-2">
        <a
          :class="[ notificationShow ? ' text-white bg-blue-900 dropdown-open'
            : 'text-blue-900 transition-colors hover:bg-blue-900 hover:text-white']"
          class=" dropdown dropdown-top dropdown-right cursor-pointer flex p-2 text-blue-900 transition-colors rounded-lg  hover:bg-blue-900 hover:text-white mx-2 shadow-lg"
          @click="clickNotifyOrInfo('notify')"
        >
          <div class="flex w-full space-x-2 items-center ">
            <div

              class="relative"
            >
              <font-awesome-icon :icon="['fas', 'bell']" />
              <div
                v-if="unreadCount(listNotifications)"
                class="absolute -top-3 -right-2 bg-red-500 rounded-full text-white px-1 font-bold text-xs"
              >
                {{ unreadCount(listNotifications) }}
              </div>
            </div>
            <span
              v-if="open"
              class="font-semibold"
            >Thông báo</span>
          </div>
          <div
            v-if="notificationShow"
            class="dropdown-content card card-compact p-2 shadow bg-slate-50 text-base-content ml-2 mt-32"
          >
            <div class="card-body w-[480px] h-96 overflow-y-auto">
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
                class="flex flex-col"
              >
                <div
                  :class="[noti.isRead ? 'bg-white' : 'bg-green-300']"
                  class="mt-2 mx-2 px-6 py-4 bg-white rounded-lg shadow cursor-pointer"
                  @click="readNotification(noti._id)"
                >
                  <div class="flex items-center justify-between w-full">
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
        </a>
        <!-- Sign out -->
        <a
          class="cursor-pointer flex p-2 items-center space-x-2  mt-2 rounded-lg text-blue-900 transition-colors hover:bg-blue-900 hover:text-white ml-2 mr-2 shadow-lg font-semibold"
          @click="signOut"
        >
          <font-awesome-icon :icon="['fas', 'right-to-bracket']" />
          <span v-if="open">Đăng xuất</span>
        </a>
      </div>
      <div
        class="flex pl-2 border-y-2 bg-slate-300"
      >
        <div class="flex my-1 items-center">
          <img
            class="w-8 h-8 rounded-full ml-2"
            :src="userInfo ? userInfo.picture : defaultAvatarUrl"
            alt="Avatar"
          > <span
            v-if="open"
            class="mx-2 font-semibold text-blue-800"
          >
            {{ userName }}
          </span>
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import moment from 'moment';
import 'moment/dist/locale/vi';

export default {
  name: 'ManageBar',
  props: {
    listItems: [],
    listTasks: [],
    listSchedules: [],
  },
  data () {
    return {
      open: true,
      miniAvatarShow: false,
      notificationShow: false,
      imageUrl: '',
    };
  },
  computed: {
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    imageLogoUrl () {
      const imageUrl = new URL('/src/assets/images/fit.png', import.meta.url);
      return imageUrl;
    },
    imageUrlBanner () {
      const imageUrlBanner = new URL('/src/assets/images/banner_UTE.png', import.meta.url).href;
      return imageUrlBanner;
    },
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token', 'userInfo', 'userName',
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
    updateModuleTask (code) {
      const number = parseInt(code.split('-')[1], 10);
      this.$store.dispatch('url/updateModule', code);
      this.$store.dispatch('task/updateTopicId', number);
    },
    updateModuleTaskSchedule (code) {
      const number = parseInt(code.split('-')[2], 10);
      this.$store.dispatch('url/updateModule', code);
      this.$store.dispatch('task/updateScheduleId', number);
      this.$store.dispatch('task/updateTopicId', null);
    },
    updateModule (module) {
      this.$store.dispatch('url/updateModule', module);
      this.$store.dispatch('url/updateSection', `${module}-list`);
    },
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
      const date = this.formatDate(createdAt);
      return moment(date).fromNow();
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
    formatDate (rawDate) {
      try {
        if (!rawDate || rawDate === '') return '';
        const date = new Date(rawDate);
        const dateString = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
          .toISOString();
        const localDate = moment(dateString).local();
        return localDate.format('YYYY-MM-DD HH:mm:ss');
      } catch (e) {
        return '';
      }
    },
  },
};
</script>
