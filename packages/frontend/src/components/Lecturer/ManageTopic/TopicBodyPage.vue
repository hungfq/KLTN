<template>
  <ManageTopicAdminV2
    v-if="section === `${modulePage}-list`"
    :type-user="typeUser"
  />
  <FormTopic v-if="isSeeForm" />
</template>

<script>
import { mapGetters } from 'vuex';
import ManageTopicAdminV2 from './ManageTopicLecturerV2.vue';
import FormTopic from './FormTopicV2.vue';

export default {
  name: 'TopicBodyPage',
  components: {
    ManageTopicAdminV2,
    FormTopic,
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
