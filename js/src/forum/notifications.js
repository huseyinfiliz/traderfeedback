import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default function registerNotifications() {
  // New Feedback Notification
  app.notificationComponents.newFeedback = class extends Notification {
    icon() {
      const notification = this.attrs.notification;
      const data = notification.content() || notification.data || {};
      const feedbackType = data.feedbackType || 'neutral';
      
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
      const data = notification.content() || notification.data || {};
      
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.new_feedback_title', {
        username: user ? username(user) : 'Someone',
        type: app.translator.trans(`huseyinfiliz-traderfeedback.forum.form.type_${data.feedbackType || 'neutral'}`)
      });
    }
  };

  // Feedback Approved Notification
  app.notificationComponents.feedbackApproved = class extends Notification {
    icon() {
      return 'fas fa-check-circle';
    }

    href() {
      return app.route('user.feedbacks', {
        username: app.session.user?.slug()
      });
    }

    content() {
      const notification = this.attrs.notification;
      const data = notification.content() || notification.data || {};
      
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_approved_title', {
        username: data.toUsername || data.toDisplayName || 'a user'
      });
    }
  };

  // Feedback Rejected Notification
  app.notificationComponents.feedbackRejected = class extends Notification {
    icon() {
      return 'fas fa-times-circle';
    }

    href() {
      return app.route('user.feedbacks', {
        username: app.session.user?.slug()
      });
    }

    content() {
      const notification = this.attrs.notification;
      const data = notification.content() || notification.data || {};
      
      return app.translator.trans('huseyinfiliz-traderfeedback.forum.notifications.feedback_rejected_title', {
        username: data.toUsername || data.toDisplayName || 'a user'
      });
    }
  };
}