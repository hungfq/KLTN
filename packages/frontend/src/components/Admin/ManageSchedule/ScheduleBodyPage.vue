<template>
  <template v-if="module === 'schedule'">
    <ManageScheduleAdminV2 v-if="section === 'schedule-list'" />
    <FormScheduleV2 v-if="isSeeForm" />
  </template>
</template>

<script>
import { mapGetters } from 'vuex';
import FormScheduleV2 from './FormScheduleV2.vue';
import ManageScheduleAdminV2 from './ManageScheduleAdminV2.vue';

export default {
  name: 'ScheduleBodyPage',
  components: {
    FormScheduleV2,
    ManageScheduleAdminV2,
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
