var DataTypes = require("sequelize").DataTypes;
var _committees = require("./committees");
var _criteria = require("./criteria");
var _grades = require("./grades");
var _migrations = require("./migrations");
var _notifications = require("./notifications");
var _permissions = require("./permissions");
var _role_has_permissions = require("./role_has_permissions");
var _roles = require("./roles");
var _schedule_criteria = require("./schedule_criteria");
var _schedule_students = require("./schedule_students");
var _schedules = require("./schedules");
var _task_comments = require("./task_comments");
var _tasks = require("./tasks");
var _topic_proposal_students = require("./topic_proposal_students");
var _topic_proposals = require("./topic_proposals");
var _topic_students = require("./topic_students");
var _topics = require("./topics");
var _user_has_roles = require("./user_has_roles");
var _users = require("./users");

function initModels(sequelize) {
  var committees = _committees(sequelize, DataTypes);
  var criteria = _criteria(sequelize, DataTypes);
  var grades = _grades(sequelize, DataTypes);
  var migrations = _migrations(sequelize, DataTypes);
  var notifications = _notifications(sequelize, DataTypes);
  var permissions = _permissions(sequelize, DataTypes);
  var role_has_permissions = _role_has_permissions(sequelize, DataTypes);
  var roles = _roles(sequelize, DataTypes);
  var schedule_criteria = _schedule_criteria(sequelize, DataTypes);
  var schedule_students = _schedule_students(sequelize, DataTypes);
  var schedules = _schedules(sequelize, DataTypes);
  var task_comments = _task_comments(sequelize, DataTypes);
  var tasks = _tasks(sequelize, DataTypes);
  var topic_proposal_students = _topic_proposal_students(sequelize, DataTypes);
  var topic_proposals = _topic_proposals(sequelize, DataTypes);
  var topic_students = _topic_students(sequelize, DataTypes);
  var topics = _topics(sequelize, DataTypes);
  var user_has_roles = _user_has_roles(sequelize, DataTypes);
  var users = _users(sequelize, DataTypes);

  permissions.belongsToMany(roles, { as: 'role_id_roles', through: role_has_permissions, foreignKey: "permission_id", otherKey: "role_id" });
  roles.belongsToMany(permissions, { as: 'permission_id_permissions', through: role_has_permissions, foreignKey: "role_id", otherKey: "permission_id" });
  roles.belongsToMany(users, { as: 'user_id_users', through: user_has_roles, foreignKey: "role_id", otherKey: "user_id" });
  schedules.belongsToMany(users, { as: 'student_id_users', through: schedule_students, foreignKey: "schedule_id", otherKey: "student_id" });
  topic_proposals.belongsToMany(users, { as: 'student_id_users_topic_proposal_students', through: topic_proposal_students, foreignKey: "topic_id", otherKey: "student_id" });
  topics.belongsToMany(users, { as: 'student_id_users_topic_students', through: topic_students, foreignKey: "topic_id", otherKey: "student_id" });
  users.belongsToMany(roles, { as: 'role_id_roles_user_has_roles', through: user_has_roles, foreignKey: "user_id", otherKey: "role_id" });
  users.belongsToMany(schedules, { as: 'schedule_id_schedules', through: schedule_students, foreignKey: "student_id", otherKey: "schedule_id" });
  users.belongsToMany(topic_proposals, { as: 'topic_id_topic_proposals', through: topic_proposal_students, foreignKey: "student_id", otherKey: "topic_id" });
  users.belongsToMany(topics, { as: 'topic_id_topics', through: topic_students, foreignKey: "student_id", otherKey: "topic_id" });
  topics.belongsTo(committees, { as: "committee", foreignKey: "committee_id"});
  committees.hasMany(topics, { as: "topics", foreignKey: "committee_id"});
  grades.belongsTo(criteria, { as: "criterion", foreignKey: "criteria_id"});
  criteria.hasMany(grades, { as: "grades", foreignKey: "criteria_id"});
  schedule_criteria.belongsTo(criteria, { as: "criterion", foreignKey: "criteria_id"});
  criteria.hasMany(schedule_criteria, { as: "schedule_criteria", foreignKey: "criteria_id"});
  role_has_permissions.belongsTo(permissions, { as: "permission", foreignKey: "permission_id"});
  permissions.hasMany(role_has_permissions, { as: "role_has_permissions", foreignKey: "permission_id"});
  role_has_permissions.belongsTo(roles, { as: "role", foreignKey: "role_id"});
  roles.hasMany(role_has_permissions, { as: "role_has_permissions", foreignKey: "role_id"});
  user_has_roles.belongsTo(roles, { as: "role", foreignKey: "role_id"});
  roles.hasMany(user_has_roles, { as: "user_has_roles", foreignKey: "role_id"});
  schedule_criteria.belongsTo(schedules, { as: "schedule", foreignKey: "schedule_id"});
  schedules.hasMany(schedule_criteria, { as: "schedule_criteria", foreignKey: "schedule_id"});
  schedule_students.belongsTo(schedules, { as: "schedule", foreignKey: "schedule_id"});
  schedules.hasMany(schedule_students, { as: "schedule_students", foreignKey: "schedule_id"});
  topics.belongsTo(schedules, { as: "schedule", foreignKey: "schedule_id"});
  schedules.hasMany(topics, { as: "topics", foreignKey: "schedule_id"});
  task_comments.belongsTo(tasks, { as: "task", foreignKey: "task_id"});
  tasks.hasMany(task_comments, { as: "task_comments", foreignKey: "task_id"});
  topic_proposal_students.belongsTo(topic_proposals, { as: "topic", foreignKey: "topic_id"});
  topic_proposals.hasMany(topic_proposal_students, { as: "topic_proposal_students", foreignKey: "topic_id"});
  grades.belongsTo(topics, { as: "topic", foreignKey: "topic_id"});
  topics.hasMany(grades, { as: "grades", foreignKey: "topic_id"});
  tasks.belongsTo(topics, { as: "topic", foreignKey: "topic_id"});
  topics.hasMany(tasks, { as: "tasks", foreignKey: "topic_id"});
  topic_students.belongsTo(topics, { as: "topic", foreignKey: "topic_id"});
  topics.hasMany(topic_students, { as: "topic_students", foreignKey: "topic_id"});
  grades.belongsTo(users, { as: "student", foreignKey: "student_id"});
  users.hasMany(grades, { as: "grades", foreignKey: "student_id"});
  notifications.belongsTo(users, { as: "to", foreignKey: "to_id"});
  users.hasMany(notifications, { as: "notifications", foreignKey: "to_id"});
  schedule_students.belongsTo(users, { as: "student", foreignKey: "student_id"});
  users.hasMany(schedule_students, { as: "schedule_students", foreignKey: "student_id"});
  topic_proposal_students.belongsTo(users, { as: "student", foreignKey: "student_id"});
  users.hasMany(topic_proposal_students, { as: "topic_proposal_students", foreignKey: "student_id"});
  topic_students.belongsTo(users, { as: "student", foreignKey: "student_id"});
  users.hasMany(topic_students, { as: "topic_students", foreignKey: "student_id"});
  user_has_roles.belongsTo(users, { as: "user", foreignKey: "user_id"});
  users.hasMany(user_has_roles, { as: "user_has_roles", foreignKey: "user_id"});

  return {
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
    users,
  };
}
module.exports = initModels;
module.exports.initModels = initModels;
module.exports.default = initModels;
