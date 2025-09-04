import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import app from 'flarum/forum/app';

export default class ReportModal extends Modal {
  className() {
    return 'ReportModal Modal--small';
  }

  title() {
    return app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.title');
  }

  oninit(vnode) {
    super.oninit(vnode);
    this.feedback = this.attrs.feedback;
    this.reason = '';
  }

  content() {
    return (
      <div className="Modal-body">
        <div className="Form Form--centered">
          <div className="Form-group">
            <label className="label">
              {app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.reason_label')}
            </label>
            <textarea
              className="FormControl"
              rows="3"
              value={this.reason}
              placeholder={app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.reason_placeholder')}
              oninput={(e) => this.reason = e.target.value}
            />
          </div>

          <div className="Form-group">
            <Button
              className="Button Button--primary Button--block"
              type="submit"
              loading={this.loading}
              disabled={!this.reason.trim()}
            >
              {app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.submit_button')}
            </Button>
          </div>
        </div>
      </div>
    );
  }

  onsubmit(e) {
    e.preventDefault();
    
    this.loading = true;
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/feedback/' + this.feedback.id() + '/report',
      data: {
        reason: this.reason
      }
    })
    .then(() => {
      this.loading = false;
      this.hide();
      app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.success'));
    })
    .catch(error => {
      this.loading = false;
      this.onerror(error);
    });
  }
}