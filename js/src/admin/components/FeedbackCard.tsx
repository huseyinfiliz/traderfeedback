import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import avatar from 'flarum/common/helpers/avatar';
import humanTime from 'flarum/common/helpers/humanTime';

export default class FeedbackCard extends Component {
  view() {
    const { feedback, included, onApprove, onReject } = this.attrs;
    
    const fromUserId = feedback.relationships?.fromUser?.data?.id || feedback.relationships?.from?.data?.id;
    const toUserId = feedback.relationships?.toUser?.data?.id || feedback.relationships?.to?.data?.id;
    
    // Get from store or push to store if not exists
    let fromUser = fromUserId ? app.store.getById('users', fromUserId) : null;
    let toUser = toUserId ? app.store.getById('users', toUserId) : null;
    
    // If not in store, find in included and push to store
    if (!fromUser && fromUserId) {
      const fromData = included?.find((item: any) => item.type === 'users' && item.id === fromUserId);
      if (fromData) {
        fromUser = app.store.pushPayload({ data: fromData });
      }
    }
    
    if (!toUser && toUserId) {
      const toData = included?.find((item: any) => item.type === 'users' && item.id === toUserId);
      if (toData) {
        toUser = app.store.pushPayload({ data: toData });
      }
    }

    const typeClass = feedback.attributes.type === 'positive' ? 'success' : 
                     feedback.attributes.type === 'negative' ? 'danger' : 'warning';

    const typeIcon = feedback.attributes.type === 'positive' ? 'thumbs-up' : 
                    feedback.attributes.type === 'negative' ? 'thumbs-down' : 'minus';

    return (
      <div className={`FeedbackCard FeedbackCard--${feedback.attributes.type}`}>
        <div className="FeedbackCard-header">
          <div className="FeedbackCard-users">
            <div className="FeedbackCard-user">
              {fromUser && avatar(fromUser)}
              <div className="FeedbackCard-userInfo">
                <span className="FeedbackCard-label">From:</span>
                <strong>{fromUser ? fromUser.displayName() : `User #${fromUserId}`}</strong>
              </div>
            </div>
            <i className="fas fa-arrow-right FeedbackCard-arrow"></i>
            <div className="FeedbackCard-user">
              {toUser && avatar(toUser)}
              <div className="FeedbackCard-userInfo">
                <span className="FeedbackCard-label">To:</span>
                <strong>{toUser ? toUser.displayName() : `User #${toUserId}`}</strong>
              </div>
            </div>
          </div>
          
          <div className="FeedbackCard-meta">
            <span className={`FeedbackCard-type Badge Badge--${typeClass}`}>
              <i className={`fas fa-${typeIcon}`}></i>
              {feedback.attributes.type}
            </span>
            <span className="FeedbackCard-date">
              {humanTime(new Date(feedback.attributes.created_at))}
            </span>
          </div>
        </div>

        <div className="FeedbackCard-body">
          <div className="FeedbackCard-details">
            <div className="FeedbackCard-detail">
              <span className="FeedbackCard-detailLabel">Role:</span>
              <span className="FeedbackCard-detailValue">
                <i className="fas fa-user"></i>
                {feedback.attributes.role}
              </span>
            </div>
            
            {feedback.attributes.discussion_id && (
              <div className="FeedbackCard-detail">
                <span className="FeedbackCard-detailLabel">Discussion:</span>
                <span className="FeedbackCard-detailValue">
                  <i className="fas fa-comments"></i>
                  #{feedback.attributes.discussion_id}
                </span>
              </div>
            )}
          </div>

          {feedback.attributes.comment && (
            <div className="FeedbackCard-comment">
              <i className="fas fa-quote-left"></i>
              <p>{feedback.attributes.comment}</p>
            </div>
          )}
        </div>

        <div className="FeedbackCard-actions">
          <Button
            className="Button Button--primary"
            icon="fas fa-check"
            onclick={() => onApprove(feedback)}
          >
            Approve
          </Button>
          <Button
            className="Button Button--danger"
            icon="fas fa-times"
            onclick={() => onReject(feedback)}
          >
            Reject
          </Button>
        </div>
      </div>
    );
  }
}