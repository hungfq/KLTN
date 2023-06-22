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
            Xuất dữ liệu điểm
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
        <div
          v-if="!loading"
          class="p-6 space-y-6"
        >
          <div class="font-medium">
            Vui lòng chọn đợt để xuất điểm:
          </div>
          <Multiselect
            v-model="scheduleId"
            :options="listScheduleSelect"
            :searchable="true"
            :can-deselect="false"
            no-results-text="Không có kết quả"
            no-options-text="Không có lựa lựa chọn"
            :can-clear="false"
            :placeholder="'Đợt đăng ký'"
          />
        </div>
        <div
          v-else
          class="my-2"
        >
          <LoadingProcess />
        </div>
        <!-- Modal footer -->
        <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200  ">
          <button
            class="btn btn-primary"
            @click="$emit('export', scheduleId)"
          >
            <font-awesome-icon :icon="['fas', 'paper-plane']" />
            <span class="ml-1">Xuất điểm</span>
          </button>
        </div>
      </div>
    </div>
  </vue-final-modal>
</template>
<script>
import { mapState, mapGetters } from 'vuex';
import Multiselect from '@vueform/multiselect';
import LoadingProcess from '../common/Loading.vue';

export default {
  name: 'SendInviteModal',
  components: {
    Multiselect,
    LoadingProcess,
  },
  emits: ['export'],
  data () {
    return {
      scheduleId: null,
      loading: false,
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),

    ...mapGetters('schedule', [
      'listSchedules',
    ]),
    listScheduleSelect () {
      const arr = [];
      this.listSchedules.forEach((schedule) => {
        arr.push({ value: schedule._id, label: schedule.code });
      });
      return arr;
    },
  },
  async mounted () {
    // console.log('hiihh');
    this.loading = true;
    try {
      await this.$store.dispatch('schedule/fetchListSchedules', this.token);
      if (this.listSchedules.length > 0) {
        this.scheduleId = this.listSchedules[0]._id;
        // console.log('🚀 ~ file: SendInvite.vue:119 ~ mounted ~ this.scheduleId:', this.scheduleId);
      }
    } catch (e) {
      console.log(e);
    }
    this.loading = false;
  },

  method: {
    // onChangeSchedule (value) {
    //   console.log('hiihh');
    //   console.log('🚀 ~ file: SendInvite.vue:109 ~ onChange ~ value:', value);
    //   this.scheduleId = value;
    // },
    sendInvite () {
      // console.log('🚀 ~ file: SendInvite.vue:118 ~ sendInvite ~ this.scheduleId:', this.scheduleId);
      // console.log('🚀 ~ file: SendInvite.vue:133 ~ sendInvite ~ this.scheduleId:', this.scheduleId);
      // this.$emit('sendInvite', this.scheduleId);
    },
  },
};
</script>
