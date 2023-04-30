import axios from 'axios';
import urlWithPagination from '../generate_url';

const apiDest = 'http://localhost:5000/v1';
axios.defaults.baseURL = apiDest;

export default class TopicProposalApi {
  static async listAllTopicsByLecturer (token, options, scheduleId) {
    let url = urlWithPagination('/topic-proposal?is_lecturer=1', options);
    if (scheduleId) url += `&scheduleId=${scheduleId}`;
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }

  static async listAllTopicsByCreated (token, scheduleId) {
    const res = await axios.get(`/topic-proposal?is_created=1&scheduleId=${scheduleId}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async addTopicProposal (token, value) {
    const res = await axios.post('/topic-proposal/', value, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async updateTopicProposal (token, value) {
    const res = await axios.put(`/topic-proposal/${value._id}`, value, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async removeTopicProposal (token, id) {
    const res = await axios.delete(`/topic-proposal/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async declineTopicProposal (token, id) {
    const res = await axios.post(`/topic-proposal/${id}/lecturer/decline`, {}, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }

  static async approveTopicProposalByLecturer (token, id) {
    const res = await axios.post(`/topic-proposal/${id}/lecturer/approve`, {}, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data;
  }
}
