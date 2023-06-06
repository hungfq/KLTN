<template>
  <template
    v-if="topicId"
  >
    <button
      class=" m-4 text-white py-2 px-4 rounded"
      :class="[ showStatistic ? ' text-white bg-indigo-600 hover:bg-indigo-700' : 'text-indigo-600 bg-gray-100 hover:bg-blue-100 hover:text-indigo-700']"
      @click="statisticHandler"
    >
      Thống kê nhiệm vụ
    </button>
    <div class="inline-block w-fit border-2 rounded-md">
      <SearchInput
        v-model="searchVal"
        @keydown.space.enter="search"
      />
    </div>
    <div class="inline-block p-2 rounded-md">
      <select
        v-model="selectVal"
        class="mt-1 block w-full rounded-md bg-gray-100 border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
        @change="selectHandler"
      >
        <option
          :key="`key-null`"
          :value="''"
        />
        <option
          v-for="option in listStudents"
          :key="`key-${option._id}`"
          :value="option._id"
        >
          {{ option.name }}
        </option>
      </select>
    </div>
    <button
      v-if="!showStatistic"
      class=" m-4 bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded float-right"
      @click="addTaskHandler"
    >
      Thêm nhiệm vụ
    </button>
  </template>
  <div
    class="flex mt-4 justify-center "
    :class="{'min-h-[600px]' : tasks.length === 0}"
  >
    <div class="flex">
      <div
        v-for="column in columns"
        :key="column.title"
        class="bg-gray-100 px-3 py-3 rounded mr-4 "
      >
        <div class="body mr-5">
          <draggable
            v-model="values[column.value]"
            item-key="_id"
            class="flex flex-col"
            :class="column.value"
            group="people"
            @change="log(column.value, $event)"
          >
            <template #header>
              <div class="title flex justify-center w-64">
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
                @click="addPeople"
              >
                Thêm nhiệm vụ
              </button>
            </template>
          </draggable>
        </div>
      </div>
      <div class="flex p-2 pr-0">
        <template v-if="false && !showStatistic" />
      </div>
      <template v-if="showStatistic">
        <table class="w-full text-sm text-left text-gray-500">
          <thead class="text-xs text-gray-700 uppercase bg-gray-300">
            <tr>
              <th
                scope="col"
                class="py-3 px-6"
              >
                Mã
              </th>
              <th
                scope="col"
                class="py-3 px-6"
              >
                Tiêu đề
              </th>
              <th
                scope="col"
                class="py-3 px-6"
              >
                Trạng thái
              </th>
              <th
                scope="col"
                class="py-3 px-6"
              >
                Được phân công
              </th>
            </tr>
          </thead>
          <tr
            v-for="task in tasks"
            :key="task._id"
            class="bg-slate-300 hover:bg-gray-50"
          >
            <td class="py-2 px-6 font-medium text-gray-900 whitespace-nowrap">
              <!-- {{ task }} -->
              {{ task.code }}
            </td>
            <td class="py-2 px-6 font-medium text-gray-900 whitespace-nowrap">
              {{ task.title }}
            </td>
            <td class="py-2 px-6 font-medium text-gray-900 whitespace-nowrap">
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
            <td class="py-2 px-6 font-medium text-gray-900 whitespace-nowrap">
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
      </template>
    </div>
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
import TaskDetailModalVue from '../Modal/TaskDetailModal.vue';
import TaskCard from './TaskCard.vue';

export default {
  name: 'TaskDraggable',
  components: {
    TaskCard,
    TaskDetailModalVue,
    SearchInput,
    Draggable,
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
    listTaskUpdate: {
      async handler () {
        [...this.listTaskUpdate.values()].forEach(async (task) => {
          if (task) {
            await this.$store.dispatch('task/updateTaskStatus', { token: this.token, value: task });
            this.listTaskUpdate.delete(task._id);
          }
        });
      },
      deep: true,
    },
  },
  async mounted () {
    if (this.topicId) {
      await this.$store.dispatch('task/fetchAllTask', { token: this.token, topicId: this.topicId });
      this.tasks = this.listTask;
      this.calulateProgress();
    }
  },
  methods: {
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
      // this.statusEdit = false;
    },
    async editUpdatePositionTask (column) {
      if (this.editTask) {
        this.editTask.status = column.value;
        await this.$store.dispatch('task/updateTaskStatus', { token: this.token, value: this.editTask });
      }
      this.editTask = null;
    },
    handleChange (task) {
      this.editTask = task;
      this.listIdTaskUpdate.push(...new Set([...this.listIdTaskUpdate, task._id]));
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
    selectHandler () {
      if (this.selectVal !== '') {
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
          await this.$store.dispatch('task/updateTaskStatus', { token: this.token, value: { ...this.values[status][index], status } });
        }
      }
    },
  },
};
</script>

<style scoped>
.column-width {
  min-width: 276px;
  width: 276px;
}
/* Unfortunately @apply cannot be setup in code sandbox,
but you'd use "@apply border opacity-50 border-blue-500 bg-gray-200" here */
/* .ghost-card {
  opacity: 0.5;
  background: #F7FAFC;
  border: 1px solid #4299e1;
} */
</style>
