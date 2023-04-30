<template>
  <ApproveToCommittee
    v-if="section === `${modulePage}-list`"
  />
  <FormTopic v-if="isSeeForm" />
</template>

<script>
import { mapGetters } from 'vuex';
import ApproveToCommittee from './ApproveToCommittee.vue';

export default {
  name: 'TopicBodyPage',
  components: {
    ApproveToCommittee,
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
