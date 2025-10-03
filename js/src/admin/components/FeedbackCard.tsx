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
    
    let fromUser = fromUserId ? app.store.getById('users', fromUserId) : null;
    let toUser = toUserId ? app.store.getById('users', toUserId) : null;
    
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

    const badgeClass = feedback.attributes.type === 'positive' ? 'Badge--success' : 
                      feedback.attributes.type === 'negative' ? 'Badge--danger' : 'Badge--warning';

    const typeIcon = feedback.attributes.type === 'positive' ? 'thumbs-up' : 
                    feedback.attributes.type === 'negative' ? 'thumbs-down' : 'minus';

    const roleIcon = feedback.attributes.role === 'buyer' ? 'shopping-cart' :
                    feedback.attributes.role === 'seller' ? 'store' : 'exchange-alt';

    return (
      <div className={`FeedbackCard FeedbackCard--${feedback.attributes.type}`}>
        <div className="FeedbackCard-header">
          <div className="FeedbackCard-users">
            <div className="FeedbackCard-user">
              {fromUser && avatar(fromUser)}
              <strong>
                {fromUser 
                  ? fromUser.displayName() 
                  : app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.user_id_format', { id: fromUserId })
                }
              </strong>
            </div>
            <i className="fas fa-arrow-right FeedbackCard-arrow"></i>
            <div className="FeedbackCard-user">
              {toUser && avatar(toUser)}
              <strong>
                {toUser 
                  ? toUser.displayName() 
                  : app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.user_id_format', { id: toUserId })
                }
              </strong>
            </div>
          </div>
          
          <div className="FeedbackCard-meta">
            <span className={`Badge ${badgeClass}`}>
              <i className={`fas fa-${typeIcon}`}></i>
            </span>
            
            <span className="FeedbackCard-roleBadge">
              <i className={`fas fa-${roleIcon}`}></i>
              <span>{app.translator.trans(`huseyinfiliz-traderfeedback.admin.roles.${feedback.attributes.role}`)}</span>
            </span>
            
            {(feedback.attributes.created_at || feedback.attributes.updated_at) && (
              <span className="FeedbackCard-dateBadge">
                <i className="far fa-clock"></i>
                <span>{humanTime(new Date(feedback.attributes.created_at || feedback.attributes.updated_at))}</span>
              </span>
            )}
          </div>
        </div>

        {feedback.attributes.comment && (
          <div className="FeedbackCard-comment">
            <p>{feedback.attributes.comment}</p>
          </div>
        )}
        
        {feedback.attributes.discussion_id && (
          <a 
            href={app.route('discussion', { id: feedback.attributes.discussion_id })} 
            className="FeedbackCard-discussion"
            target="_blank"
          >
            <i className="fas fa-comments"></i>
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.view_discussion')}
          </a>
        )}

        <div className="FeedbackCard-actions">
          <Button
            className="Button Button--primary"
            icon="fas fa-check"
            onclick={() => onApprove(feedback)}
          >
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.approve_button')}
          </Button>
          <Button
            className="Button"
            icon="fas fa-times"
            onclick={() => onReject(feedback)}
          >
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.reject_button')}
          </Button>
        </div>
      </div>
    );
  }
}