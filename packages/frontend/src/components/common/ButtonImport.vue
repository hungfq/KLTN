<template>
  <div
    class=" rounded bg-blue-800 text-white font-sans font-semibold py-2 px-2 cursor-pointer my-2"
    @click="handleImport"
  >
    {{ title }}
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
