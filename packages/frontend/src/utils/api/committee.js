import axios from 'axios';

const apiDest = 'http://localhost:8001/api/v2';
axios.defaults.baseURL = apiDest;

export default class CommitteeApi {
  static async listAllCommittee (token) {
    const res = await axios.get('/committee', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async addCommittee (token, value) {
    const res = await axios.post('/committee', value, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async updateCommittee (token, id, value) {
    const res = await axios.put(`/committee/${id}`, value, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async deleteCommittee (token, id) {
    const res = await axios.delete(`/committee/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
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
