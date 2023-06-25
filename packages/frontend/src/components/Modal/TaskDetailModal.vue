<template>
  <vue-final-modal
    v-slot="{ close }"
    v-bind="$attrs"
    @before-open="handleShow(taskId)"
  >
    <div class="relative p-4 w-full max-w-6xl mx-auto mt-[5%]">
      <!-- Modal content -->

      <div
        class="relative bg-white rounded-md shadow"
        :class="{'h-[300px]': loading}"
      >
        <template v-if="!loading">
          <!-- Modal header -->
          <div class="flex justify-between items-start p-4 rounded-t border-b ">
            <h2 class="text-xl font-semibold text-gray-900 ">
              <input
                v-model="taskDetail.title"
                class="w-full p-2"
                placeholder="Tên nhiệm vụ"
              >
            </h2>
            <button
              type="button"
              class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
              data-modal-toggle="defaultModal"
              @click="close"
            >
              <svg
                aria-hidden="true"
                class="w-5 h-5"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
              ><path
                fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"
              /></svg>
              <span class="sr-only">Close modal</span>
            </button>
          </div>
          <!-- Modal body -->
          <div class="px-6 grid grid-cols-12 gap-4">
            <!-- Left body -->
            <div class="col-span-9">
              <div class="tabs">
                <a
                  v-for="t in tabs"
                  :key="t.value"
                  class="tab tab-lifted"
                  :class="{ 'tab-active': tab === t.value }"
                  @click="tab = t.value"
                >{{ t.name }}</a>
              </div>
              <div
                class="h-80 overflow-y-auto mb-4 overflow-x-hidden"
                :class="{'!h-[400px]': tab ===0}"
              >
                <ckeditor
                  v-if="tab ===0 "
                  v-model="editorData"
                  :editor="editor"
                  @input="changeDescription"
                />
                <template v-if="tab === 1">
                  <div
                    v-for="item in taskDetail.comments"
                    :key="item._id"
                  >
                    <TaskDetailComment
                      :comment="item"
                      :task-id="taskDetail._id"
                    />
                  </div>
                </template>
              </div>
              <div
                v-if="taskDetail._id && tab ===1"
                class="my-4 flex"
              >
                <textarea
                  v-show="!loadingComment"
                  v-model="comment"
                  class=" p-2 border-2 grow mr-1"
                  placeholder="Thêm nhận xét..."
                  type="text"
                  @keyup.enter="addComment"
                />
                <div
                  v-if="loadingComment"
                  class="grow flex justify-center"
                >
                  <loading
                    v-model:active="loadingComment"
                    class="mx-auto"
                    :can-cancel="true"
                    :is-full-page="false"
                  />
                </div>
                <button
                  class="
                btn
                btn-primary
                text-white
                py-2
                px-4"
                  @click="addComment"
                >
                  <font-awesome-icon
                    :icon="['fas', 'paper-plane']"
                    class="mr-2"
                  />
                  Gửi
                </button>
              </div>
            </div>
            <!-- Right body -->
            <div class="col-span-3">
              <div class="font-normal my-4">
                Trạng thái:
                <select
                  v-model="taskDetail.status"
                  class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                >
                  <option
                    v-for="option in statuses"
                    :key="`key-${option.value}`"
                    :value="option.value"
                  >
                    {{ option.text }}
                  </option>
                </select>
              </div>
              <div class="font-normal my-4">
                Được phân công:
                <select
                  v-model="taskDetail.assignTo"
                  class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                >
                  <option
                    v-for="option in listStudents"
                    :key="`key-${option._id}`"
                    :value="option._id"
                  >
                    {{ option.name }}
                  </option>
                </select>
              </div>
              <template v-if="taskDetail.createdByFilter">
                <div class="font-normal my-4">
                  Tạo bởi: {{ taskDetail.createdByFilter ? taskDetail.createdByFilter.name : 'none' }}
                </div>
              </template>
              <template v-if="taskDetail.createdAt">
                <div class="font-normal my-4">
                  Tạo mới: {{ timeAgo(taskDetail.createdAt) }}
                </div>
              </template>
              <template v-if="taskDetail.updatedAt">
                <div class="font-normal my-4">
                  Cập nhật: {{ timeAgo(taskDetail.updatedAt) }}
                </div>
              </template>
              <button
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full"
                @click="saveHandle(close)"
              >
                {{ taskDetail._id ? 'Cập nhật' : 'Lưu' }}
              </button>
            </div>
          </div>
        </template>
        <div
          v-else
          class="flex items-center justify-center"
        >
          <LoadingProcess />
        </div>
      </div>
    </div>
  </vue-final-modal>
</template>
<script>
import { mapGetters } from 'vuex';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import moment from 'moment';
import 'moment/dist/locale/vi';
import Loading from 'vue-loading-overlay';
import TaskDetailComment from '../Lecturer/TaskDetailComment.vue';
import LoadingProcess from '../common/Loading.vue';

export default {
  name: 'TaskDetailModal',
  components: {
    TaskDetailComment,
    LoadingProcess,
    Loading,
  },
  inheritAttrs: false,
  props: {
    // eslint-disable-next-line vue/require-prop-type-constructor, vue/require-default-prop
    taskId: '',
  },
  emits: ['closeDetailModal'],
  data () {
    return {
      editor: ClassicEditor,
      editorData: '',
      statuses: [
        { text: 'CHƯA GIẢI QUYẾT', value: 'PENDING' },
        { text: 'SẼ LÀM', value: 'TODO' },
        { text: 'ĐANG LÀM', value: 'IN_PROCESS' },
        { text: 'ĐÃ HOÀN THÀNH', value: 'DONE' },
      ],
      comment: '',
      loading: false,
      loadingComment: false,
      tabs: [
        {
          name: 'Mô tả',
          value: 0,
        },
        {
          name: 'Bình luận',
          value: 1,
        },
      ],
      tab: 0,
    };
  },
  computed: {
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    ...mapGetters('task', [
      'listScheduleTopic', 'listTopic', 'topicId', 'listStudents', 'taskDetail',
    ]),
  },
  methods: {
    errorHandler (e) {
      if (e.response.data.error.code === 400) this.$toast.error(e.response.data.error.message);
      else { this.$toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    },
    async handleShow (taskId) {
      try {
        this.loading = true;
        await this.$store.dispatch('task/fetchTaskDetail', { token: this.token, taskId });
        if (this.taskDetail) {
          this.editorData = this.taskDetail.description;
        }
        this.loading = false;
      } catch (e) {
        this.loading = false;
        // console.log(e);
        this.errorHandler(e);
        // this.$toast.error(e.message);
      }
      if (!this.taskDetail._id) {
        this.taskDetail.status = 'PENDING';
        this.taskDetail.assignTo = this.listStudents[0]._id;
      }
    },
    async changeDescription (data) {
      if (this.taskDetail) {
        this.taskDetail.description = data;
      }
    },
    async saveHandle (close) {
      this.loading = true;
      try {
        if (this.taskDetail._id) {
          await this.$store.dispatch('task/updateTask', { token: this.token, value: this.taskDetail });
        } else {
          await this.$store.dispatch('task/insertTask', { token: this.token, value: this.taskDetail, topicId: this.topicId });
        }
        if (this.topicId) {
          await this.$store.dispatch('task/fetchAllTask', { token: this.token, topicId: this.topicId });
        }
      } catch (e) {
        console.error(e);
      }
      this.loading = false;
      this.$emit('closeDetailModal', close);
    },
    async addComment () {
      this.loadingComment = true;
      if (this.comment !== '') {
        await this.$store.dispatch('task/insertComment', { token: this.token, message: this.comment, taskId: this.taskDetail._id });
        this.comment = '';
      }
      this.loadingComment = false;
    },
    timeAgo (createdAt) {
      const date = this.formatDate(createdAt);
      moment.updateLocale('vi');
      return moment(date).fromNow();
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
