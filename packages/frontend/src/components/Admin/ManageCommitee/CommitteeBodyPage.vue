<template>
  <ManageCommitteeAdminV2
    v-if="section === `${modulePage}-list`"
  />
  <FormCommittee
    v-if="section === `${modulePage}-update` || section === `${modulePage}-import` || section === `${modulePage}-view`"
  />
  <FormTopicCommittee v-if="section === 'committee-add-topic'" />
</template>

<script>
import { mapGetters } from 'vuex';
import FormCommittee from './FormCommittee.vue';
import FormTopicCommittee from './FormTopicCommittee.vue';
import ManageCommitteeAdminV2 from './ManageCommitteeAdmin.vue';

export default {
  name: 'UserBodyPage',
  components: {
    ManageCommitteeAdminV2,
    FormCommittee,
    FormTopicCommittee,
  },
  computed: {
    ...mapGetters('url', {
      modulePage: 'module', section: 'section',
    }),
  },

};
</script>
