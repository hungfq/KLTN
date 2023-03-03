<!-- eslint-disable max-len -->
<template>
  <div
    class="fixed inset-y-0 left-0 z-10 flex-shrink-0 w-64 bg-white border-r-2 border-indigo-100 shadow-lg sm:left-16 rounded-tr-3xl rounded-br-3xl sm:w-72 lg:static lg:w-64"
  >
    <nav
      aria-label="Main"
      class="flex flex-col h-full"
    >
      <!-- Logo -->
      <div class="flex items-center justify-center flex-shrink-0 py-10">
        <a href="#">
          <img
            class="w-2/3 mx-auto"
            src="/fit.png"
          >
        </a>
      </div>

      <!-- Links -->
      <div class="flex-1 px-4 space-y-2 overflow-hidden hover:overflow-auto">
        <a
          v-for="item in listItems"
          :key="item.id"
          class="cursor-pointer"
          :class="[ item.id == module ? 'flex p-2 items-center w-full space-x-2 text-white bg-indigo-600 rounded-lg' : 'flex p-2 items-center space-x-2 text-indigo-600 transition-colors rounded-lg group hover:bg-indigo-600 hover:text-white']"
          @click="updateModule(item.id)"
        >
          {{ item.value }}
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
  computed: {
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
  },
  methods: {
    updateModule (module) {
      this.$store.dispatch('url/updateModule', module);
      this.$store.dispatch('url/updateSection', `${module}-list`);
    },
  },
};
</script>
