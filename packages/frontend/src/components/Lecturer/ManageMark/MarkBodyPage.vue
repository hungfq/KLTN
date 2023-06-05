<template>
  <ManageTopicMark
    v-if="section === `${modulePage}-list`"
  />
  <FormMark v-if="section === `${modulePage}-update`" />
</template>

<script>
import { mapGetters } from 'vuex';
import ManageTopicMark from './ManageTopicMark.vue';
import FormMark from './FormMark.vue';

export default {
  name: 'TopicBodyPage',
  components: {
    ManageTopicMark,
    FormMark,
  },
  computed: {
    ...mapGetters('url', {
      modulePage: 'module', section: 'section',
    }),
    isSeeForm () {
      const openFormType = [`${this.modulePage}-update`];
      return openFormType.includes(this.section);
    },
  },

};
</script>

  <style />
