<template>
  <vue-final-modal
    v-slot="{ params, close }"
    v-bind="$attrs"
    @before-open="prepareData(rateLecturer)"
  >
    <div class="relative p-4 w-full max-w-2xl h-full md:h-auto mx-auto mt-[10%]">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow ">
        <!-- Modal header -->
        <div class="flex justify-between items-start p-4 rounded-t border-b ">
          <h3 class="text-xl font-semibold text-gray-900 ">
            Tỷ lệ điểm của giáo viên
          </h3>
          <button
            type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
            data-modal-toggle="defaultModal"
            @click="close"
          >
            <svg
              aria-hidden="true"
              class="w-5 h-5"
              fill="currentColor"
              viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg"
            ><path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            /></svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <div class="p-6 space-y-6">
          <div class=" flex flex-col">
            <div class="flex">
              <FormKit
                v-model.number="rate.advisor_score_rate"
                label="Thang điểm của giáo viên hướng dẫn"
                type="number"
                inner-class="score-rate-lecture"
                name="score-rate"
                :suffix="'%'"
                validation="required|min:1|max:100"
                :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này',
                                        min: 'Tỷ lệ điểm phải lớn hơn 1 %',
                                        max: 'Tỷ lệ điểm phải nhỏ hơn 100 %' }"
              />
              <span class="ml-2 mt-8">%</span>
            </div>
            <div class="flex">
              <FormKit
                v-model.number="rate.critical_score_rate"
                label="Thang điểm của giáo viên phản biện"
                type="number"
                inner-class="score-rate-lecture"
                name="score-rate"
                :suffix="'%'"
                validation="required|min:1|max:100"
                :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này',
                                        min: 'Tỷ lệ điểm phải lớn hơn 1 %',
                                        max: 'Tỷ lệ điểm phải nhỏ hơn 100 %' }"
              />
              <span class="ml-2 mt-8">%</span>
            </div>
            <div class="flex">
              <FormKit
                v-model.number="rate.president_score_rate"
                label="Thang điểm của giáo viên chủ tịch hội đồng"
                type="number"
                name="score-rate"
                :suffix="'%'"
                inner-class="score-rate-lecture"
                validation="required|min:1|max:100"
                :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này',
                                        min: 'Tỷ lệ điểm phải lớn hơn 1 %',
                                        max: 'Tỷ lệ điểm phải nhỏ hơn 100 %' }"
              />
              <span class="ml-2 mt-8">%</span>
            </div>
            <div class="flex">
              <FormKit
                v-model.number="rate.secretary_score_rate"
                label="Thang điểm của giáo viên thư ký hội đồng"
                type="number"
                name="score-rate"
                inner-class="score-rate-lecture"
                :suffix="'%'"
                validation="required|min:1|max:100"
                :validation-messages="{ required: 'Vui lòng điền thông tin vào ô này',
                                        min: 'Tỷ lệ điểm phải lớn hơn 1 %',
                                        max: 'Tỷ lệ điểm phải nhỏ hơn 100 %' }"
              />
              <span class="ml-2 mt-8">%</span>
            </div>
            <div class="flex">
              <span
                v-if="!checkTotalEqual100Percent"
                class="text-red-500"
              >Tổng tỷ lệ điểm điểm phải bằng 100%</span>
            </div>
          </div>
        </div>
        <!-- Modal footer -->
        <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200  ">
          <button
            type="button"
            class="btn btn-primary"
            :disabled="!validateData"
            @click="submitRate"
          >
            Lưu lựa chọn
          </button>
        </div>
      </div>
    </div>
  </vue-final-modal>
</template>
<script>
import {
  ref, watch, onMounted, computed,
} from 'vue';
import {
  cloneDeep, unionBy, omitBy, isNull, defaults,
} from 'lodash';

export default {
  name: 'LecturerPage',
  inheritAttrs: false,
  props: {
    rateLecturer: {

      type: Object,
      default () {
        return {
          advisor_score_rate: 0,
          critical_score_rate: 0,
          president_score_rate: 0,
          secretary_score_rate: 0,
        };
      },
    },

  },

  emits: ['change-rate'],

  setup (props, { emit }) {
    const rate = ref({
      advisor_score_rate: 0,
      critical_score_rate: 0,
      president_score_rate: 0,
      secretary_score_rate: 0,
    });
    const prevRate = ref({});
    const shorter = defaults(omitBy(props.rateLecturer, isNull), {
      advisor_score_rate: 0,
      critical_score_rate: 0,
      president_score_rate: 0,
      secretary_score_rate: 0,
    });

    rate.value = cloneDeep(prevRate.value);
    const checkTotalEqual100Percent = computed(() => {
      const total = rate.value.advisor_score_rate + rate.value.critical_score_rate + rate.value.secretary_score_rate + rate.value.president_score_rate;
      return total === 100;
    });

    const validateData = computed(() => {
      const equalPrevSelected = JSON.stringify(prevRate.value) === JSON.stringify(rate.value);
      if (!rate.value.advisor_score_rate && rate.value.advisor_score_rate > 0 && rate.value.advisor_score_rate < 100) return false;
      if (!rate.value.critical_score_rate && rate.value.critical_score_rate > 0 && rate.value.critical_score_rate < 100) return false;
      if (!rate.value.president_score_rate && rate.value.president_score_rate > 0 && rate.value.president_score_rate < 100) return false;
      if (!rate.value.secretary_score_rate && rate.value.secretary_score_rate > 0 && rate.value.secretary_score_rate < 100) return false;
      return !equalPrevSelected && checkTotalEqual100Percent.value;
    });

    const submitRate = () => {
      emit('change-rate', rate.value);
    };

    const prepareData = (rateLecturer) => {
      prevRate.value = defaults(omitBy(rateLecturer, isNull), {
        advisor_score_rate: 0,
        critical_score_rate: 0,
        president_score_rate: 0,
        secretary_score_rate: 0,
      });

      rate.value = cloneDeep(prevRate.value);
    };
    return {
      rate,
      prepareData,
      validateData,
      submitRate,
      checkTotalEqual100Percent,
    };
  },
};
</script>
<style>
.suffix::after {
  content: '%';
}
.score-rate-lecture {
  width: 300px !important;
}
</style>
