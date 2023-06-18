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
      <div class="col-md-5 col-sm-6 mb-3">
        <div
          class="mx-auto"
          :style="compact ? { width: '200px' } : {}"
        >
          <VueFileAgent
            ref="vueFileAgent"
            v-model="fileRecords"
            :auto="auto"
            :average-color="averageColor"
            :upload-url="uploadUrl"
            :upload-headers="uploadHeaders"
            :multiple="multiple"
            :meta="meta"
            :deletable="deletable"
            :editable="editable"
            :linkable="linkable"
            :readonly="readonly"
            :resumable="resumable"
            :disabled="disabled"
            :compact="compact"
            :accept="valAccept"
            :capture="valCapture"
            :max-size="valMaxSize"
            :max-files="valMaxFiles"
            :theme="theme"
            @select="filesSelected($event)"
            @delete="fileDeleted($event)"
            @beforedelete="onBeforeDelete($event)"
            @sort="onSort($event)"
            @upload="uploadEvent('upload', $event)"
            @upload:error="uploadEvent('upload:error', $event)"
            @upload:delete="uploadEvent('upload:delete', $event)"
            @upload:delete:error="uploadEvent('upload:delete:error', $event)"
            @upload:update="uploadEvent('upload:update', $event)"
            @upload:update:error="uploadEvent('upload:update:error', $event)"
          />
        </div>
      </div>
    </template>
  </div>
</template>
<script>
import {
  addTusClient, getFileRecordsInitial, uploadUrl,
} from './base';

export default {
  components: {
  },

  data () {
    return {
      rawFileRecords: getFileRecordsInitial(),
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
    };
  },
  computed: {
    fileRecordsInvalid () {
      const fileRecordsInvalid = [];
      for (let i = 0; i < this.fileRecords.length; i++) {
        if (this.fileRecords[i].error) {
          fileRecordsInvalid.push(this.rawFileRecords[i]);
        }
      }
      return fileRecordsInvalid;
    },
    uploadEndpoint () {
      if (this.resumable && this.uploadUrl.indexOf('mocky.io') !== -1) {
        return 'https://master.tus.io/files/';
      }
      return this.uploadUrl;
    },
  },
  watch: {
    fileRecords () {
      console.log('fileRecords changed');
    },
  },
  mounted () {
    const plugins = {
      tus: null,
    };
    addTusClient(plugins);
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
    setProgress () {
      const fileRecord = this.getSelectedFileRecord();
      if (!fileRecord) {
        return;
      }
      const prg = (this.$refs.prgInput).value;
      fileRecord.progress(parseInt(prg, 10));
    },
    removeInvalid () {
      let fileRecordsNew = this.rawFileRecords.concat([]);
      for (let i = 0; i < this.fileRecordsInvalid.length; i++) {
        const idx = fileRecordsNew.indexOf(this.fileRecordsInvalid[i]);
        if (idx !== -1) {
          fileRecordsNew.splice(idx, 1);
        }
      }
      fileRecordsNew = [];
      for (let i = 0; i < this.fileRecords.length; i++) {
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
    moveIndex (dir) {
      console.log('moveIndex', dir);
      const from = this.selectedIdx - 1;
      let to = from + dir;
      if (to < 0) {
        to = this.rawFileRecords.length - 1;
      }
      if (to >= this.rawFileRecords.length) {
        to = 0;
      }
      if (!this.rawFileRecords[from]) {
        return;
      }
      (this.$refs.vueFileAgent).sort(from, to);
    },
    sortBy (prop) {
      // var asc = this['_is_sorted_desc_' + prop] = !this['_is_sorted_desc_' + prop];
      const direction = this.sortDirection[prop];
      this.sortDirection[prop] = direction === 'DESC' ? 'ASC' : 'DESC';
      // console.log('sortBy', prop, this.fileRecords);
      const ret = direction === 'DESC' ? -1 : 1;
      this.fileRecords = this.fileRecords.sort((fd1, fd2) => {
        const f1 = fd1.file || fd1;
        const f2 = fd2.file || fd2;
        return f1[prop] > f2[prop] ? 1 * ret : -1 * ret;
      });
      // console.log('sortBy after', prop, this.fileRecords);
    },

    uploadFiles () {
      // Using the default uploader. You may use another uploader instead.
      (this.$refs.vueFileAgent).upload(
        this.uploadEndpoint,
        this.uploadHeaders,
        this.fileRecordsForUpload,
      );
      this.fileRecordsForUpload = [];
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
      for (let i = 0; i < fileRecords.length; i++) {
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
    onSort (event, sortedData) {
      console.log(
        'sorted',
        event.oldIndex,
        event.newIndex,
        sortedData.map((fd) => fd.name),
      );
    },
  },
};
</script>
