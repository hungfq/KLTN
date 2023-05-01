import { createApp } from 'vue';
import './style.css';
import vue3GoogleLogin from 'vue3-google-login';
import { vfmPlugin } from 'vue-final-modal';
import Toaster from '@meforma/vue-toaster';
import { plugin, defaultConfig } from '@formkit/vue';
import CKEditor from '@ckeditor/ckeditor5-vue';
import Vue3EasyDataTable from 'vue3-easy-data-table';
import * as VeeValidate from 'vee-validate';
import { defineRule } from 'vee-validate';
import App from './App.vue';
import router from './router/index';
import store from './store/index';
import '@formkit/themes/genesis';
import 'vue3-easy-data-table/dist/style.css';
import ToastPlugin from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';
/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core';

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

/* import specific icons */
import {
  faUserSecret, faTrashCan, faDownLeftAndUpRightToCenter, faArrowRightFromBracket,
  faLeftRight, faAnglesRight, faBan, faCheck,
} from '@fortawesome/free-solid-svg-icons';

/* add icons to the library */
library.add(
  faUserSecret,
  faTrashCan,
  faDownLeftAndUpRightToCenter,
  faArrowRightFromBracket,
  faLeftRight,
  faAnglesRight,
  faBan,
  faCheck,
);

const app = createApp(App);
app.use(vue3GoogleLogin, {
  clientId: '425982821596-l2c683uo8ivvn2r3klbkjh110ura031u.apps.googleusercontent.com',
  scope: 'email profile openid',
});
app.use(router);
app.use(store);
app.use(Toaster);
app.use(plugin, defaultConfig);
app.use(CKEditor);
app.component('EasyDataTable', Vue3EasyDataTable);
app.mount('#app');
app.use(VeeValidate);
app.use(ToastPlugin);
app.component('FontAwesomeIcon', FontAwesomeIcon);

app.use(vfmPlugin({
  key: '$vfm',
  componentName: 'VueFinalModal',
  dynamicContainerName: 'ModalsContainer',
}));

// Define rules
defineRule('required', (value) => {
  if (!value || !value.length) {
    return 'Giá trị yêu cầu bắt buộc nhập';
  }
  return true;
});

defineRule('email', (value) => {
  // Field is empty, should pass
  if (!value || !value.length) {
    return true;
  }
  // Check if email
  if (!/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/.test(value)) {
    return 'Bạn phải nhập một email hợp lệ';
  }
  return true;
});

defineRule('minLength', (value, [limit]) => {
  // The field is empty so it should pass
  if (!value || !value.length) {
    return true;
  }
  if (value.length < limit) {
    return `Phải lớn hơn ${limit} kí tự`;
  }
  return true;
});

defineRule('maxLength', (value, [limit]) => {
  // The field is empty so it should pass
  if (!value || !value.length) {
    return true;
  }
  if (value.length > limit) {
    return `Phải nhỏ hơn ${limit} kí tự`;
  }
  return true;
});

defineRule('equalLength', (value, [limit]) => {
  // The field is empty so it should pass
  if (!value || !value.length) {
    return true;
  }
  if (value.length < limit || value.length > limit) {
    return `Phải bằng ${limit} kí tự`;
  }
  return true;
});

defineRule('minMax', (value, [min, max]) => {
  // The field is empty so it should pass
  if (!value || !value.length) {
    return true;
  }
  const numericValue = Number(value);
  if (numericValue < min) {
    return `Giá trị nhập vào phải nhỏ hơn ${min}`;
  }
  if (numericValue > max) {
    return `Giá trị nhập vào phải lớn hơn ${max}`;
  }
  return true;
});

defineRule('confirmed', (value, [target]) => {
  if (value === target) {
    return true;
  }
  return 'gía trị nhập vào phải giống nhau';
});
