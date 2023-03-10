/* eslint-disable max-len */
const fs = require('fs');
const { upload } = require('../utils/file');
const {
  importTopics, insertTopic, listTopic, updateOneTopic, deleteOneTopic,
  findOneTopic, updateTopicStudent, updateTopicLecturer,
  addProposalTopic, listProposalTopic, approveProposalTopic, removeProposalTopic,
  listTopicReviewByLecturer, listTopicProposalByCreatedId, updateProposalByUser,
  approveProposalByLecturer, listTopicReviewByAdmin, getListTopicAcceptRegister,
  addNewRegisterTopicStudent, getResultRegister, getTopicMember, removeRegisterTopicStudent,
  addNewRegisterTopicStudentNew, getTopicStudent,
  listTopicAdvisorApprove,
  listTopicCriticalApprove,
  topicAdvisorApprove,
  topicCriticalApprove,
  declineProposalTopic,
  listTopicCommitteeApprove,
  listTopicToMark,
} = require('../controller/topic.controller');

const authMiddleware = require('../middlewares/auth.middleware');
// const roleMiddleware = require('../middlewares/role.middlewares');

const { isAuth } = authMiddleware;
// const { permit } = roleMiddleware;

const router = (app) => {
  // Topic
  app.post('/v1/topic-import/', upload.single('xlsx'), importTopics); // done
  app.post('/v1/topic', isAuth, insertTopic); // done
  app.get('/v1/topic/:id', isAuth, findOneTopic); // done
  app.put('/v1/topic/:id', isAuth, updateOneTopic); // done
  app.delete('/v1/topic/:id', isAuth, deleteOneTopic); // done
  app.get('/v1/topic', listTopic); // public api in homepage // done
  app.get('/v1/topic/:id/students', getTopicStudent); // done

  // Result register of student
  app.get('/v1/topic/student/result', isAuth, getResultRegister); // done

  // Register
  app.delete('/v1/register/:id', isAuth, removeRegisterTopicStudent); // done
  app.post('/v1/register/:id', isAuth, addNewRegisterTopicStudentNew); // done

  // Proposal Topic
  app.post('/v1/topic-proposal', isAuth, addProposalTopic); // done
  app.post('/v1/topic-proposal/approve/:id', isAuth, approveProposalTopic);
  app.delete('/v1/topic-proposal/:id', isAuth, removeProposalTopic); // done
  app.delete('/v1/topic-proposal-decline/:id', isAuth, declineProposalTopic);
  app.put('/v1/topic-proposal/:id', isAuth, updateProposalByUser); // dơn
  app.get('/v1/topic-proposal/lecturer', isAuth, listTopicReviewByLecturer); // gom
  app.get('/v1/topic-proposal/admin', isAuth, listTopicReviewByAdmin); // gom
  app.get('/v1/topic-proposal/created', isAuth, listTopicProposalByCreatedId); // gom

  // Topic teacher
  app.get('/v1/topic-advisor', isAuth, listTopicAdvisorApprove);
  app.get('/v1/topic-advisor/approve/:id', isAuth, topicAdvisorApprove);
  app.get('/v1/topic-critical', isAuth, listTopicCriticalApprove);
  app.get('/v1/topic-critical/approve/:id', isAuth, topicCriticalApprove);

  // Committee
  app.get('/v1/topic-committee/', isAuth, listTopicToMark);
  app.get('/v1/topic-committee/:id', isAuth, listTopicCommitteeApprove);

  // Download template
  app.get('/template/Topic', (req, res) => {
    const file = fs.createReadStream('public/template/TopicTemplate.xlsx');
    const stat = fs.statSync('public/template/TopicTemplate.xlsx');
    res.setHeader('Content-Length', stat.size);
    res.setHeader('Content-Type', 'application/xml');
    res.setHeader('Content-Disposition', 'attachment; filename=Topic.xlsx');
    file.pipe(res);
  });
  // app.put('/v1/topic-student/:id', isAuth, updateTopicStudent);
  // app.post('/v1/topic-student/:id', isAuth, addNewRegisterTopicStudent);
  // app.put('/v1/topic-lecturer/:id', isAuth, updateTopicLecturer);
  // app.get('/v1/topic/:id/members', getTopicMember);
  // app.get('/v1/topic-proposal', isAuth, listProposalTopic);
  // app.get('/v1/topic-proposal/student', isAuth, getListTopicAcceptRegister);
  // app.get('/v1/approve/:id', isAuth, approveProposalByLecturer);
};

module.exports = router;
