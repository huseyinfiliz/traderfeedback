import app from 'flarum/forum/app';
import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import humanTime from 'flarum/common/helpers/humanTime';
import FeedbackUserDisplay from './FeedbackUserDisplay';

export default class FeedbackItem extends Component {
    loading: boolean = false;

    view() {
        const { feedback, includedUsers, onDelete, onReport } = this.attrs;
        const attrs = feedback.attributes || {};

        const fromUserRelationship = feedback.relationships?.fromUser?.data;
        const fromUserId = fromUserRelationship?.id || attrs.fromUserId || attrs.from_user_id;

        const feedbackDate = attrs.createdAt || attrs.created_at || new Date().toISOString();

        return (
            <div className={`FeedbackItem FeedbackItem--${attrs.type}`} key={feedback.id}>
                <div className="FeedbackItem-header">
                    <div className="FeedbackItem-headerLeft">
                        {FeedbackUserDisplay.view({
                            userId: fromUserId,
                            includedUsers: includedUsers
                        })}
                        
                        {this.renderTypeBadge(attrs.type)}
                        {this.renderRoleBadge(attrs.role)}
                    </div>
                    
                    <div className="FeedbackItem-headerRight">
                        <span className="FeedbackItem-date">
                            {humanTime(feedbackDate)}
                        </span>
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

    renderTypeBadge(type) {
        const icons = {
            positive: 'fas fa-thumbs-up',
            neutral: 'fas fa-minus',
            negative: 'fas fa-thumbs-down'
        };

        const text = app.translator.trans(`huseyinfiliz-traderfeedback.forum.form.type_${type}`);

        return (
            <span className={`FeedbackItem-type FeedbackItem-type--${type}`}>
                <i className={`${icons[type]} FeedbackItem-typeIcon`}></i>
                <span className="FeedbackItem-typeText">{text}</span>
            </span>
        );
    }

    renderRoleBadge(role) {
        const icons = {
            buyer: 'fas fa-shopping-cart',
            seller: 'fas fa-store',
            trader: 'fas fa-exchange-alt'
        };

        const text = app.translator.trans(`huseyinfiliz-traderfeedback.forum.feedback_item.as_${role}`);

        return (
            <span className="FeedbackItem-role">
                <i className={`${icons[role]} FeedbackItem-roleIcon`}></i>
                <span className="FeedbackItem-roleText">{text}</span>
            </span>
        );
    }

    renderDiscussionLink(attrs) {
        if (!attrs.discussionId && !attrs.discussion_id) {
            return null;
        }

        if (attrs.discussionExists === false) {
            return (
                <span className="FeedbackItem-discussionLink FeedbackItem-discussionLink--deleted">
                    <i className="fas fa-unlink"></i>
                </span>
            );
        }

        if (attrs.canViewDiscussion === false) {
            return (
                <span className="FeedbackItem-discussionLink FeedbackItem-discussionLink--locked">
                    <i className="fas fa-lock"></i>
                </span>
            );
        }

        return (
            <a 
                href={app.route('discussion', { id: attrs.discussionId || attrs.discussion_id })} 
                className="FeedbackItem-discussionLink" 
                target="_blank" 
                rel="noopener noreferrer"
            >
                <i className="fas fa-external-link-alt"></i>
                <span className="FeedbackItem-discussionLinkText">
                    {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.discussion_link')}
                </span>
                <span className="FeedbackItem-discussionLinkTextShort">
                    {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.discussion_link_short')}
                </span>
            </a>
        );
    }

    renderActions(feedback, fromUserId) {
        const currentUser = app.session.user;
        if (!currentUser) return null;

        const attrs = feedback.attributes || {};
        const canReport = attrs.canReport !== false && currentUser.attribute('canReportFeedback') !== false;
        const canDelete = attrs.canDelete || currentUser.attribute('canDeleteFeedback');
        const canModerate = currentUser.attribute('canModerateFeedback');
        const isOwn = currentUser.id() === String(fromUserId);

        if (!canReport && !canDelete && !isOwn && !canModerate) {
            return null;
        }

        return (
            <div className="FeedbackItem-actions">
                {!isOwn && canReport && (
                    <Button
                        className="Button Button--link FeedbackItem-reportBtn"
                        onclick={() => this.attrs.onReport(feedback)}
                        title={app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.report_button')}
                    >
                        <i className="fas fa-flag"></i>
                        <span className="FeedbackItem-actionText">
                            {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.report_button')}
                        </span>
                    </Button>
                )}

                {(isOwn || canDelete || canModerate) && (
                    <Button
                        className="Button Button--link Button--danger FeedbackItem-deleteBtn"
                        onclick={() => this.attrs.onDelete(feedback)}
                        title={app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.delete_button')}
                    >
                        <i className="fas fa-trash"></i>
                        <span className="FeedbackItem-actionText">
                            {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.delete_button')}
                        </span>
                    </Button>
                )}
            </div>
        );
    }
}