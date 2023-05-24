const express = require('express');
const AdminBro = require('admin-bro');
const AdminBroSequelize = require('admin-bro-sequelize');
const { buildRouter } = require('admin-bro-expressjs');
const { Sequelize } = require('sequelize');
const initModel = require('../models');


// Set up database connection
const sequelize = new Sequelize('kltn', 'root', '123456', {
  host: 'localhost',
  dialect: 'mysql',
});

const { 
  committees,
  criteria,
  grades,
  migrations,
  notifications,
  permissions,
  role_has_permissions,
  roles,
  schedule_criteria,
  schedule_students,
  schedules,
  task_comments,
  tasks,
  topic_proposal_students,
  topic_proposals,
  topic_students,
  topics,
  user_has_roles,
  users, } = initModel(sequelize);
// Initialize Express app
const app = express();
const port = process.env.PORT || 5000;


// Register AdminBro Sequelize adapter
AdminBro.registerAdapter(AdminBroSequelize);

// Initialize AdminBro instance


// const UserResource = {
//   resource: User,
//   options: {
//     properties: {
//       gender: {
//         availableValues: [
//           { value: 'male', label: 'Nam' },
//           { value: 'female', label: 'Nu' },
//         ],
//       },
//       roles: {
//         isVisible: {
//           edit: true,
//           show: true,
//           list: false,
//           filter: false,
//         },
//         availableValues: async () => {
//           const roles = await Role.findAll();
//           return roles.map(role => ({ value: role.id, label: role.name }));
//         },
//       }
//     },
//   },
// };
// const CommitteeResource = {
//   resource: Committee,

// };
// const NotificationResource = {
//   resource: Notification,
//   options: {
//     properties: {
      
//     },
//   },
// };

// const PermissionResource = {
//   resource: Permission,
//   options: {
//     properties: {
      
//     },
//   },
// };

// const RoleResource = {
//   resource: Role,
//   options: {
//     properties: {
      
//     },
//   },
// };

// const ScheduleResource = {
//   resource: Schedule,
//   options: {
//     properties: {
      
//     },
//   },
// };

// const TaskCommentResource = {
//   resource: TaskComment,
//   options: {
//     properties: {
      
//     },
//   },
// };
// // const prepareData = (data) => {
// //   const schedules = await Schedule.findAll();
// //   console.log("🚀 ~ file: index.js:98 ~ schedules:", schedules)
// // }

// // console.log("🚀 ~ file: index.js:101 ~ listValueSchedule ~ listValueSchedule:", listValueSchedule)
// const TaskResource = {
//   resource: Task,
//   options: {
//     properties: {
      
//     },
//   },
// };

// const TopicProposalResource = {
//   resource: TopicProposal,
//   options: {
//     properties: {
      
//     },
//   },
// };

// const TopicResource = {
//   resource: Topic,
//   options: {
//     properties: {
//       schedule_id: {

//       }
//     },
//   },
// };


// const UserHasRoleResource = {
//   resource: UserHasRole,
//   options: {
//     isVisible: false,
//   },
// };

// const RoleHasPermissionResource = {
//   resource: RoleHasPermission,
//   options: {
//     isVisible: false,
//   },
// };

// const TopicProposalStudentResource = {
//   resource: TopicProposalStudent,
//   options: {
//     isVisible: false,
//   },
// };

// const TopicStudentResource = {
//   resource: TopicStudent,
//   options: {
//     isVisible: false,
//   },
// };

// const ScheduleStudentResource = {
//   resource: ScheduleStudent,
//   options: {
//     isVisible: false,
//   },
// };




const adminBroOptions = {
  resources: [
    { resource: committees},
    { resource: criteria},
    { resource: grades},
    { resource: migrations},
    { resource: notifications},
    { resource: permissions},
    { resource: role_has_permissions},
    { resource: roles},
    { resource: schedule_criteria},
    { resource: schedule_students},
    { resource: schedules},
    { resource: task_comments},
    { resource: tasks},
    { resource: topic_proposal_students},
    { resource: topic_proposals},
    { resource: topic_students},
    { resource: topics},
    { resource: user_has_roles},
    { resource: users},
  ],
  branding: {
    companyName: 'KLTN',
  },
};

const adminBro = new AdminBro({
  // databases: [sequelize],
  rootPath: '/',
  ...adminBroOptions,
});
adminBro.options = { ...adminBro.options, ...adminBroOptions };




// Build and use AdminBro route}r
const adminRouter = buildRouter(adminBro);
app.use(adminBro.options.rootPath, adminRouter);

// Start the server
app.listen(port, () => {
  console.log(`Server is running on http://localhost:${port}`);
});
