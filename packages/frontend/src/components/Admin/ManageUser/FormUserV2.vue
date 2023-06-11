<!-- eslint-disable max-len -->
<template>
  <div>
    <div class="p-2 w-full h-full overflow-hidden">
      <!-- Modal content -->
      <div class="bg-white rounded-lg shadow">
        <!-- Modal header -->
        <div class="flex justify-between items-start p-4 rounded-t border-b">
          <h3 class="text-xl font-semibold text-gray-900">
            Thông tin người dùng
          </h3>
          <div
            class="mx-2 my-2 px-2 py-2 bg-slate-500 w-fit text-white font-semibold cursor-pointer"
            @click="rollBack"
          >
            Quay về
          </div>
        </div>
        <!-- Modal body -->
        <custom-form
          v-if="!loading"
          :title="formTitle"
          :form-fields="formFields"
          :on-submit="submitForm"
          :is-view="isView"
          :is-update="isUpdate"
          :is-save="isSave"
          :object="user"
        />
        <Loading v-else />
      </div>
    </div>
  </div>
</template>

<script>

import { mapGetters } from 'vuex';
import CustomForm from '../../common/CustomForm.vue';
import UserApi from '../../../utils/api/user';
import Loading from '../../common/Loading.vue';

export default {
  name: 'FormUser',
  components: {
    CustomForm,
    Loading,
  },
  data () {
    return {
      loading: false,
      formTitle: 'Registration Form',
      formFields: [
        {
          name: 'name',
          label: 'Tên:',
          type: 'text',
          rules: 'required',
        },
        {
          name: 'code',
          label: 'Mã',
          type: 'text',
          rules: 'required|equalLength:8',
        },
        {
          name: 'email',
          label: 'Email:',
          type: 'email',
          rules: 'required',
          isDisabled: true,
        },
        {
          name: 'gender',
          label: 'Giới tính:',
          type: 'select',
          options: [
            {
              label: 'Nam',
              value: 'male',
            },
            {
              label: 'Nữ',
              value: 'female',
            },
          ],
          rules: 'required',
        },
      ],
      user: {},
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
      return this.section === 'student-import' || this.section === 'lecturer-import' || this.section === 'admin-import';
    },
    isUpdate () {
      return this.section === 'student-update' || this.section === 'lecturer-update' || this.section === 'admin-update';
    },
    isView () {
      return this.section === 'student-view' || this.section === 'lecturer-view' || this.section === 'admin-view';
    },
  },
  mounted () {
    this.loading = true;
    const { section } = this.$store.state.url;
    if (section === 'student-update' || section === 'student-view') {
      const { id } = this.$store.state.url;
      const { listStudents } = this.$store.state.student;
      const student = listStudents.find((s) => s._id.toString() === id.toString());
      if (student) {
        this.user = student;
      }
    } else if (section === 'lecturer-update' || section === 'lecturer-view') {
      const { id } = this.$store.state.url;
      const { listLecturer } = this.$store.state.lecturer;
      const lecturer = listLecturer.find((s) => s._id.toString() === id.toString());
      if (lecturer) {
        this.user = lecturer;
      }
    } else if (section === 'admin-update' || section === 'admin-view') {
      const { id } = this.$store.state.url;
      const { listAdmins } = this.$store.state.admin;
      const admin = listAdmins.find((s) => s._id.toString() === id.toString());
      if (admin) {
        this.user = admin;
      }
    }
    this.loading = false;
  },
  methods: {
    rollBack () {
      this.$store.dispatch('url/updateSection', `${this.module}-list`);
    },
    async submitForm (value) {
      this.loading = true;
      try {
        if (this.isUpdate) {
          await UserApi.updateUser(this.token, { ...this.user, ...value });
          this.$toast.success('Cập nhật thành công!');
        } else {
          await UserApi.addUser(this.token, { ...this.user, ...value }, this.module.toUpperCase());
          this.$toast.success('Thêm thành công!');
        }
        this.loading = false;
        this.rollBack();
      } catch (e) {
        this.loading = false;
        if (e.response.data.error.code === 400) this.$toast.error(e.response.data.error.message);
        else { this.$toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
      }
    },
  },
};
</script>

<!-- <style lang="scss" scoped> -->

<style src="@vueform/multiselect/themes/default.css">

</style>
