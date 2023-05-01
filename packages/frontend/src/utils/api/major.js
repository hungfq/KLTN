import axios from 'axios';

const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class MajorApi {
  static async listAllMajor (token) {
    const res = await axios.get('/majors', {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async getMajor (token) {
    const res = await axios.get('/majors', {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data;
  }

  static async createMajor (token, name, description) {
    const res = await axios.post('/majors', {
      name, description,
    }, {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data;
  }

  static async updateMajor (token, id, name, description) {
    const res = await axios.put(`/majors/${id}`, {
      name, description,
    }, {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data;
  }
}
