<template>
  <ManageProposalLecturer
    v-if="section === `${modulePage}-list`"
  />
  <FormProposalVue v-if="isSeeForm" />
</template>

<script>
import { mapGetters } from 'vuex';
import ManageProposalLecturer from './ManageProposalLecturerV2.vue';
import FormProposalVue from './FormApproveProposalV2.vue';

export default {
  name: 'TopicBodyPage',
  components: {
    ManageProposalLecturer,
    FormProposalVue,
  },
  computed: {
    ...mapGetters('url', {
      modulePage: 'module', section: 'section',
    }),
    isSeeForm () {
      const openFormType = [`${this.modulePage}-update`, `${this.modulePage}-view`];
      return openFormType.includes(this.section);
    },
  },

};
</script>

  <style />
