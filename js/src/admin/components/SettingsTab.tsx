import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';

export default class SettingsTab extends Component {
  view() {
    const { buildSettingComponent, submitButton } = this.attrs;

    return (
      <div className="TraderFeedbackSettings">
        <div className="SettingsSection">
          <h3>
            <i className="fas fa-cog"></i>
            General Settings
          </h3>
          
          <div className="SettingsSection-content">
            <div className="Form-group">
              {buildSettingComponent({
                type: 'boolean',
                setting: 'huseyinfiliz.traderfeedback.requireApproval',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_approval_label'),
              })}
            </div>

            <div className="Form-group">
              {buildSettingComponent({
                type: 'boolean',
                setting: 'huseyinfiliz.traderfeedback.allowNegative',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.allow_negative_label'),
              })}
            </div>
          </div>
        </div>

        <div className="SettingsSection">
          <h3>
            <i className="fas fa-comments"></i>
            Discussion Settings
          </h3>
          
          <div className="SettingsSection-content">
            <div className="Form-group">
              {buildSettingComponent({
                type: 'boolean',
                setting: 'huseyinfiliz.traderfeedback.requireDiscussion',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_discussion_label'),
                help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_discussion_help'),
              })}
            </div>

            <div className="Form-group">
              {buildSettingComponent({
                type: 'boolean',
                setting: 'huseyinfiliz.traderfeedback.onePerDiscussion',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.one_per_discussion_label'),
                help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.one_per_discussion_help'),
              })}
            </div>
          </div>
        </div>

        <div className="SettingsSection">
          <h3>
            <i className="fas fa-comment-dots"></i>
            Comment Settings
          </h3>
          
          <div className="SettingsSection-content">
            <div className="Form-group">
              {buildSettingComponent({
                type: 'number',
                setting: 'huseyinfiliz.traderfeedback.minLength',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_length_label'),
                placeholder: '10',
                min: 1,
              })}
            </div>

            <div className="Form-group">
              {buildSettingComponent({
                type: 'number',
                setting: 'huseyinfiliz.traderfeedback.maxLength',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.max_length_label'),
                placeholder: '1000',
                min: 1,
              })}
            </div>
          </div>
        </div>

        <div className="SettingsSection">
          <h3>
            <i className="fas fa-user-check"></i>
            User Requirements
          </h3>
          
          <div className="SettingsSection-content">
            <div className="Form-group">
              {buildSettingComponent({
                type: 'number',
                setting: 'huseyinfiliz.traderfeedback.minDays',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_days_label'),
                help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_days_help'),
                placeholder: '0',
                min: 0,
              })}
            </div>

            <div className="Form-group">
              {buildSettingComponent({
                type: 'number',
                setting: 'huseyinfiliz.traderfeedback.minPosts',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_posts_label'),
                help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_posts_help'),
                placeholder: '0',
                min: 0,
              })}
            </div>
          </div>
        </div>

        <div className="Form-group">
          {submitButton()}
        </div>
      </div>
    );
  }
}