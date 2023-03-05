import axios from 'axios';

const apiDest = 'http://localhost:8001/api/v1';
axios.defaults.baseURL = apiDest;

export default class AdminApi {
  static async listAllAdmin (token) {
    const res = await axios.get('/user?type=ADMIN', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v1',
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
      baseURL: 'http://localhost:8001/api/v1',
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
      baseURL: 'http://localhost:8001/api/v1',
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
        baseURL: 'http://localhost:8001/api/v1',
      },
    );
    return res;
  }
}
