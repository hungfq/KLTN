<!-- eslint-disable max-len -->
<template>
  <div
    class="inset-y-0 left-0 w-64 bg-white shadow-lg sm:left-16 sm:w-72"
    :class="{ '!w-0' : !open}"
  >
    <nav
      class="flex flex-col h-full relative"
    >
      <!-- Logo -->
      <div
        v-if="open"
        class="flex items-center justify-center flex-shrink-0 py-10 "
      >
        <a href="#">
          <img
            class="w-2/3 mx-auto"
            :src="imageUrl"
          >
        </a>
      </div>
      <div
        class="font-bold text-xl absolute -right-4 top-8 px-1 py-1 bg-white rounded"
        @click="open =!open"
      >
        <font-awesome-icon :icon="['fas', 'left-right']" />
      </div>

      <!-- Links -->
      <div
        v-if="open"
        class="flex-1 px-4 space-y-2 overflow-hidden hover:overflow-auto"
      >
        <a
          v-for="item in listItems"
          :key="item.id"
          class="cursor-pointer"
          :class="[ item.id == module ? 'flex p-2 items-center w-full space-x-2 text-white bg-blue-900 rounded-lg' : 'flex p-2 items-center space-x-2 text-blue-900 transition-colors rounded-lg group hover:bg-indigo-600 hover:text-white']"
          @click="updateModule(item.id)"
        >
          <font-awesome-icon :icon="item.icon" />
          <span> {{ item.value }} </span>
        </a>
      </div>
    </nav>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'ManageBar',
  props: {
    listItems: [],
  },
  data () {
    return {
      open: true,
    };
  },
  computed: {
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    imageUrl () {
      const imageUrl = new URL('/src/assets/images/fit.png', import.meta.url);
      return imageUrl;
    },
  },
  methods: {
    updateModule (module) {
      this.$store.dispatch('url/updateModule', module);
      this.$store.dispatch('url/updateSection', `${module}-list`);
    },
  },
};
</script>
