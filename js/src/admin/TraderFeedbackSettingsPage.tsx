import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import StatsCards from './components/StatsCards';
import FeedbackCard from './components/FeedbackCard';
import ReportCard from './components/ReportCard';
import SettingsTab from './components/SettingsTab';

export default class TraderFeedbackSettingsPage extends ExtensionPage {
  activeTab: string = 'settings';
  loading: boolean = false;
  reports: any[] = [];
  pendingFeedbacks: any[] = [];
  stats: any = null;
  included: any[] = [];

  oninit(vnode: any) {
    super.oninit(vnode);
    this.loadStats();
  }

  content() {
    return (
      <div className="TraderFeedbackPage">
        <StatsCards stats={this.stats} />
        {this.tabs()}
        <div className="TraderFeedbackPage-content">
          {this.activeTabContent()}
        </div>
      </div>
    );
  }

  tabs() {
    return (
      <div className="TraderFeedbackTabs">
        <button
          className={'TabButton' + (this.activeTab === 'settings' ? ' active' : '')}
          onclick={() => {
            this.activeTab = 'settings';
          }}
        >
          <i className="fas fa-cog"></i>
          <span>Settings</span>
        </button>

        <button
          className={'TabButton' + (this.activeTab === 'approvals' ? ' active' : '')}
          onclick={() => {
            this.activeTab = 'approvals';
            if (this.pendingFeedbacks.length === 0) this.loadPendingFeedbacks();
          }}
        >
          <i className="fas fa-check-circle"></i>
          <span>Pending Approvals</span>
          {this.pendingFeedbacks.length > 0 && (
            <span className="TabButton-badge">{this.pendingFeedbacks.length}</span>
          )}
        </button>

        <button
          className={'TabButton' + (this.activeTab === 'reports' ? ' active' : '')}
          onclick={() => {
            this.activeTab = 'reports';
            if (this.reports.length === 0) this.loadReports();
          }}
        >
          <i className="fas fa-flag"></i>
          <span>Reports</span>
          {this.reports.length > 0 && (
            <span className="TabButton-badge TabButton-badge--warning">{this.reports.length}</span>
          )}
        </button>
      </div>
    );
  }

  activeTabContent() {
    switch (this.activeTab) {
      case 'settings':
        return (
          <SettingsTab
            buildSettingComponent={this.buildSettingComponent.bind(this)}
            submitButton={this.submitButton.bind(this)}
          />
        );
      case 'approvals':
        return this.approvalsContent();
      case 'reports':
        return this.reportsContent();
      default:
        return null;
    }
  }

  approvalsContent() {
    if (this.loading) {
      return <LoadingIndicator />;
    }

    if (this.pendingFeedbacks.length === 0) {
      return (
        <div className="EmptyState">
          <div className="EmptyState-icon">
            <i className="fas fa-check-circle"></i>
          </div>
          <h3>No Pending Approvals</h3>
          <p>All feedbacks have been reviewed</p>
        </div>
      );
    }

    return (
      <div className="FeedbackList">
        {this.pendingFeedbacks.map((feedback) => (
          <FeedbackCard
            key={feedback.id}
            feedback={feedback}
            included={this.included}
            onApprove={(fb: any) => this.approveFeedback(fb)}
            onReject={(fb: any) => this.rejectFeedback(fb)}
          />
        ))}
      </div>
    );
  }

  reportsContent() {
    if (this.loading) {
      return <LoadingIndicator />;
    }

    if (this.reports.length === 0) {
      return (
        <div className="EmptyState">
          <div className="EmptyState-icon">
            <i className="fas fa-shield-alt"></i>
          </div>
          <h3>No Active Reports</h3>
          <p>Your community is safe and clean</p>
        </div>
      );
    }

    return (
      <div className="ReportList">
        {this.reports.map((report) => (
          <ReportCard
            key={report.id}
            report={report}
            included={this.included}
            onDismiss={(r: any) => this.dismissReport(r)}
            onDelete={(r: any) => this.deleteReportedFeedback(r)}
          />
        ))}
      </div>
    );
  }

  /**
   * Load stats - JSON API format parse
   */
  loadStats() {
    app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/trader/stats/summary',
    }).then((response: any) => {
      // JSON API formatını parse et
      let attrs;
      
      if (response.data && Array.isArray(response.data) && response.data[0]) {
        attrs = response.data[0].attributes;
      } else if (response.data && response.data.attributes) {
        attrs = response.data.attributes;
      } else if (response.attributes) {
        attrs = response.attributes;
      } else if (response.total !== undefined) {
        attrs = response;
      } else {
        attrs = { total: 0, positive: 0, neutral: 0, negative: 0 };
      }
      
      this.stats = {
        total: attrs.total || 0,
        positive: attrs.positive || 0,
        neutral: attrs.neutral || 0,
        negative: attrs.negative || 0,
      };
      
      console.log('✅ Stats loaded:', this.stats);
      m.redraw();
    }).catch((error) => {
      console.error('❌ Stats load error:', error);
      this.stats = { total: 0, positive: 0, neutral: 0, negative: 0 };
      m.redraw();
    });
  }

  loadPendingFeedbacks() {
    this.loading = true;
    this.included = [];
    m.redraw();

    app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/trader/feedback/pending',
    }).then((response: any) => {
      this.pendingFeedbacks = response.data || [];
      this.included = response.included || [];
      
      // Store'a kullanıcıları ekle
      this.processIncluded(response);
      
      this.loading = false;
      console.log('✅ Pending feedbacks loaded:', this.pendingFeedbacks.length);
      m.redraw();
    }).catch((error) => {
      console.error('❌ Pending feedbacks error:', error);
      this.loading = false;
      m.redraw();
    });
  }

  loadReports() {
    this.loading = true;
    this.included = [];
    m.redraw();

    app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/trader/reports',
    }).then((response: any) => {
      this.reports = response.data || [];
      this.included = response.included || [];
      
      // Store'a kullanıcıları ekle
      this.processIncluded(response);
      
      this.loading = false;
      console.log('✅ Reports loaded:', this.reports.length);
      m.redraw();
    }).catch((error) => {
      console.error('❌ Reports error:', error);
      app.alerts.show({ type: 'error' }, 'Failed to load reports');
      this.loading = false;
      m.redraw();
    });
  }

  processIncluded(response: any) {
    if (response.included) {
      response.included.forEach((item: any) => {
        if (item.type === 'users' && item.id) {
          const existing = app.store.getById('users', item.id);
          if (!existing) {
            const user = app.store.createRecord('users');
            user.pushAttributes(item.attributes || {});
            user.id(item.id);
          }
        }
      });
    }
  }

  approveFeedback(feedback: any) {
    if (!confirm('Approve this feedback?')) return;

    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/approve',
    }).then(() => {
      app.alerts.show({ type: 'success' }, 'Feedback approved');
      this.loadPendingFeedbacks();
      this.loadStats();
    }).catch((error) => {
      console.error('Approve error:', error);
      app.alerts.show({ type: 'error' }, 'Failed to approve feedback');
    });
  }

  rejectFeedback(feedback: any) {
    if (!confirm('Reject this feedback?')) return;

    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/reject',
    }).then(() => {
      app.alerts.show({ type: 'success' }, 'Feedback rejected');
      this.loadPendingFeedbacks();
      this.loadStats();
    }).catch((error) => {
      console.error('Reject error:', error);
      app.alerts.show({ type: 'error' }, 'Failed to reject feedback');
    });
  }

  dismissReport(report: any) {
    if (!confirm('Dismiss this report?')) return;

    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/dismiss',
    }).then(() => {
      app.alerts.show({ type: 'success' }, 'Report dismissed');
      this.loadReports();
    }).catch((error) => {
      console.error('Dismiss error:', error);
      app.alerts.show({ type: 'error' }, 'Failed to dismiss report');
    });
  }

  deleteReportedFeedback(report: any) {
    if (!confirm('Delete the reported feedback? This action cannot be undone.')) return;

    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/reject',
    }).then(() => {
      app.alerts.show({ type: 'success' }, 'Feedback deleted');
      this.loadReports();
      this.loadStats();
    }).catch((error) => {
      console.error('Delete error:', error);
      app.alerts.show({ type: 'error' }, 'Failed to delete feedback');
    });
  }
}