<template>
  <div class="bg-white shadow rounded px-3 pt-3 pb-5 border border-white">
    <div class="flex justify-between">
      <p class="text-gray-700 font-semibold font-sans tracking-wide text-sm">
        {{ task.title }}
      </p>
      <img
        class="w-6 h-6 rounded-full ml-3"
        :src="getImage(task.assignTo)"
        alt="Avatar"
      >
    </div>
    <div class="flex mt-4 justify-between items-center">
      <span class="text-sm text-gray-600">{{ task.code }}</span>
      <badge>
        {{ getAssignName(task.assignTo) ? getAssignName(task.assignTo).name : '' }}
      </badge>
    </div>
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
import Badge from './TaskBadge.vue';

export default {
  components: {
    Badge,
  },
  props: {
    task: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    ...mapGetters('task', [
      'listStudents',
    ]),
    ...mapGetters('task', [
      'listTask', 'topicId', 'listStudents',
    ]),
    defaultAvatarUrl () {
      const imageUrl = new URL('/src/assets/images/default_avatar.png', import.meta.url);
      return imageUrl;
    },
  },
  methods: {
    getAssignName (id) {
      return this.listStudents.find((e) => e._id === id);
    },
    getImage (id) {
      if (!this.getAssignName(id)) return this.defaultAvatarUrl;
      if (!this.getAssignName(id).picture) return this.defaultAvatarUrl;
      return this.getAssignName(id).picture;
    },
  },
};
</script>
