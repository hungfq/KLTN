<template>
  <a
    class="btn btn-neutral text-white mx-2"
    :href="link"
  ><font-awesome-icon
    :icon="['fas', 'download']"
    class="mr-2"
  />Tải mẫu tệp excel</a>
</template>
<script>
export default {
  props: {
    link: {
      type: String,
      default: '',
    },
  },
};
</script>
