<template>
  <vue-final-modal
    v-slot="{ close }"
    v-bind="$attrs"
    @before-open="prepareData(scheduleId)"
  >
    <div class="relative p-4 w-full max-w-4xl mx-auto mt-[5%] ">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow ">
        <!-- Modal header -->
        <div class="flex justify-between items-start p-4 rounded-t border-b">
          <h3 class="text-xl font-semibold text-gray-900 ">
            Chọn sinh viên
          </h3>
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
        <div class="px-6 py-2">
          <SearchInput
            v-model="searchVal"
            :search-icon="true"
            @keydown.space.enter="search"
          />
        </div>
        <!-- Modal body -->
        <div class="max-h-96 overflow-y-auto">
          <EasyDataTable
            v-model:items-selected="itemsSelected"
            v-model:server-options="serverOptions"
            :server-items-length="serverItemsLength"
            show-index
            :headers="headers"
            :items="usersShow"
            :loading="loading"
            buttons-pagination
            :rows-items="rowItems"
          />
        </div>
        <!-- Modal footer -->
        <div class="flex justify-between items-center px-6 p-2 space-x-2 rounded-b border-t border-gray-200  ">
          <div v-if="!enabledExcel">
            <button
              data-modal-toggle="defaultModal"
              type="button"
              class="btn btn-accent mx-2"
              @click="$emit('import-excel',scheduleId)"
            >
              Nhập bằng excel
            </button>
            <a
              class="btn btn-accent"
              :href="`${BASE_API_URL}/api/v2/template?type=User`"
            >Tải mẫu nhập sinh viên</a>
          </div>
          <button
            data-modal-toggle="defaultModal"
            type="button"
            :disabled="isEqualSelected"
            class="btn btn-primary"
            @click="submitStudents"
          >
            Lưu lựa chọn
          </button>
        </div>
      </div>
    </div>
  </vue-final-modal>
</template>

<script>
import { useStore } from 'vuex';
import SearchInput from 'vue-search-input';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import 'vue-search-input/dist/styles.css';
import { isEqual, cloneDeep } from 'lodash';
import UserApi from '../../utils/api/user';
import ScheduleApi from '../../utils/api/schedule';

export default {
  name: 'SelectStudent',
  components: {
    SearchInput,
  },
  props: {
    scheduleId: {
      type: Number,
      default: null,
    },
    type: {
      type: String,
      default: 'SCHEDULE',
    },
    selected: {
      type: Array,
      default: () => [],
    },
    enabledExcel: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['change-students', 'import-excel'],
  setup (props, { emit }) {
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const store = useStore();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const itemsPrevSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const users = ref([]);
    const headers = [
      { text: 'Mã số', value: 'code', sortable: true },
      { text: 'Tên', value: 'name', sortable: true },
      { text: 'Email', value: 'email', sortable: true },
    ];
    const searchVal = ref('');
    const items = [];
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: 'updated_at',
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    const loadToServer = async (option) => {
      loading.value = true;
      let response = { data: [], meta: { pagination: { total: 0 } } };
      if (props.type === 'SCHEDULE') {
        response = await UserApi.listUser(token, 'STUDENT', option);
      } else if (props.type === 'TOPIC' || props.type === 'TOPIC_PROPOSAL') {
        if (props.scheduleId) {
          response = await ScheduleApi.fetchStudentsOfSchedule(token, props.scheduleId, option);
        }
      }
      users.value = response.data;
      serverItemsLength.value = (response.meta && response.meta.pagination && response.meta.pagination.total) || 0;
      loading.value = false;
    };

    const isToggle = ref(false);
    // onMounted(async () => {
    //   await loadToServer(serverOptions.value);
    // });

    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });

    const usersShow = computed(() => {
      if (!users.value) return [];
      return users.value.map((user) => ({
        _id: user._id,
        code: user.code,
        name: user.name,
        email: user.email,
      }));
    });

    const search = async () => {
      if (!searchVal.value || searchVal.value === '') {
        await loadToServer(serverOptions.value);
      } else {
        await loadToServer({ ...serverOptions.value, search: searchVal.value });
      }
    };

    const prepareData = async (scheduleId) => {
      if (!scheduleId) itemsSelected.value = [];
      else {
        let listStudentsRaw = [];
        if (props.type === 'SCHEDULE') {
          const result = await ScheduleApi.fetchStudentsOfSchedule(token, scheduleId);
          listStudentsRaw = result.data;
        } else if (props.type === 'TOPIC' || props.type === 'TOPIC_PROPOSAL') {
          listStudentsRaw = props.selected;
        }
        itemsSelected.value = listStudentsRaw.map((user) => ({
          _id: user._id || user.id,
          code: user.code,
          name: user.name,
          email: user.email,
        }));
        itemsPrevSelected.value = cloneDeep(itemsSelected.value);
        await loadToServer(serverOptions.value);
      }
    };

    const isEqualSelected = computed(() => {
      const selectedCopy = [...itemsSelected.value].map((item) => item._id).sort();
      const prevSelectedCopy = [...itemsPrevSelected.value].map((item) => item._id).sort();
      return isEqual(selectedCopy, prevSelectedCopy);
    });

    const submitStudents = async () => {
      const selectedCopy = [...itemsSelected.value].map((item) => item.code).sort();
      if (props.type === 'SCHEDULE' || props.type === 'TOPIC') {
        emit('change-students', selectedCopy);
      } else {
        emit('change-students', itemsSelected.value);
      }
    };

    return {
      headers,
      items,
      itemsSelected,
      loading,
      serverOptions,
      users,
      serverItemsLength,
      rowItems,
      usersShow,
      isToggle,
      BASE_API_URL,
      searchVal,
      search,
      prepareData,
      isEqualSelected,
      itemsPrevSelected,
      submitStudents,
    };
  },
};
</script>
