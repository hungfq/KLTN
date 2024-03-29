import axios from 'axios';
import urlWithPagination from '../generate_url';

const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class UserApi {
  static async listUser (token, type, option) {
    const url = urlWithPagination(`/user?type=${type}`, option);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async addUser (token, value, type) {
    const {
      email, code, name, gender,
    } = value;
    const res = await axios.post('/user', {
      type,
      email,
      code,
      name,
      gender,

    }, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async updateUser (token, value) {
    const {
      email, code, name, gender, _id, status,
    } = value;
    const res = await axios.put(`/user/${_id}`, {
      email,
      code,
      name,
      gender,
      picture: null,
      status,
    }, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async importUser (token, xlsx, type) {
    const formData = new FormData();

    formData.append('file', xlsx);
    formData.append('type', type);
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
