import axios from 'axios';

const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001/';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class AdminApi {
  static async listAllAdmin (token) {
    const res = await axios.get('/user?type=ADMIN', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async addAdmin (token, value) {
    const {
      email, code, name, gender,
    } = value;
    const res = await axios.post('/user', {
      type: 'ADMIN',
      email,
      code,
      name,
      gender,
      picture: null,

    }, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async updateAdmin (token, value) {
    const {
      email, code, name, gender, id,
    } = value;
    const res = await axios.put(`/user/${id}`, {
      type: 'ADMIN',
      email,
      code,
      name,
      gender,
      picture: null,
    }, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async importAdmin (token, xlsx) {
    const formData = new FormData();

    formData.append('file', xlsx);
    formData.append('type', 'ADMIN');
    const res = await axios.post(
      '/user/import',
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
