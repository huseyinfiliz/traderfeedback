import app from 'flarum/forum/app';
import addUserProfilePage from './addUserProfilePage';
import addUserControls from './addUserControls';
import FeedbackModal from './modals/FeedbackModal';
import registerNotifications from './notifications';

export {default as extend} from './extend';

app.initializers.add('huseyinfiliz-traderfeedback', () => {
  // 1. Modal'ı global olarak kaydet
  app.feedbackModal = FeedbackModal;
  
  // 2. BİLDİRİM KOMPONENTLERİNİ İLK SIRADA KAYDET
  // Bu çok kritik - Flarum'un bildirim sistemini başlatmadan önce olmalı
  registerNotifications();
  
  // 3. Sayfaları ve kontrolleri ekle (bunlar extend kullanıyor, boot sonrası çalışır)
  addUserProfilePage();
  addUserControls();
  
  // Debug log
  console.log('✅ Trader feedback extension initialized');
  console.log('Registered notification components:', Object.keys(app.notificationComponents));
});