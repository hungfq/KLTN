<template>
  <ManageTopicMark
    v-if="section === `${modulePage}-list`"
  />
</template>

<script>
import { mapGetters } from 'vuex';
import ManageTopicMark from './ManageTopicMark.vue';

export default {
  name: 'TopicBodyPage',
  components: {
    ManageTopicMark,
  },
  computed: {
    ...mapGetters('url', {
      modulePage: 'module', section: 'section',
    }),
    isSeeForm () {
      const openFormType = [`${this.modulePage}-update`, `${this.modulePage}-import`, `${this.modulePage}-view`];
      return openFormType.includes(this.section);
    },
  },

};
</script>

  <style />
