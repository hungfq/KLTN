<template>
  <ManageProposal
    v-if="section === `${modulePage}-list`"
    :open="open"
  />
  <FormTopic v-if="isSeeForm" />
</template>

<script>
import { mapGetters } from 'vuex';
import ManageProposal from './ManageTopicProposalStudent.vue';
import FormTopic from './FormTopicProposal.vue';

export default {
  name: 'TopicBodyPage',
  components: {
    ManageProposal,
    FormTopic,
  },
  data () {
    return {
      open: false,
    };
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
  mounted () {
    const schedules = this.$store.state.schedule.listScheduleProposalStudent;
    if (schedules && schedules.length > 0) {
      this.open = true;
    }
  },

};
</script>

  <style />
