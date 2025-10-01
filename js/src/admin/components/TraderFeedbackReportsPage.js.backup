import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Button from 'flarum/common/components/Button';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import humanTime from 'flarum/common/helpers/humanTime';
import app from 'flarum/admin/app';

export default class TraderFeedbackReportsPage extends ExtensionPage {
  oninit(vnode) {
    super.oninit(vnode);
    
    this.loading = false;
    this.reports = [];
    
    this.loadReports();
  }
  
  content() {
    return (
      <div className="TraderFeedbackReportsPage">
        <div className="ExtensionPage-header">
          <div className="container">
            <h2>{app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.title')}</h2>
            <Button
              className="Button Button--primary"
              onclick={() => this.loadReports()}
              loading={this.loading}
            >
              <i className="fas fa-sync"></i> Refresh
            </Button>
          </div>
        </div>
        
        <div className="ExtensionPage-content">
          <div className="container">
            {this.reportsList()}
          </div>
        </div>
      </div>
    );
  }
  
  reportsList() {
    if (this.loading) {
      return <LoadingIndicator />;
    }
    
    if (this.reports.length === 0) {
      return (
        <div className="TraderFeedbackReportsPage-empty">
          <i className="fas fa-inbox fa-3x"></i>
          <p>{app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.no_reports')}</p>
        </div>
      );
    }
    
    return (
      <div className="TraderFeedbackReportsList">
        {this.reports.map(report => this.reportItem(report))}
      </div>
    );
  }
  
  reportItem(report) {
    const feedback = report.attributes || {};
    const reporter = report.relationships?.user?.data;
    const feedbackData = report.relationships?.feedback?.data;
    
    return (
      <div className="TraderFeedbackReportItem" key={report.id}>
        <div className="TraderFeedbackReportItem-header">
          <div className="TraderFeedbackReportItem-info">
            <strong>Report #{report.id}</strong>
            <span className="TraderFeedbackReportItem-date">
              {humanTime(report.attributes.created_at)}
            </span>
          </div>
        </div>
        
        <div className="TraderFeedbackReportItem-content">
          <div className="TraderFeedbackReportItem-reason">
            <strong>Reason:</strong>
            <p>{report.attributes.reason}</p>
          </div>
          
          {feedbackData && (
            <div className="TraderFeedbackReportItem-feedback">
              <strong>Reported Feedback:</strong>
              <div className="TraderFeedbackReportItem-feedback-info">
                <span className={`feedback-type feedback-type-${feedbackData.attributes?.type || 'neutral'}`}>
                  {feedbackData.attributes?.type || 'Unknown'}
                </span>
                <p>{feedbackData.attributes?.comment || 'No comment'}</p>
              </div>
            </div>
          )}
        </div>
        
        <div className="TraderFeedbackReportItem-actions">
          <Button
            className="Button Button--success"
            onclick={() => this.approveReport(report)}
            loading={this.loading}
          >
            <i className="fas fa-check"></i> Dismiss Report
          </Button>
          
          <Button
            className="Button Button--danger"
            onclick={() => this.rejectReport(report)}
            loading={this.loading}
          >
            <i className="fas fa-trash"></i> Delete Feedback
          </Button>
        </div>
      </div>
    );
  }
  
  loadReports() {
    this.loading = true;
    m.redraw();
    
    app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/trader/reports'
    })
    .then(response => {
      this.reports = response.data || [];
      this.loading = false;
      m.redraw();
    })
    .catch(error => {
      this.loading = false;
      app.alerts.show({ type: 'error' }, 'Failed to load reports');
      m.redraw();
    });
  }
  
  approveReport(report) {
    if (!confirm('Dismiss this report without taking action?')) return;
    
    this.loading = true;
    m.redraw();
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/dismiss'
    })
    .then(() => {
      app.alerts.show({ type: 'success' }, 'Report dismissed');
      this.loadReports();
    })
    .catch(error => {
      this.loading = false;
      app.alerts.show({ type: 'error' }, 'Failed to dismiss report');
      m.redraw();
    });
  }
  
  rejectReport(report) {
    if (!confirm('Delete the reported feedback? This action cannot be undone.')) return;
    
    this.loading = true;
    m.redraw();
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/reject'
    })
    .then(() => {
      app.alerts.show({ type: 'success' }, 'Feedback deleted and report resolved');
      this.loadReports();
    })
    .catch(error => {
      this.loading = false;
      app.alerts.show({ type: 'error' }, 'Failed to process report');
      m.redraw();
    });
  }
}