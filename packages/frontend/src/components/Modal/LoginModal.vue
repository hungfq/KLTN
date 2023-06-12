<template>
  <vue-final-modal
    v-slot="{ params, close }"
    v-bind="$attrs"
  >
    <div class="relative p-4 w-full max-w-2xl h-full md:h-auto mx-auto mt-[10%]">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow ">
        <!-- Modal header -->
        <div class="flex justify-between items-start p-4 rounded-t border-b ">
          <h3 class="text-xl font-semibold text-gray-900 ">
            Đăng nhập tài khoản
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
        <!-- Modal body -->
        <div class="p-6 space-y-6">
          <div class="font-medium">
            Đăng nhập với vai trò là:
          </div>
          <Multiselect
            v-model="role"
            :options="roles"
            :can-deselect="false"
            :searchable="true"
            no-results-text="Không có kết quả"
            no-options-text="Không có lựa lựa chọn"
            :can-clear="false"
            @change="onChangeType"
          />
        </div>
        <!-- Modal footer -->
        <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200  ">
          <button
            data-modal-toggle="defaultModal"
            type="button"
            class="btn btn-primary"
            @click="$emit('login',close,role)"
          >
            Đăng nhập
          </button>
        </div>
      </div>
    </div>
  </vue-final-modal>
</template>
<script>
import Multiselect from '@vueform/multiselect';

export default {
  name: 'LoginModal',
  components: {
    Multiselect,
  },
  inheritAttrs: false,
  data () {
    return {
      roles: [
        {
          label: 'Sinh viên',
          value: 'STUDENT',
        },
        {
          label: 'Giảng viên',
          value: 'LECTURER',
        },
        {
          label: 'Người quản trị',
          value: 'ADMIN',
        },
      ],
      role: 'STUDENT',
    };
  },
  method: {
    onChangeType (value) {
      console.log('🚀 ~ file: LoginModal.vue:103 ~ onChangeType ~ value:', value);
      this.role = value;
    },
  },
};
</script>
