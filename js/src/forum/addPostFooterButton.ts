import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import CommentPost from 'flarum/forum/components/CommentPost';
import Button from 'flarum/common/components/Button';
import FeedbackModal from './modals/FeedbackModal';
import { allowsFeedback } from './utils/discussionRestrictions';

export default function addPostFooterControls() {
  extend(CommentPost.prototype, 'footerItems', function (items) {
    const post = this.attrs.post;
    const user = post.user();

    if (!app.session.user || !user || !user.attribute('canGiveFeedback')) {
      return;
    }

    if (!app.forum.attribute('huseyinfiliz.traderfeedback.showFeedbackInPostFooter')) {
      return;
    }

    const onlyFirstPost = app.forum.attribute('huseyinfiliz.traderfeedback.footerOnlyFirstPost');
    if (onlyFirstPost && post.number() !== 1) {
      return;
    }

    if (!allowsFeedback(post.discussion())) {
      return;
    }

    items.add(
      'giveFeedbackFooter',
      Button.component(
        {
          className: 'Button Button--link',
          icon: 'fas fa-right-left',
          onclick: () => {
            app.modal.show(FeedbackModal, {
              user: user,
              discussionId: post.discussion().id(),
              autoFillDiscussion: true,
            });
          },
        },
        app.translator.trans('huseyinfiliz-traderfeedback.forum.post_actions.give_feedback_short')
      ),
      -1
    );
  });
}
