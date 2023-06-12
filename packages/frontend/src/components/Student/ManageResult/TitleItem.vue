<!-- eslint-disable max-len -->
<template>
  <div class="bg-blue-200 px-2 py-3 flex items-center border rounded-t-xl">
    <h1 class="font-bold text-xl text-gray-800">
      {{ title || '' }}
    </h1>
  </div>
</template>

<script>

export default {
  name: 'TitleItem',
  components: {
  },
  props: {
    title: '',
  },
};
</script>
