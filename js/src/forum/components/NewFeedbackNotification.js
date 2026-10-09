import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default class NewFeedbackNotification extends Notification {
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
    const data = this.getData();
    const feedbackType = data.feedbackType || 'neutral';

    if (feedbackType === 'positive') return 'fas fa-thumbs-up';
    if (feedbackType === 'negative') return 'fas fa-thumbs-down';
    return 'fas fa-right-left';
  }

  href() {
    const notification = this.attrs.notification;
    const data = this.getData();
    const recipient = notification.user() || app.session.user;
    const userSlug =
      data.toUserSlug ||
      (recipient && typeof recipient.slug === 'function' ? recipient.slug() : null) ||
      (data.toUserId ? String(data.toUserId) : null);

    if (!userSlug) return app.route('index');

    return app.route('user.feedbacks', {
      username: userSlug,
    });
  }

  content() {
    const notification = this.attrs.notification;
    const fromUser = notification.fromUser();
    const data = this.getData();

    if (!fromUser) return 'Someone gave you feedback';

    const feedbackType = data.feedbackType || 'neutral';
    const role = data.role || 'buyer';

    const roleText =
      role === 'seller'
        ? app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_seller')
        : app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_buyer');

    const typeText = app.translator.trans(`huseyinfiliz-traderfeedback.forum.form.type_${feedbackType}`);

    return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.new_feedback_title', {
      username: username(fromUser),
      type: typeText,
      role: roleText,
    });
  }

  excerpt() {
    const data = this.getData();
    return data.comment || '';
  }
}
