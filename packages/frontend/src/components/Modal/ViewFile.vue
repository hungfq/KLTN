<template>
  <vue-final-modal
    v-slot="{ close }"
    v-bind="$attrs"
    @before-open="handleShow(topicId)"
  >
    <div class="relative p-4 w-full max-w-4xl mx-auto mt-[5%] ">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow ">
        <!-- Modal header -->
        <div class="flex justify-between items-start p-4 rounded-t border-b">
          <h3 class="text-xl font-semibold text-gray-900 ">
            Thông tin tệp tin
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
        <div class="px-2 h-96 overflow-y-scroll">
          <div class="flex flex-col bg-gray-100 mt-3">
            <div id="app">
              <easy-data-table
                :headers="headers"
                :items="items"
                :loading="loading"
                hide-footer
              >
                <template #item-operation="item">
                  <div class="flex">
                    <div
                      class="tooltip tooltip-bottom pr-3"
                      data-tip="Tải tệp tin"
                    >
                      <font-awesome-icon
                        class="cursor-pointer"
                        icon="fa-solid fa-file-arrow-down"
                        size="2xl"
                        @click="downloadFile(item)"
                      />
                    </div>
                  </div>
                </template>
                <template #empty-message>
                  <div class="text-center text-gray-500">
                    Không có dữ liệu
                  </div>
                </template>
              </easy-data-table>
            </div>
          </div>
        </div>
        <!-- Modal footer -->
        <div class="flex items-center pl-6 p-2 space-x-2 rounded-b border-t border-gray-200 justify-between">
          <button
            type="button"
            class="btn btn-secondary"
            @click="downloadAllFile(close)"
          >
            Tải tất cả các các tệp
          </button>
          <button
            data-modal-toggle="defaultModal"
            type="button"
            class="btn btn-primary"
            @click="close"
          >
            OK
          </button>
        </div>
      </div>
    </div>
  </vue-final-modal>
</template>
<script>
import { mapState, mapGetters } from 'vuex';
import { saveAs } from 'file-saver';
import DocumentApi from '../../utils/api/document';
import TopicApi from '../../utils/api/topic';
import TopicProposalApi from '../../utils/api/topic_proposal';

export default {
  components: {
  },
  props: {
    topicId: null,
    type: {
      type: String,
      default: 'TOPIC',
    },
  },
  data () {
    return {
      fileRecords: [],
      uploadHeaders: {},
      valAccept: 'image/*,video/*,.pdf,.doc,.docx,.ods',
      tabs: [
        {
          label: 'Xem tệp tin',
          value: 0,
        },
      ],
      tab: 0,
      BASE_API_URL: import.meta.env.BASE_API_URL || 'http://localhost:8001',
      topic: null,
      headers: [
        { text: 'Tên tệp', value: 'file_name' },
        { text: 'Kích thước', value: 'sizeFormatted' },
        { text: 'Hoạt động', value: 'operation' },
      ],
      items: [],
      loading: false,
    };
  },
  computed: {
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token', 'userInfo', 'userName',
    ]),
  },
  methods: {
    async handleShow (topicId) {
      this.items = [];
      if (this.topicId) {
        if (this.type === 'TOPIC_PROPOSAL') {
          this.topic = await TopicProposalApi.getTopicProposal(this.token, this.topicId);
        } else {
          this.topic = await TopicApi.getTopic(this.token, this.topicId);
          console.log('🚀 ~ file: ViewFile.vue:143 ~ handleShow ~ this.topic:', this.topic);
        }
      }
      this.fetch();
    },
    formatSizeUnits (bytes) {
      if (bytes >= 1073741824) { bytes = `${(bytes / 1073741824).toFixed(2)} GB`; } else if (bytes >= 1048576) { bytes = `${(bytes / 1048576).toFixed(2)} MB`; } else if (bytes >= 1024) { bytes = `${(bytes / 1024).toFixed(2)} KB`; } else if (bytes > 1) { bytes += ' bytes'; } else if (bytes == 1) { bytes += ' byte'; } else { bytes = '0 bytes'; }
      return bytes;
    },
    async fetch () {
      this.loading = true;
      try {
        if (this.topicId) {
          const files = await DocumentApi.listAllDocsByOwner(this.token, this.topic.code);
          this.items = files.map((f) => ({ ...f, sizeFormatted: this.formatSizeUnits(f.size) }));
        }
      } catch (e) {
        console.log('🚀 ~ file: UploadFile.vue:205 ~ fetch ~ e:', e);
      }
      this.loading = false;
    },
    async downloadFile (item) {
      const response = await DocumentApi.getFile(this.token, item.id);
      saveAs(response.data, item.file_name);
    },

    async downloadAllFile (close) {
      this.loading = true;
      const response = await DocumentApi.getAllFile(this.token, this.topic.code);
      saveAs(response.data, `${this.topic.code}.zip` || 'Unknown.zip');
      close();
      this.loading = false;
    },
  },
};
</script>
<style>
.file-name {
  display: flex;
  align-items: center;
}
</style>
