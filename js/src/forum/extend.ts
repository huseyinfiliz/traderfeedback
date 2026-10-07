import Extend from 'flarum/common/extenders';
import Forum from 'flarum/common/models/Forum';
import User from 'flarum/common/models/User';
import Feedback from '../common/models/Feedback';
import TraderStats from '../common/models/TraderStats';

export default [
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
    .attribute('canModerateFeedback'),
  new Extend.Store()
    .add('trader-feedbacks', Feedback)
    .add('trader-stats', TraderStats),
];
