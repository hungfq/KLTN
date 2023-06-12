<!-- eslint-disable max-len -->
<template>
  <div
    class="mx-4 my-4 rounded px-2 py-2 bg-slate-500 w-fit text-white font-semibold cursor-pointer"
    @click="rollBack"
  >
    Quay về
  </div>
  <div class="p-4 w-full h-full md:h-auto mx-auto mt-[10px]">
    <!-- Modal content -->
    <div class="bg-white rounded-lg shadow">
      <!-- Modal header -->
      <div class="flex justify-between items-start p-4 rounded-t border-b">
        <h3 class="text-xl font-semibold text-gray-900">
          Thông tin tiêu chí
        </h3>
      </div>
      <div
        v-if="!loading"
        class="ml-5 grid grid-cols-2"
      >
        <FormKit
          v-model="title"
          type="text"
          name="title"
          label="Tên tiêu chí"
          help="Không quá 50 từ. Ví dụ: Công nghệ mới."
          validation="required"
          :disabled="isView"
        />
        <FormKit
          v-model="description"
          name="description"
          type="textarea"
          label="Mô tả"
          help="Ghi các thông tin chi tiết tại đây"
          validation="required"
          :disabled="isView"
        />
      </div>
      <Loading v-else />
      <!-- Modal footer -->
      <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200">
        <button
          v-if="!isView && !loading"
          type="button"
          class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4  focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
          @click="handleAddCriteriaAdmin"
        >
          {{ isSave ? 'Lưu' : 'Cập nhật' }}
        </button>
      </div>
    </div>
  </div>
  <InfoStudentVue
    v-model="showInfo"
    :schedule-id="id"
  />
</template>

<script>
import { mapGetters } from 'vuex';
import CriteriaApi from '../../../utils/api/criteria';
import Loading from '../../common/Loading.vue';

export default {
  name: 'FormCriteria',
  components: {
    Loading,
  },
  data () {
    return {
      showInfo: false,
      title: '',
      description: '',
      loading: false,
    };
  },
  computed: {
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    ...mapGetters('auth', [
      'token',
    ]),
    isSave () {
      return this.section === `${this.module}-import`;
    },
    isUpdate () {
      return this.section === `${this.module}-update`;
    },
    isView () {
      return this.section === `${this.module}-view`;
    },
  },
  async mounted () {
    this.loading = true;
    if (this.isUpdate || this.isView) {
      const { id } = this.$store.state.url;
      try {
        const criterion = await CriteriaApi.fetchOne(this.token, id);
        if (criterion) {
          this.title = criterion.title;
          this.description = criterion.description;
        }
      } catch (e) {
        this.errorHandler(e);
        // this.$toast.error('Đã có lỗi xảy ra, vui lòng liên hệ quản trị viên!');
        this.rollBack();
      }
    }
    this.loading = false;
  },
  methods: {
    errorHandler (e) {
      if (e.response.data.error.code === 400) this.$toast.error(e.response.data.error.message);
      else { this.$toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    },
    rollBack () {
      this.$store.dispatch('url/updateSection', `${this.module}-list`);
    },
    async handleAddCriteriaAdmin () {
      try {
        const value = {
          id: this.id,
          title: this.title,
          description: this.description,
        };
        if (this.isSave) {
          await CriteriaApi.create(this.token, value);
          this.$toast.success('Đã thêm thành công!');
          this.rollBack();
        } else if (this.isUpdate) {
          await CriteriaApi.update(this.token, value);
          this.$toast.success('Đã cập nhật thành công!');
          this.rollBack();
        }
      } catch (e) {
        // this.$toast.error('Đã có lỗi xảy ra, vui lòng kiểm tra lại dữ liệu!');
        this.errorHandler(e);
      }
    },
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
