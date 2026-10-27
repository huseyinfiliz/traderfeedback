import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import PostControls from 'flarum/forum/utils/PostControls';
import Button from 'flarum/common/components/Button';
import FeedbackModal from './modals/FeedbackModal';
import { allowsFeedback } from './utils/discussionRestrictions';

export default function addPostControls() {
  extend(PostControls, 'userControls', function (items, post) {
    const user = post.user();

    if (!app.session.user || !user || !user.attribute('canGiveFeedback')) {
      return;
    }

    if (!app.forum.attribute('huseyinfiliz.traderfeedback.showFeedbackInPostMenu')) {
      return;
    }

    if (!allowsFeedback(post.discussion())) {
      return;
    }

    items.add(
      'giveFeedback',
      Button.component(
        {
          icon: 'fas fa-right-left',
          onclick: () => {
            app.modal.show(FeedbackModal, {
              user: user,
              discussionId: post.discussion().id(),
              autoFillDiscussion: true,
            });
          },
        },
        app.translator.trans('huseyinfiliz-traderfeedback.forum.post_actions.give_feedback')
      ),
      10
    );
  });
}
