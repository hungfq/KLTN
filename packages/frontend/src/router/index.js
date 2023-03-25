import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../page/HomePage.vue';
import AdminPage from '../page/AdminPage.vue';
import LecturerPage from '../page/LecturerPage.vue';
import StudentPage from '../page/StudentPage.vue';
import Test from '../page/Test.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage,
  },
  {
    path: '/admin',
    name: 'admin.default',
    component: AdminPage,
  },
  {
    path: '/lecturer',
    name: 'lecturer.default',
    component: LecturerPage,
  },
  {
    path: '/student',
    name: 'student.default',
    component: StudentPage,
  },
  {
    path: '/test',
    name: 'Test',
    component: Test,
  },
];
const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
