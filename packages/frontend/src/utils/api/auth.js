import axios from 'axios';

// const apiDest = 'http://localhost:5000/v1';
// axios.defaults.baseURL = apiDest;

// export const signInWithGoogle = (access_token, type) => axios({
//   method: 'POST',
//   url: 'auth',
//   data: {
//     access_token, type,
//   },
// });

export const signInWithGoogle = (access_token, type) => axios({
  method: 'POST',
  baseURL: 'http://localhost:8001/api/v2/auth',
  url: 'login',
  data: {
    access_token, type,
  },
});

export default signInWithGoogle;
