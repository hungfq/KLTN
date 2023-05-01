import axios from 'axios';
import urlWithPagination from '../generate_url';



const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001/';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class CommitteeApi {
  static async listAllCommittee (token, options) {
    const url = urlWithPagination('/committee', options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async addCommittee (token, value) {
    const res = await axios.post('/committee', value, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async updateCommittee (token, id, value) {
    const res = await axios.put(`/committee/${id}`, value, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async deleteCommittee (token, id) {
    const res = await axios.delete(`/committee/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async importCommittee (token, xlsx) {
    const formData = new FormData();

    formData.append('xlsx', xlsx);
    const res = await axios.post(
      '/committee-import',
      formData,
      {
        headers: {
          authorization: `bearer ${token}`,
          'Content-Type': 'multipart/form-data',
        },
      },
    );
    return res;
  }
}
