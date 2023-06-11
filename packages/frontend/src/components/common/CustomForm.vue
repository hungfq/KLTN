<template>
  <VeeForm
    v-slot="{ handleSubmit, errors }"
    class="w-full mx-4 mt-6 flex flex-col"
  >
    <form @submit="handleSubmit($event, handleSubmitForm)">
      <div class="flex w-full">
        <div class="w-full">
          <div
            v-for="(field, index) in formFields.slice(0, Math.ceil(formFields.length / 2))"
            :key="index"
            class="mb-4 mr-8 ml-4"
          >
            <div class="form-control">
              <label
                class="label"
                :for="field.name"
              >
                <span class="label-text text-bold">{{ field.label }}</span>
              </label>
              <VeeField
                v-if="field.type === 'text' || field.type === 'email' || field.type === 'password'"
                :id="field.name"
                v-model="formValues[field.name]"
                data-theme="light"
                :name="field.name"
                :type="field.type"
                :placeholder="field.label"
                class="input input-bordered pr-2"
                :rules="field.rules"
                :disabled="checkDisabled(field.isDisabled)"
              />
              <VeeField
                v-else-if="field.type === 'textarea'"
                :id="field.name"
                v-model="formValues[field.name]"
                :rules="field.rules"
                :name="field.name"
                :placeholder="field.label"
                class="input input-bordered pr-2"
                :disabled="checkDisabled(field.isDisabled)"
                as="textarea"
              />
              <VeeField
                v-else-if="field.type === 'select'"
                :id="field.name"
                v-model="formValues[field.name]"
                data-theme="light"
                :name="field.name"
                :rules="field.rules"
                class="input input-bordered pr-2"
                :disabled="checkDisabled(field.isDisabled)"
                as="select"
              >
                <option
                  v-for="(option, index) in field.options"
                  :key="index"
                  :value="option.value"
                  :selected="formValues[field.name] && formValues[field.name] === option.value"
                >
                  {{ option.label }}
                </option>
              </VeeField>
              <ErrorMessage
                v-if="!isView"
                class="pl-1 text-red-500"
                :name="field.name"
              />
            </div>
          </div>
        </div>
        <div class="w-full">
          <div
            v-for="(field, index) in formFields.slice(Math.ceil(formFields.length / 2), formFields.length)"
            :key="index"
            class="mb-4 mr-8 ml-4"
          >
            <div class="form-control">
              <label
                class="label"
                :for="field.name"
              >
                <span class="label-text">{{ field.label }}</span>
              </label>
              <VeeField
                v-if="field.type === 'text' || field.type === 'email' || field.type === 'password'"
                :id="field.name"
                v-model="formValues[field.name]"
                data-theme="light"
                :name="field.name"
                :type="field.type"
                :placeholder="field.label"
                class="input input-bordered pr-2"
                :rules="field.rules"
                :disabled="checkDisabled(field.isDisabled)"
              />
              <VeeField
                v-else-if="field.type === 'textarea'"
                :id="field.name"
                v-model="formValues[field.name]"
                data-theme="light"
                :rules="field.rules"
                :name="field.name"
                :placeholder="field.label"
                class="input input-bordered pr-2"
                :disabled="checkDisabled(field.isDisabled)"
              />

              <VeeField
                v-else-if="field.type === 'select'"
                :id="field.name"
                v-model="formValues[field.name]"
                data-theme="light"
                :name="field.name"
                :rules="field.rules"
                class="input input-bordered pr-2"
                :disabled="checkDisabled(field.isDisabled)"
                as="select"
              >
                <option
                  v-for="(option, index) in field.options"
                  :key="index"
                  :value="option.value"
                  :selected="formValues[field.name] && formValues[field.name] === option.value"
                >
                  {{ option.label }}
                </option>
              </VeeField>
              <ErrorMessage
                v-if="!isView"
                class="pl-1 text-red-500"
                :name="field.name"
              />
            </div>
          </div>
        </div>
      </div>
    </form>
    <div class="flex justify-end mr-16">
      <button
        v-if="!isView"
        type="submit"
        :disabled="!!errors.length"
        class="btn btn-primary my-4"
      >
        {{ isUpdate ? 'Cập nhật' : 'Lưu' }}
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
    formTitle: {
      type: String,
      default: 'Form',
    },
    formFields: {
      type: Array,
      default: () => [],
    },
    isView: false,
    isUpdate: false,
    isSave: true,
    object: {},
  },
  // emits: ['on-submit'],
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
    handleSubmitForm () {
      console.log('🚀 ~ file: CustomForm.vue:180 ~ onSubmit ~ formFields:', this.formValues);
      // this.$emit('on-submit', this.formFields);
    },
    checkDisabled (value) {
      if (this.isView) return true;
      if (this.isSave) return false;
      return !!value;
    },
  },
};
</script>
