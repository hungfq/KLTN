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
}
