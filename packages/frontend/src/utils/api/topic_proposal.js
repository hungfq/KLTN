import axios from 'axios';

const apiDest = 'http://localhost:5000/v1';
axios.defaults.baseURL = apiDest;

export default class TopicProposalApi {
  static async listAllTopicsByLecturer (token, scheduleId) {
    const res = await axios.get(`/topic-proposal/lecturer?scheduleId=${scheduleId}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async listAllTopicsByAdmin (token) {
    const res = await axios.get('/topic-proposal/admin', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:5000/v1',
    });
    return res.data;
  }

  static async listAllTopicsByCreated (token, scheduleId) {
    const res = await axios.get(`/topic-proposal?created=1&scheduleId=${scheduleId}`, {
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
    const res = await axios.delete(`/topic-proposal-decline/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async approveTopicProposalByLecturer (token, id) {
    const res = await axios.get(`/approve/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async approveTopicProposalByAdmin (token, value) {
    const res = await axios.post(`/topic-proposal/approve/${value.id}`, value, {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }
}
