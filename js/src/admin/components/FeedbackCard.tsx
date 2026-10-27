import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import Avatar from 'flarum/common/components/Avatar';
import Icon from 'flarum/common/components/Icon';
import humanTime from 'flarum/common/helpers/humanTime';
import type { ComponentAttrs } from 'flarum/common/Component';
import type User from 'flarum/common/models/User';
import type { FeedbackData } from '../../common/types';
import type { SavedModelData } from 'flarum/common/Model';

interface FeedbackCardAttrs extends ComponentAttrs {
  feedback: FeedbackData;
  included: SavedModelData[];
  onApprove: (feedback: FeedbackData) => void;
  onReject: (feedback: FeedbackData) => void;
}

export default class FeedbackCard extends Component<FeedbackCardAttrs> {
  view() {
    const { feedback, included, onApprove, onReject } = this.attrs;

    const fromUserId = feedback.relationships?.fromUser?.data?.id || feedback.relationships?.from?.data?.id;
    const toUserId = feedback.relationships?.toUser?.data?.id || feedback.relationships?.to?.data?.id;

    let fromUser = fromUserId ? app.store.getById<User>('users', fromUserId) : undefined;
    let toUser = toUserId ? app.store.getById<User>('users', toUserId) : undefined;

    if (!fromUser && fromUserId) {
      const fromData = included?.find((item: any) => item.type === 'users' && item.id === fromUserId);
      if (fromData) {
        fromUser = app.store.pushPayload<User>({ data: fromData });
      }
    }

    if (!toUser && toUserId) {
      const toData = included?.find((item: any) => item.type === 'users' && item.id === toUserId);
      if (toData) {
        toUser = app.store.pushPayload<User>({ data: toData });
      }
    }

    const badgeClass =
      feedback.attributes.type === 'positive'
        ? 'Badge--success'
        : feedback.attributes.type === 'negative'
          ? 'Badge--danger'
          : 'Badge--warning';

    const typeIcon =
      feedback.attributes.type === 'positive' ? 'thumbs-up' : feedback.attributes.type === 'negative' ? 'thumbs-down' : 'minus';

    const roleIcon =
      feedback.attributes.role === 'buyer' ? 'cart-shopping' : feedback.attributes.role === 'seller' ? 'store' : 'right-left';

    return (
      <div className={`FeedbackCard FeedbackCard--${feedback.attributes.type}`}>
        <div className="FeedbackCard-header">
          <div className="FeedbackCard-users">
            <div className="FeedbackCard-user">
              {fromUser && <Avatar user={fromUser} />}
              <strong>
                {fromUser
                  ? fromUser.displayName()
                  : app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.user_id_format', { id: fromUserId })}
              </strong>
            </div>
            <Icon name="fas fa-arrow-right" className="FeedbackCard-arrow" />
            <div className="FeedbackCard-user">
              {toUser && <Avatar user={toUser} />}
              <strong>
                {toUser
                  ? toUser.displayName()
                  : app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.user_id_format', { id: toUserId })}
              </strong>
            </div>
          </div>

          <div className="FeedbackCard-meta">
            <span className={`Badge ${badgeClass}`}>
              <Icon name={`fas fa-${typeIcon}`} />
            </span>

            <span className="FeedbackCard-roleBadge">
              <Icon name={`fas fa-${roleIcon}`} />
              <span>{app.translator.trans(`huseyinfiliz-traderfeedback.admin.roles.${feedback.attributes.role}`)}</span>
            </span>

            {(feedback.attributes.createdAt || feedback.attributes.updatedAt) && (
              <span className="FeedbackCard-dateBadge">
                <Icon name="far fa-clock" />
                <span>{humanTime(new Date(feedback.attributes.createdAt || feedback.attributes.updatedAt))}</span>
              </span>
            )}
          </div>
        </div>

        {feedback.attributes.comment && (
          <div className="FeedbackCard-comment">
            <p>{feedback.attributes.comment}</p>
          </div>
        )}

        {feedback.attributes.discussionId && (
          <a
            href={app.forum.attribute('baseUrl') + '/d/' + feedback.attributes.discussionId}
            className="FeedbackCard-discussion"
            target="_blank"
            rel="noopener noreferrer"
          >
            <Icon name="fas fa-comments" />
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.view_discussion')}
          </a>
        )}

        <div className="FeedbackCard-actions">
          <Button className="Button Button--primary" icon="fas fa-check" onclick={() => onApprove(feedback)}>
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.approve_button')}
          </Button>
          <Button className="Button" icon="fas fa-xmark" onclick={() => onReject(feedback)}>
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.reject_button')}
          </Button>
        </div>
      </div>
    );
  }
}
