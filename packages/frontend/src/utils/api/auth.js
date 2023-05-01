import axios from 'axios';

//
const baseUrl = import.meta.env.BASE_API_URL || 'http://localhost:8001';
const apiDest = `${baseUrl}/api/v2`;
axios.defaults.baseURL = apiDest;

// export const signInWithGoogle = (access_token, type) => axios({
//   method: 'POST',
//   url: 'auth',
//   data: {
//     access_token, type,
//   },
// });

export const signInWithGoogle = (access_token, type) => axios({
  method: 'POST',
  baseURL: apiDest,
  url: 'auth/login',
  data: {
    access_token, type,
  },
});

export default signInWithGoogle;
