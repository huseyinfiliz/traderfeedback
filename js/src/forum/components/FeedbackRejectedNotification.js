import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default class FeedbackRejectedNotification extends Notification {
  icon() {
    return 'fas fa-times-circle';
  }

  href() {
    const notification = this.attrs.notification;
    const subject = notification.subject();
    
    // GUARD: Subject silinmişse ana sayfaya git
    if (!subject) {
      return app.route('index');
    }
    
    // fromUser varsa onun feedback sayfasına git
    const fromUser = notification.fromUser();
    if (fromUser) {
      return app.route('user.feedbacks', {
        username: fromUser.slug()
      });
    }
    
    // Fallback: Kendi profilime git
    return app.route('user.feedbacks', {
      username: app.session.user?.slug()
    });
  }

  content() {
    const notification = this.attrs.notification;
    const fromUser = notification.fromUser();
    
    return app.translator.trans(
      'huseyinfiliz-traderfeedback.forum.notifications.feedback_rejected_title',
      { username: fromUser ? username(fromUser) : 'Someone' }
    );
  }

  excerpt() {
    const notification = this.attrs.notification;
    const subject = notification.subject();
    
    // Subject varsa comment'ini göster
    if (subject && subject.comment) {
      return subject.comment();
    }
    
    return '';
  }
}