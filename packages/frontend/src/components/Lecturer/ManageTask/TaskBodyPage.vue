<template>
  <div class="flex flex-col h-[600px] mt-2">
    <template v-if="!topicId">
      <div class="text-center font-semibold text-xl">
        Danh sách đề tài trong quá trình thực hiện
      </div>
      <div class="mx-4 mt-4">
        <EasyDataTable
          :headers="headers"
          :items="listTopics"
          hide-footer
        >
          <template #item-operation="item">
            <div
              class="tooltip tooltip-left"
              data-tip="Xem quá trình thực hiện"
            >
              <font-awesome-icon
                class="cursor-pointer"
                :icon="['fas', 'diagram-predecessor']"
                size="xl"
                @click="showTask(item)"
              />
            </div>
          </template>
        </EasyDataTable>
      </div>
    </template>
    <div
      v-else
      class="flex flex-col"
    >
      <div class="mt-2 ml-2">
        <div
          class="btn btn-secondary"
          @click="rollBack"
        >
          Quay về
        </div>
      </div>
      <div class="mt-4">
        <TaskDraggable />
      </div>
    </div>
  </div>
</template>

<script>
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { mapState, mapGetters, useStore } from 'vuex';
import TaskDraggable from '../TaskDraggable.vue';

export default {

  name: 'TaskBodyPage',
  components: {
    TaskDraggable,
  },
  setup () {
    const BASE_API_URL = ref(import.meta.env.BASE_API_URL || 'http://localhost:8001');
    const store = useStore();
    const headers = [
      { text: 'Mã', value: 'code', sortable: true },
      { text: 'Đề tài', value: 'title', sortable: true },
      { text: 'Xem quá trình', value: 'operation', width: 200 },
    ];
    const items = [];

    const token = store.getters['auth/token'];
    const modulePage = computed(() => store.getters['url/module']);
    const scheduleId = computed(() => store.getters['task/scheduleId']);
    const listTopics = computed(() => store.getters['task/listTopic']);
    const topicId = computed(() => store.getters['task/topicId']);
    const showTask = (item) => {
      console.log('🚀 ~ file: TaskBodyPage.vue:77 ~ showTask ~ item:', item);
      // store.dispatch('url/updateSection', `${modulePage.value}-view`);
      store.dispatch('task/updateTopicId', item._id);
    };

    const rollBack = () => {
      store.dispatch('task/updateTopicId', null);
    };
    return {
      headers,
      items,
      showTask,
      listTopics,
      scheduleId,
      modulePage,
      BASE_API_URL,
      rollBack,
      topicId,
    };
  },
  computed: {
    ...mapGetters('url', {
      modulePage: 'module', section: 'section',
    }),
  },
  mounted () {
    // console.log('🚀 ~ file: TaskBodyPage.vue:24 ~ mounted ~ scheduleId:', this.scheduleId);
    // console.log(this.listTopic);
  },
};
</script>

  <style />
