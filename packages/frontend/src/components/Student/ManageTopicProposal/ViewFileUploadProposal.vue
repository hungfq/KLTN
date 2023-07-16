<template>
  <div class="flex flex-col bg-gray-100 mt-3">
    <div id="app">
      <easy-data-table
        :headers="headers"
        :items="items"
        :loading="loading"
        hide-footer
      >
        <template #empty-message>
          <div class="text-center text-gray-500">
            Không có dữ liệu
          </div>
        </template>
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
            <div
              v-if="!isView"
              class="tooltip tooltip-bottom pr-3"
              data-tip="Xóa tệp tin"
            >
              <font-awesome-icon
                class="cursor-pointer"
                icon="fa-solid fa-trash-can"
                size="2xl"
                @click="deleteFile(item)"
              />
            </div>
          </div>
        </template>
      </easy-data-table>
    </div>
  </div>
</template>
<script>
import { mapState, mapGetters } from 'vuex';
import { saveAs } from 'file-saver';
import DocumentApi from '../../../utils/api/document';
import TopicProposalApi from '../../../utils/api/topic_proposal';

export default {
  components: {
  },
  props: {
    topicId: {
      type: String,
      default: '',
    },
    isView: {
      type: Boolean,
      default: false,
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
        {
          label: 'Tải tệp tin',
          value: 1,
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
  async mounted () {
    if (this.topicId) {
      this.topic = await TopicProposalApi.getTopicProposal(this.token, this.topicId);
    }
    this.fetch();
  },
  methods: {
    formatSizeUnits (bytes) {
      if (bytes >= 1073741824) { bytes = `${(bytes / 1073741824).toFixed(2)} GB`; } else if (bytes >= 1048576) { bytes = `${(bytes / 1048576).toFixed(2)} MB`; } else if (bytes >= 1024) { bytes = `${(bytes / 1024).toFixed(2)} KB`; } else if (bytes > 1) { bytes += ' bytes'; } else if (bytes == 1) { bytes += ' byte'; } else { bytes = '0 bytes'; }
      return bytes;
    },
    async fetch () {
      this.loading = true;
      try {
        const files = await DocumentApi.listAllDocsByOwner(this.token, this.topic.code);
        this.items = files.map((f) => ({ ...f, sizeFormatted: this.formatSizeUnits(f.size) }));
      } catch (e) {
        console.log('🚀 ~ file: UploadFile.vue:205 ~ fetch ~ e:', e);
      }
      this.loading = false;
    },
    errorHandler (e) {
      this.$toast.error('Tải tập tin thất bại');
      console.log(e.file_name);
    },

    async downloadFile (item) {
      const response = await DocumentApi.getFile(this.token, item.id);
      saveAs(response.data, item.file_name);
    },
    async deleteFile (item) {
      this.loading = true;
      try {
        await DocumentApi.deleteDocument(this.token, item.id);
        this.$toast.success(`Xóa tệp tin ${item.file_name} thành công`);
        this.fetch();
      } catch (e) {
        this.$toast.error(`Xóa tệp tin ${item.file_name} thất bại`);
      }
      this.loading = false;
    },
  },
};
</script>
<style scoped>
.file-name {
  display: flex;
  align-items: center;
}
.vue3-easy-data-table {
  width: 860px !important;
}
</style>
