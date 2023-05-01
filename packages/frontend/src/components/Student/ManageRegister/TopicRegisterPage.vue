<template>
  <ManageRegisterStudent
    v-if="section === `${modulePage}-list`"
  />
  <FormTopic v-if="isSeeForm" />
</template>

<script>
import { mapGetters } from 'vuex';
import ManageRegisterStudent from './ManageRegisterStudent.vue';
import FormTopic from './FormTopicV2.vue';

export default {
  name: 'TopicBodyPage',
  components: {
    ManageRegisterStudent,
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
