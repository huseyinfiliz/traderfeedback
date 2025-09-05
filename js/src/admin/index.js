import app from 'flarum/admin/app';
import TraderFeedbackSettingsPage from './components/TraderFeedbackSettingsPage';

app.initializers.add('huseyinfiliz-traderfeedback', () => {
  // Register the main settings page with tabs
  app.extensionData
    .for('huseyinfiliz-traderfeedback')
    .registerPage(TraderFeedbackSettingsPage)
    .registerPermission({
      icon: 'fas fa-exchange-alt',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.permissions.give_feedback'),
      permission: 'huseyinfiliz-traderfeedback.giveFeedback'
    }, 'reply')
    .registerPermission({
      icon: 'fas fa-exchange-alt',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.permissions.moderate_feedback'),
      permission: 'huseyinfiliz-traderfeedback.moderateFeedback'
    }, 'moderate');
});