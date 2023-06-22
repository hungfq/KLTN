<template>
  <div class="flex flex-col bg-gray-100 mt-3">
    <div class="tabs">
      <a
        v-for="t in tabs"
        :key="t.value"
        :class="{ 'tab-active': tab === t.value }"
        class="tab tab-lifted"
        @click="tab = t.value"
      >{{ t.label }}</a>
    </div>
    <template v-if="tab ===0">
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
    </template>
    <template v-if="tab===1">
      <VueFileAgent
        ref="vueFileAgent"
        v-model="fileRecords"
        :theme="'list'"
        :multiple="true"
        :deletable="true"
        :upload-url="uploadUrl"
        :upload-headers="uploadHeaders"
        :meta="true"
        :accept="'image/*,.zip,.rar,.doc,.docx,.ods,.pdf'"
        :max-size="'30MB'"
        :max-files="14"
        :help-text="'Tải lên tệp tin'"
        :error-text="{
          type: 'Tệp không hợp lệ, chỉ chấp nhận file ảnh, tệp nén, tài liệu và pdf',
          size: 'Tệp không quá 30MB',
        }"
        @upload="afterUploadFiles"
        @upload:error="errorHandler"
      />
    </template>
  </div>
</template>
<script>
import { mapState, mapGetters } from 'vuex';
import { saveAs } from 'file-saver';
import {
  addTusClient, uploadUrl,
} from './base';
import DocumentApi from '../../utils/api/document';
import TopicApi from '../../utils/api/topic';

export default {
  components: {
  },

  data () {
    return {
      fileRecords: [],
      uploadUrl,
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
    ...mapGetters('task', [
      'listTask', 'topicId', 'listStudents',
    ]),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token', 'userInfo', 'userName',
    ]),
    fileRecordsInvalid () {
      const fileRecordsInvalid = [];
      for (let i = 0; i < this.fileRecords.length; i += 1) {
        if (this.fileRecords[i].error) {
          fileRecordsInvalid.push(this.rawFileRecords[i]);
        }
      }
      return fileRecordsInvalid;
    },
  },
  async mounted () {
    const plugins = {
      tus: null,
    };
    addTusClient(plugins);
    this.uploadHeaders = {
      authorization: `bearer ${this.token}`,
      'Content-Type': 'multipart/form-data',
    };
    if (this.topicId) {
      this.topic = await TopicApi.getTopic(this.token, this.topicId);
    }
    this.uploadUrl = `${this.BASE_API_URL}/api/v2/documents?owner=${this.topic.code}`;
    this.uploadHeaders = {
      authorization: `bearer ${this.token}`,
    };
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
    afterUploadFiles () {
      this.fetch();
    },
    errorHandler (e) {
      this.$toast.error('Tải tập tin thất bại');
      console.log(e.file_name);
    },
    onBeforeDelete (fileRecord) {
      const i = this.fileRecordsForUpload.indexOf(fileRecord);
      if (i !== -1) {
        // queued file, not yet uploaded. Just remove from the arrays
        this.fileRecordsForUpload.splice(i, 1);
      } else if (confirm('Bạn có muốn xóa tệp này không')) {
        (this.$refs.vueFileAgent).deleteFileRecord(fileRecord); // will trigger 'delete' event
      }
    },
    fileDeleted (fileRecord) {
      const i = this.fileRecordsForUpload.indexOf(fileRecord);
      if (i !== -1) {
        this.fileRecordsForUpload.splice(i, 1);
      } else {
        this.deleteUploadedFile(fileRecord);
      }
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
<style>
.file-name {
  display: flex;
  align-items: center;
}
</style>
