import axios from 'axios';

const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001/';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class RolesApi {
  static async getAllRoles (token) {
    const res = await axios.get('/roles', {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async getPermissionsOfRole (token, roleId) {
    const res = await axios.get(`/roles/${roleId}/permissions`, {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async updatePermissions (token, roleId, permissions) {
    const res = await axios.put(`/roles/${roleId}/permissions`, {
      permissions,
    }, {
      headers: {
        authorization: `Bearer ${token}`,
      },
    });
    return res.data;
  }
}
