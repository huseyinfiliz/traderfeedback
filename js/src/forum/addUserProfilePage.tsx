import { extend } from 'flarum/common/extend';
import UserPage from 'flarum/forum/components/UserPage';
import LinkButton from 'flarum/common/components/LinkButton';
import app from 'flarum/forum/app';
import type ItemList from 'flarum/common/utils/ItemList';

export default function addUserProfilePage() {
  extend(UserPage.prototype, 'navItems', function (items) {
    const user = this.user;
    if (!user) return;

    const feedbackCount = user.attribute<number>('traderFeedbackCount') || 0;

    items.add(
      'traderFeedbacksLink',
      <LinkButton href={app.route('user.feedbacks', { username: user.slug() })} name="feedbacks" icon="fas fa-right-left">
        {app.translator.trans('huseyinfiliz-traderfeedback.forum.nav.feedback_link')}
        {feedbackCount > 0 && <span className="Button-badge">{feedbackCount}</span>}
      </LinkButton>,
      79
    );
  });

  // Extend the lazy-loaded notification preferences grid.
  extend('flarum/forum/components/NotificationGrid', 'notificationTypes', function (items: ItemList<unknown>) {
    items.add('newFeedback', {
      name: 'newFeedback',
      icon: 'fas fa-right-left',
      label: app.translator.trans('huseyinfiliz-traderfeedback.forum.settings.notify_new_feedback_label'),
    });

    items.add('feedbackApproved', {
      name: 'feedbackApproved',
      icon: 'fas fa-circle-check',
      label: app.translator.trans('huseyinfiliz-traderfeedback.forum.settings.notify_feedback_approved_label'),
    });

    items.add('feedbackRejected', {
      name: 'feedbackRejected',
      icon: 'fas fa-circle-xmark',
      label: app.translator.trans('huseyinfiliz-traderfeedback.forum.settings.notify_feedback_rejected_label'),
    });

    items.add('feedbackNeedsApproval', {
      name: 'feedbackNeedsApproval',
      icon: 'fas fa-clock',
      label: app.translator.trans('huseyinfiliz-traderfeedback.forum.settings.notify_feedback_needs_approval_label'),
    });

    items.add('feedbackReported', {
      name: 'feedbackReported',
      icon: 'fas fa-flag',
      label: app.translator.trans('huseyinfiliz-traderfeedback.forum.settings.notify_feedback_reported_label'),
    });
  });
}
