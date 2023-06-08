<!-- eslint-disable max-len -->
<template>
  <form
    class="flex justify-center"
    @submit.prevent="upload"
  >
    <input
      id="upload123"
      ref="uploadBtn"
      class="hidden"
      type="file"
      accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
      @change="handleNewButtonClick"
    >
    <label
      class=" btn btn-primary"
      for="upload123"
    > <font-awesome-icon
      :icon="['fas', 'file-import']"
      class="mr-2"
    />Tải lên tệp excel</label>
    <button
      ref="submitBtn"
      type="submit"
      class="hidden"
    >
      Tải lên tệp excel
    </button>
  </form>
</template>

<script>
export default {
  name: 'UploadFileExcel',
  methods: {
    upload () {
      const { files } = this.$refs.uploadBtn;
      this.$emit('uploadFileExcel', files);
    },
    handleNewButtonClick () {
      this.$refs.submitBtn.click();
    },
  },
};
</script>

<style lang="scss" scoped>

</style>
