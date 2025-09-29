import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default function registerNotifications() {
  // Feedback Approved Notification  
  app.notificationComponents.feedbackApproved = class extends Notification {
    icon() {
      return 'fas fa-check-circle';
    }

    href() {
      const notification = this.attrs.notification;
      
      // Önce content data'dan al (backend'de düzelttik, artık doğru geliyor)
      const data = notification.content() || {};
      console.log('FeedbackApproved href data:', data); // Debug
      
      if (data.toUsername) {
        return app.route('user.feedbacks', {
          username: data.toUsername
        });
      }
      
      // fromUser'ı kontrol et (düzeltme sonrası doğru olmalı)
      const fromUser = notification.fromUser();
      if (fromUser) {
        console.log('FeedbackApproved fromUser:', fromUser.username()); // Debug
        // Admin değilse kullan
        if (fromUser.id() !== '1') {
          return app.route('user.feedbacks', {
            username: fromUser.slug()
          });
        }
      }
      
      // Fallback: kendi profiline git
      return app.route('user.feedbacks', {
        username: app.session.user?.slug()
      });
    }

    content() {
      const notification = this.attrs.notification;
      const data = notification.content() || {};
      
      console.log('FeedbackApproved content data:', data); // Debug
      
      // Data'dan username al
      if (data.toUsername) {
        return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_approved_title', {
          username: data.toUsername
        });
      }
      
      // fromUser'dan al
      const fromUser = notification.fromUser();
      if (fromUser && fromUser.id() !== '1') {
        return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_approved_title', {
          username: username(fromUser)
        });
      }
      
      // Varsayılan
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_approved_title', {
        username: 'a user'
      });
    }
  };

  // Feedback Rejected Notification
  app.notificationComponents.feedbackRejected = class extends Notification {
    icon() {
      return 'fas fa-times-circle';
    }

    href() {
      const notification = this.attrs.notification;
      
      // Önce content data'dan al
      const data = notification.content() || {};
      console.log('FeedbackRejected href data:', data); // Debug
      
      if (data.toUsername) {
        return app.route('user.feedbacks', {
          username: data.toUsername
        });
      }
      
      // fromUser'ı kontrol et
      const fromUser = notification.fromUser();
      if (fromUser) {
        console.log('FeedbackRejected fromUser:', fromUser.username()); // Debug
        // Admin değilse kullan
        if (fromUser.id() !== '1') {
          return app.route('user.feedbacks', {
            username: fromUser.slug()
          });
        }
      }
      
      // Fallback: kendi profiline git
      return app.route('user.feedbacks', {
        username: app.session.user?.slug()
      });
    }

    content() {
      const notification = this.attrs.notification;
      const data = notification.content() || {};
      
      console.log('FeedbackRejected content data:', data); // Debug
      
      // Data'dan username al
      if (data.toUsername) {
        return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_rejected_title', {
          username: data.toUsername
        });
      }
      
      // fromUser'dan al
      const fromUser = notification.fromUser();
      if (fromUser && fromUser.id() !== '1') {
        return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_rejected_title', {
          username: username(fromUser)
        });
      }
      
      // Varsayılan
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_rejected_title', {
        username: 'a user'
      });
    }
  };

  // New Feedback Notification
  app.notificationComponents.newFeedback = class extends Notification {
    icon() {
      const data = this.attrs.notification.content() || {};
      const feedbackType = data.feedbackType || 'neutral';
      
      console.log('NewFeedback icon data:', data); // Debug
      
      if (feedbackType === 'positive') return 'fas fa-thumbs-up';
      if (feedbackType === 'negative') return 'fas fa-thumbs-down';
      return 'fas fa-exchange-alt';
    }

    href() {
      const fromUser = this.attrs.notification.fromUser();
      if (fromUser) {
        return app.route('user.feedbacks', {
          username: fromUser.slug()
        });
      }
      
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
  
  console.log('Trader feedback notifications registered'); // Debug
}