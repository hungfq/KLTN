<template>
  <div
    class=" rounded mr-auto ml-4 my-2 bg-blue-800 text-white font-sans font-semibold py-2 px-4 cursor-pointer"
    @click="handleImport"
  >
    Thêm
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
  },
};
</script>
