import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import Select from 'flarum/common/components/Select';
import Stream from 'flarum/common/utils/Stream';
import app from 'flarum/forum/app';

export default class FeedbackModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);
    
    this.user = this.attrs.user;
    this.loading = false;
    
    this.type = Stream('positive');
    this.role = Stream('buyer');
    this.comment = Stream('');
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
    return m('.Modal-body', [
      m('.Form', [
        m('.Form-group', [
          m('label', app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_label')),
          Select.component({
            value: this.type(),
            options: {
              'positive': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_positive'),
              'neutral': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_neutral'),
              'negative': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_negative')
            },
            onchange: this.type
          })
        ]),
        
        m('.Form-group', [
          m('label', app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_label')),
          Select.component({
            value: this.role(),
            options: {
              'buyer': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_buyer'),
              'seller': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_seller'),
              'trader': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.role_trader')
            },
            onchange: this.role
          })
        ]),
        
        
        m('.Form-group', [
          m('label', app.translator.trans('huseyinfiliz-traderfeedback.forum.form.comment_label')),
          m('textarea.FormControl', {
            value: this.comment(),
            oninput: (e) => this.comment(e.target.value),
            placeholder: app.translator.trans('huseyinfiliz-traderfeedback.forum.form.comment_placeholder'),
            rows: 5
          })
        ]),
        
        m('.Form-group', [
          Button.component({
            type: 'submit',
            className: 'Button Button--primary',
            loading: this.loading,
            disabled: !this.comment().trim()
          }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.submit_button'))
        ])
      ])
    ]);
  }
  
  onsubmit(e) {
    e.preventDefault();
    
    if (!this.comment().trim()) return;
    
    const minLength = app.forum.attribute('huseyinfiliz.traderfeedback.minLength') || 10;
    const maxLength = app.forum.attribute('huseyinfiliz.traderfeedback.maxLength') || 1000;
    
    if (this.comment().length < minLength) {
      app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.error_too_short', {min: minLength}));
      return;
    }
    
    if (this.comment().length > maxLength) {
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
            type: this.type(),
            role: this.role(),
            comment: this.comment()
          }
        }
      }
    })
    .then(() => {
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