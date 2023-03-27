<template>
  <div class="flex flex-col">
    <div class="flex mt-4">
      <div
        class=" rounded mr-auto ml-4 my-2 bg-blue-800 text-white font-sans font-semibold py-2 px-4 cursor-pointer"
        @click="$store.dispatch('url/updateSection', 'student-import')"
      >
        Thêm sinh viên
      </div>
      <UploadButtonVue @uploadFileExcel="upload" />
      <div class="flex items-center justify-center mr-4">
        <a
          class=" rounded ml-auto mr-4 my-2 bg-gray-800 text-white font-sans font-semibold py-2 px-4 cursor-pointer"
          href="http://localhost:8001/api/v2/template?type=User"
        >Tải mẫu tệp excel</a>
      </div>
    </div>
    <div class="shadow-md sm:rounded-lg m-4">
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
        @click-row="showRow"
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
            <img
              src="https://cdn-icons-png.flaticon.com/512/1827/1827951.png"
              class="operation-icon w-6 h-6 mx-2 cursor-pointer"
              @click="editItem(item)"
            >
          </div>
        </template>
      </EasyDataTable>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters, useStore } from 'vuex';
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { useToast } from 'vue-toast-notification';
import UploadButtonVue from './UploadButton.vue';
import UserApi from '../../utils/api/user';

export default {
  name: 'ManageStudentAdmin',
  components: {
    UploadButtonVue,
  },
  setup () {
    const store = useStore();
    const loading = ref(false);
    const itemsSelected = ref([]);
    const serverItemsLength = ref(0);
    const rowItems = [10, 20, 50];
    const users = ref([]);
    const headers = [
      { text: 'Mã sinh viên', value: 'code', sortable: true },
      { text: 'Ten sinh vien', value: 'name', sortable: true },
      { text: 'Email', value: 'email', sortable: true },
      { text: 'Giới tính', value: 'gender', sortable: true },
      { text: 'Trạng thái', value: 'status' },
      { text: 'Hanh dong', value: 'operation' },
    ];
    const items = [];
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: headers[0].value,
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];

    const loadToServer = async (option) => {
      loading.value = true;
      const response = await UserApi.listUser(token, 'STUDENT', option);
      users.value = response.data;
      store.state.student.listStudents = users.value;
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
      store.dispatch('url/updateSection', 'student-view');
      store.dispatch('url/updateId', item._id);
    };

    const editItem = (item) => {
      store.dispatch('url/updateSection', 'student-update');
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
    watch(serverOptions, (value) => { loadToServer(value); }, { deep: true });

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
    };
  },
  data () {
    return {
      searchVal: '',
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('student', [
      'studentId', 'studentEmail', 'student', 'listStudents',
    ]),
  },
  methods: {
    handleUpdateStudent (id) {
      this.$store.dispatch('url/updateSection', 'student-update');
      this.$store.dispatch('url/updateId', id);
    },
    handleShowStudent (id) {
      this.$store.dispatch('url/updateSection', 'student-view');
      this.$store.dispatch('url/updateId', id);
    },
    async upload (files) {
      if (files.length > 0) {
        await this.$store.dispatch('student/importStudent', { token: this.token, xlsx: files[0] })
          .then((data) => {
            if (data.status === 201) {
              this.$toast.success('Đã nhập thành công!');
            }
          });

        this.search();
      } else {
        this.$toast.error('File không tồn tại');
      }
    },
  },
};
</script>
