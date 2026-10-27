import app from 'flarum/forum/app';
import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import Icon from 'flarum/common/components/Icon';
import humanTime from 'flarum/common/helpers/humanTime';
import FeedbackUserDisplay from './FeedbackUserDisplay';
import type { ComponentAttrs } from 'flarum/common/Component';
import type { FeedbackAttributes, FeedbackData, FeedbackRole, FeedbackType } from '../../../common/types';

export interface FeedbackItemAttrs extends ComponentAttrs {
  feedback: FeedbackData;
  includedUsers: Map<string, any>;
  onDelete: (feedback: FeedbackData) => void;
  onReport: (feedback: FeedbackData) => void;
}

export default class FeedbackItem extends Component<FeedbackItemAttrs> {
  loading: boolean = false;

  view() {
    const { feedback, includedUsers, onDelete, onReport } = this.attrs;
    const attrs = feedback.attributes;

    const fromUserRelationship = feedback.relationships?.fromUser?.data;
    const fromUserId = fromUserRelationship?.id || attrs.fromUserId;

    const feedbackDate = new Date(attrs.createdAt);

    return (
      <div className={`FeedbackItem FeedbackItem--${attrs.type}`}>
        <div className="FeedbackItem-header">
          <div className="FeedbackItem-headerLeft">
            {FeedbackUserDisplay.view({
              userId: fromUserId,
              includedUsers: includedUsers,
            })}

            {this.renderTypeBadge(attrs.type)}
            {this.renderRoleBadge(attrs.role)}
          </div>

          <div className="FeedbackItem-headerRight">
            <span className="FeedbackItem-date">{humanTime(feedbackDate)}</span>
            {this.renderDiscussionLink(attrs)}
          </div>
        </div>

        <div className="FeedbackItem-comment">
          {attrs.comment || app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.no_comment')}
        </div>

        {this.renderActions(feedback, fromUserId)}
      </div>
    );
  }

  renderTypeBadge(type: FeedbackType) {
    const icons = {
      positive: 'fas fa-thumbs-up',
      neutral: 'fas fa-minus',
      negative: 'fas fa-thumbs-down',
    };

    const text = app.translator.trans(`huseyinfiliz-traderfeedback.forum.form.type_${type}`);

    return (
      <span className={`FeedbackItem-type FeedbackItem-type--${type}`}>
        <Icon name={icons[type]} className="FeedbackItem-typeIcon" />
        <span className="FeedbackItem-typeText">{text}</span>
      </span>
    );
  }

  renderRoleBadge(role: FeedbackRole) {
    const icons = {
      buyer: 'fas fa-cart-shopping',
      seller: 'fas fa-store',
      trader: 'fas fa-right-left',
    };

    const text = app.translator.trans(`huseyinfiliz-traderfeedback.forum.feedback_item.as_${role}`);

    return (
      <span className="FeedbackItem-role">
        <Icon name={icons[role]} className="FeedbackItem-roleIcon" />
        <span className="FeedbackItem-roleText">{text}</span>
      </span>
    );
  }

  renderDiscussionLink(attrs: FeedbackAttributes) {
    if (!attrs.discussionId) {
      return null;
    }

    if (attrs.discussionExists === false) {
      return (
        <span className="FeedbackItem-discussionLink FeedbackItem-discussionLink--deleted">
          <Icon name="fas fa-link-slash" />
        </span>
      );
    }

    if (attrs.canViewDiscussion === false) {
      return (
        <span className="FeedbackItem-discussionLink FeedbackItem-discussionLink--locked">
          <Icon name="fas fa-lock" />
        </span>
      );
    }

    return (
      <a
        href={app.route('discussion', { id: attrs.discussionId })}
        className="FeedbackItem-discussionLink"
        target="_blank"
        rel="noopener noreferrer"
      >
        <Icon name="fas fa-arrow-up-right-from-square" />
        <span className="FeedbackItem-discussionLinkText">
          {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.discussion_link')}
        </span>
        <span className="FeedbackItem-discussionLinkTextShort">
          {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.discussion_link_short')}
        </span>
      </a>
    );
  }

  renderActions(feedback: FeedbackData, fromUserId: string | number) {
    const currentUser = app.session.user;
    if (!currentUser) return null;

    const attrs = feedback.attributes;
    const canReport = attrs.canReport === true;
    const canDelete = attrs.canDelete === true;
    const isOwn = currentUser.id() === String(fromUserId);

    if (!canReport && !canDelete) {
      return null;
    }

    return (
      <div className="FeedbackItem-actions">
        {!isOwn && canReport && (
          <Button
            className="Button Button--link FeedbackItem-reportBtn"
            icon="fas fa-flag"
            onclick={() => this.attrs.onReport(feedback)}
            title={app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.report_button')}
          >
            <span className="FeedbackItem-actionText">
              {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.report_button')}
            </span>
          </Button>
        )}

        {canDelete && (
          <Button
            className="Button Button--link Button--danger FeedbackItem-deleteBtn"
            icon="fas fa-trash"
            onclick={() => this.attrs.onDelete(feedback)}
            title={app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.delete_button')}
          >
            <span className="FeedbackItem-actionText">
              {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.delete_button')}
            </span>
          </Button>
        )}
      </div>
    );
  }
}
