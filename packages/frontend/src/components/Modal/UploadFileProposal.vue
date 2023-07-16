<template>
  <div class="flex flex-col bg-gray-100 mt-4 w-[860px]">
    <VueFileAgent
      ref="vueFileAgent"
      v-model="fileRecords"
      :theme="'list'"
      :multiple="true"
      :deletable="true"
      :meta="true"
      :accept="'image/*,.zip,.rar,.doc,.docx,.ods,.pdf'"
      :max-size="'30MB'"
      :max-files="14"
      :help-text="'Tải lên tệp tin'"
      :error-text="{
        type: 'Tệp không hợp lệ, chỉ chấp nhận file ảnh, tệp nén, tài liệu và pdf',
        size: 'Tệp không quá 30MB',
      }"
    />
  </div>
</template>
<script>
import { mapState, mapGetters } from 'vuex';
import { saveAs } from 'file-saver';
// import {
//   addTusClient, uploadUrl,
// } from './base';
import DocumentApi from '../../utils/api/document';
import TopicApi from '../../utils/api/topic';

export default {
  components: {
  },
  props: {
    modelValue: [],
  },

  data () {
    return {
      // fileRecords: [],
      uploadUrl: '',
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
    fileRecords: {
      get () {
        return this.modelValue;
      },
      set (value) {
        this.$emit('update:modelValue', value);
      },
    },
  },
  watch: {
    // whenever question changes, this function will run
    fileUpload (newFileUpload) {
      this.$emit('update:modelValue', newFileUpload);
    },
  },
  methods: {
    uploadFiles () {
      // Using the default uploader. You may use another uploader instead.
      // this.$refs.vueFileAgent.upload(this.uploadUrl, this.uploadHeaders, this.fileRecordsForUpload);
      // this.fileRecordsForUpload = [];
    },
    formatSizeUnits (bytes) {
      if (bytes >= 1073741824) { bytes = `${(bytes / 1073741824).toFixed(2)} GB`; } else if (bytes >= 1048576) { bytes = `${(bytes / 1048576).toFixed(2)} MB`; } else if (bytes >= 1024) { bytes = `${(bytes / 1024).toFixed(2)} KB`; } else if (bytes > 1) { bytes += ' bytes'; } else if (bytes == 1) { bytes += ' byte'; } else { bytes = '0 bytes'; }
      return bytes;
    },

    afterUploadFiles () {
      this.fetch();
    },
    errorHandler (e) {
      this.$toast.error('Tải tập tin thất bại');
      console.log(e.file_name);
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
