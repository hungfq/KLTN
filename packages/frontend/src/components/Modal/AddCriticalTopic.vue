<template>
  <vue-final-modal
    v-slot="{ close }"
    v-bind="$attrs"
    @before-open="handleShow()"
  >
    <div class="relative p-4 w-full max-w-4xl mx-auto mt-[5%] ">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow ">
        <!-- Modal header -->
        <div class="flex justify-between items-start p-4 rounded-t border-b">
          <h3 class="text-xl font-semibold text-gray-900 ">
            Chỉnh sửa giáo viên phản biện cho đề tài
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
        <div class="flex flex-col">
          <div class="px-6 py-2 h-96 flex flex-col justify-center items-center">
            <div class="w-full m-2">
              <div class="my-2 font-semibold">
                Chọn đợt đăng ký
              </div>
              <Multiselect
                v-model="selectSchedule"
                :options="listScheduleSelect"
                :searchable="true"
                :can-clear="false"
                :can-deselect="false"
                no-results-text="Không có kết quả"
                no-options-text="Không có lựa lựa chọn"
                :placeholder="'Đợt đăng ký'"
                @change="selectHandlerSchedule"
              />
            </div>
            <div class="w-full m-2">
              <div class="my-2 font-semibold">
                Chọn giáo viên phản biện cho đề tài
              </div>
              <Multiselect
                v-model="selectLecturer"
                :options="listLecturerSelect"
                :can-deselect="false"
                :searchable="true"
                no-results-text="Không có kết quả"
                no-options-text="Không có lựa lựa chọn"
                :can-clear="false"
                :placeholder="'Giảng viên hướng dẫn'"
                @change="selectHandlerLecturer"
              />
            </div>
          </div>
          <div
            class="my-4 w-[300px] mx-auto mt-2"
          >
            <ul class="steps">
              <li
                v-for="(step, index) in steps"
                :key="index"
                class="step"
                :class="{ 'step-primary': page >= step.page }"
                @click="page=step.page"
              >
                {{ step.label }}
              </li>
            </ul>
          </div>
          <!-- <EasyDataTable
            show-index
            :headers="headers"
            :items="listTopics"
            body-text-direction="center"
            header-text-direction="center"
            buttons-pagination="false"
            hide-footer
          >
            <template #empty-message>
              <div class="text-center text-gray-500">
                Không có dữ liệu
              </div>
            </template>
          </EasyDataTable> -->
          <div class="flex justify-end">
            <button
              class="btn btn-primary m-2"
              @click="close"
            >
              Đóng
            </button>
          </div>
        </div>
      </div>
    </div>
  </vue-final-modal>
</template>
<script>
import { mapGetters } from 'vuex';
import Multiselect from '@vueform/multiselect';

export default {
  name: 'InfoModal',
  components: {
    Multiselect,
  },
  inheritAttrs: false,
  props: {
    listScheduleSelectRaw: [],
    listLecturerSelectRaw: [],
    selectedSchedule: null,
  },
  data () {
    return {
      selectLecturer: null,
      selectSchedule: null,
      listScheduleSelect: [],
      listLecturerSelect: [],
      listTopics: [],
      headers: [
        { text: 'Mã số', value: 'code', sortable: true },
        { text: 'Tên đề tài ', value: 'title', sortable: true },
        { text: 'Giảng viên hướng dẫn', value: 'lecturer' },
      ],
      steps: [
        { label: 'Chọn giảng viên và và đợt', page: 1 },
        { label: 'Chọn đề tài', page: 2 },
      ],
      page: 1,
    };
  },
  computed: {
    ...mapGetters('auth', [
      'token',
    ]),
    ...mapGetters('schedule', [
      'listStudentsOfSchedule',
    ]),
  },
  methods: {
    async handleShow () {
      this.listLecturerSelect = [...this.listLecturerSelectRaw];
      this.listScheduleSelect = [...this.listScheduleSelectRaw];
      this.listScheduleSelect.shift();
      this.listLecturerSelect.shift();
      // default value
      this.selectSchedule = this.selectedSchedule;
      if (!this.selectSchedule && this.listLecturerSelect.length > 0) {
        this.selectSchedule = this.listLecturerSelect[0].value;
      }
      if (this.listLecturerSelect.length > 0) {
        this.selectLecturer = this.listLecturerSelect[0].value;
      }
    },

    selectHandlerLecturer (value) {
      this.selectLecturer = value;
    },

    selectHandlerSchedule (value) {
      this.selectSchedule = value;
    },
  },
};
</script>
