import app from 'flarum/admin/app';
import TraderFeedbackSettingsPage from './TraderFeedbackSettingsPage';

app.initializers.add('huseyinfiliz-traderfeedback', () => {
  // Register the main settings page with tabs
  app.extensionData
    .for('huseyinfiliz-traderfeedback')
    .registerPage(TraderFeedbackSettingsPage)
    .registerPermission({
      icon: 'fas fa-comment',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.permissions.give_feedback'),
      permission: 'huseyinfiliz-traderfeedback.give'
    }, 'reply', true) // true = varsayılan olarak Members grubuna verilir
    .registerPermission({
      icon: 'fas fa-flag',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.permissions.report_feedback'),
      permission: 'huseyinfiliz-traderfeedback.report'
    }, 'reply', true) // true = varsayılan olarak Members grubuna verilir
    .registerPermission({
      icon: 'fas fa-trash',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.permissions.delete_feedback'),
      permission: 'huseyinfiliz-traderfeedback.delete'
    }, 'moderate')
    .registerPermission({
      icon: 'fas fa-shield-alt',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.permissions.moderate_feedback'),
      permission: 'huseyinfiliz-traderfeedback.moderate'
    }, 'moderate');
});