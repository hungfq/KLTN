import axios from 'axios';

const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class PermissionApi {
  static async getAllPermissions (token) {
    const res = await axios.get('/permission', {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }
}
