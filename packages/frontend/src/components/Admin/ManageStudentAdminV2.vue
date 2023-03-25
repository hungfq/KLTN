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
        <template #item-status="item">
          <input
            type="checkbox"
            class="toggle toggle-info"
            :checked="item"
          >
        </template>
        <template #item-operation="item">
          <div class="flex">
            <img
              src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRez0gfCWKJG2UnApuVyZXNyRhtB93Feywc0FYHYWD8&s"
              class="operation-icon w-4 h-4 mx-2"
              @click="deleteItem(item)"
            >
            <img
              src="https://cdn-icons-png.flaticon.com/512/1827/1827951.png"
              class="operation-icon w-4 h-4 mx-2"
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
import { ref, watch, onMounted } from 'vue';
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
      { text: 'Trạng thái', value: 'status', sortable: true },
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
    onMounted(async () => {
      await loadToServer(serverOptions.value);
    });
    const showRow = (item) => {
      store.dispatch('url/updateSection', 'student-update');
      store.dispatch('url/updateId', item._id);
    };

    const editItem = (item) => {
      store.dispatch('url/updateSection', 'student-view');
      store.dispatch('url/updateId', item._id);
    };

    const deleteItem = (item) => {
      store.dispatch('url/updateSection', 'student-view');
      store.dispatch('url/updateId', item._id);
    };
    watch(serverOptions, (value) => { loadToServer(value); }, { deep: true });
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
      deleteItem,
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
    usersShow () {
      if (!this.users) return [];
      return this.users.map((user) => ({
        _id: user._id,
        code: user.code,
        name: user.name,
        email: user.email,
        gender: user.gender === 'male' ? 'Nam' : 'Nu',
        status: user.status === 'AC',
      }));
    },
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
