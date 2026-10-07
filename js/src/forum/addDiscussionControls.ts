import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';
import Button from 'flarum/common/components/Button';
import FeedbackModal from './modals/FeedbackModal';
import SelectUserModal from './modals/SelectUserModal';

export default function addDiscussionControls() {
  extend(DiscussionPage.prototype, 'sidebarItems', function (items) {
    const discussion = this.discussion;

    if (!app.forum.attribute('huseyinfiliz.traderfeedback.showFeedbackBelowReply')) {
      return;
    }

    if (!app.session.user) {
      return;
    }

    if (!shouldShowInDiscussion(discussion)) {
      return;
    }

    if (!shouldShowWhenLocked(discussion)) {
      return;
    }

    const isDiscussionOwner = app.session.user.id() === discussion?.user()?.id();

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
              // ✅ Sadece discussion ID gönder
              app.modal.show(FeedbackModal, {
                user: discussion.user(),
                discussionId: discussion.id(), // URL yerine ID
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

function shouldShowInDiscussion(discussion: any): boolean {
  const tagFilterJson = app.forum.attribute('huseyinfiliz.traderfeedback.feedbackActionTagFilter') || '[]';
  let allowedTags: string[] = [];

  try {
    allowedTags = JSON.parse(tagFilterJson);
  } catch (e) {
    allowedTags = [];
  }

  if (allowedTags.length === 0) {
    return true;
  }

  if (!discussion) return false;

  const discussionTags = discussion.tags ? discussion.tags() : [];
  if (!discussionTags || discussionTags.length === 0) return false;

  return discussionTags.some((tag: any) => allowedTags.includes(tag.id()));
}

function shouldShowWhenLocked(discussion: any): boolean {
  const onlyWhenLocked = app.forum.attribute('huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked');

  if (!onlyWhenLocked) {
    return true;
  }

  if (!discussion) return false;

  return discussion.isLocked && discussion.isLocked();
}
