import app from 'flarum/admin/app';

app.initializers.add('huseyinfiliz-traderfeedback', () => {
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
      setting: 'huseyinfiliz.traderfeedback.minDays',
      label: 'Minimum membership days required',
      help: 'Users must be a member for this many days before giving feedback (0 = disabled)',
      type: 'number',
      min: 0
    })
    .registerSetting({
      setting: 'huseyinfiliz.traderfeedback.minPosts',
      label: 'Minimum posts required',
      help: 'Users must have this many posts before giving feedback (0 = disabled)',
      type: 'number',
      min: 0
    })
    .registerSetting({
      setting: 'huseyinfiliz.traderfeedback.requireDiscussion',
      label: 'Require discussion link',
      help: 'Feedback must be linked to a discussion',
      type: 'boolean'
    })
    .registerSetting({
      setting: 'huseyinfiliz.traderfeedback.minLength',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_length_label'),
      type: 'number',
      min: 1
    })
    .registerSetting({
      setting: 'huseyinfiliz.traderfeedback.maxLength',
      label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.max_length_label'),
      type: 'number',
      min: 1
    })
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