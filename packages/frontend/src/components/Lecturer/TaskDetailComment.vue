<template>
  <div v-if="comment.createdByFilter">
    <template v-if="!showDelete(comment.createdBy)">
      <div class="chat chat-start">
        <div class="flex flex-col">
          <div class="chat-header ml-4 font-bold">
            {{ comment.createdByFilter.name }}
          </div>
          <div
            class="flex flex-col"
          >
            <div class="chat-bubble min-w-[200px] chat-bubble-accent max-w-[500px]">
              {{ comment.message }}
            </div>
            <time class="text-xs opacity-50 ml-2"> {{ timeAgo(comment.createdAt) }}</time>
          </div>
        </div>
      </div>
    </template>
    <template v-else>
      <div class="chat chat-end">
        <div class="flex items-center">
          <div
            class="tooltip tooltip-bottom pl-3 cursor-pointer"
            data-tip="Xóa bình luận"
          >
            <font-awesome-icon
              icon="fa-solid fa-trash-can"
              size="xl"
              class="mr-2 text-gray-500"
              @click="deleteClick(comment._id)"
            />
          </div>
          <div class="flex flex-col">
            <div class="chat-header font-bold mr-2 ml-2">
              {{ comment.createdByFilter.name }}
            </div>
            <div class="flex flex-col">
              <div class="chat-bubble min-w-[200px] chat-bubble-info max-w-[400px] break-words">
                <span v-html="$sanitize(comment.message)" />
              </div>
            </div>
            <div class="flex justify-end">
              <time class="text-xs opacity-50 ml-2 justify-end">{{ timeAgo(comment.createdAt) }}</time>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
import moment from 'moment';
import 'moment/dist/locale/vi';

export default {
  props: {
    comment: {
      type: Object,
      default: () => ({}),
    },
    taskId: {
      type: String,
      default: () => (''),
    },
  },
  computed: {
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
  },
  methods: {
    timeAgo (createdAt) {
      const date = this.formatDate(createdAt);
      moment.updateLocale('vi');
      return moment(date).fromNow();
    },
    showDelete (id) {
      return this.userId === id;
    },
    async deleteClick (commentId) {
      await this.$store.dispatch('task/removeComment', { token: this.token, commentId, taskId: this.taskId });
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
