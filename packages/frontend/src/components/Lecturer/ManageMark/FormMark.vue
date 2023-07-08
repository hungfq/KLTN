<!-- eslint-disable camelcase -->
<template>
  <div class="mx-8 my-8">
    <div
      class=" my-4 rounded px-2 py-2 bg-slate-500 w-fit text-white font-semibold cursor-pointer"
      @click="rollBack"
    >
      Quay về
    </div>
    <template v-if="!loading">
      <div
        v-if="typeLecturer==='SE'"
        class="mt-2 bg-slate-100 py-2"
      >
        <div class="tabs ml-4">
          <a
            v-for="option in headerTabs"
            :key="option.code"
            class="tab tag-lg tab-lifted min-w-[100px] text-blue-900 font-semibold"
            :class="{'tab-active' : option.code === type}"
            @click="type= option.code"
          >{{ option.text }}</a>
        </div>
      </div>
      <div class="flex justify-between list-decimal my-4">
        <label
          class="labe font-semibold text-xl"
        > Danh sách tiêu chí</label>
        <div class="flex">
          <label
            v-for="(student, i) in studentSort"
            :key="`label-${i}`"
            class="label items-center w-[240px] font-semibold"
          > {{ `Sinh viên: ${student.code}` }}</label>
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
            v-for="(student, i) in studentSort"
            :key="i"
            class="flex"
          >
            <div class="flex w-[240px]">
              <FormKit
                v-model.number="field.values[`${student.id}`]"
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
          type="button"
          :disabled="!checkValuesInRange || isSamePrev"
          class="btn btn-primary"
          @click="submitGrades"
        >
          Chấm điểm
        </button>
      </div>
    </template>
    <Loading v-else />
  </div>
</template>

<script>
import _, { cloneDeep, orderBy } from 'lodash';
import { mapGetters } from 'vuex';
import TopicApi from '../../../utils/api/topic';
import CriteriaApi from '../../../utils/api/criteria';
import Loading from '../../common/Loading.vue';

export default {
  components: {
    Loading,
  },
  emits: ['submit-grades'],
  data () {
    return {
      scheduleId: null,
      students: [],
      list_students: [],
      criteria: [],
      topicId: 0,
      formValues: [],
      prevFormValues: {},
      loading: false,
      type: 'SE',
      headerTabs: [
        { code: 'LT', text: 'Hướng dẫn' },
        { code: 'CR', text: 'Phản biện' },
        { code: 'SE', text: 'Thư ký' },
        { code: 'PD', text: 'Chủ tịch' },
      ],

      mapValue: {
        advisor: 'LT',
        critical: 'CR',
        secretary: 'SE',
        president: 'PD',
      },
      typeLecturer: 'LT',
    };
  },
  computed: {
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
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
    studentSort () {
      return orderBy(this.list_students, 'id');
    },
    isSamePrev () {
      return _.isEqual(this.formValues, this.prevFormValues);
    },
  },
  watch: {
    async type (newQuestion, oldQuestion) {
      this.loading = true;
      try {
        await this.fetchTopicScore();
      } catch (e) {
        this.$toast.error('Vui lòng thử thử lại!');
      }
      this.loading = false;
    },
  },

  async mounted () {
    const { id } = this.$store.state.url;
    this.topicId = id;
    const { type } = this.$store.state.url;
    this.type = type;
    this.typeLecturer = type;
    await this.fetchData();
  },
  methods: {
    async fetchData () {
      this.loading = true;
      try {
        const topic = await TopicApi.listTopicById(this.token, this.topicId);
        this.scheduleId = topic.scheduleId._id;
        this.students = topic.students;
        this.list_students = topic.list_students;
        await this.fetchCriteria();
        await this.fetchTopicScore();
      } catch (e) {
        this.$toast.error('Vui lòng thử thử lại!');
      }
      this.loading = false;
    },
    async fetchCriteria () {
      const listCriteria = await CriteriaApi.listBySchedule(this.token, this.scheduleId);
      this.criteria = listCriteria;
    },
    async fetchTopicScore () {
      const scoreList = await TopicApi.getGradeForTopicByLecturer(this.token, this.topicId, this.type);
      if (scoreList.data) {
        this.normalizeData(scoreList);
      } else {
        this.defaultValuesForFormValue();
      }
      this.prevFormValues = cloneDeep(this.formValues);
    },

    errorHandler (e) {
      if (e.response.data.error.code === 400) this.$toast.error(e.response.data.error.message);
      else { this.$toast.error('Có lỗi xảy ra, vui lòng liên hệ quản trị để kiểm tra.'); }
    },
    rollBack () {
      this.$store.dispatch('url/updateSection', `${this.module}-list`);
    },
    async  submitGrades () {
      const details = this.transformGrade();
      // const { type } = this.$store.state.url;
      const { type } = this;
      const data = {
        type, details,
      };
      try {
        await TopicApi.updateGradeForTopic(this.token, this.topicId, data);
        this.$toast.success('Đã cập nhật điểm thành công!');
        this.rollBack();
      } catch (error) {
        this.errorHandler(e);
        // this.$toast.error('Đã có lỗi xảy ra, vui lòng thử thử lại!');
      }
    },
    transformGrade () {
      const arrValues = [];
      this.formValues.forEach((formValue) => {
        const keys = Object.keys(formValue.values);
        keys.forEach((id) => {
          arrValues.push({
            criteria_id: formValue.id,
            student_id: parseInt(id, 10),
            score: formValue.values[id],
          });
        });
      });
      return arrValues;
    },

    // structure of object formValue
    // {
    //   id,
    //   title,
    //   values: {
    //     student_id1: score_rate1,
    //     student_id2: score_rate2,
    //   };
    // }
    normalizeData (scoreList) {
      const transformed = scoreList.data.reduce((acc, curr) => {
        const {
          // eslint-disable-next-line camelcase
          criteria_id, title, student_id, score,
        } = curr;
          // eslint-disable-next-line camelcase
        const key = `${criteria_id}`;
        if (!acc[key]) {
          acc[key] = {
            // eslint-disable-next-line camelcase
            id: criteria_id,
            title,
            values: {},
          };
        }
        // eslint-disable-next-line camelcase
        acc[key].values[student_id] = score || 0;
        return acc;
      }, {});
      const result = Object.values(transformed).map(({ id, title, values }) => ({
        id,
        title,
        values,
      }));
      this.formValues = result;
    },
    defaultValuesForFormValue () {
      const object = {};
      this.list_students.forEach((student) => {
        object[student.id] = 0;
      });
      this.formValues = this.criteria.map((criterion) => ({
        id: criterion.criteria_id,
        title: criterion.title,
        values: cloneDeep(object),
      }));
    },
  },
};
</script>
