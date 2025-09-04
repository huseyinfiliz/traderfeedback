import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import Select from 'flarum/common/components/Select';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import app from 'flarum/forum/app';
//import m from 'mithril'; // Import mithril

export default class FeedbackForm extends Component {
  oninit(vnode) {
    super.oninit(vnode);
    
    this.user = this.attrs.user;
    this.loading = false;
    
    this.type = 'positive';
    this.role = 'buyer';
    this.comment = '';
    this.transactionId = '';
  }
  
  view() {
    return (
      <div className="FeedbackForm">
        <h3>{app.translator.trans('huseyinfiliz-traderfeedback.forum.form.title', {username: this.user.displayName()})}</h3>
        
        <div className="Form-group">
          <label>{app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_label')}</label>
          <Select
            value={this.type}
            options={{
              'positive': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_positive'),
              'neutral': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_neutral'),
              'negative': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_negative')
            }}
            onchange={this.handleTypeChange.bind(this)}
          />
        </div>
        
        <div className="Form-group">
          <label>{app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_label')}</label>
          <Select
            value={this.role}
            options={{
              'buyer': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_buyer'),
              'seller': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_seller'),
              'trader': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_trader')
            }}
            onchange={this.handleRoleChange.bind(this)}
          />
        </div>
        
        <div className="Form-group">
          <label>{app.translator.trans('huseyinfiliz-traderfeedback.forum.form.transaction_id_label')}</label>
          <input 
            className="FormControl" 
            value={this.transactionId} 
            oninput={e => this.transactionId = e.target.value}
            placeholder={app.translator.trans('huseyinfiliz-traderfeedback.forum.form.transaction_id_placeholder')}
          />
        </div>
        
        <div className="Form-group">
          <label>{app.translator.trans('huseyinfiliz-traderfeedback.forum.form.comment_label')}</label>
          <textarea 
            className="FormControl" 
            value={this.comment} 
            oninput={e => this.comment = e.target.value}
            placeholder={app.translator.trans('huseyinfiliz-traderfeedback.forum.form.comment_placeholder')}
            rows={5}
          />
        </div>
        
        {this.loading ? (
          <LoadingIndicator />
        ) : (
          <Button 
            className={`Button Button--primary ${this.type}`} 
            onclick={this.submit.bind(this)}
            disabled={!this.comment.trim()}
          >
            {app.translator.trans('huseyinfiliz-traderfeedback.forum.form.submit_button')}
          </Button>
        )}
      </div>
    );
  }
  
  handleTypeChange(value) {
    this.type = value;
  }
  
  handleRoleChange(value) {
    this.role = value;
  }
  
  submit() {
    // Validate form
    if (!this.comment.trim()) {
      return;
    }
    
    const minLength = app.forum.attribute('huseyinfiliz.traderfeedback.minLength') || 10;
    const maxLength = app.forum.attribute('huseyinfiliz.traderfeedback.maxLength') || 1000;
    
    if (this.comment.length < minLength) {
      app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.error_too_short', {min: minLength}));
      return;
    }
    
    if (this.comment.length > maxLength) {
      app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.error_too_long', {max: maxLength}));
      return;
    }
    
    // Submit feedback
    this.loading = true;
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/feedback',
      data: {
        data: {
          attributes: {
            to_user_id: this.user.id(),
            type: this.type,
            role: this.role,
            comment: this.comment,
            transaction_id: this.transactionId || null
          }
        }
      }
    })
    .then(response => {
      app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.success'));
      this.comment = '';
      this.transactionId = '';
      this.type = 'positive';
      m.redraw();
      
      // Refresh the page to show the new feedback
      window.location.reload();
    })
    .catch(error => {
      this.loading = false;
      m.redraw();
      app.alerts.show({ type: 'error' }, error.response.errors[0].detail);
    });
  }
}