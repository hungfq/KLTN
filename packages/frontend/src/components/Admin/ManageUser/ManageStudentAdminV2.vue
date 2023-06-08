<template>
  <div class="flex flex-col">
    <div class="flex mt-2 justify-end">
      <div class="mr-4">
        <ButtonImport
          :handle-import="handleImport"
          :title="`Thêm ${nameRole}`"
        />
      </div>
      <UploadButtonVue @uploadFileExcel="upload" />
      <ButtDownloadTemplate :link="`${BASE_API_URL}/api/v2/template?type=User`" />
    </div>
    <div class="mx-4 mt-2">
      <SearchInput
        v-model="searchVal"
        :search-icon="true"
        @keydown.space.enter="search"
      />
    </div>
    <EasyDataTable
      v-model:items-selected="itemsSelected"
      v-model:server-options="serverOptions"
      :server-items-length="serverItemsLength"
      table-class-name="mx-4"
      show-index
      :headers="headers"
      :items="usersShow"
      :loading="loading"
      buttons-pagination
      :rows-items="rowItems"
    >
      <template #item-gender="item">
        <span>{{ item.gender === 'male' ? 'Nam' : 'Nữ' }}</span>
      </template>
      <template #item-status="item">
        <img
          v-if="item.status"
          src="https://cdn-icons-png.flaticon.com/512/5720/5720464.png"
          class="operation-icon w-10 h-10 mx-2 cursor-pointer"
          @click="toggleActive(item)"
        >
        <img
          v-if="!item.status"
          src="https://cdn-icons-png.flaticon.com/512/5720/5720465.png"
          class="operation-icon w-10 h-10 mx-2 cursor-pointer"
          @click="toggleActive(item)"
        >
      </template>
      <template #item-operation="item">
        <div class="flex">
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
        </div>
      </template>
    </EasyDataTable>
  </div>
</template>

<script>
import { mapState, mapGetters, useStore } from 'vuex';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { useToast } from 'vue-toast-notification';
import SearchInput from 'vue-search-input';
import UploadButtonVue from '../UploadButton.vue';
import UserApi from '../../../utils/api/user';
import ButtonImport from '../../common/ButtonImport.vue';
import ButtDownloadTemplate from '../../common/ButtonDownLoadTemplate.vue';

export default {
  name: 'ManageUser',
  components: {
    ButtDownloadTemplate,
    UploadButtonVue,
    SearchInput,
    ButtonImport,
  },
  setup () {
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const store = useStore();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const searchVal = ref('');
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const users = ref([]);
    const headers = [
      { text: 'Mã số', value: 'code', sortable: true },
      { text: 'Tên', value: 'name', sortable: true },
      { text: 'Email', value: 'email', sortable: true },
      { text: 'Giới tính', value: 'gender', sortable: true },
      { text: 'Trạng thái', value: 'status', sortable: true },
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
      const response = await UserApi.listUser(token, modulePage.value.toUpperCase(), option);
      users.value = response.data;
      if (modulePage.value === 'student') {
        store.state.student.listStudents = users.value;
      } else if (modulePage.value === 'lecturer') {
        store.state.lecturer.listLecturer = users.value;
      } else {
        store.state.admin.listAdmins = users.value;
      }
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
      store.dispatch('url/updateId', item._id);
    };

    const editItem = (item) => {
      store.dispatch('url/updateSection', `${modulePage.value}-update`);
      store.dispatch('url/updateId', item._id);
    };
    const toggleActive = async (item) => {
      try {
        loading.value = true;
        isToggle.value = true;
        const value = { ...item, status: item.status ? 'IA' : 'AC' };
        await UserApi.updateUser(token, value);
        await loadToServer(serverOptions.value);
        $toast.success('Đã thay đổi trạng thái thành công');
        loading.value = false;
        isToggle.value = false;
      } catch (e) {
        $toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên để xử lý');
      }
    };
    watch(serverOptions, async (value) => { await loadToServer(value); }, { deep: true });
    watch(modulePage, async () => {
      serverOptions.value = {
        page: 1,
        rowsPerPage: 10,
        sortBy: 'updated_at',
        sortType: 'desc',
      };
      await loadToServer(serverOptions.value);
    });

    const usersShow = computed(() => {
      if (!users.value) return [];
      return users.value.map((user) => ({
        _id: user._id,
        code: user.code,
        name: user.name,
        email: user.email,
        gender: user.gender,
        status: user.status === 'AC',
      }));
    });
    const handleImport = () => {
      store.dispatch('url/updateSection', `${modulePage.value}-import`);
    };
    const nameRole = computed(() => {
      if (modulePage.value === 'student') {
        return 'sinh viên';
      } if (modulePage.value === 'lecturer') {
        return 'giảng viên';
      }
      return 'quản trị viên';
    });
    const search = async () => {
      serverOptions.value.page = 1;
      if (!searchVal.value || searchVal.value === '') await loadToServer(serverOptions.value);
      else await loadToServer({ ...serverOptions.value, search: searchVal.value });
    };
    return {
      headers,
      items,
      showRow,
      itemsSelected,
      loading,
      serverOptions,
      users,
      serverItemsLength,
      rowItems,
      editItem,
      toggleActive,
      usersShow,
      isToggle,
      modulePage,
      handleImport,
      BASE_API_URL,
      nameRole,
      searchVal,
      search,
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
  },
  methods: {
    async upload (files) {
      if (files.length > 0) {
        try {
          await this.$store.dispatch('student/importStudent', { token: this.token, xlsx: files[0] })
            .then((data) => {
              if (data.status === 200 && data.headers.get('Content-Type') === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                this.$toast.error('File không đúng chuẩn hoặc người dùng đã tồn tại');
              } else if (data.status === 200) {
                this.$toast.success('Đã nhập thành công!');
              }
            });
        } catch (e) {
          this.$toast.error('File không đúng chuẩn!');
        }
      } else {
        this.$toast.error('File không tồn tại');
      }
    },
  },
};
</script>
