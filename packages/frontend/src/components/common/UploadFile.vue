<template>
  <div class="flex flex-col">
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
      Hello world
    </template>
    <template v-if="tab===1">
      <VueFileAgent
        ref="vueFileAgent"
        v-model="fileRecords"
        :theme="'list'"
        :multiple="true"
        :deletable="true"
        :meta="true"
        :accept="'image/*,.zip'"
        :max-size="'10MB'"
        :max-files="14"
        :help-text="'Choose images or zip files'"
        :error-text="{
          type: 'Invalid file type. Only images or zip Allowed',
          size: 'Files should not exceed 10MB in size',
        }"
        @select="filesSelected($event)"
        @delete="fileDeleted($event)"
        @beforedelete="onBeforeDelete($event)"
      />
      <hr>
      <div class="my-2 space-x-2">
        <button
          class="btn btn-outline-secondary mb-2"
          :disabled="!fileRecordsForUpload.length"
          @click="uploadFiles()"
        >
          Upload Queue ({{ fileRecordsForUpload.length }})
        </button>

        <button
          class="btn btn-danger mb-2"
          :disabled="!fileRecordsInvalid.length"
          @click="removeInvalid()"
        >
          Remove Invalid ({{ fileRecordsInvalid.length }})
        </button>

        <button
          type="button"
          class="btn btn-outline-danger mb-2"
          :disabled="!rawFileRecords.length"
          @click="removeAll()"
        >
          Remove All ({{ rawFileRecords.length }})
        </button>
      </div>
    </template>
  </div>
</template>
<script>
import { mapState, mapGetters } from 'vuex';
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
      rawFileRecords: [],
      fileRecords: [],
      fileRecordsForUpload: [],
      auto: false,
      averageColor: true,
      uploadUrl,
      uploadHeaders: {},
      meta: true,
      multiple: true,
      deletable: true,
      editable: true,
      linkable: true,
      sortable: false,
      readonly: false,
      resumable: false,
      disabled: false,
      compact: false,
      theme: 'list',
      sortDirection: {
        lastModified: 'ASC',
        name: 'ASC',
      },
      selectedIdx: 1,
      valAccept: 'image/*,video/*,.pdf,.doc,.docx,.ods',
      valCapture: undefined,
      valMaxSize: '10MB',
      valMaxFiles: 14,
      tabs: [
        {
          label: 'Xem tệp tin',
          value: 0,
        },
        {
          label: 'Thay đổi tệp tin',
          value: 1,
        },
      ],
      tab: 0,
      BASE_API_URL: import.meta.env.BASE_API_URL || 'http://localhost:8001',
      topic: null,

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
    uploadEndpoint () {
      if (this.resumable && this.uploadUrl.indexOf('mocky.io') !== -1) {
        // return 'https://master.tus.io/files/';
        return `${this.BASE_API_URL}/api/v2/documents`;
      }
      return `${this.BASE_API_URL}/api/v2/documents`;
    },
  },
  watch: {
    fileRecords () {
      console.log('fileRecords changed');
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
    this.uploadUrl = `${this.BASE_API_URL}/api/v2/documents`;
    if (this.topicId) {
      this.topic = await TopicApi.getTopic(this.token, this.topicId);
    }
  },
  methods: {
    uploadEvent (eventName, data) {
      console.log('UPLOAD EVENT ', eventName, data);
    },
    getSelectedFileRecord () {
      let i = this.selectedIdx;
      i -= 1;
      if (!this.fileRecords[i]) {
        return;
      }
      return this.fileRecords[i];
    },
    removeAll () {
      console.log(this.rawFileRecords);
      this.rawFileRecords = [];
      this.fileRecordsForUpload = [];
    },
    setProgress (prg) {
      const fileRecord = this.getSelectedFileRecord();
      if (!fileRecord) {
        return;
      }
      // const prg = (this.$refs.prgInput).value;
      fileRecord.progress(parseInt(prg, 10));
    },
    removeInvalid () {
      let fileRecordsNew = this.rawFileRecords.concat([]);
      for (let i = 0; i < this.fileRecordsInvalid.length; i += 1) {
        const idx = fileRecordsNew.indexOf(this.fileRecordsInvalid[i]);
        if (idx !== -1) {
          fileRecordsNew.splice(idx, 1);
        }
      }
      fileRecordsNew = [];
      for (let i = 0; i < this.fileRecords.length; i += 1) {
        if (!this.fileRecords[i].error) {
          fileRecordsNew.push(this.rawFileRecords[i]);
        }
      }
      this.rawFileRecords = fileRecordsNew; // mutate at once, do not splice each
    },
    remove () {
      console.log('removing...');

      let i = this.selectedIdx;
      i -= 1;
      if (!this.fileRecords[i]) {
        return;
      }

      (this.$refs.vueFileAgent).removeFileRecord(this.fileRecords[i]);
    },
    update () {
      const fileRecord = this.getSelectedFileRecord();
      if (!fileRecord) {
        return;
      }
      if (!fileRecord.file) {
        alert('This is not a user selected file');
        return;
      }
      (this.$refs.vueFileAgent).updateUpload(
        this.uploadUrl,
        this.uploadHeaders,
        fileRecord,
      );
    },
    upload () {
      console.log('let au debug');
      const fileRecord = this.getSelectedFileRecord();
      if (!fileRecord) {
        return;
      }
      if (!fileRecord.file) {
        alert('This is not a user selected file');
        return;
      }
      const i = this.fileRecordsForUpload.indexOf(fileRecord);
      if (i !== -1) {
        this.fileRecordsForUpload.splice(i, 1);
      }

      (this.$refs.vueFileAgent)
        .upload(this.uploadEndpoint, this.uploadHeaders, [fileRecord])
        .then((result) => {
          console.log('uploaded: ', result);
          console.log('after upload: ', fileRecord);
          console.log('after upload all: ', this.fileRecords);
        });
    },
    async uploadFiles () {
      for (let i = 0; i < this.fileRecordsForUpload.length; i += 1) {
        const index = this.fileRecords.indexOf(this.fileRecordsForUpload[i]);
        this.selectedIdx = index;
        await DocumentApi.uploadDocuments(this.token, this.topic.code, this.fileRecordsForUpload[i].file, this.fileRecordsForUpload[i].customName || this.fileRecordsForUpload[i].file.name);
        this.setProgress();
      }
      this.fileRecordsForUpload = [];
      // uploadHeaders;
      // console.log('🚀 ~ file: UploadFile.vue:279 ~ uploadFiles ~ this.uploadHeaders:', this.uploadHeaders);
      // // this.fileRecordsForUpload = [];
      // console.log('🚀 ~ file: UploadFile.vue:280 ~ uploadFiles ~ this.fileRecordsForUpload:', this.fileRecordsForUpload);
    },
    deleteUploadedFile (fileRecord) {
      // Using the default uploader. You may use another uploader instead.
      (this.$refs.vueFileAgent).deleteUpload(
        this.uploadEndpoint,
        this.uploadHeaders,
        fileRecord,
      );
    },
    filesSelected (fileRecords) {
      console.log('filesSelected', fileRecords);
      const validFileRecords = [];
      for (let i = 0; i < fileRecords.length; i += 1) {
        if (!fileRecords[i].error) {
          validFileRecords.push(fileRecords[i]);
        }
      }
      console.log('filesSelected', fileRecords, validFileRecords);
      this.fileRecordsForUpload = this.fileRecordsForUpload.concat(validFileRecords);
    },
    onBeforeDelete (fileRecord) {
      const i = this.fileRecordsForUpload.indexOf(fileRecord);
      if (i !== -1) {
        // queued file, not yet uploaded. Just remove from the arrays
        this.fileRecordsForUpload.splice(i, 1);
      } else if (confirm('Are you sure you want to delete?')) {
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
  },
};
</script>
<style>
.file-name {
  display: flex;
  align-items: center;
}
</style>
