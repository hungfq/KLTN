<template>
  <ManageCriteriaAdmin
    v-if="section === `${modulePage}-list`"
    :type-user="typeUser"
  />
  <FormCriteria
    v-if="section === `${modulePage}-update` || section === `${modulePage}-import` || section === `${modulePage}-view`"
  />
</template>

<script>
import { mapGetters } from 'vuex';
import FormCriteria from './FormCriteria.vue';
import ManageCriteriaAdmin from './ManageCriteriaAdmin.vue';

export default {
  name: 'UserBodyPage',
  components: {
    FormCriteria,
    ManageCriteriaAdmin,
  },
  computed: {
    ...mapGetters('url', {
      modulePage: 'module', section: 'section',
    }),
  },

};
</script>
