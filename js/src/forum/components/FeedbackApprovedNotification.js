import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default class FeedbackApprovedNotification extends Notification {
  icon() {
    return 'fas fa-check-circle';
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
    
    if (!fromUser) return 'Your feedback was approved';
    
    return app.translator.trans(
      'huseyinfiliz-traderfeedback.forum.notifications.feedback_approved_title',
      { username: username(fromUser) }
    );
  }

  excerpt() {
    return '';
  }
}