import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import addUserProfilePage from './addUserProfilePage';
import addUserControls from './addUserControls';
import addUserCardStats from './addUserCardStats';
import addPostBadge from './addPostBadge';
import FeedbackModal from './modals/FeedbackModal';
import registerNotifications from './notifications';
import Feedback from '../common/models/Feedback';

export { default as extend } from './extend';

// YENİ importlar
import addPostControls from './addPostControls';
import addDiscussionControls from './addDiscussionControls';
import addPostFooterButton from './addPostFooterButton';

app.initializers.add('huseyinfiliz-traderfeedback', () => {
  // Register model in store
  app.store.models['trader-feedbacks'] = Feedback;

  // Register modal globally
  (app as any).feedbackModal = FeedbackModal;

  // Register notification components
  registerNotifications();

  // Add pages and controls
  addUserProfilePage();
  addUserControls();
  addUserCardStats();
  addPostBadge();

  // YENİ: Post ve discussion feedback butonlarını ekle
  addPostControls();
  addDiscussionControls();
  addPostFooterButton();
});
