import Form from 'flarum/common/components/Form';
import FormModal from 'flarum/common/components/FormModal';
import Button from 'flarum/common/components/Button';
import app from 'flarum/forum/app';

export default class ReportModal extends FormModal {
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
        <Form className="Form--centered">
          <div className="Form-group">
            <label className="label" for="TraderFeedback-reportReason">
              {app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.reason_label')}
            </label>
            <textarea
              id="TraderFeedback-reportReason"
              name="reason"
              className="FormControl"
              rows="3"
              required
              maxlength="1000"
              disabled={this.loading}
              value={this.reason}
              placeholder={app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.reason_placeholder')}
              oninput={(e) => (this.reason = e.target.value)}
            />
          </div>

          <div className="Form-group">
            <Button className="Button Button--primary Button--block" type="submit" loading={this.loading} disabled={!this.reason.trim()}>
              {app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.submit_button')}
            </Button>
          </div>
        </Form>
      </div>
    );
  }

  onsubmit(e) {
    e.preventDefault();

    if (this.loading || !this.reason.trim()) return;

    this.loading = true;

    app
      .request({
        method: 'POST',
        url:
          app.forum.attribute('apiUrl') +
          '/trader/feedback/' +
          (typeof this.feedback.id === 'function' ? this.feedback.id() : this.feedback.id) +
          '/report',
        body: {
          data: { attributes: { reason: this.reason.trim() } },
        },
      })
      .then(() => {
        this.loading = false;
        this.hide();
        app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.success'));
      })
      .catch((error) => {
        this.loading = false;
        this.onerror(error);
      });
  }
}
