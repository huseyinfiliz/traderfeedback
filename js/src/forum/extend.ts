import Extend from 'flarum/common/extenders';
import Forum from 'flarum/common/models/Forum';
import User from 'flarum/common/models/User';
import Feedback from '../common/models/Feedback';
import TraderStats from '../common/models/TraderStats';
import TraderFeedbackPage from './Pages/ProfilePage';
import UserPageResolver from 'flarum/forum/resolvers/UserPageResolver';

export default [
  new Extend.Routes().add('user.feedbacks', '/u/:username/feedbacks', TraderFeedbackPage, UserPageResolver),
  new Extend.Model(Forum)
    .attribute('huseyinfilizTraderAdmin')
    .attribute('huseyinfilizTraderUser'),
  new Extend.Model(User)
    .hasOne('traderStats')
    .hasMany('feedbacksReceived')
    .hasMany('feedbacksGiven')
    .attribute('canGiveFeedback')
    .attribute('canReportFeedback')
    .attribute('canDeleteFeedback')
    .attribute('canModerateFeedback')
    .attribute('traderFeedbackCount')
    .attribute('pendingFeedbackCount')
    .attribute('pendingReportCount'),
  new Extend.Store()
    .add('trader-feedbacks', Feedback)
    .add('trader-stats', TraderStats),
];

