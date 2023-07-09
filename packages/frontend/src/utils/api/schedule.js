import axios from 'axios';
import urlWithPagination from '../generate_url';

const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

export default class ScheduleApi {
  static async listAllSchedule (token, options) {
    const url = urlWithPagination('/schedule', options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listScheduleToday (token) {
    const res = await axios.get('/schedule/student/today', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  // static async listScheduleToday (token) {
  //   const res = await axios.get('/schedule/student/today', {
  //     headers: {
  //       authorization: `bearer ${token}`,
  //     },
  //     baseURL: apiDest,
  //   });
  //   return res.data;
  // }

  static async updateScoreRate (token, id, value) {
    const res = await axios.put(`/schedule/${id}/score-rate`, value, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async listScheduleApproveLecturer (token, options) {
    const url = urlWithPagination('/schedule?is_approve_time=1', options);
    const res = await axios.get(url, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async addSchedule (token, value) {
    const res = await axios.post('/schedule', value, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async updateSchedule (token, value) {
    const res = await axios.put(`/schedule/${value._id}`, value, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async removeSchedule (token, id) {
    const res = await axios.delete(`/schedule/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async lecturerListScheduleTopic (token) {
    const res = await axios.get('/schedule-topic-lecturer', {
      headers: {
        authorization: `bearer ${token}`,
      },
    });
    return res.data;
  }

  static async lecturerListScheduleTopicShort (token) {
    const res = await axios.get('/schedule/with-topic?is_lecturer=1', {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data.data;
  }

  static async importTopic (token, id, xlsx) {
    const formData = new FormData();

    formData.append('xlsx', xlsx);
    const res = await axios.post(
      `/schedule/${id}/topic`,
      formData,
      {
        headers: {
          authorization: `bearer ${token}`,
          'Content-Type': 'multipart/form-data',
        },
      },
    );
    return res;
  }

  static async importStudent (token, id, xlsx) {
    const formData = new FormData();

    formData.append('file', xlsx);
    const res = await axios.post(
      `/schedule/${id}/student/import`,
      formData,
      {
        headers: {
          authorization: `bearer ${token}`,
          'Content-Type': 'multipart/form-data',
        },
      },
    );
    return res;
  }

  static async importListStudents (token, id, listStudents) {
    const res = await axios.put(`/schedule/${id}/student`, { students: listStudents }, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async fetchStudentsOfSchedule (token, id, options) {
    const url = urlWithPagination(`/schedule/${id}/student`, options);
    const res = await axios.get(
      url,
      {
        headers: {
          authorization: `bearer ${token}`,
        },
        baseURL: apiDest,
      },
    );
    return res.data;
  }

  static async fetchSchedule (token, id) {
    const res = await axios.get(`/schedule/${id}`, {
      headers: {
        authorization: `bearer ${token}`,
      },
      baseURL: apiDest,
    });
    return res.data;
  }

  static async exportExcel (token, id) {
    const timestamp = Date.now();
    const res = await axios.get(
      `/schedule/${id}/export?t=${timestamp}`,
      {
        headers: {
          authorization: `bearer ${token}`,
        },
      },
    ).then((response) => {
      const blob = new Blob([response.data], {
        type: 'application/xml',
      });

      // href = URL.createObjectURL(blob);

      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', 'ScheduleReport.xlsx');
      document.body.appendChild(link);
      link.click();
    });
    return res;
  }

  static async exportGradeExcel (token, id) {
    const timestamp = Date.now();
    const res = await axios.get(
      `/schedule/${id}/grade/export?t=${timestamp}`,
      {
        headers: {
          authorization: `bearer ${token}`,
        },
        responseType: 'blob',
        baseURL: apiDest,
      },
    );
    return res;
  }

  static async sendMailToCommitteeBySchedule (token, id) {
    const res = await axios.get(
      `/schedule/${id}/lecturer/mail`,
      {
        headers: {
          authorization: `bearer ${token}`,
        },
      },
    );
    return res.data;
  }
}
