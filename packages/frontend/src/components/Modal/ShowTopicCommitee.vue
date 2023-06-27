<template>
  <vue-final-modal
    v-slot="{ close }"
    v-bind="$attrs"
    @before-open="handleShow(topics)"
  >
    <div class="relative p-4 w-full max-w-4xl mx-auto mt-[5%] ">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow ">
        <!-- Modal header -->
        <div class="flex justify-between items-start p-4 rounded-t border-b">
          <h3 class="text-xl font-semibold text-gray-900 ">
            Thông tin sinh viên
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
        <div class="px-6 py-2">
          <SearchInput
            v-model="searchVal"
            :search-icon="true"
            @keydown.space.enter="search"
          />
        </div>
        <!-- Modal body -->
        <div class="flex flex-col">
          <EasyDataTable
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
          </EasyDataTable>
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
import SearchInput from 'vue-search-input';

export default {
  name: 'InfoModal',
  components: {
    SearchInput,
  },
  inheritAttrs: false,
  props: {
    topics: {
      type: String,
      default: '',
    },
  },
  data () {
    return {
      searchVal: '',
      listTopics: [],
      headers: [
        { text: 'Mã số', value: 'code', sortable: true },
        { text: 'Tên đề tài ', value: 'title', sortable: true },
        { text: 'Giảng viên hướng dẫn', value: 'lecturer' },
        // { text: 'Giảng viên phản biện', value: 'critical' },
        // { text: 'Đợt đăng ký', value: 'schedule' },
      ],
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
    async handleShow (topics) {
      this.listTopics = topics;
    },

    search () {
      if (this.searchVal !== '') {
        const listTopicsFilter = this.topics.filter((st) => {
          const re = new RegExp(`\\b${this.searchVal}`, 'gi');
          if (st.title.match(re)) return true;
          if (st.code.match(re)) return true;
          return false;
        });
        this.listTopics = listTopicsFilter;
      } else this.listTopics = this.topics;
    },
  },
};
</script>
