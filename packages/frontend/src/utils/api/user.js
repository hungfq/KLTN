import axios from 'axios';

const apiDest = 'http://localhost:8001/api/v2';
axios.defaults.baseURL = apiDest;

export default class UserApi {
  static async listUser (token, type, option) {
    const {
      page,
      rowsPerPage,
      sortBy,
      sortType,
    } = option;

    const res = await axios.get(`/user?type=${type}&limit=${rowsPerPage}&sort[${sortBy}]=${sortType}&page=${page - 1}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
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
      picture: null,

    }, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async updateUser (token, value) {
    const {
      email, code, name, gender, id,
    } = value;
    const res = await axios.put(`/user/${id}`, {
      email,
      code,
      name,
      gender,
      picture: null,
    }, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
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
        baseURL: 'http://localhost:8001/api/v2',
      },
    );
    return res;
  }
}
