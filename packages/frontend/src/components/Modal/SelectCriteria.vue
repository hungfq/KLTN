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
            Chọn tiêu chí chấm điểm
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
        <template v-if="currentPage===0">
          <div class="px-6">
            <SearchInput
              v-model="searchVal"
              :search-icon="true"
              @keydown.space.enter="search"
            />
          </div>
          <!-- Modal body -->
          <div class="max-h-96 pl-6 pr-2 overflow-y-auto">
            <EasyDataTable
              v-model:items-selected="itemsSelected"
              v-model:server-options="serverOptions"
              :server-items-length="serverItemsLength"
              show-index
              :headers="headers"
              :items="criteriaShow"
              :loading="loading"
              buttons-pagination
              :rows-items="rowItems"
            >
              <template #item-operation="item">
                <div class="flex justify-self-auto">
                  <div
                    class="tooltip tooltip-bottom pr-3"
                    data-tip="Xem tiêu chí"
                  >
                    <font-awesome-icon
                      class="cursor-pointer"
                      icon="fa-solid fa-eye"
                      size="xl"
                      @click="showRow(item)"
                    />
                  </div>
                  <div
                    class="tooltip tooltip-bottom"
                    data-tip="Chỉnh sửa tiêu chí"
                  >
                    <font-awesome-icon
                      class="cursor-pointer"
                      :icon="['fas', 'pen-to-square']"
                      size="xl"
                      @click="editItem(item)"
                    />
                  </div>
                  <div
                    class="tooltip tooltip-bottom pl-3"
                    data-tip="Xóa tiêu chí"
                  >
                    <font-awesome-icon
                      icon="fa-solid fa-trash-can"
                      size="xl"
                      @click="handleRemove(item.id)"
                    />
                  </div>
                </div>
              </template>
              <template #item-description="item">
                <div
                  class="max-w-xl rounded break-words overflow-hidden"
                >
                  {{ item.description }}
                </div>
              </template>
            </EasyDataTable>
          </div>
        </template>
        <template v-if="currentPage===1">
          <div class="max-h-96 pl-6 pr-2 overflow-y-auto">
            <table class="table table-compact w-full !bg-white">
              <thead class="!bg-slate-300">
                <tr class="!bg-slate-300">
                  <th />
                  <th>Tiêu chí</th>
                  <th>Tỷ lệ điểm</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(criterion, index) in itemsSelectedScore"
                  :key="criterion.id"
                >
                  <th>{{ index + 1 }}</th>
                  <td>{{ criterion.title }}</td>
                  <td>
                    <div class="flex items-start">
                      <FormKit
                        v-model.number="criterion.score_rate"
                        outer-class="w-20"
                        type="number"
                        name="score-rate"
                        :suffix="'%'"
                        validation="required|min:1|max:100"
                        :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này',
                                                min: 'Tỷ lệ điểm phải lớn hơn 1 %',
                                                max: 'Tỷ lệ điểm phải nhỏ hơn 100 %' }"
                      />
                      <span class="ml-2 mt-2">%</span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <th />
                  <td />
                  <td>
                    <div class="flex">
                      <span
                        v-if="!checkTotalEqual100Percent"
                        class="text-red-500"
                      >Tổng tỷ lệ điểm điểm phải bằng 100%</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </template>
        <!-- Modal footer -->
        <div class="flex justify-end items-center px-6 p-2 space-x-2 rounded-b border-t border-gray-200  ">
          <button
            v-if="currentPage===0"
            type="button"
            class="btn btn-primary"
            :disabled="itemsSelected.length===0"
            @click="continuePage"
          >
            Tiếp tục
          </button>
          <template v-else>
            <button
              type="button"
              class="btn btn-secondary"
              @click="currentPage=0"
            >
              Quay lại
            </button>
            <button
              type="button"
              class="btn btn-primary"
              :disabled="!validateData"
              @click="submitCriteria"
            >
              Lưu lựa chọn
            </button>
          </template>
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
import { cloneDeep, unionBy } from 'lodash';
import { useToast } from 'vue-toast-notification';
import CriteriaApi from '../../utils/api/criteria';

export default {
  name: 'SelectCriteria',
  components: {
    SearchInput,
  },
  props: {
    scheduleId: {
      type: Number,
      default: null,
    },
  },
  emits: ['change-criteria', 'import-excel'],
  setup (props, { emit }) {
    const currentPage = ref(0);
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const store = useStore();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const itemsSelectedScore = ref([]);
    const itemsPrevSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const criteria = ref([]);
    const headers = [
      { text: 'Tiêu đề', value: 'title', sortable: true },
      { text: 'Mô tả', value: 'description', sortable: true },
    ];
    const searchVal = ref('');
    const items = [];
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: 'updated_at',
      sortType: 'desc',
    });
    const $toast = useToast();
    const errorHandler = (e) => {
      if (e.response.data.error.code === 400) $toast.error(e.response.data.error.message);
      else { $toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    };
    const token = store.getters['auth/token'];
    const loadToServer = async (option) => {
      loading.value = true;
      const response = await CriteriaApi.listAll(token, option);
      criteria.value = response.data;
      serverItemsLength.value = response.meta.pagination.total;
      loading.value = false;
    };

    const isToggle = ref(false);
    onMounted(async () => {
      await loadToServer(serverOptions.value);
    });

    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });

    const criteriaShow = computed(() => {
      if (!criteria.value) return [];
      return criteria.value.map((item) => ({
        id: item.id,
        title: item.title,
        description: item.description,
      }));
    });

    const search = async () => {
      try {
        if (!searchVal.value || searchVal.value === '') {
          await loadToServer(serverOptions.value);
        } else {
          await loadToServer({ ...serverOptions.value, searchVal: searchVal.value });
        }
      } catch (e) {
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
        errorHandler(e);
      }
    };

    const prepareData = async (scheduleId) => {
      loading.value = true;
      currentPage.value = 0;
      if (!scheduleId) itemsSelected.value = [];
      else {
        try {
          const listStudentsRaw = await CriteriaApi.listBySchedule(token, scheduleId);
          itemsSelected.value = listStudentsRaw.map((criterion) => ({
            id: criterion.criteria_id,
            title: criterion.title,
            description: criterion.description,
          }));
          itemsPrevSelected.value = listStudentsRaw.map((criterion) => ({
            id: criterion.criteria_id,
            title: criterion.title,
            description: criterion.description,
            score_rate: criterion.score_rate,
          }));
        } catch (e) {
          // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
          errorHandler(e);
        }
      }
      loading.value = false;
    };

    const continuePage = () => {
      const mergedArray = unionBy(itemsPrevSelected.value, itemsSelected.value, 'id');
      itemsSelectedScore.value = cloneDeep(mergedArray);
      currentPage.value = 1;
    };
    const checkTotalEqual100Percent = computed(() => {
      const totalScoreRate = itemsSelectedScore.value.reduce((acc, curr) => acc + curr.score_rate, 0);
      return totalScoreRate === 100;
    });

    const validateData = computed(() => {
      const equalPrevSelected = JSON.stringify(itemsSelectedScore.value) === JSON.stringify(itemsPrevSelected.value);
      const isValid = itemsSelectedScore.value.every((item) => {
        const scoreRate = item.score_rate;
        return !!scoreRate && scoreRate > 1 && scoreRate <= 100;
      });
      return !equalPrevSelected && isValid && checkTotalEqual100Percent.value;
    });

    const submitCriteria = async () => {
      if (!validateData.value) return;
      const criteriaScore = itemsSelectedScore.value.map((item) => ({ criteria_id: item.id, score_rate: item.score_rate }));
      emit('change-criteria', criteriaScore);
    };

    const closeModal = (close) => {
      close();
      itemsSelected.value = [];
    };
    return {
      headers,
      items,
      itemsSelected,
      loading,
      serverOptions,
      criteria,
      serverItemsLength,
      rowItems,
      criteriaShow,
      closeModal,
      isToggle,
      BASE_API_URL,
      continuePage,
      searchVal,
      search,
      prepareData,
      itemsPrevSelected,
      submitCriteria,
      currentPage,
      checkTotalEqual100Percent,
      itemsSelectedScore,
      validateData,
    };
  },
};
</script>
<style scoped>
th {
  background: white
  }
</style>
