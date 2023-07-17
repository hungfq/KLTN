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
import dayjs from 'dayjs';
import 'dayjs/locale/vi'; // Set Vietnamese locale globally
import VueFileAgentNext from '@boindil/vue-file-agent-next';
import ganttastic from '@infectoone/vue-ganttastic';
/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core';

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { vi } from '@formkit/i18n';

import '@boindil/vue-file-agent-next/dist/vue-file-agent-next.css';

/* import specific icons */
import {
  faUserSecret, faTrashCan, faDownLeftAndUpRightToCenter, faArrowRightFromBracket,
  faLeftRight, faAnglesRight, faBan, faCheck, faBullseye, faEye, faPeopleGroup, faFileExport, faUserGraduate, faShieldHalved,
  faCirclePlus, faCalendarDays, faPersonChalkboard, faBook, faCrown, faPenToSquare, faListCheck, faScaleBalanced,
  faBell, faRightToBracket, faArrowLeft, faUserCheck, faDiagramPredecessor, faFileImport, faDownload, faPaperPlane, faFileArrowDown,
  faCloudDownload, faSearchLocation, faSearch
} from '@fortawesome/free-solid-svg-icons';
import Vue3Sanitize from 'vue-3-sanitize';

dayjs.locale('vi');
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
  faEye,
  faPeopleGroup,
  faFileExport,
  faCirclePlus,
  faCalendarDays,
  faPersonChalkboard,
  faUserGraduate,
  faShieldHalved,
  faBook,
  faCrown,
  faPenToSquare,
  faListCheck,
  faBell,
  faScaleBalanced,
  faRightToBracket,
  faArrowLeft,
  faBullseye,
  faUserCheck,
  faDiagramPredecessor,
  faFileImport,
  faDownload,
  faPaperPlane,
  faFileArrowDown,
  faCloudDownload,
  faSearchLocation,
  faSearch,
);

const app = createApp(App);
app.use(vue3GoogleLogin, {
  clientId: '425982821596-l2c683uo8ivvn2r3klbkjh110ura031u.apps.googleusercontent.com',
  scope: 'email profile openid',
});
app.use(router);
app.use(store);
app.use(Toaster);
app.use(plugin, defaultConfig({
  locales: { vi },
  // Define the active locale
  locale: 'vi',
}));
app.use(CKEditor);
app.component('EasyDataTable', Vue3EasyDataTable);
app.mount('#app');
app.use(VeeValidate);
app.use(ToastPlugin);
app.use(ganttastic);
app.component('FontAwesomeIcon', FontAwesomeIcon);
app.use(Vue3Sanitize);
app.use(vfmPlugin({
  key: '$vfm',
  componentName: 'VueFinalModal',
  dynamicContainerName: 'ModalsContainer',
}));

app.use(VueFileAgentNext);

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
  if (!/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i.test(value)) {
    return 'Bạn phải nhập một email hợp lệ'; // You must enter a valid email
  }

  return true; // Rule passes
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
