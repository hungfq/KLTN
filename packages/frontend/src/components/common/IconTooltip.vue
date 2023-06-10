<template>
  <div
    class="tooltip tooltip-bottom pr-3"
    :data-tip="title"
  >
    <font-awesome-icon
      class="cursor-pointer"
      :icon="icon"
      size="xl"
      @click="$emit('clickIcon')"
    />
  </div>
</template>
<script>
export default {
  name: 'IconTooltip',
  props: {
    icon: {
      type: String,
      default: 'fa-solid fa-eye',
    },
    title: {
      type: String,
      default: 'Xem',
    },
  },
  emits: ['clickIcon'],
};
</script>
