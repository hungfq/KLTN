<template>
  <ManageStudentAdminVueV2
    v-if="section === `${modulePage}-list`"
    :type-user="typeUser"
  />
  <FormUserVueV2
    v-if="section === `${modulePage}-update` || section === `${modulePage}-import` || section === `${modulePage}-view`"
  />
</template>

<script>
import { mapGetters } from 'vuex';
import FormUserVueV2 from './FormUserV2.vue';
import ManageStudentAdminVueV2 from './ManageStudentAdminV2.vue';

export default {
  name: 'UserBodyPage',
  components: {
    FormUserVueV2,
    ManageStudentAdminVueV2,
  },
  computed: {
    ...mapGetters('url', {
      modulePage: 'module', section: 'section',
    }),
  },

};
</script>
