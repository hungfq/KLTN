<template>
  <template v-if="!open">
    <div class="py-2 mx-2 font-medium text-red-600 ">
      Hiện tại đang không có đợt kết quả đăng ký, vui lòng chọn mục khác!
    </div>
  </template>
  <template v-if="open">
    <div class="tabs tabs-boxed bg-white">
      <a
        v-for="option in headerTabs"
        :key="option"
        class="tab rounded-md"
        :class="{'tab-active' : option === tab}"
        @click="tab= option"
      >{{ option }}</a>
    </div>
    <div v-if="tab">
      <div class="flex flex-col">
        <div class="flex my-2">
          <div class="w-3/5 bg-white mx-4 border rounded-lg overflow-hidden">
            <div class="bg-gray-200 px-4 py-3 flex items-center justify-between">
              <div class="flex items-center">
                <h1 class="font-bold text-lg text-gray-800">
                  {{ currentTopic.title || '' }}
                </h1>
              </div>
            </div>     <!-- Body -->
            <div class="flex flex-col md:flex-row md:items-center px-4 py-3 bg-white">
              <div class="md:w-1/2">
                <LineItem
                  :title="'Mã đề tài: '"
                  :content="currentTopic.name"
                />
                <LineItem
                  :title="'Ngày phản biện: '"
                  :content="currentTopic.thesisDefenseDate || ''"
                />
                <LineItem
                  :title="'Mô tả: '"
                  :content="currentTopic.description"
                />
              </div>
              <div class="md:w-1/2">
                <LineItem
                  :title="'Đợt đăng ký: '"
                  :content="`${currentTopic.scheduleId.code}: ${currentTopic.scheduleId.name}`"
                />
                <LineItem
                  :title="'Giáo viên phản biện: '"
                  :content="currentTopic.criticalLecturerId?.name || ''"
                />
                <LineItem
                  :title="'Giáo viên hướng dẫn: '"
                  :content="currentTopic.lecturerId?.name || ''"
                />
              </div>
            </div>
          </div>
          <div class="w-2/5 flex flex-col">
            <!-- Danh sach sinh -->
            <div class="bg-white mx-4 border rounded-lg">
              <div class="bg-gray-200 px-4 py-3 flex items-center justify-between">
                <div class="flex items-center">
                  <h1 class="font-bold text-lg text-gray-800">
                    Danh sách thành viên
                  </h1>
                </div>
              </div>     <!-- Body -->
              <div class="flex flex-col md:flex-row md:items-center px-4 py-3 bg-white">
                <div class="md:w-1/2">
                  <ol class="list-decimal pl-8">
                    <li
                      v-for="student in currentTopic.students"
                      :key="student"
                    >
                      {{ student }}
                    </li>
                  </ol>
                </div>
              </div>
            </div>
            <!-- Phe duyet de tai -->
            <div class="bg-white mx-4 border rounded-lg my-2">
              <div class="bg-gray-200 px-4 py-3 flex items-center justify-between">
                <div class="flex items-center">
                  <h1 class="font-bold text-lg text-gray-800">
                    Phê duyệt ra hội đồng
                  </h1>
                </div>
              </div>     <!-- Body -->
              <div class="flex flex-col md:flex-row md:items-center px-4 py-3 bg-white">
                <div class="flex flex-col">
                  <LineItem
                    :title="'Giáo viên hướng dẫn: '"
                    :content="currentTopic.advisorLecturerApprove ? 'Đồng ý' : 'Chưa đồng ý'"
                  />
                  <LineItem
                    :title="'Giáo viên phản biện: '"
                    :content="currentTopic.criticalLecturerApprove ? 'Đồng ý' : 'Chưa đồng ý'"
                  />
                </div>
              </div>
            </div>
            <!-- Phe duyet de tai -->
            <div class="bg-white mx-4 border rounded-lg my-2">
              <div class="bg-gray-200 px-4 py-3 flex items-center justify-between">
                <div class="flex items-center">
                  <h1 class="font-bold text-lg text-gray-800">
                    Danh sách hội đồng
                  </h1>
                </div>
              </div>     <!-- Body -->
              <div class="flex flex-col md:flex-row md:items-center px-4 py-3 bg-white">
                <div class="md:w-1/2">
                  <ol class="list-decimal pl-8">
                    <li>Coffee</li>
                    <li>Tea</li>
                    <li>Milk</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-white mx-4 border rounded-lg overflow-hidden">
          <div class="bg-gray-200 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center">
              <h1 class="font-bold text-lg text-gray-800">
                Điểm số
              </h1>
            </div>
          </div>     <!-- Body -->
          <div class="flex flex-col md:flex-row md:items-center px-4 py-3 bg-white">
            <div>
              <LineItem
                :title="'Điểm của giáo viên hướng dẫn:'"
                :content="currentTopic.advisorLecturerGrade || 0 "
              />
              <LineItem
                :title="'Điểm của giáo viên phản biện:'"
                :content="currentTopic.criticalLecturerGrade || 0"
              />
              <LineItem
                :title="'Điểm của chủ tịch hội đồng:'"
                :content="currentTopic.committeePresidentGrade || 0"
              />
              <LineItem
                :title="'Điểm của thư ký hội đồng:'"
                :content="currentTopic.committeeSecretaryGrade || 0"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import 'vue-search-input/dist/styles.css';
import TopicApi from '../../utils/api/topic';
import LineItem from './LineItem.vue';

export default {
  name: 'ManageTopicStudent',
  components: {
    LineItem,
  },
  props: {
    open: {
      type: Boolean,
      default: true,
    },
  },
  data () {
    return {
      searchVal: '',
      currentScheduleId: '',
      listSchedules: [],
      topics: [],
      headerTabs: [],
      hashTopics: new Map(),
      tab: '',
      // currentTopic: null,
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: ({ auth: { isAuthenticated } }) => isAuthenticated,
    }),
    ...mapGetters('auth', [
      'userId', 'userEmail', 'userRole', 'token',
    ]),
    ...mapGetters('url', [
      'page', 'module', 'section', 'id',
    ]),
    ...mapGetters('student', [
      'studentId', 'studentEmail', 'student', 'listStudents',
    ]),
    ...mapGetters('schedule', [
      'listScheduleRegisterStudent',
    ]),
    currentTopic () {
      if (!this.tab) return null;
      return this.hashTopics.get(this.tab);
    },
  },
  async mounted () {
    const topicResult = await TopicApi.getResultRegister(this.token);
    topicResult.forEach((topic) => {
      const { scheduleId } = topic;
      if (!scheduleId || !scheduleId.code) return;
      this.hashTopics.set(scheduleId.code, topic);
    });
    this.headerTabs = [...this.hashTopics.keys()];
    if (this.headerTabs.length > 0) this.tab = this.headerTabs[0];
    this.topics = this.topicResult;
    // console.log('🚀 ~ file: ManageTopicResult.vue:211 ~ mounted ~ this.currentTopic:', this.currentTopic);
  },
};
</script>
