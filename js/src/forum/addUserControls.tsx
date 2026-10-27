import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import Button from 'flarum/common/components/Button';
import UserControls from 'flarum/forum/utils/UserControls';
import FeedbackModal from './modals/FeedbackModal';

export default function addUserControls() {
  extend(UserControls, 'controls', (items, user) => {
    if (app.session.user && user.attribute('canGiveFeedback')) {
      items.add(
        'giveFeedback',
        Button.component(
          {
            icon: 'fas fa-right-left',
            onclick() {
              app.modal.show(FeedbackModal, {
                user: user,
              });
            },
          },
          app.translator.trans('huseyinfiliz-traderfeedback.forum.form.submit_button')
        ),
        100
      );
    }
  });
}
