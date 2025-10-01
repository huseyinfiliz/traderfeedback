import Model from 'flarum/common/Model';

export default class TraderStats extends Model {
  positiveCount = Model.attribute('positive_count');
  negativeCount = Model.attribute('negative_count');
  neutralCount = Model.attribute('neutral_count');
  score = Model.attribute('score');
  lastUpdated = Model.attribute('last_updated', Model.transformDate);
}