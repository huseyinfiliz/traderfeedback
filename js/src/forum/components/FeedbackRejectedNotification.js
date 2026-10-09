import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default class FeedbackRejectedNotification extends Notification {
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
    return 'fas fa-circle-xmark';
  }

  href() {
    const notification = this.attrs.notification;
    const fromUser = notification.fromUser();
    const data = this.getData();
    const userSlug =
      data.toUserSlug ||
      (fromUser && typeof fromUser.slug === 'function' ? fromUser.slug() : null) ||
      (data.toUserId ? String(data.toUserId) : null);

    if (!userSlug) return app.route('index');

    return app.route('user.feedbacks', {
      username: userSlug,
    });
  }

  content() {
    const notification = this.attrs.notification;
    const fromUser = notification.fromUser();

    if (!fromUser) return 'Your feedback was rejected';

    return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_rejected_title', { username: username(fromUser) });
  }

  excerpt() {
    return '';
  }
}
