import axios from 'axios';
import urlWithPagination from '../generate_url';

const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class TopicProposalApi {
  static async listAllTopicsByLecturer (token, options, scheduleId) {
    let url = urlWithPagination('/topic-proposal?is_lecturer=1&is_approve_time=1', options);
    if (scheduleId) url += `&scheduleId=${scheduleId}`;
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listAllTopicsByCreated (token, scheduleId, options) {
    let rawUrl = '/topic-proposal?is_created=1';
    if (scheduleId) rawUrl += `&scheduleId=${scheduleId}`;
    const url = urlWithPagination(rawUrl, options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async addTopicProposal (token, value) {
    const {
      title,
      limit,
      description,
      deadline,
      students,
      lecturerId,
      scheduleId,
      status,
      files,
    } = value;
    const formData = new FormData();

    formData.append('title', title);
    formData.append('limit', limit);
    formData.append('description', description);
    formData.append('deadline', deadline);
    formData.append('lecturerId', lecturerId);
    formData.append('scheduleId', scheduleId);
    formData.append('status', status);

    for (let i = 0; i < students.length; i += 1) {
      formData.append(`students[${i}]`, students[i]);
    }
    for (let i = 0; i < files.length; i += 1) {
      formData.append(`files[${i}]`, files[i].file);
    }
    const res = await axios.post('/topic-proposal/', formData, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async updateTopicProposal (token, value) {
    const res = await axios.put(`/topic-proposal/${value._id}`, value, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async removeTopicProposal (token, id) {
    const res = await axios.delete(`/topic-proposal/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async getTopicProposal (token, id) {
    const res = await axios.get(`/topic-proposal/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async declineTopicProposal (token, id) {
    const res = await axios.post(`/topic-proposal/${id}/lecturer/decline`, {}, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async approveTopicProposalByLecturer (token, id) {
    const res = await axios.post(`/topic-proposal/${id}/lecturer/approve`, {}, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }
}
