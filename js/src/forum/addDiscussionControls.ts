import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';
import Button from 'flarum/common/components/Button';
import FeedbackModal from './modals/FeedbackModal';
import SelectUserModal from './modals/SelectUserModal';
import { allowsFeedback } from './utils/discussionRestrictions';

export default function addDiscussionControls() {
  extend(DiscussionPage.prototype, 'sidebarItems', function (items) {
    const discussion = this.discussion;

    if (!app.forum.attribute('huseyinfiliz.traderfeedback.showFeedbackBelowReply')) {
      return;
    }

    if (!app.session.user) {
      return;
    }

    if (!discussion || !allowsFeedback(discussion)) {
      return;
    }

    const owner = discussion.user();
    if (!owner) return;
    const isDiscussionOwner = app.session.user.id() === owner.id();

    items.add(
      'giveFeedbackButton',
      Button.component(
        {
          className: 'Button Button--primary',
          icon: 'fas fa-right-left',
          onclick: () => {
            if (isDiscussionOwner) {
              app.modal.show(SelectUserModal, {
                discussion,
              });
            } else {
              app.modal.show(FeedbackModal, {
                user: discussion.user(),
                discussionId: discussion.id(),
                autoFillDiscussion: true,
              });
            }
          },
        },
        app.translator.trans('huseyinfiliz-traderfeedback.forum.discussion_actions.give_feedback')
      ),
      99
    );
  });
}
