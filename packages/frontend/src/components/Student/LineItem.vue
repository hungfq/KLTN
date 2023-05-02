<!-- eslint-disable max-len -->
<template>
  <div class="my-2">
    <span class="font-bold text-gray-700">{{ title }} </span> <slot> <span class="text-gray-600">{{ content }}</span></slot>
  </div>
</template>

<script>

export default {
  name: 'LineItem',
  components: {
  },
  props: {
    title: '',
    content: '',
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
