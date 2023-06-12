<template>
  <vue-final-modal
    v-slot="{ close }"
    v-bind="$attrs"
    @before-open="handleShow(topicId)"
  >
    <div class="relative p-4 w-full max-w-6xl mx-auto mt-[5%]">
      <!-- Modal content -->
      <div class="relative bg-white rounded-md shadow">
        <!-- Modal header -->
        <div class="flex justify-between items-start p-4 rounded-t border-b ">
          <h2 class="text-xl font-semibold text-gray-900 ">
            Chi tiết điểm số
          </h2>
          <button
            type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
            data-modal-toggle="defaultModal"
            @click="close"
          >
            <svg
              aria-hidden="true"
              class="w-5 h-5"
              fill="currentColor"
              viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg"
            ><path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            /></svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <div class="flex flex-col">
          <EasyDataTable
            show-index
            :headers="headers"
            :items="items"
            body-text-direction="center"
            header-text-direction="center"
            buttons-pagination="false"
            :loading="loading"
            hide-footer
          >
            <template #empty-message>
              <div class="text-center text-gray-500">
                Không có dữ liệu
              </div>
            </template>
          </EasyDataTable>
          <div class="flex justify-end">
            <button
              class="btn btn-primary m-2"
              @click="close"
            >
              Đóng
            </button>
          </div>
        </div>
      </div>
    </div>
  </vue-final-modal>
</template>
<script>
import {
  ref, watch, onMounted, computed,
} from 'vue';
import { useStore } from 'vuex';
import TopicApi from '../../utils/api/topic';

export default {
  name: 'ShowGradeModal',
  components: {

  },
  inheritAttrs: false,
  props: {
    // eslint-disable-next-line vue/require-prop-type-constructor, vue/require-default-prop
    topicId: '',
  },
  emits: ['closeDetailModal'],
  setup () {
    const store = useStore();
    const items = ref([]);
    const loading = ref(false);
    const headers = [
      { text: 'Tiêu chí', value: 'title', sortable: true },
      { text: 'Giảng viên hướng dẫn ', value: 'lecturer_grade', width: 170 },
      { text: 'Giảng viên phản biện', value: 'critical_grade', width: 170 },
      { text: 'Chủ tịch hội đồng', value: 'president_grade', width: 170 },
      { text: 'Thư ký hội đồng', value: 'secretary_grade', width: 170 },
    ];
    const token = store.getters['auth/token'];
    const handleShow = async (topicId) => {
      loading.value = true;
      if (topicId) {
        items.value = await TopicApi.getGradeTopicByStudent(token, topicId);
      }
      loading.value = false;
    };
    return {
      items,
      loading,
      headers,
      handleShow,
    };
  },
};
</script>
