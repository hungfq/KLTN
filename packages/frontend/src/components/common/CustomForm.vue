<template>
  <form
    class="w-full mx-4 mt-6 flex flex-col"
    @submit.prevent="submitForm"
  >
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
            <input
              v-if="field.type === 'text' || field.type === 'email' || field.type === 'password'"
              :id="field.name"
              v-model="formValues[field.name]"
              :name="field.name"
              :type="field.type"
              :placeholder="field.label"
              class="input input-bordered pr-2"
              :required="field.required"
            >
            <textarea
              v-else-if="field.type === 'textarea'"
              :id="field.name"
              v-model="formValues[field.name]"
              :name="field.name"
              :placeholder="field.label"
              class="input input-bordered pr-2"
              :required="field.required"
            />
            <select
              v-else-if="field.type === 'select'"
              :id="field.name"
              v-model="formValues[field.name]"
              :name="field.name"
              class="input input-bordered pr-2"
              :required="field.required"
            >
              <option
                v-for="(option, index) in field.options"
                :key="index"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>
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
            <input
              v-if="field.type === 'text' || field.type === 'email' || field.type === 'password'"
              :id="field.name"
              v-model="formValues[field.name]"
              :name="field.name"
              :type="field.type"
              :placeholder="field.label"
              class="input input-bordered pr-2"
              :required="field.required"
            >
            <textarea
              v-else-if="field.type === 'textarea'"
              :id="field.name"
              v-model="formValues[field.name]"
              :name="field.name"
              :placeholder="field.label"
              class="input input-bordered pr-2"
              :required="field.required"
            />
            <select
              v-else-if="field.type === 'select'"
              :id="field.name"
              v-model="formValues[field.name]"
              :name="field.name"
              class="input input-bordered pr-2"
              :required="field.required"
            >
              <option
                v-for="(option, index) in field.options"
                :key="index"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <button
      v-if="!isView"
      type="submit"
      class="py-2 px-4 mb-2 bg-blue-500 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50 w-fit ml-4"
    >
      {{ isUpdate ? 'Cập nhật' : 'Lưu' }}
    </button>
  </form>
</template>

<script>
import _ from 'lodash';

export default {
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

  methods: {
    submitForm () {
      console.log('Form submitted with values:', this.formValues);
      // Here you can perform your form submit logic
    },
  },
};
</script>
