import axios from 'axios';

const apiDest = 'http://localhost:8001/api/v2';
axios.defaults.baseURL = apiDest;

export default class NotificationApi {
  static async listAllNotification (token) {
    const res = await axios.get('/notification', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async readNotification (token, id) {
    const res = await axios.put(`/notification/${id}`, {}, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }

  static async deleteNotification (token, id) {
    const res = await axios.delete(`/notification/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: 'http://localhost:8001/api/v2',
    });
    return res.data.data;
  }
}
