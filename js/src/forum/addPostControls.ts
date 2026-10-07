import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import PostControls from 'flarum/forum/utils/PostControls';
import Button from 'flarum/common/components/Button';
import FeedbackModal from './modals/FeedbackModal';

export default function addPostControls() {
  // PostControls.userControls - Kullanıcı kontrolleri için
  extend(PostControls, 'userControls', function (items, post) {
    const user = post.user();

    // Kendine feedback veremez
    if (!app.session.user || app.session.user.id() === user?.id()) {
      return;
    }

    // Ayar kontrolü
    if (!app.forum.attribute('huseyinfiliz.traderfeedback.showFeedbackInPostMenu')) {
      return;
    }

    // Tag filter kontrolü
    if (!shouldShowInDiscussion(post.discussion())) {
      return;
    }

    // Lock kontrolü
    if (!shouldShowWhenLocked(post.discussion())) {
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
      10 // Pozitif priority - user controls'da
    );
  });
}

// Tag filter kontrolü
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

// Lock kontrolü
function shouldShowWhenLocked(discussion: any): boolean {
  const onlyWhenLocked = app.forum.attribute('huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked');

  if (!onlyWhenLocked) {
    return true;
  }

  if (!discussion) return false;

  return discussion.isLocked && discussion.isLocked();
}
