<template>
  <div
    class=" btn btn-primary"
    @click="handleImport"
  >
    <font-awesome-icon :icon="['fas', 'circle-plus']" />
    <span class="ml-2"> {{ title }}</span>
  </div>
</template>
<script>
export default {
  name: 'ButtonImport',
  props: {
    handleImport: {
      type: Function,
      default: () => {},
    },
    title: {
      type: String,
      default: 'Thêm',
    },
  },
};
</script>
