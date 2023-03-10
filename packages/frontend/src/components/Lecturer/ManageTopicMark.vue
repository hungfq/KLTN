<template>
  <div>
    <div class="flex">
      <div class="inline-block p-2 rounded-md">
        <select
          v-model="selectVal"
          class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
          @change="selectHandler"
        >
          <!-- <option
          :key="`key-null`"
          :value="''"
        /> -->
          <option
            v-for="option in listSchedules"
            :key="`key-${option._id}`"
            :value="option._id"
          >
            {{ option.code }} : {{ option.name }}
          </option>
        </select>
      </div>
    </div>
    <div class="shadow-md sm:rounded-lg m-4">
      <SearchInput
        v-model="searchVal"
        :search-icon="true"
        @keydown.space.enter="search"
      />
      <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-300">
          <tr>
            <th
              scope="col"
              class="py-3 px-6"
            >
              Mã đề tài
            </th>
            <th
              scope="col"
              class="py-3 px-6"
            >
              Tên đề tài
            </th>
            <th
              scope="col"
              class="py-3 px-6"
            >
              Mô tả
            </th>
            <th
              scope="col"
              class="py-3 px-6"
            >
              Danh sách sinh viên
            </th>
            <th
              scope="col"
              class="py-3 px-6"
            >
              <span class="sr-only">Edit</span>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="topic in topics"
            :key="`topic-${topic._id}`"
            class="bg-slate-300 hover:bg-gray-50 "
          >
            <th
              :key="`topic-${topic._id}`"
              scope="row"
              class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap "
            >
              {{ topic.code }}
            </th>
            <th
              :key="`topic-${topic._id}`"
              scope="row"
              class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap "
            >
              {{ topic.title }}
            </th>
            <th
              :key="`topic-${topic._id}`"
              scope="row"
              class="py-4 px-6 font-medium text-gray-900 w-4"
            >
              {{ topic.description }}
            </th>
            <td class="py-4 px-6">
              <div class="font-mono cursor-pointer">
                <!-- {{ topic.studentInfo }} -->
                <template v-if="topic.studentInfo && topic.studentInfo.length > 0">
                  <li
                    v-for="student in topic.studentInfo"
                    :key="`${Math.floor(Math.random() * 10000000000)}-${student}`"
                  >
                    {{ student ? student.name : '' }} - {{ student ? student.code : '' }}
                  </li>
                </template>
              </div>
            </td>
            <td class="py-4 px-6 text-right">
              <a
                class="cursor-pointer font-medium text-blue-600 dark:text-blue-500 hover:underline mx-2"
                @click="showTopicMaskModal(topic._id)"
              >Cho điểm</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <TopicMaskModalVue
      v-model="showTopicModal"
      :task-id="1"
      @close-detail-modal="closeTopicMaskModal"
    />
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import SearchInput from 'vue-search-input';
// Optionally import default styling
import 'vue-search-input/dist/styles.css';
import TopicMaskModalVue from '../Modal/TopicMaskModal.vue';

export default {
  name: 'ManageTopicMark',
  components: {
    SearchInput,
    TopicMaskModalVue,
  },
  data () {
    return {
      selectVal: '',
      searchVal: '',
      topics: [],
      canEdit: false,
      showTopicModal: false,
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('topic', [
      'listTopicsByLecturerSchedule',
    ]),
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    ...mapGetters('student', [
      'studentId', 'studentEmail', 'student', 'listStudents',
    ]),
    ...mapGetters('schedule', [
      'listSchedules',
    ]),
    listTopicsLecturer () {
      const list = this.listTopicsByLecturerSchedule.map((t) => {
        const listStudents = t.students.map((s) => this.listStudents.find((st) => st.code.toString() === s.toString()));
        return {
          ...t, studentInfo: listStudents,
        };
      });
      const newList = list.filter((item) => {
        if (!item.lecturerId) return false;
        return item.lecturerId._id.toString() === this.userId.toString();
      });
      return newList;
    },
  },
  watch: {
    listTopicsByLecturerSchedule: {
      handler () {
        this.topics = this.listTopicsLecturer;
      },
    },
  },
  async mounted () {
    await this.$store.dispatch('student/fetchListStudent', this.token);
    await this.$store.dispatch('schedule/fetchListSchedules', this.token);
    this.topics = this.listTopicsLecturer;
    this.selectVal = this.listSchedules[0] ? this.listSchedules[0]._id : null;
    this.$store.commit('topic/setTopicScheduleId', this.selectVal);
    if (this.selectVal) { await this.$store.dispatch('topic/fetchListTopicByLecturerSchedule', { token: this.token, lecturerId: this.userId, scheduleId: this.selectVal }); }
    this.checkCanEdit(this.selectVal);
  },
  methods: {
    async handleUpdateTopic (id) {
      await this.$store.dispatch('url/updateSection', `${this.module}-update`);
      await this.$store.dispatch('url/updateId', id);
    },
    async handleShowTopic (id) {
      await this.$store.dispatch('url/updateSection', `${this.module}-view`);
      await this.$store.dispatch('url/updateId', id);
    },
    displayLecturer (lecturer) {
      return lecturer ? lecturer.name : '';
    },
    search () {
      if (this.searchVal !== '') {
        const topicFilter = this.listTopicsLecturer.filter((topic) => {
          const re = new RegExp(`\\b${this.searchVal}`, 'gi');
          if (topic.title.match(re)) return true;
          if (topic.code.match(re)) return true;
          for (let i; i < topic.students.length; i += 1) {
            if (topic.students[i]) {
              if (topic.students[i].name.match(re)) return true;
              if (topic.students[i].code.match(re)) return true;
            }
          }
          return false;
        });
        this.topics = topicFilter;
      } else this.topics = this.listTopicsLecturer;
    },
    async selectHandler () {
      this.checkCanEdit(this.selectVal);
      this.$store.commit('topic/setTopicScheduleId', this.selectVal);
      await this.$store.dispatch('topic/fetchListTopicByLecturerSchedule', { token: this.token, lecturerId: this.userId, scheduleId: this.selectVal });
    },
    checkCanEdit (scheduleId) {
      const schedule = this.listSchedules.filter((sc) => sc._id === scheduleId)[0];
      if (schedule) {
        const now = Date.now();
        const start = new Date(schedule.startApproveDate);
        const end = new Date(schedule.endApproveDate);
        this.canEdit = (start < now && now < end);
      }
    },
    showTopicMaskModal () {
      this.showTaskDetail = true;
    },
    async closeTopicMaskModal (close) {
      await close();
    },
  },
};
</script>
