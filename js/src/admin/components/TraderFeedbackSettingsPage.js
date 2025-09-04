import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Switch from 'flarum/common/components/Switch';
import Button from 'flarum/common/components/Button';
import app from 'flarum/admin/app';

export default class TraderFeedbackSettingsPage extends ExtensionPage {
  oninit(vnode) {
    super.oninit(vnode);
    
    this.settings = {
      'huseyinfiliz.traderfeedback.allowNegative': this.setting('huseyinfiliz.traderfeedback.allowNegative')(),
      'huseyinfiliz.traderfeedback.requireApproval': this.setting('huseyinfiliz.traderfeedback.requireApproval')(),
      'huseyinfiliz.traderfeedback.minLength': this.setting('huseyinfiliz.traderfeedback.minLength')(),
      'huseyinfiliz.traderfeedback.maxLength': this.setting('huseyinfiliz.traderfeedback.maxLength')()
    };
  }
  
  content() {
    return (
      <div className="TraderFeedbackSettingsPage">
        <div className="container">
          <form onsubmit={this.onsubmit.bind(this)}>
            <div className="Form-group">
              <label>{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.title')}</label>
              <p className="helpText">{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.help_text')}</p>
              
              <div className="Form-group">
                <Switch
                  state={this.settings['huseyinfiliz.traderfeedback.allowNegative']}
                  onchange={(value) => this.settings['huseyinfiliz.traderfeedback.allowNegative'] = value}
                >
                  {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.allow_negative_label')}
                </Switch>
              </div>
              
              <div className="Form-group">
                <Switch
                  state={this.settings['huseyinfiliz.traderfeedback.requireApproval']}
                  onchange={(value) => this.settings['huseyinfiliz.traderfeedback.requireApproval'] = value}
                >
                  {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_approval_label')}
                </Switch>
              </div>
              
              <div className="Form-group">
                <label>{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_length_label')}</label>
                <input
                  className="FormControl"
                  type="number"
                  min="1"
                  value={this.settings['huseyinfiliz.traderfeedback.minLength']}
                  oninput={(e) => this.settings['huseyinfiliz.traderfeedback.minLength'] = e.target.value}
                />
              </div>
              
              <div className="Form-group">
                <label>{app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.max_length_label')}</label>
                <input
                  className="FormControl"
                  type="number"
                  min="1"
                  value={this.settings['huseyinfiliz.traderfeedback.maxLength']}
                  oninput={(e) => this.settings['huseyinfiliz.traderfeedback.maxLength'] = e.target.value}
                />
              </div>
              
              <Button
                type="submit"
                className="Button Button--primary"
                loading={this.loading}
              >
                {app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.submit_button')}
              </Button>
            </div>
          </form>
        </div>
      </div>
    );
  }
  
  onsubmit(e) {
    e.preventDefault();
    
    this.loading = true;
    
    // Save settings
    Object.keys(this.settings).forEach(key => {
      this.setting(key)(this.settings[key]);
    });
    
    app.alerts.show({ type: 'success' }, app.translator.trans('core.admin.settings.saved_message'));
    
    this.loading = false;
  }
}