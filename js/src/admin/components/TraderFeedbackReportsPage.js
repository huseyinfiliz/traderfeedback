import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import avatar from 'flarum/common/helpers/avatar';
import username from 'flarum/common/helpers/username';
import humanTime from 'flarum/common/helpers/humanTime';
import app from 'flarum/admin/app';
//import m from 'mithril'; // Import mithril

export default class TraderFeedbackReportsPage extends Component {
  oninit(vnode) {
    super.oninit(vnode);
    
    this.loading = true;
    this.reports = [];
    
    this.loadReports();
  }
  
  loadReports() {
    this.loading = true;
    
    app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/trader/reports'
    })
    .then(response => {
      this.reports = response.data;
      this.loading = false;
      m.redraw();
    })
    .catch(error => {
      this.loading = false;
      m.redraw();
    });
  }
  
  view() {
    if (this.loading) {
      return <div className="TraderFeedbackReportsPage"><LoadingIndicator /></div>;
    }
    
    return (
      <div className="TraderFeedbackReportsPage">
        <div className="container">
          <h2>{app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.title')}</h2>
          
          {this.reports.length === 0 ? (
            <div className="TraderFeedbackReportsPage-empty">
              {app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.no_reports')}
            </div>
          ) : (
            <div className="TraderFeedbackReportsPage-list">
              {this.reports.map(report => this.reportItem(report))}
            </div>
          )}
        </div>
      </div>
    );
  }
  
  reportItem(report) {
    const feedback = report.feedback;
    const reporter = report.user;
    
    return (
      <div className="TraderFeedbackReportsPage-item" key={report.id}>
        <div className="TraderFeedbackReportsPage-item-header">
          <div className="TraderFeedbackReportsPage-item-user">
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.user_reported', {username: username(reporter)})}
          </div>
          <div className="TraderFeedbackReportsPage-item-date">
            {humanTime(report.created_at)}
          </div>
        </div>
        
        <div className="TraderFeedbackReportsPage-item-reason">
          <strong>{app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.reason_label')}:</strong> {report.reason}
        </div>
        
        <div className="TraderFeedbackReportsPage-item-feedback">
          <div className="TraderFeedbackReportsPage-item-feedback-header">
            <div className="TraderFeedbackReportsPage-item-feedback-user">
              {avatar(feedback.fromUser)}
              {username(feedback.fromUser)} → {username(feedback.toUser)}
            </div>
            <div className="TraderFeedbackReportsPage-item-feedback-type">
              {feedback.type}
            </div>
          </div>
          
          <div className="TraderFeedbackReportsPage-item-feedback-comment">
            {feedback.comment}
          </div>
        </div>
        
        <div className="TraderFeedbackReportsPage-item-actions">
          <Button
            className="Button Button--primary"
            onclick={() => this.approveReport(report)}
          >
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.approve_button')}
          </Button>
          
          <Button
            className="Button Button--danger"
            onclick={() => this.rejectReport(report)}
          >
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.reject_button')}
          </Button>
          
          <Button
            className="Button"
            onclick={() => this.dismissReport(report)}
          >
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.dismiss_button')}
          </Button>
        </div>
      </div>
    );
  }
  
  approveReport(report) {
    if (confirm(app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.confirm_approve'))) {
      app.request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/approve'
      })
      .then(() => {
        this.loadReports();
      });
    }
  }
  
  rejectReport(report) {
    if (confirm(app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.confirm_reject'))) {
      app.request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/reject'
      })
      .then(() => {
        this.loadReports();
      });
    }
  }
  
  dismissReport(report) {
    if (confirm(app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.confirm_dismiss'))) {
      app.request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/dismiss'
      })
      .then(() => {
        this.loadReports();
      });
    }
  }
}