<template>
  <div
    class="btn btn-primary"
    @click="$emit('handleImport')"
  >
    <font-awesome-icon :icon="['fas', 'circle-plus']" />
    <span class="ml-2">{{ title }} </span>
  </div>
</template>
<script lang="js">
export default {
  props: {
    title: {
      type: String,
      default: 'Thêm đợt đăng ký',
    },
  },
  emits: ['handleImport'],
};
</script>
