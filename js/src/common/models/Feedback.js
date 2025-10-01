import Model from 'flarum/common/Model';

export default class Feedback extends Model {
  // Attributes
  type = Model.attribute('type');
  comment = Model.attribute('comment');
  role = Model.attribute('role');
  isApproved = Model.attribute('isApproved');
  createdAt = Model.attribute('createdAt', Model.transformDate);
  
  // Relationships
  fromUser = Model.hasOne('fromUser');
  toUser = Model.hasOne('toUser');
  //discussion = Model.hasOne('discussion');
}