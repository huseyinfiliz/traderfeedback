import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import addUserProfilePage from './addUserProfilePage';
import addUserControls from './addUserControls';
import addUserCardStats from './addUserCardStats';
import addPostBadge from './addPostBadge'; // ✅ YENİ
import FeedbackModal from './modals/FeedbackModal';
import registerNotifications from './notifications';
import Feedback from '../common/models/Feedback';

export {default as extend} from './extend';

app.initializers.add('huseyinfiliz-traderfeedback', () => {
  // ✅ KRİTİK: Model'i store'a kaydet
  app.store.models['trader-feedbacks'] = Feedback;
  
  // Modal'ı global olarak kaydet
  app.feedbackModal = FeedbackModal;
  
  // Bildirim komponentlerini kaydet
  registerNotifications();
  
  // Sayfaları ve kontrolleri ekle
  addUserProfilePage();
  addUserControls();
  addUserCardStats();
  addPostBadge(); // ✅ YENİ
  
  // Debug log
  console.log('✅ Trader feedback extension initialized');
  console.log('Registered notification components:', Object.keys(app.notificationComponents));
  console.log('Registered models:', Object.keys(app.store.models));
});