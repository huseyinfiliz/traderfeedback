import Model from 'flarum/common/Model';

export default class TraderStats extends Model {
  positiveCount = Model.attribute('positiveCount');
  negativeCount = Model.attribute('negativeCount');
  neutralCount = Model.attribute('neutralCount');
  score = Model.attribute('score');
  lastUpdated = Model.attribute('lastUpdated', Model.transformDate);
}
