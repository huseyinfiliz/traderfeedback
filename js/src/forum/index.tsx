import app from 'flarum/forum/app';
import addUserProfilePage from './addUserProfilePage';
import addUserControls from './addUserControls';
import FeedbackModal from './modals/FeedbackModal';
import registerNotifications from './notifications';

export {default as extend} from './extend';

app.initializers.add('huseyinfiliz-traderfeedback', () => {
  // Modal'ı global olarak kaydet
  app.feedbackModal = FeedbackModal;
  
  // Bildirim komponentlerini kaydet
  registerNotifications();
  
  // Sayfaları ve kontrolleri ekle
  addUserProfilePage();
  addUserControls();
});