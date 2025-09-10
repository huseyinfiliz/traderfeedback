import { extend } from 'flarum/common/extend';
import UserPage from 'flarum/forum/components/UserPage';
import LinkButton from 'flarum/common/components/LinkButton';
import NotificationGrid from 'flarum/forum/components/NotificationGrid';
import app from 'flarum/forum/app';
import TraderFeedbackPage from './Pages/ProfilePage';
import { ItemList } from 'flarum/common/utils/ItemList';

export default function addUserProfilePage() {
  app.routes['user.feedbacks'] = {
    path: '/u/:username/feedbacks',
    component: TraderFeedbackPage
  };

  extend(UserPage.prototype, 'navItems', function (items: ItemList) {
      items.add(
        'traderFeedbacksLink',
        <LinkButton href={app.route('user.feedbacks', { username: this.user?.slug() })} name="feedbacks" icon="fas fa-exchange-alt">
			{app.translator.trans('huseyinfiliz-traderfeedback.forum.nav.feedback_link')}
		</LinkButton>,
        79,
      );
  });
  
  // NotificationGrid'e feedback notification tiplerini ekle
  extend(NotificationGrid.prototype, 'notificationTypes', function(items) {
    items.add('newFeedback', {
      name: 'newFeedback',
      icon: 'fas fa-exchange-alt',
      label: app.translator.trans('huseyinfiliz-traderfeedback.forum.settings.notify_new_feedback_label')
    });
    
    items.add('feedbackApproved', {
      name: 'feedbackApproved',
      icon: 'fas fa-check-circle',
      label: app.translator.trans('huseyinfiliz-traderfeedback.forum.settings.notify_feedback_approved_label')
    });
    
    items.add('feedbackRejected', {
      name: 'feedbackRejected',
      icon: 'fas fa-times-circle',
      label: app.translator.trans('huseyinfiliz-traderfeedback.forum.settings.notify_feedback_rejected_label')
    });
  });
}