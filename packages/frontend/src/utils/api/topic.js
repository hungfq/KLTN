import axios from 'axios';
import urlWithPagination from '../generate_url';

const apiDest = 'http://localhost:5000/v1';
axios.defaults.baseURL = apiDest;

export default class TopicApi {
  static async listAllTopics (token, options) {
    const url = urlWithPagination('/topic', options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }

  static async getTopic (token, id) {
    const res = await axios.get(`/topic/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async listAllTopicsByLecturerId (token, lecturerId, options) {
    const url = urlWithPagination(`/topic?lecturerId=${lecturerId}`, options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }

  static async listAllTopicsByCritical (token, criticalId, options) {
    const url = urlWithPagination(`/topic?criticalId=${criticalId}`, options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }

  static async listAllTopicsByCriticalAndScheduleId (token, criticalId, scheduleId, options) {
    const url = urlWithPagination(`/topic?criticalId=${criticalId}&scheduleId=${scheduleId}`, options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }

  static async listAllTopicsByLecturerIdAndScheduleId (token, lecturerId, scheduleId, options) {
    const url = urlWithPagination(`/topic?lecturerId=${lecturerId}&scheduleId=${scheduleId}`, options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }

  static async listAllTopicsByScheduleId (token, scheduleId) {
    const res = await axios.get(`/topic?scheduleId=${scheduleId}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
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
      baseURL: 'http://localhost:8001/api/v2',
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
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async updateTopicById (token, value) {
    const res = await axios.put(`/topic/${value._id}`, value, {
      headers: {
        authorization: `Bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async deleteTopicById (token, id) {
    const res = await axios.delete(`/topic/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
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
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }

  static async removeRegisterTopicStudent (token, id) {
    const res = await axios.delete(`/topic/${id}/register`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }

  static async getResultRegister (token) {
    const res = await axios.get('/topic/student/result', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
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
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async listTopicAdvisorApprove (token) {
    const res = await axios.get('/topic?is_lecturer_approve=1', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async listTopicCriticalApprove (token) {
    const res = await axios.get('/topic?is_critical_approve=1', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async topicAdvisorApprove (token, id) {
    const res = await axios.post(`/topic/${id}/lecturer/approve`, {}, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }

  static async topicCriticalApprove (token, id) {
    const res = await axios.post(`/topic/${id}/critical/approve`, {}, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }

  static async topicCommitteeByCritical (token, id) {
    const res = await axios.get('/topic?as_least_lecturer_approve=1', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
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
        baseURL: 'http://localhost:8001/api/v2',
      },
    );
    return res;
  }
}
