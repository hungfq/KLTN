import axios from 'axios';
import urlWithPagination from '../generate_url';

const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class CriteriaApi {
  static async listAll (token, options) {
    const url = urlWithPagination('/criteria', options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async fetchOne (token, id) {
    const res = await axios.get(`/criteria/${id}`, {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data.data;
  }

  static async delete (token, id) {
    const res = await axios.delete(`/criteria/${id}`, {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data.data;
  }

  static async update (token, value) {
    const { id } = value;
    const res = await axios.put(`/criteria/${id}`, value, {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data.data;
  }

  static async create (token, value) {
    const res = await axios.post('/criteria', value, {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data.data;
  }
}
