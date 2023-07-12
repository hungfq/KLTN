<template>
  <template
    v-if="topicId"
  >
    <div class="mx-1 flex my-2 justify-between">
      <button
        class=" mx-2"
        :class="[ showStatistic ? 'btn btn-active' : 'btn btn-info' ]"
        @click="statisticHandler"
      >
        Thống kê nhiệm vụ
      </button>
      <div class="">
        <SearchInput
          v-model="searchVal"
          @keydown.space.enter="search"
        />
      </div>
      <div class="w-64 mx-2">
        <Multiselect
          v-model="selectVal"
          :options="listStudentSelect"
          :can-deselect="false"
          :searchable="true"
          no-results-text="Không có kết quả"
          no-options-text="Không có lựa lựa chọn"
          :placeholder="'Sinh viên'"
          :can-clear="false"
          @change="selectHandler"
        />
      </div>
      <div class="w-96">
        <litepie-datepicker
          v-model="dateValue"
          placeholder="Khoảng thời gian"
          separator=" đến "
          :formatter="formatter"
          i18n="vi"
          :auto-apply="false"
          :options="options"
        />
      </div>
      <!-- <button
        v-if="!showStatistic"
        class="btn btn-primary mx-2"
        @click="addTaskHandler"
      >
        Tải lên tệp báo cáo
      </button> -->
      <div
        class="dropdown dropdown-bottom dropdown-end  mx-2"
        :class="openSelectFile ? 'dropdown-open' : ''"
      >
        <label
          class="btn btn-primary"
          @click="openSelectFile = !openSelectFile"
        >Tải lên tệp báo cáo</label>
        <div
          class="dropdown-content menu p-2 shadow-lg border-solid border-2 bg-base-100 rounded-box w-[600px]"
        >
          <UploadFile />
        </div>
      </div>
    </div>
  </template>
  <div
    class="flex mt-4 justify-center 2xl:min-h-[730px] lg:min-h-[550px]"
  >
    <div
      v-if="!loading"
      class="flex"
    >
      <template v-if="!showStatistic">
        <div
          v-for="column in columns"
          :key="column.title"
          class="bg-gray-300 px-3 py-3 rounded mr-4"
        >
          <div class="body">
            <draggable
              v-model="values[column.value]"
              item-key="code"
              class="flex flex-col "
              :class="column.value"
              :sort="false"
              group="people"
              @change="log(column.value, $event)"
            >
              <template #header>
                <div class="title flex justify-center w-52">
                  <p class="text-gray-700 font-semibold font-sans">
                    {{ column.title }}
                  </p>
                </div>
              </template>
              <template #item="{element}">
                <task-card
                  :key="element._id"
                  :task="element"
                  class="mt-3 cursor-move"
                  :class="element._id"
                  @click="showTaskDetailModal(element._id)"
                />
              </template>
              <template #footer>
                <button
                  class="btn btn-primary mt-2"
                  @click="addTaskHandler"
                >
                  Thêm nhiệm vụ
                </button>
              </template>
            </draggable>
          </div>
        </div>
      </template>
      <template v-if="showStatistic">
        <div class="flex flex-col">
          <table
            data-theme="light"
            class="table bg-gray-300"
          >
            <thead>
              <tr>
                <th
                  scope="col"
                  class="py-3 min-h-[300px] w-[300px]"
                >
                  Mã
                </th>
                <th
                  scope="col"
                  class="py-3 min-h-[300px] w-[300px]"
                >
                  Tiêu đề
                </th>
                <th
                  scope="col"
                  class="py-3 min-h-[300px] w-[300px]"
                >
                  Trạng thái
                </th>
                <th
                  scope="col"
                  class="py-3 min-h-[300px]"
                >
                  Được phân công
                </th>
              </tr>
            </thead>
            <tr
              v-for="task in tasks"
              :key="task._id"
            >
              <td>
                <!-- {{ task }} -->
                {{ task.code }}
              </td>
              <td>
                {{ task.title }}
              </td>
              <td>
                <select
                  v-model="task.status"
                  disabled
                  class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                >
                  <option
                    v-for="option in columns"
                    :key="`key-${option.value}`"
                    :value="option.value"
                  >
                    {{ option.title }}
                  </option>
                </select>
              </td>
              <td>
                <select
                  v-model="task.assignTo"
                  disabled
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
              </td>
            </tr>
          </table>
          <div class="flex justify-between mt-4">
            <div class="font-semibold mx-2">
              Tổng số: {{ tasks.length }}
            </div>
            <div class="font-semibold mx-2">
              Chưa giải quyết: {{ values.PENDING.length }}
            </div>
            <div class="font-semibold mx-2">
              Sẽ làm: {{ values['TODO'].length }}
            </div>
            <div class="font-semibold mx-2">
              Đang làm: {{ values['IN_PROCESS'].length }}
            </div>
            <div class="font-semibold mx-2">
              Hoàn thành: {{ values['DONE'].length }}
            </div>
          </div>
        </div>
      </template>
    </div>
    <LoadingProcess v-else />
  </div>
  <TaskDetailModalVue
    v-model="showTaskDetail"
    :task-id="taskId"
    @close-detail-modal="closeTaskDetailModal"
  />
</template>

<script>
import { mapGetters } from 'vuex';
import SearchInput from 'vue-search-input';
import Draggable from 'vuedraggable';
import { debounce } from 'lodash';
import Multiselect from '@vueform/multiselect';
import LitepieDatepicker from 'litepie-datepicker';
import moment from 'moment';
import TaskDetailModalVue from '../Modal/TaskDetailModal.vue';
import TaskCard from './TaskCard.vue';
import LoadingProcess from '../common/Loading.vue';
import TaskApi from '../../utils/api/task';
import DocumentApi from '../../utils/api/document';
import UploadFile from '../common/UploadFile.vue';

export default {
  name: 'TaskDraggable',
  components: {
    TaskCard,
    TaskDetailModalVue,
    SearchInput,
    LoadingProcess,
    Draggable,
    Multiselect,
    LitepieDatepicker,
    UploadFile,
  },

  data () {
    return {
      showTaskDetail: false,
      showStatistic: false,
      taskId: '',
      columns: [
        {
          title: 'CHƯA GIẢI QUYẾT',
          value: 'PENDING',
        },
        {
          title: 'SẼ LÀM',
          value: 'TODO',
        },
        {
          title: 'ĐANG LÀM',
          value: 'IN_PROCESS',
        },
        {
          title: 'ĐÃ HOÀN THÀNH',
          value: 'DONE',
        },
      ],
      editTask: null,
      searchVal: '',
      selectVal: '',
      tasks: [],
      preTasks: [],
      statusEdit: false,
      listTaskUpdate: new Map(),
      values: {
        PENDING: [],
        TODO: [],
        IN_PROCESS: [],
        DONE: [],
      },
      loading: false,
      timeOut: null,
      timer: 1500,
      dateValue: {
        startDate: '',
        endDate: '',
      },
      options: {
        shortcuts: {
          today: 'Hôm nay',
          yesterday: 'Hôm trước',
          past: (period) => `${period} ngày trước`,
          currentMonth: 'Tháng nay',
          pastMonth: 'Tháng trước',
        },
        footer: {
          apply: 'Áp dụng',
          cancel: 'Huỷ',
        },
      },
      formatter: {
        date: 'L LTS',
        month: 'MMMM',
      },
      openSelectFile: false,
    };
  },
  computed: {
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('url', [
      'page', 'module', 'subModule', 'section', 'id',
    ]),
    ...mapGetters('task', [
      'listTask', 'topicId', 'listStudents',
    ]),
    listStudentSelect () {
      const arr = [{ value: 0, label: 'Tất cả sinh viên' }];
      this.listStudents.forEach((st) => {
        arr.push({ value: st._id, label: st.name });
      });
      return arr;
    },
  },
  watch: {
    listTask: {
      handler () {
        this.tasks = this.listTask;
      },
    },
    tasks: {
      handler () {
        this.calulateProgress();
      },
    },
    topicId: {
      async handler () {
        await this.fetch();
      },
    },
    dateValue: {
      async handler () {
        await this.fetch();
      },
      deep: true,
    },
  },
  async mounted () {
    await this.fetch();
  },
  methods: {
    async  fetch () {
      this.loading = true;
      if (this.topicId) {
        if (this.dateValue.startDate && this.dateValue.endDate) {
          await this.$store.dispatch('task/fetchAllTask', {
            token: this.token,
            topicId: this.topicId,
            startDate: this.convertToUTC(this.dateValue.startDate).format(),
            endDate: this.convertToUTC(this.dateValue.endDate).format(),
          });
        } else {
          await this.$store.dispatch('task/fetchAllTask', { token: this.token, topicId: this.topicId });
        }
        await this.$store.dispatch('task/fetchListStudents', { token: this.token, topicId: this.topicId });
        this.tasks = this.listTask;
        this.calulateProgress();
      }
      this.loading = false;
    },
    calulateProgress () {
      const values = {
        PENDING: [],
        TODO: [],
        IN_PROCESS: [],
        DONE: [],
      };
      this.tasks.forEach((task) => {
        values[task.status].push(task);
      });
      this.values = values;
    },
    showTaskDetailModal (id) {
      this.showTaskDetail = true;
      this.taskId = id;
    },
    addTaskHandler () {
      this.showTaskDetail = true;
      this.taskId = '';
    },
    async closeTaskDetailModal (close) {
      await close();
    },
    sleep (ms) {
      // eslint-disable-next-line no-promise-executor-return
      return new Promise((resolve) => setTimeout(resolve, ms));
    },
    async onDragEnd (column) {
      // this.editUpdatePositionTask(column);
      if (this.editTask) {
        const updatedArray = this.tasks.map((obj) => {
          if (this.editTask._id === obj._id) {
            return { ...obj, status: column.value }; // create new object with updated values
          }
          return obj; // return original object
        });
        this.tasks = updatedArray;
      }
      this.editTask = null;
    },
    parseDate (date) {
      return date.replaceAll('/', '-');
    },
    convertToUTC (dateStr) {
      const date = moment(dateStr, 'L LTS'); // parse the date string
      const utc = date.utc(); // convert to UTC
      return utc;
    },
    search () {
      if (this.searchVal !== '') {
        const taskFilters = this.listTask.filter((st) => {
          const re = new RegExp(`\\b${this.searchVal}`, 'gi');
          if (st.title.match(re)) return true;
          if (st.code.match(re)) return true;
          return false;
        });
        this.tasks = taskFilters;
      } else {
        this.tasks = this.listTask;
      }
    },
    selectHandler (value) {
      this.selectVal = value;
      if (this.selectVal) {
        const taskFilters = this.listTask.filter((st) => st.assignTo === this.selectVal);
        this.tasks = taskFilters;
      } else {
        this.tasks = this.listTask;
      }
    },
    statisticHandler () {
      this.showStatistic = !this.showStatistic;
    },
    async log (status, event) {
      if (event.added?.element?._id) {
        const id = event.added.element._id;
        const index = this.values[status].findIndex((obj) => parseInt(obj._id, 10) === parseInt(id, 10));
        if (index !== -1) {
          this.values[status][index] = { ...this.values[status][index], status };
          this.listTaskUpdate.set(parseInt(id, 10), this.values[status][index]);
          clearTimeout(this.timeOut);

          this.timeOut = setTimeout(async () => {
            if (this.listTaskUpdate.size > 0) {
              await TaskApi.updateManyTask(this.token, [...this.listTaskUpdate.values()]);
              this.listTaskUpdate = new Map();
            }
          }, this.timer);
        }
      }
    },
  },
};
</script>

<style>
/* .column-width {
  min-width: 276px;
  width: 276px;
} */
/* Unfortunately @apply cannot be setup in code sandbox,
but you'd use "@apply border opacity-50 border-blue-500 bg-gray-200" here */
/* .ghost-card {
  opacity: 0.5;
  background: #F7FAFC;
  border: 1px solid #4299e1;
} */
/* .file-preview-wrapper {
  height: 100%;
} */
</style>
