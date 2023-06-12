<template>
  <div class="flex flex-col">
    <div class="flex items-center justify-end mt-4 mx-4">
      <ButtonImport
        :handle-import="handleImport"
        title="Thêm tiêu chí"
      />
    </div>
    <div class="mx-4 mt-2">
      <SearchInput
        v-model="searchVal"
        :search-icon="true"
        @keydown.space.enter="search"
      />
    </div>
    <EasyDataTable
      v-model:server-options="serverOptions"
      :server-items-length="serverItemsLength"
      show-index
      table-class-name="mx-4"
      :headers="headers"
      :items="criteriaShow"
      :loading="loading"
      buttons-pagination
      :rows-items="rowItems"
    >
      <template #empty-message>
        <div class="text-center text-gray-500">
          Không có dữ liệu
        </div>
      </template>
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
  <ConfirmModal
    v-model="showConfirmModal"
    @confirm="removeItem"
    @cancel="showConfirmModal=false"
  >
    <template #title>
      Xác nhận
    </template>
    <div>Bạn có xác nhận xóa đợt đăng ký này không?</div>
  </ConfirmModal>
</template>

<script>
import { mapState, mapGetters, useStore } from 'vuex';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { useToast } from 'vue-toast-notification';
import SearchInput from 'vue-search-input';
import CriteriaApi from '../../../utils/api/criteria';
import ButtonImport from '../../common/ButtonImport.vue';
import ConfirmModal from '../../Modal/ConfirmModal.vue';

export default {
  name: 'ManageCriteriaAdmin',
  components: {
    SearchInput,
    ButtonImport,
    ConfirmModal,
  },
  setup () {
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const store = useStore();
    const loading = ref(false);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const criteria = ref([]);
    const searchVal = ref('');
    const headers = [
      { text: 'Tiêu chí', value: 'title', sortable: true },
      { text: 'Mô tả', value: 'description' },
      { text: 'Hành động', value: 'operation' },
    ];
    const items = [];
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: 'updated_at',
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    const modulePage = computed(() => store.getters['url/module']);
    const loadToServer = async (option) => {
      loading.value = true;
      const response = await CriteriaApi.listAll(token, option);
      criteria.value = response.data;
      serverItemsLength.value = response.meta.pagination.total;
      loading.value = false;
    };
    const $toast = useToast();

    const isToggle = ref(false);
    onMounted(async () => {
      await loadToServer(serverOptions.value);
    });

    const showRow = (item) => {
      if (isToggle.value || loading.value) return;
      store.dispatch('url/updateSection', `${modulePage.value}-view`);
      store.dispatch('url/updateId', item.id);
    };

    const editItem = (item) => {
      store.dispatch('url/updateSection', `${modulePage.value}-update`);
      store.dispatch('url/updateId', item.id);
    };
    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });
    watch(modulePage, async () => { await loadToServer(serverOptions.value); });

    const criteriaShow = computed(() => {
      if (!criteria.value) return [];
      return criteria.value;
    });
    const handleImport = () => {
      store.dispatch('url/updateSection', `${modulePage.value}-import`);
    };

    const removeId = ref(0);
    const showConfirmModal = ref(false);

    const handleRemove = (id) => {
      removeId.value = id;
      showConfirmModal.value = true;
    };
    const errorHandler = (e) => {
      if (e.response.data.error.code === 400) this.$toast.error(e.response.data.error.message);
      else { this.$toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    };

    const removeItem = async () => {
      try {
        showConfirmModal.value = false;
        await CriteriaApi.delete(token, removeId.value);
        $toast.success('Đã xóa tiêu chí thành công!');
        await loadToServer(serverOptions.value);
      } catch (e) {
        // $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ hệ quản trị viên!');
        errorHandler(e);
      }
    };

    const search = async () => {
      if (!searchVal.value || searchVal.value === '') await loadToServer(serverOptions.value);
      else await loadToServer({ ...serverOptions.value, search: searchVal.value });
    };
    return {
      headers,
      items,
      removeId,
      showRow,
      loading,
      serverOptions,
      serverItemsLength,
      rowItems,
      editItem,
      criteriaShow,
      isToggle,
      modulePage,
      handleImport,
      BASE_API_URL,
      removeItem,
      searchVal,
      showConfirmModal,
      handleRemove,
      search,
    };
  },
};
</script>
