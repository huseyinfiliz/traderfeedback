import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default class NewFeedbackNotification extends Notification {
  icon() {
    const notification = this.attrs.notification;
    const subject = notification.subject();
    
    // Subject varsa type'ına göre icon göster
    if (subject && subject.type) {
      const type = subject.type();
      
      if (type === 'positive') return 'fas fa-thumbs-up';
      if (type === 'negative') return 'fas fa-thumbs-down';
    }
    
    // Fallback: Genel feedback icon
    return 'fas fa-exchange-alt';
  }

  href() {
    const notification = this.attrs.notification;
    const fromUser = notification.fromUser();
    
    // GUARD: fromUser yoksa ana sayfaya git
    if (!fromUser) {
      return app.route('index');
    }
    
    // Feedback veren kişinin profiline git
    return app.route('user.feedbacks', {
      username: fromUser.slug()
    });
  }

  content() {
    const notification = this.attrs.notification;
    const fromUser = notification.fromUser();
    const subject = notification.subject();
    
    // Subject'ten type ve role al
    let feedbackType = 'neutral';
    let role = 'buyer';
    
    if (subject) {
      if (subject.type) feedbackType = subject.type();
      if (subject.role) role = subject.role();
    }
    
    // Role text'ini translate et
    const roleText = role === 'seller' 
      ? app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_seller')
      : app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_buyer');
    
    // Type text'ini translate et
    const typeText = app.translator.trans(
      `huseyinfiliz-traderfeedback.forum.form.type_${feedbackType}`
    );
    
    return app.translator.trans(
      'huseyinfiliz-traderfeedback.forum.notifications.new_feedback_title',
      {
        username: fromUser ? username(fromUser) : 'Someone',
        type: typeText,
        role: roleText
      }
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