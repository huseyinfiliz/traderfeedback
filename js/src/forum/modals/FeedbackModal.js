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
    this.isSubmitting = false;
    
    this.type = Stream('positive');
    this.role = Stream('buyer');
    this.comment = Stream('');
    this.discussionInput = Stream('');
    this.discussionId = null;
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
    const requireDiscussion = app.forum.attribute('huseyinfiliz.traderfeedback.requireDiscussion') || false;
    const allowNegative = app.forum.attribute('huseyinfiliz.traderfeedback.allowNegative') !== false;
    
    const typeOptions = {
      'positive': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_positive'),
      'neutral': app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_neutral')
    };
    
    if (allowNegative) {
      typeOptions['negative'] = app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_negative');
    }
    
    return m('.Modal-body', [
      m('.Form', [
        m('.Form-group', [
          m('label', app.translator.trans('huseyinfiliz-traderfeedback.forum.form.type_label')),
          Select.component({
            value: this.type(),
            options: typeOptions,
            onchange: this.type,
            disabled: this.isSubmitting
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
            onchange: this.role,
            disabled: this.isSubmitting
          })
        ]),
        
        m('.Form-group', [
          m('label', [
            app.translator.trans('huseyinfiliz-traderfeedback.forum.form.discussion_label'),
            requireDiscussion && m('span.required', ' *')
          ]),
          m('input.FormControl', {
            value: this.discussionInput(),
            oninput: (e) => {
              this.discussionInput(e.target.value);
              this.parseDiscussionId(e.target.value);
            },
            placeholder: app.translator.trans('huseyinfiliz-traderfeedback.forum.form.discussion_placeholder'),
            required: requireDiscussion,
            disabled: this.isSubmitting
          }),
          m('.helpText', app.translator.trans('huseyinfiliz-traderfeedback.forum.form.discussion_help'))
        ]),
        
        m('.Form-group', [
          m('label', app.translator.trans('huseyinfiliz-traderfeedback.forum.form.comment_label')),
          m('textarea.FormControl', {
            value: this.comment(),
            oninput: (e) => this.comment(e.target.value),
            placeholder: app.translator.trans('huseyinfiliz-traderfeedback.forum.form.comment_placeholder'),
            rows: 5,
            disabled: this.isSubmitting
          })
        ]),
        
        m('.Form-group', [
          Button.component({
            type: 'submit',
            className: 'Button Button--primary',
            loading: this.loading,
            disabled: this.isSubmitting || !this.comment().trim() || (requireDiscussion && !this.discussionId)
          }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.submit_button'))
        ])
      ])
    ]);
  }
  
  parseDiscussionId(input) {
    if (!input) {
      this.discussionId = null;
      return;
    }
    
    // Parse URL format: /d/{id}-{slug} or /d/{id}
    const urlMatch = input.match(/\/d\/(\d+)(?:-[^\/]+)?/);
    if (urlMatch) {
      this.discussionId = parseInt(urlMatch[1]);
      return;
    }
    
    // Check if it's just a number
    const idMatch = input.match(/^\d+$/);
    if (idMatch) {
      this.discussionId = parseInt(input);
      return;
    }
    
    this.discussionId = null;
  }
  
  onsubmit(e) {
    e.preventDefault();
    
    if (this.isSubmitting) {
      return;
    }
    
    if (!this.comment().trim()) return;
    
    const requireDiscussion = app.forum.attribute('huseyinfiliz.traderfeedback.requireDiscussion') || false;
    if (requireDiscussion && !this.discussionId) {
      app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.error_discussion_required'));
      return;
    }
    
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
    
    // Allow negative kontrolü - form submit edilirken ekstra kontrol
    const allowNegative = app.forum.attribute('huseyinfiliz.traderfeedback.allowNegative') !== false;
    if (!allowNegative && this.type() === 'negative') {
      app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.api.validation.negative_not_allowed'));
      return;
    }
    
    this.isSubmitting = true;
    this.loading = true;
    m.redraw(); // UI'ı hemen güncelle
    
    const data = {
      to_user_id: this.user.id(),
      type: this.type(),
      role: this.role(),
      comment: this.comment()
    };
    
    // Add discussion_id if provided
    if (this.discussionId) {
      data.discussion_id = this.discussionId;
    }
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/feedback',
      body: {
        data: {
          type: 'feedbacks',
          attributes: data
        }
      }
    })
    .then(() => {
      app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.success'));
      this.hide();
      window.location.reload();
    })
    .catch(error => {
      this.isSubmitting = false;
      this.loading = false;
      m.redraw();
      
      if (error.response && error.response.errors) {
        app.alerts.show({ type: 'error' }, error.response.errors[0].detail);
      } else {
        app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.error_submit'));
      }
    });
  }
}