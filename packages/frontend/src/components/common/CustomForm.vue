<template>
  <VeeForm
    v-slot="{ handleSubmit, errors }"
    class="w-full mx-4 mt-6 flex flex-col"
    @submit="onSubmit"
  >
    <form @submit="handleSubmit($event, onSubmit)">
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
                :name="field.name"
                :type="field.type"
                :placeholder="field.label"
                class="input input-bordered pr-2"
                :rules="field.rules"
                :disabled="isView || !!field.isDisabled"
              />
              <textarea
                v-else-if="field.type === 'textarea'"
                :id="field.name"
                v-model="formValues[field.name]"
                :name="field.name"
                :placeholder="field.label"
                class="input input-bordered pr-2"
                :disabled="isView || !!field.isDisabled"
              />
              <select
                v-else-if="field.type === 'select'"
                :id="field.name"
                v-model="formValues[field.name]"
                :name="field.name"
                class="input input-bordered pr-2"
                :disabled="isView || !!field.isDisabled"
              >
                <option
                  v-for="(option, index) in field.options"
                  :key="index"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
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
                :name="field.name"
                :type="field.type"
                :placeholder="field.label"
                class="input input-bordered pr-2"
                :rules="field.rules"
                :disabled="isView"
              />
              <VeeField
                v-else-if="field.type === 'textarea'"
                :id="field.name"
                v-model="formValues[field.name]"
                :name="field.name"
                :placeholder="field.label"
                class="input input-bordered pr-2"
                :disabled="isView"
                :rules="field.name"
                as="textarea"
              />

              <VeeField
                v-else-if="field.type === 'select'"
                :id="field.name"
                v-model="formValues[field.name]"
                :name="field.name"
                class="input input-bordered pr-2"
                :disabled="isView"
                as="select"
              >
                <option
                  value=""
                  disabled
                >
                  Vui lòng chọn giới tính
                </option>
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
    <button
      v-if="!isView"
      type="submit"
      :disabled="!!errors.length"
      class="py-2 px-4 mb-2 bg-blue-500 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50 w-fit ml-4"
    >
      {{ isUpdate ? 'Cập nhật' : 'Lưu' }}
    </button>
  </VeeForm>
</template>

<script>
import _ from 'lodash';
import { Field as VeeField, Form as VeeForm, ErrorMessage } from 'vee-validate';
// import { setLocale } from '@vee-validate/i18n';
// import { localize } from '@vee-validate/i18n';
// import ar from '@vee-validate/i18n/dist/locale/ar.json';
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
  watch: {
    object (newValue) {
      this.formValues = _.cloneDeep(newValue);
    },
  },
  mounted () {
    // setLocale('vi');
  },
  methods: {
    onSubmit () {
      console.log('🚀 ~ file: CustomForm.vue:180 ~ onSubmit ~ formFields:', this.formValues);
      // this.$emit('on-submit', this.formFields);
    },
  },
};
</script>
