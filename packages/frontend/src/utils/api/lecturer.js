import axios from 'axios';

const apiDest = 'http://localhost:8001/api/v2';
axios.defaults.baseURL = apiDest;

export default class LectureApi {
  static async listAllLecturer (token) {
    const res = await axios.get('/user?type=LECTURER', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async addLecturer (token, value) {
    const {
      email, code, name, gender,
    } = value;
    const res = await axios.post('/user', {
      type: 'LECTURER',
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

  static async updateLecturer (token, value) {
    const {
      email, code, name, gender, id,
    } = value;
    const res = await axios.put(`/user/${id}`, {
      type: 'LECTURER',
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

  static async importLecturer (token, xlsx) {
    const formData = new FormData();

    formData.append('file', xlsx);
    formData.append('type', 'LECTURER');
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
