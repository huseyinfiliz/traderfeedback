import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import Select from 'flarum/common/components/Select';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import app from 'flarum/forum/app';

export default class FeedbackForm extends Modal {
  oninit(vnode) {
    super.oninit(vnode);
    
    this.user = this.attrs.user;
    this.loading = false;
    
    this.type = 'positive';
    this.role = 'buyer';
    this.comment = '';
  }
  
  className() {
    return 'Modal Modal--medium';
  }
  
  title() {
    return app.translator.trans('huseyinfiliz-traderfeedback.forum.form.title', {
      username: this.user.displayName()
    });
  }
  
  content() {
    return (
      <div className="Modal-body">
        <div className="Form">
          <div className="Form-group">
            <label>{app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_label')}</label>
            <Select
              value={this.type}
              options={{
                'positive': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_positive'),
                'neutral': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_neutral'),
                'negative': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_negative')
              }}
              onchange={(value) => this.type = value}
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
              onchange={(value) => this.role = value}
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
          
          <div className="Form-group">
            <Button 
              type="submit"
              className="Button Button--primary" 
              loading={this.loading}
              disabled={!this.comment.trim()}
              onclick={() => this.onsubmit()}
            >
              {app.translator.trans('huseyinfiliz-traderfeedback.forum.form.submit_button')}
            </Button>
          </div>
        </div>
      </div>
    );
  }
  
  onsubmit(e) {
    if (e) e.preventDefault();
    
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
    
    this.loading = true;
    m.redraw();
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/feedback',
      body: {
        data: {
          type: 'feedbacks',
          attributes: {
            to_user_id: this.user.id(),
            type: this.type,
            role: this.role,
            comment: this.comment
          }
        }
      }
    })
    .then(response => {
      app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.success'));
      this.hide();
      window.location.reload();
    })
    .catch(error => {
      this.loading = false;
      m.redraw();
      
      if (error.response && error.response.errors) {
        app.alerts.show({ type: 'error' }, error.response.errors[0].detail);
      } else {
        app.alerts.show({ type: 'error' }, 'Error submitting feedback');
      }
    });
  }
}