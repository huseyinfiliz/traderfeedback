import app from 'flarum/admin/app';
import TraderFeedbackSettingsPage from './components/TraderFeedbackSettingsPage';
import TraderFeedbackReportsPage from './components/TraderFeedbackReportsPage';
import TraderFeedbackApprovalsPage from './components/TraderFeedbackApprovalsPage';

app.initializers.add('huseyinfiliz-traderfeedback', () => {
  // Register settings page
  app.extensionData
    .for('huseyinfiliz-traderfeedback')
    .registerSetting({
      setting: 'huseyinfiliz.traderfeedback.allowNegative',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.allow_negative_label'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'huseyinfiliz.traderfeedback.requireApproval',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_approval_label'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'huseyinfiliz.traderfeedback.minLength',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_length_label'),
      type: 'number'
    })
    .registerSetting({
      setting: 'huseyinfiliz.traderfeedback.maxLength',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.max_length_label'),
      type: 'number'
    })
    .registerPermission({
      icon: 'fas fa-exchange-alt',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.permissions.give_feedback'),
      permission: 'huseyinfiliz-traderfeedback.giveFeedback'
    }, 'reply')
    .registerPermission({
      icon: 'fas fa-exchange-alt',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.permissions.moderate_feedback'),
      permission: 'trader.moderateFeedback'
    }, 'moderate');
});