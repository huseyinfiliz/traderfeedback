import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default function registerNotifications() {
  // New Feedback Notification
  app.notificationComponents.newFeedback = class extends Notification {
    icon() {
      // content() metodunu kullan
      const data = this.attrs.notification.content() || {};
      const feedbackType = data.feedbackType || 'neutral';
      
      console.log('NewFeedback icon data:', data); // Debug
      
      if (feedbackType === 'positive') return 'fas fa-thumbs-up';
      if (feedbackType === 'negative') return 'fas fa-thumbs-down';
      return 'fas fa-exchange-alt';
    }

    href() {
      return app.route('user.feedbacks', {
        username: app.session.user?.slug()
      });
    }

    content() {
      const notification = this.attrs.notification;
      const user = notification.fromUser();
      const data = notification.content() || {};
      
      console.log('NewFeedback content data:', data); // Debug
      console.log('From user:', user); // Debug
      
      // Role bilgisini de ekleyelim
      const roleText = data.role === 'seller' 
        ? app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_seller')
        : app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_buyer');
      
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.new_feedback_title', {
        username: user ? username(user) : 'Someone',
        type: app.translator.trans(`huseyinfiliz-traderfeedback.forum.form.type_${data.feedbackType || 'neutral'}`),
        role: roleText
      });
    }
  };

  // Feedback Approved Notification  
  app.notificationComponents.feedbackApproved = class extends Notification {
    icon() {
      return 'fas fa-check-circle';
    }

    href() {
      const data = this.attrs.notification.content() || {};
      
      console.log('FeedbackApproved href data:', data); // Debug
      
      if (data.toUsername) {
        return app.route('user.feedbacks', {
          username: data.toUsername
        });
      }
      
      return app.route('user.feedbacks', {
        username: app.session.user?.slug()
      });
    }

    content() {
      const notification = this.attrs.notification;
      const data = notification.content() || {};
      
      console.log('FeedbackApproved content data:', data); // Debug
      
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_approved_title', {
        username: data.toUsername || 'a user'
      });
    }
  };

  // Feedback Rejected Notification
  app.notificationComponents.feedbackRejected = class extends Notification {
    icon() {
      return 'fas fa-times-circle';
    }

    href() {
      const data = this.attrs.notification.content() || {};
      
      console.log('FeedbackRejected href data:', data); // Debug
      
      if (data.toUsername) {
        return app.route('user.feedbacks', {
          username: data.toUsername
        });
      }
      
      return app.route('user.feedbacks', {
        username: app.session.user?.slug()
      });
    }

    content() {
      const notification = this.attrs.notification;
      const data = notification.content() || {};
      
      console.log('FeedbackRejected content data:', data); // Debug
      
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_rejected_title', {
        username: data.toUsername || 'a user'
      });
    }
  };
  
  console.log('Trader feedback notifications registered'); // Debug
}