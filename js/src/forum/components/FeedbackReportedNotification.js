import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default class FeedbackReportedNotification extends Notification {
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
    return 'fas fa-flag';
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
    const data = this.getData();

    const authorName = data.fromUserName || 'A user';
    const recipientName = data.toUserName || 'a member';

    return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_reported_title', {
      username: authorName,
      recipient: recipientName,
    });
  }

  excerpt() {
    const data = this.getData();
    return data.reason || '';
  }
}
