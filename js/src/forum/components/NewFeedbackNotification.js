import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default class NewFeedbackNotification extends Notification {
  icon() {
    const notification = this.attrs.notification;
    const data = notification.data || {};
    const feedbackType = data.feedbackType || 'neutral';
    
    if (feedbackType === 'positive') return 'fas fa-thumbs-up';
    if (feedbackType === 'negative') return 'fas fa-thumbs-down';
    return 'fas fa-exchange-alt';
  }

  href() {
    const notification = this.attrs.notification;
    const fromUser = notification.fromUser();
    
    if (!fromUser) return app.route('index');
    
    return app.route('user.feedbacks', {
      username: fromUser.slug()
    });
  }

  content() {
    const notification = this.attrs.notification;
    const fromUser = notification.fromUser();
    const data = notification.data || {};
    
    if (!fromUser) return 'Someone gave you feedback';
    
    const feedbackType = data.feedbackType || 'neutral';
    const role = data.role || 'buyer';
    
    const roleText = role === 'seller' 
      ? app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_seller')
      : app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_buyer');
    
    const typeText = app.translator.trans(
      `huseyinfiliz-traderfeedback.forum.form.type_${feedbackType}`
    );
    
    return app.translator.trans(
      'huseyinfiliz-traderfeedback.forum.notifications.new_feedback_title',
      {
        username: username(fromUser),
        type: typeText,
        role: roleText
      }
    );
  }

  excerpt() {
    const notification = this.attrs.notification;
    const data = notification.data || {};
    return data.comment || '';
  }
}