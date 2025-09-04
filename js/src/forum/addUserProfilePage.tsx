import { extend } from 'flarum/common/extend';
import UserPage from 'flarum/forum/components/UserPage';
import LinkButton from 'flarum/common/components/LinkButton';
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
      		); // Added closing parenthesis here
  });
}