import axios from 'axios';
import urlWithPagination from '../generate_url';

const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class TopicApi {
  static async listAllTopics (token, options, lecturerId, scheduleId) {
    let url = urlWithPagination('/topic', options);
    if (lecturerId) url += `&lecturerId=${lecturerId}`;
    if (scheduleId) url += `&scheduleId=${scheduleId}`;
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async getTopic (token, id) {
    const res = await axios.get(`/topic/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async listAllTopicsByLecturerId (token, lecturerId, options) {
    const url = urlWithPagination(`/topic?lecturerId=${lecturerId}`, options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listAllTopicsMarkGrade (token, options, type, scheduleId) {
    let url = urlWithPagination('/topic', options);
    let lecturer = 'is_lecturer_mark=1';
    if (type === 'CR') lecturer = 'is_critical_mark=1';
    else if (type === 'PD') lecturer = 'is_president_mark=1';
    else if (type === 'SE') lecturer = 'is_secretary_mark=1';
    url += `&${lecturer}`;
    if (scheduleId) url += `&scheduleId=${scheduleId}`;
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listAllTopicsByCritical (token, criticalId, options) {
    const url = urlWithPagination(`/topic?criticalId=${criticalId}`, options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listAllTopicsByCriticalAndScheduleId (token, criticalId, scheduleId, options) {
    let url = urlWithPagination('/topic', options);
    if (criticalId) url += `&criticalId=${criticalId}`;
    if (scheduleId) url += `&scheduleId=${scheduleId}`;
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async updateGradeForTopic (token, topicId, value) {
    const res = await axios.put(`/topic/${topicId}/grade`, value, {
      headers: {
        authorization: `Bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async getGradeForTopicByLecturer (token, topicId, type) {
    const res = await axios.get(`/topic/${topicId}/grade?type=${type}`, {
      headers: {
        authorization: `Bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listAllTopicsByLecturerIdAndScheduleId (token, lecturerId, scheduleId, options) {
    const url = urlWithPagination(`/topic?lecturerId=${lecturerId}&scheduleId=${scheduleId}`, options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listAllTopicsByScheduleIds (token, scheduleIds, options) {
    const scheduleParams = scheduleIds.join(',');
    const url = urlWithPagination(`/topic?schedule_ids[]=${scheduleParams}`, options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listAllTopicsByScheduleId (token, scheduleId, options) {
    const url = urlWithPagination(`/topic?scheduleId=${scheduleId}`, options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listTopicWithName (token, value, type) {
    const res = await axios.get(`/topic-search?value=${value}&type=${type}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async listTopicById (token, id) {
    const res = await axios.get(`/topic/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async listAllTopicsWithMajor (token, majorId) {
    const res = await axios.get(`/topic?majorId=${majorId}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async listTopicSearch (token, value) {
    const res = await axios.get(`/topic-search?value=${value}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async listTopicSearchWithTitle (token, value) {
    const res = await axios.get(`/topic-search?type=title&value=${value}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async listTopicSearchWithNameTeacher (token, nameTeacher) {
    const res = await axios.get(`/topic-search?type=teacher&value=${nameTeacher}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async createTopic (token, value) {
    const res = await axios.post('/topic', value, {
      headers: {
        authorization: `Bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async updateTopicById (token, value) {
    const res = await axios.put(`/topic/${value._id}`, value, {
      headers: {
        authorization: `Bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async deleteTopicById (token, id) {
    const res = await axios.delete(`/topic/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async listTopicAcceptRegisters (token) {
    const res = await axios.get('/topic-proposal/student', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:5000/v1',
    });
    return res.data;
  }

  static async addRegisterTopic (token, id) {
    const res = await axios.post(`/topic-student/${id}`, {}, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:5000/v1',
    });
    return res.data;
  }

  static async addRegisterTopicNew (token, id) {
    const res = await axios.post(`/topic/${id}/register`, {}, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async removeRegisterTopicStudent (token, id) {
    const res = await axios.delete(`/topic/${id}/register`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async getResultRegister (token) {
    const res = await axios.get('/topic/student/result', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async listTopicMember (token, topicId) {
    const res = await axios.get(`/topic/${topicId}/members`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:5000/v1',
    });
    return res.data;
  }

  static async listTopicStudents (token, topicId) {
    const res = await axios.get(`/topic/${topicId}/students`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async listTopicAdvisorApprove (token, options, scheduleId) {
    let url = urlWithPagination('/topic?is_lecturer_approve=1', options);
    if (scheduleId) url += `&schedule_id=${scheduleId}`;
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listTopicCriticalApprove (token, options, scheduleId) {
    let url = urlWithPagination('/topic?is_critical_approve=1', options);
    if (scheduleId) url += `&schedule_id=${scheduleId}`;
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async topicAdvisorApprove (token, id) {
    const res = await axios.post(`/topic/${id}/lecturer/approve`, {}, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async topicAdvisorDecline (token, id) {
    const res = await axios.delete(`/topic/${id}/lecturer/decline`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async topicCriticalApprove (token, id) {
    const res = await axios.post(`/topic/${id}/critical/approve`, {}, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async topicCriticalDecline (token, id) {
    const res = await axios.delete(`/topic/${id}/critical/decline`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async topicCommitteeByCritical (token, id) {
    const res = await axios.get('/topic?as_least_lecturer_approve=1', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async importTopic (token, xlsx) {
    const formData = new FormData();

    formData.append('file', xlsx);
    const res = await axios.post(
      '/topic/import',
      formData,
      {
        headers: {
          authorization: `bearer ${token}`,
          'Content-Type': 'multipart/form-data',
        },
        baseURL: apiDest,
      },
    );
    return res;
  }
}
