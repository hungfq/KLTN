<template>
  <VeeForm
    v-slot="{ handleSubmit, errors }"
    class="mx-4 mt-6 flex flex-col"
  >
    <form @submit="handleSubmit($event, handleSubmitForm)">
      <div
        v-for="(field, index) in fields"
        :key="index"
        class="flex justify-between list-decimal border my-4"
      >
        <label
          class="label"
          :for="field.name"
        >
          <span class="label-text text-bold">{{ `${index + 1}. ${field.name}` }}</span>
        </label>
        <div class="flex flex-col items-end">
          <VeeField
            :id="field.id"
            v-model="formValues[field.id]"
            :name="field.name"
            type="number"
            placeholder="Vui lòng nhập điểm..."
            class="input input-bordered"
            rules="required|minMax:0,10"
          />
          <ErrorMessage
            class="pl-1 text-red-500"
            :name="field.name"
          />
        </div>
      </div>
    </form>
    <div>
      <button
        type="submit"
        :disabled="!!errors.length"
        class="btn btn-primary"
      >
        Chấm điểm
      </button>
    </div>
  </VeeForm>
</template>

<script>
import _ from 'lodash';
import { Field as VeeField, Form as VeeForm, ErrorMessage } from 'vee-validate';

export default {
  components: {
    VeeField,
    VeeForm,
    ErrorMessage,
  },
  props: {
    fields: {
      type: Array,
      default: () => [
        { id: 1, name: 'feature 1' },
        { id: 2, name: 'feature 2' },
        { id: 3, name: 'feature 3' },
        { id: 4, name: 'feature 4' },
      ],
    },
  },
  data () {
    return {
      formValues: {},
    };
  },
  computed: {

  },
  watch: {
    object (newValue) {
      this.formValues = _.cloneDeep(newValue);
    },
  },
  mounted () {
    // setLocale('vi');
  },
  methods: {
  },
};
</script>
