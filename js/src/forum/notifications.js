import app from 'flarum/forum/app';
import FeedbackApprovedNotification from './components/FeedbackApprovedNotification';
import FeedbackRejectedNotification from './components/FeedbackRejectedNotification';
import NewFeedbackNotification from './components/NewFeedbackNotification';

export default function registerNotifications() {
  // Core extension pattern: Simple assignment, no inline classes
  // Type names must match exactly with Blueprint's getType() return values
  app.notificationComponents.feedbackApproved = FeedbackApprovedNotification;
  app.notificationComponents.feedbackRejected = FeedbackRejectedNotification;
  app.notificationComponents.newFeedback = NewFeedbackNotification;
  
}