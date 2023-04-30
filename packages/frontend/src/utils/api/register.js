import axios from 'axios';

const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001/';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class RegisterApi {
  static async registerTopic (token, studentId, topicId) {
    const res = await axios.post(`/${topicId}/register`, { }, {
      headers: {
        authorization: `Bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async studentViewRegister (token) {
    const res = await axios.get('/register', {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data;
  }

  static async studentCancelRegister (token, registerId) {
    const res = await axios.delete(`/${registerId}/register`, {
      headers: {
        authorization: `Bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listAllRegistration (token) {
    const res = await axios.get('/registration', {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data;
  }

  static async createRegistration (token, topicId, studentId, group) {
    const res = await axios.post('/registration', {
      topicId, studentId, group,
    }, {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data;
  }

  static async updateRegistration (token, registerId, topicId, studentId, group) {
    const res = await axios.put(`/registration/${registerId}`, {
      topicId, studentId, group,
    }, {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data;
  }

  static async deleteRegistration (token, registerId) {
    const res = await axios.get(`/registration/${registerId}`, {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data;
  }
}
