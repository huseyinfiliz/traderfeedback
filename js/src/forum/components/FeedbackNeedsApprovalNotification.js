import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default class FeedbackNeedsApprovalNotification extends Notification {
  getData() {
    const notification = this.attrs.notification;
    if (typeof notification.content === 'function') {
      return notification.content() || {};
    }
    if (notification.content) {
      return notification.content || {};
    }
    if (typeof notification.data === 'function') {
      return notification.data() || {};
    }
    return notification.data || {};
  }

  icon() {
    return 'fas fa-clock';
  }

  href() {
    const data = this.getData();
    const toUserSlug = data.toUserSlug || (data.toUserId ? String(data.toUserId) : null);

    if (!toUserSlug) return app.route('index');

    return app.route('user.feedbacks', {
      username: toUserSlug,
    });
  }

  content() {
    const notification = this.attrs.notification;
    const fromUser = notification.fromUser();
    const data = this.getData();

    const authorName = fromUser ? username(fromUser) : data.fromUserName || 'Someone';
    const recipientName = data.toUserName || 'a user';

    return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_needs_approval_title', {
      username: authorName,
      recipient: recipientName,
    });
  }

  excerpt() {
    const data = this.getData();
    return data.comment || '';
  }
}
