<template>
  <div class="mx-8 my-8">
    <div class="flex justify-between list-decimal my-4">
      <label
        class="label"
      > Tieue chi</label>
      <div class="flex">
        <label
          v-for="(code, i) in students"
          :key="`label-${i}`"
          class="label items-center w-[240px] font-semibold"
        > {{ `Sinh vieen: ${code}` }}</label>
      </div>
    </div>
    <div
      v-for="(field, index) in formValues"
      :key="index"
      class="flex justify-between list-decimal my-4"
    >
      <label
        class="label w-[300px] break-words"
        :for="field.name"
      >
        <span class="label-text text-bold">{{ `${index + 1}. ${field.title}` }}</span>
      </label>
      <div class="flex items-start">
        <div
          v-for="(code, i) in Object.keys(field.values)"
          :key="i"
          class="flex"
        >
          <div class="flex w-[240px]">
            <FormKit
              v-model.number="field.values[code]"
              type="number"
              name="score-rate"
              outer-class="flex flex-col"
              inner-class="w-24 ml-6"
              :suffix="'%'"
              validation="required|min:0|max:10"
              :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này',
                                      min: 'Điểm phải lớn hơn 0',
                                      max: 'Điểm phải nhỏ hơn 10' }"
            >
              <template #suffix>
                <span class="mr-1">/10</span>
              </template>
            </FormKit>
          </div>
        </div>
      </div>
    </div>
    <div>
      <button
        type="submit"
        :disabled="!checkValuesInRange"
        class="btn btn-primary"
      >
        Chấm điểm
      </button>
    </div>
  </div>
</template>

<script>
import _, { cloneDeep } from 'lodash';
import { mapGetters } from 'vuex';
import TopicApi from '../../../utils/api/topic';
import CriteriaApi from '../../../utils/api/criteria';

export default {
  components: {
  },
  data () {
    return {
      scheduleId: null,
      students: [],
      criteria: [],
      topicId: [],
      formValues: [],
    };
  },
  computed: {
    ...mapGetters('auth', [
      'token',
    ]),
    checkValuesInRange () {
      const arrValues = [];
      this.formValues.forEach((formValue) => {
        const values = Object.values(formValue.values);
        arrValues.push(...values);
      });
      const check = arrValues.some((value) => value < 0 || value > 10);
      return !check;
    },
  },
  async mounted () {
    const { id } = this.$store.state.url;
    this.topicId = id;
    const topic = await TopicApi.listTopicById(this.token, id);
    this.scheduleId = topic.scheduleId._id;
    this.students = topic.students;
    const listCriteria = await CriteriaApi.listBySchedule(this.token, this.scheduleId);
    this.criteria = listCriteria;
    const object = {};
    this.students.forEach((code) => {
      object[code] = 0;
    });
    this.formValues = this.criteria.map((criterion) => ({
      id: criterion.criterion_id,
      title: criterion.title,
      values: cloneDeep(object),
    }));
    console.log('🚀 ~ file: FormMark.vue:111 ~ this.formValues=this.criteria.map ~ this.formValues:', this.formValues);
  },
  methods: {
  },
};
</script>
