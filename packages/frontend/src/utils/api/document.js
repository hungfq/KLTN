import axios from 'axios';

const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class DocumentApi {
  static async uploadDocuments (token, owner, file, title) {
    const formData = new FormData();

    formData.append('file', file);
    formData.append('owner', owner);
    formData.append('title', title);
    const res = await axios.post(
      '/documents',
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

  static async listAllDocsByOwner (token, owner) {
    const url = `/documents?owner=${owner}`;
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async deleteDocument (token, id) {
    const url = '/documents';
    const res = await axios.delete(url, {
      data: [id],
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async getFile (token, id) {
    const url = `documents/${id}/download`;
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      responseType: 'blob',
      baseURL: apiDest,
    });
    return res;
  }

  static async getAllFile (token, ownerId) {
    const url = `/documents/zip?owner=${ownerId}`;
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      responseType: 'blob',
      baseURL: apiDest,
    });
    return res;
  }
}
