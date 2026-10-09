import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';
import Icon from 'flarum/common/components/Icon';

export default class SettingsTab extends Component {
  view() {
    const { buildSettingComponent, submitButton, page } = this.attrs;

    return (
      <form className="TraderFeedbackSettings" onsubmit={page.saveSettings.bind(page)}>
        {/* General Settings */}
        <div className="SettingsSection">
          <h3>
            <Icon name="fas fa-gear" />
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_general')}
          </h3>

          <div className="SettingsSection-content">
            {buildSettingComponent({
              type: 'boolean',
              setting: 'huseyinfiliz.traderfeedback.requireApproval',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_approval_label'),
            })}

            {buildSettingComponent({
              type: 'boolean',
              setting: 'huseyinfiliz.traderfeedback.allowNegative',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.allow_negative_label'),
            })}
          </div>
        </div>

        {/* Discussion Settings */}
        <div className="SettingsSection">
          <h3>
            <Icon name="fas fa-comments" />
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_discussion')}
          </h3>

          <div className="SettingsSection-content">
            {buildSettingComponent({
              type: 'boolean',
              setting: 'huseyinfiliz.traderfeedback.requireDiscussion',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_discussion_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_discussion_help'),
            })}

            {buildSettingComponent({
              type: 'boolean',
              setting: 'huseyinfiliz.traderfeedback.onePerDiscussion',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.one_per_discussion_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.one_per_discussion_help'),
            })}
          </div>
        </div>

        {/* Comment Settings */}
        <div className="SettingsSection">
          <h3>
            <Icon name="fas fa-comment-dots" />
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_comment')}
          </h3>

          <div className="SettingsSection-content">
            {buildSettingComponent({
              type: 'number',
              setting: 'huseyinfiliz.traderfeedback.minLength',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_length_label'),
              placeholder: '10',
              min: 1,
            })}

            {buildSettingComponent({
              type: 'number',
              setting: 'huseyinfiliz.traderfeedback.maxLength',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.max_length_label'),
              placeholder: '1000',
              min: 1,
            })}
          </div>
        </div>

        {/* User Requirements */}
        <div className="SettingsSection">
          <h3>
            <Icon name="fas fa-user-check" />
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_requirements')}
          </h3>

          <div className="SettingsSection-content">
            {buildSettingComponent({
              type: 'number',
              setting: 'huseyinfiliz.traderfeedback.minDays',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_days_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_days_help'),
              placeholder: '0',
              min: 0,
            })}

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

        {/* Post Feedback Actions Settings */}
        <div className="SettingsSection">
          <h3>
            <Icon name="fas fa-hand-pointer" />
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_post_feedback_actions')}
          </h3>

          <div className="SettingsSection-content">
            {buildSettingComponent({
              type: 'boolean',
              setting: 'huseyinfiliz.traderfeedback.showFeedbackInPostMenu',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_in_post_menu_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_in_post_menu_help'),
            })}

            {buildSettingComponent({
              type: 'boolean',
              setting: 'huseyinfiliz.traderfeedback.showFeedbackBelowReply',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_below_reply_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_below_reply_help'),
            })}

            {buildSettingComponent({
              type: 'boolean',
              setting: 'huseyinfiliz.traderfeedback.showFeedbackInPostFooter',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_in_post_footer_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_feedback_in_post_footer_help'),
            })}

            {buildSettingComponent({
              type: 'boolean',
              setting: 'huseyinfiliz.traderfeedback.footerOnlyFirstPost',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.footer_only_first_post_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.footer_only_first_post_help'),
            })}

            {buildSettingComponent({
              type: 'flarum-tags.select-tags',
              setting: 'huseyinfiliz.traderfeedback.feedbackActionTagFilter',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.feedback_action_tag_filter_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.feedback_action_tag_filter_help'),
              options: {
                requireParentTag: false,
                limits: {
                  max: {
                    secondary: 0,
                  },
                },
              },
            })}

            {buildSettingComponent({
              type: 'boolean',
              setting: 'huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.feedback_only_when_locked_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.feedback_only_when_locked_help'),
            })}
          </div>
        </div>

        {/* Badge Display Settings */}
        <div className="SettingsSection SettingsSection--badge">
          <h3>
            <Icon name="fas fa-tag" />
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.section_badge_display')}
          </h3>

          <div className="SettingsSection-content">
            {buildSettingComponent({
              type: 'boolean',
              setting: 'huseyinfiliz.traderfeedback.showBadgeInPosts',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_badge_in_posts_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.show_badge_in_posts_help'),
            })}

            {/* Custom Prefix */}
            <div className="Form-group">
              <label>{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_custom_prefix_label')}</label>
              <input
                type="text"
                className="FormControl"
                value={page.setting('huseyinfiliz.traderfeedback.badgeCustomPrefix')()}
                oninput={(e: any) => page.setting('huseyinfiliz.traderfeedback.badgeCustomPrefix')(e.target.value)}
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
                onchange={(e: any) => {
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
                    <Icon name="fas fa-hashtag" /> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_var_total')}
                  </button>
                  <button type="button" className="Button" onclick={() => page.insertVariable('{score}')}>
                    <Icon name="fas fa-percent" /> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_var_score')}
                  </button>
                  <button type="button" className="Button" onclick={() => page.insertVariable('{positive}')}>
                    <Icon name="fas fa-thumbs-up" /> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_var_positive')}
                  </button>
                  <button type="button" className="Button" onclick={() => page.insertVariable('{neutral}')}>
                    <Icon name="fas fa-minus" /> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_var_neutral')}
                  </button>
                  <button type="button" className="Button" onclick={() => page.insertVariable('{negative}')}>
                    <Icon name="fas fa-thumbs-down" /> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_var_negative')}
                  </button>
                </div>

                {/* Textarea */}
                <textarea
                  className={'FormControl' + (page.customFormatError ? ' error' : '')}
                  rows={3}
                  oncreate={(vnode: any) => {
                    page.customFormatTextarea = vnode.dom;
                  }}
                  value={page.setting('huseyinfiliz.traderfeedback.badgeCustomFormat')()}
                  oninput={(e: any) => {
                    page.setting('huseyinfiliz.traderfeedback.badgeCustomFormat')(e.target.value);
                    page.validateAndPreview(e.target.value);
                  }}
                  placeholder="{total} ({score}%) - {positive}P / {neutral}N / {negative}N"
                />

                {/* Validation Error */}
                {page.customFormatError && (
                  <div className="TraderFeedback-formatError">
                    <Icon name="fas fa-triangle-exclamation" /> {page.customFormatError}
                  </div>
                )}

                {/* Live Preview */}
                <div className="TraderFeedback-preview">
                  <div className="TraderFeedback-preview-label">
                    <Icon name="fas fa-eye" /> {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_preview_label')}
                  </div>
                  <div className="TraderFeedback-preview-badge">
                    <span className="TraderBadge TraderBadge--inline">
                      <Icon name="fas fa-cart-shopping" />
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

            {buildSettingComponent({
              type: 'flarum-tags.select-tags',
              setting: 'huseyinfiliz.traderfeedback.badgeTagFilter',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_tag_filter_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_tag_filter_help'),
              options: {
                requireParentTag: false,
                limits: {
                  max: {
                    secondary: 0,
                  },
                },
              },
            })}

            {buildSettingComponent({
              type: 'boolean',
              setting: 'huseyinfiliz.traderfeedback.badgeOnlyFirstPost',
              label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_only_first_post_label'),
              help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.badge_only_first_post_help'),
            })}
          </div>
        </div>

        {/* Submit Button */}
        <div className="Form-group Form-controls">{submitButton()}</div>
      </form>
    );
  }
}
