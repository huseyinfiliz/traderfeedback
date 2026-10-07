import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';

export default class SettingsTab extends Component {
  view() {
    const { buildSettingComponent, submitButton, page } = this.attrs;

    return (
      <div className="TraderFeedbackSettings">
        {/* General Settings */}
        <div className="SettingsSection">
          <h3>
            <i className="fas fa-cog"></i>
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_general')}
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

        {/* Discussion Settings */}
        <div className="SettingsSection">
          <h3>
            <i className="fas fa-comments"></i>
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_discussion')}
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

        {/* Comment Settings */}
        <div className="SettingsSection">
          <h3>
            <i className="fas fa-comment-dots"></i>
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_comment')}
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

        {/* User Requirements */}
        <div className="SettingsSection">
          <h3>
            <i className="fas fa-user-check"></i>
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_requirements')}
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

        {/* Post Feedback Actions Settings (YENİ BÖLÜM) */}
        <div className="SettingsSection">
          <h3>
            <i className="fas fa-hand-pointer"></i>
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_post_feedback_actions')}
          </h3>

          <div className="SettingsSection-content">
            {/* Post Menu Button */}
            <div className="Form-group">
              {buildSettingComponent({
                type: 'boolean',
                setting: 'huseyinfiliz.traderfeedback.showFeedbackInPostMenu',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_in_post_menu_label'),
                help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_in_post_menu_help'),
              })}
            </div>

            {/* Below Reply/Follow Button */}
            <div className="Form-group">
              {buildSettingComponent({
                type: 'boolean',
                setting: 'huseyinfiliz.traderfeedback.showFeedbackBelowReply',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_below_reply_label'),
                help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_below_reply_help'),
              })}
            </div>

            {/* Post Footer Button */}
            <div className="Form-group">
              {buildSettingComponent({
                type: 'boolean',
                setting: 'huseyinfiliz.traderfeedback.showFeedbackInPostFooter',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_in_post_footer_label'),
                help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_in_post_footer_help'),
              })}
            </div>

            {/* Post Footer Button */}
            <div className="Form-group">
              {buildSettingComponent({
                type: 'boolean',
                setting: 'huseyinfiliz.traderfeedback.footerOnlyFirstPost',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.footer_only_first_post_label'),
                help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.footer_only_first_post_help'),
              })}
            </div>

            {/* Tag Filter for Actions */}
            <div className="Form-group">
              <label>{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.feedback_action_tag_filter_label')}</label>
              {buildSettingComponent({
                type: 'flarum-tags.select-tags',
                setting: 'huseyinfiliz.traderfeedback.feedbackActionTagFilter',
                options: {
                  requireParentTag: false,
                  limits: {
                    max: {
                      secondary: 0,
                    },
                  },
                },
              })}
              <p className="helpText">{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.feedback_action_tag_filter_help')}</p>
            </div>

            {/* Only When Locked */}
            <div className="Form-group">
              {buildSettingComponent({
                type: 'boolean',
                setting: 'huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.feedback_only_when_locked_label'),
                help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.feedback_only_when_locked_help'),
              })}
            </div>
          </div>
        </div>

        {/* Badge Display Settings */}
        <div className="SettingsSection SettingsSection--badge">
          <h3>
            <i className="fas fa-tag"></i>
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_badge_display')}
          </h3>

          <div className="SettingsSection-content">
            {/* Show Badge Toggle */}
            <div className="Form-group">
              {buildSettingComponent({
                type: 'boolean',
                setting: 'huseyinfiliz.traderfeedback.showBadgeInPosts',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_badge_in_posts_label'),
                help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_badge_in_posts_help'),
              })}
            </div>

            {/* Custom Prefix */}
            <div className="Form-group">
              <label>{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_custom_prefix_label')}</label>
              <input
                type="text"
                className="FormControl"
                value={page.setting('huseyinfiliz.traderfeedback.badgeCustomPrefix')()}
                oninput={(e) => page.setting('huseyinfiliz.traderfeedback.badgeCustomPrefix')(e.target.value)}
                placeholder={app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_custom_prefix_placeholder')}
              />
              <p className="helpText">{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_custom_prefix_help')}</p>
            </div>

            {/* Format Selection */}
            <div className="Form-group">
              <label>{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_format_label')}</label>
              <select
                className="FormControl"
                value={page.setting('huseyinfiliz.traderfeedback.badgeFormat')()}
                onchange={(e) => {
                  page.setting('huseyinfiliz.traderfeedback.badgeFormat')(e.target.value);
                  m.redraw();
                }}
              >
                <option value="percentage">
                  {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_format_percentage')} — 100%
                </option>
                <option value="count_percentage">
                  {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_format_count_percentage')} — 8 (88%)
                </option>
                <option value="letters">
                  {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_format_letters')} — 5P / 2N / 1N
                </option>
                <option value="symbols">{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_format_symbols')} — +5 =2 -1</option>
                <option value="custom">{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_format_custom')}</option>
              </select>
              <p className="helpText">{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_format_help')}</p>
            </div>

            {/* Custom Format Editor */}
            {page.setting('huseyinfiliz.traderfeedback.badgeFormat')() === 'custom' && (
              <div className="Form-group TraderFeedback-customFormat">
                <label>{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_custom_format_label')}</label>

                {/* Variable Toolbar */}
                <div className="TraderFeedback-variableToolbar">
                  <button type="button" className="Button" onclick={() => page.insertVariable('{total}')}>
                    <i className="fas fa-hashtag"></i> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_var_total')}
                  </button>
                  <button type="button" className="Button" onclick={() => page.insertVariable('{score}')}>
                    <i className="fas fa-percentage"></i> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_var_score')}
                  </button>
                  <button type="button" className="Button" onclick={() => page.insertVariable('{positive}')}>
                    <i className="fas fa-thumbs-up"></i> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_var_positive')}
                  </button>
                  <button type="button" className="Button" onclick={() => page.insertVariable('{neutral}')}>
                    <i className="fas fa-minus"></i> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_var_neutral')}
                  </button>
                  <button type="button" className="Button" onclick={() => page.insertVariable('{negative}')}>
                    <i className="fas fa-thumbs-down"></i> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_var_negative')}
                  </button>
                </div>

                {/* Textarea */}
                <textarea
                  className={'FormControl' + (page.customFormatError ? ' error' : '')}
                  rows="3"
                  oncreate={(vnode) => {
                    page.customFormatTextarea = vnode.dom;
                  }}
                  value={page.setting('huseyinfiliz.traderfeedback.badgeCustomFormat')()}
                  oninput={(e) => {
                    page.setting('huseyinfiliz.traderfeedback.badgeCustomFormat')(e.target.value);
                    page.validateAndPreview(e.target.value);
                  }}
                  placeholder="{total} ({score}%) - {positive}P / {neutral}N / {negative}N"
                />

                {/* Validation Error */}
                {page.customFormatError && (
                  <div className="TraderFeedback-formatError">
                    <i className="fas fa-exclamation-triangle"></i> {page.customFormatError}
                  </div>
                )}

                {/* Live Preview */}
                <div className="TraderFeedback-preview">
                  <div className="TraderFeedback-preview-label">
                    <i className="fas fa-eye"></i> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_preview_label')}
                  </div>
                  <div className="TraderFeedback-preview-badge">
                    <span className="TraderBadge TraderBadge--inline">
                      <i className="fas fa-shopping-cart"></i>
                      {page.setting('huseyinfiliz.traderfeedback.badgeCustomPrefix')() && (
                        <span className="TraderBadge-prefix">{page.setting('huseyinfiliz.traderfeedback.badgeCustomPrefix')()}</span>
                      )}
                      <span className="TraderBadge-score">{page.customFormatPreview}</span>
                    </span>
                  </div>
                  <div className="TraderFeedback-preview-stats">
                    {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_preview_sample')}
                  </div>
                </div>

                <p className="helpText">{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_custom_format_help')}</p>
              </div>
            )}

            {/* Tag Filter */}
            <div className="Form-group">
              <label>{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_tag_filter_label')}</label>
              {buildSettingComponent({
                type: 'flarum-tags.select-tags',
                setting: 'huseyinfiliz.traderfeedback.badgeTagFilter',
                options: {
                  requireParentTag: false,
                  limits: {
                    max: {
                      secondary: 0,
                    },
                  },
                },
              })}
              <p className="helpText">{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_tag_filter_help')}</p>
            </div>

            {/* Only First Post - Switch olarak */}
            <div className="Form-group">
              {buildSettingComponent({
                type: 'boolean',
                setting: 'huseyinfiliz.traderfeedback.badgeOnlyFirstPost',
                label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_only_first_post_label'),
                help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_only_first_post_help'),
              })}
            </div>
          </div>
        </div>

        {/* Submit Button */}
        <div className="Form-group">{submitButton()}</div>
      </div>
    );
  }
}
