<template>
  <div class="flex flex-col">
    <div class="text-center font-semibold text-xl">
      Danh sách đề tài trong quá trình thực hiện
    </div>
  </div>
</template>

<script>
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';

export default {

  name: 'TaskBodyPage',
  components: {
  },
  setup () {
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const store = useStore();
    const headers = [
      { text: 'Mã', value: 'code', sortable: true },
      { text: 'Đề tài', value: 'title' },
    ];
    const items = [];
    const serverOptions = ref({
      page: 1,
      rowsPerPage: 10,
      sortBy: 'updated_at',
      sortType: 'desc',
    });
    const token = store.getters['auth/token'];
    const modulePage = computed(() => store.getters['url/module']);
    const listTopics = computed(() => store.getters['task/listTopic']);

    const showRow = (item) => {
      store.dispatch('url/updateSection', `${modulePage.value}-view`);
      store.dispatch('url/updateId', item.id);
    };

    return {
      headers,
      items,
      showRow,
      loading,
      serverOptions,
      serverItemsLength,
      rowItems,
      modulePage,
      BASE_API_URL,
    };
  },
  computed: {
    ...mapGetters('url', {
      modulePage: 'module', section: 'section',
    }),
    ...mapGetters('task', {
      scheduleId: 'scheduleId', listTopic: 'listTopic',
    }),
  },
  mounted () {
    console.log('🚀 ~ file: TaskBodyPage.vue:24 ~ mounted ~ scheduleId:', this.scheduleId); z;
    console.log(this.listTopic);
  },
};
</script>

  <style />
