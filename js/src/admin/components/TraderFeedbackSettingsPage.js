import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Button from 'flarum/common/components/Button';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import Switch from 'flarum/common/components/Switch';
import humanTime from 'flarum/common/helpers/humanTime';
import app from 'flarum/admin/app';

export default class TraderFeedbackSettingsPage extends ExtensionPage {
  oninit(vnode) {
    super.oninit(vnode);
    
    this.activeTab = 'settings';
    this.loading = false;
    this.reports = [];
    this.pendingFeedbacks = [];
  }
  
  content() {
    return m('div', {className: 'TraderFeedbackSettingsPage'}, [
      m('div', {className: 'ExtensionPage-header'}, 
        m('div', {className: 'container'},
          m('h2', 'Trader Feedback')
        )
      ),
      
      m('div', {className: 'ExtensionPage-tabs'},
        m('div', {className: 'container'},
          m('nav', {className: 'ExtensionPage-tabsNav'}, [
            m('button', {
              className: 'Button' + (this.activeTab === 'settings' ? ' Button--primary' : ''),
              onclick: () => {
                this.activeTab = 'settings';
                m.redraw();
              }
            }, [
              m('i', {className: 'fas fa-cog'}),
              ' Settings'
            ]),
            
            m('button', {
              className: 'Button' + (this.activeTab === 'reports' ? ' Button--primary' : ''),
              onclick: () => {
                this.activeTab = 'reports';
                if (this.reports.length === 0) this.loadReports();
                m.redraw();
              }
            }, [
              m('i', {className: 'fas fa-flag'}),
              ' Reports ',
              this.reports.length > 0 ? m('span', {className: 'Badge'}, this.reports.length) : null
            ]),
            
            m('button', {
              className: 'Button' + (this.activeTab === 'approvals' ? ' Button--primary' : ''),
              onclick: () => {
                this.activeTab = 'approvals';
                if (this.pendingFeedbacks.length === 0) this.loadPendingFeedbacks();
                m.redraw();
              }
            }, [
              m('i', {className: 'fas fa-check-circle'}),
              ' Pending Approvals ',
              this.pendingFeedbacks.length > 0 ? m('span', {className: 'Badge'}, this.pendingFeedbacks.length) : null
            ])
          ])
        )
      ),
      
      m('div', {className: 'ExtensionPage-content'},
        m('div', {className: 'container'},
          this.currentTabContent()
        )
      )
    ]);
  }
  
  currentTabContent() {
    switch(this.activeTab) {
      case 'settings':
        return this.settingsContent();
      case 'reports':
        return this.reportsContent();
      case 'approvals':
        return this.approvalsContent();
      default:
        return null;
    }
  }
  
  settingsContent() {
    return m('div', {className: 'TraderFeedbackSettings'}, [
      m('.Form', [
        this.buildSettingComponent({
          type: 'boolean',
          setting: 'huseyinfiliz.traderfeedback.requireApproval',
          label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_approval_label')
        }),
        
        this.buildSettingComponent({
          type: 'boolean', 
          setting: 'huseyinfiliz.traderfeedback.allowNegative',
          label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.allow_negative_label')
        }),
        
        this.buildSettingComponent({
          type: 'number',
          setting: 'huseyinfiliz.traderfeedback.minLength',
          label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_length_label'),
          min: 1
        }),
        
        this.buildSettingComponent({
          type: 'number',
          setting: 'huseyinfiliz.traderfeedback.maxLength',
          label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.max_length_label'),
          min: 1
        }),
        
        this.buildSettingComponent({
          type: 'number',
          setting: 'huseyinfiliz.traderfeedback.minDays',
          label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_days_label'),
          help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_days_help'),
          min: 0
        }),
        
        this.buildSettingComponent({
          type: 'number',
          setting: 'huseyinfiliz.traderfeedback.minPosts',
          label: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_posts_label'),
          help: app.translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_posts_help'),
          min: 0
        }),
        
        this.submitButton()
      ])
    ]);
  }
  
  reportsContent() {
    if (this.loading) {
      return m(LoadingIndicator);
    }
    
    if (this.reports.length === 0) {
      return m('div', {className: 'TraderFeedbackReports-empty'}, [
        m('i', {className: 'fas fa-inbox fa-3x'}),
        m('p', app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.no_reports')),
        m(Button, {
          className: 'Button Button--primary',
          onclick: () => this.loadReports()
        }, [
          m('i', {className: 'fas fa-sync'}),
          ' Refresh'
        ])
      ]);
    }
    
    return m('div', {className: 'TraderFeedbackReports'}, [
      m('div', {className: 'TraderFeedbackReports-header'},
        m(Button, {
          className: 'Button Button--primary',
          onclick: () => this.loadReports()
        }, [
          m('i', {className: 'fas fa-sync'}),
          ' Refresh'
        ])
      ),
      this.reports.map(report => this.reportItem(report))
    ]);
  }
  
  approvalsContent() {
    if (this.loading) {
      return m(LoadingIndicator);
    }
    
    if (this.pendingFeedbacks.length === 0) {
      return m('div', {className: 'TraderFeedbackApprovals-empty'}, [
        m('i', {className: 'fas fa-check-circle fa-3x'}),
        m('p', app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.no_approvals')),
        m(Button, {
          className: 'Button Button--primary',
          onclick: () => this.loadPendingFeedbacks()
        }, [
          m('i', {className: 'fas fa-sync'}),
          ' Refresh'
        ])
      ]);
    }
    
    return m('div', {className: 'TraderFeedbackApprovals'}, [
      m('div', {className: 'TraderFeedbackApprovals-header'},
        m(Button, {
          className: 'Button Button--primary',
          onclick: () => this.loadPendingFeedbacks()
        }, [
          m('i', {className: 'fas fa-sync'}),
          ' Refresh'
        ])
      ),
      this.pendingFeedbacks.map(feedback => this.feedbackItem(feedback))
    ]);
  }
  
  reportItem(report) {
    return m('div', {className: 'TraderFeedbackReportItem', key: report.id}, [
      m('div', {className: 'TraderFeedbackReportItem-header'}, [
        m('strong', 'Report #' + report.id),
        m('span', humanTime(report.attributes?.created_at))
      ]),
      m('div', {className: 'TraderFeedbackReportItem-content'}, [
        m('p', [
          m('strong', 'Reason: '),
          report.attributes?.reason
        ])
      ]),
      m('div', {className: 'TraderFeedbackReportItem-actions'}, [
        m(Button, {
          className: 'Button Button--success Button--sm',
          onclick: () => this.dismissReport(report)
        }, 'Dismiss'),
        m(Button, {
          className: 'Button Button--danger Button--sm',
          onclick: () => this.deleteReportedFeedback(report)
        }, 'Delete Feedback')
      ])
    ]);
  }
  
  feedbackItem(feedback) {
    const attrs = feedback.attributes || {};
    
    return m('div', {
      className: 'TraderFeedbackApprovalItem feedback-' + attrs.type,
      key: feedback.id
    }, [
      m('div', {className: 'TraderFeedbackApprovalItem-header'}, [
        m('span', {className: 'feedback-type feedback-type-' + attrs.type}, attrs.type),
        m('span', humanTime(attrs.created_at))
      ]),
      m('div', {className: 'TraderFeedbackApprovalItem-content'}, [
        m('p', attrs.comment)
      ]),
      m('div', {className: 'TraderFeedbackApprovalItem-actions'}, [
        m(Button, {
          className: 'Button Button--success Button--sm',
          onclick: () => this.approveFeedback(feedback)
        }, 'Approve'),
        m(Button, {
          className: 'Button Button--danger Button--sm',
          onclick: () => this.rejectFeedback(feedback)
        }, 'Reject')
      ])
    ]);
  }
  
  loadReports() {
    this.loading = true;
    m.redraw();
    
    app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/trader/reports'
    }).then(response => {
      this.reports = response.data || [];
      this.loading = false;
      m.redraw();
    }).catch(error => {
      this.loading = false;
      app.alerts.show({ type: 'error' }, 'Failed to load reports');
      m.redraw();
    });
  }
  
  loadPendingFeedbacks() {
    this.loading = true;
    m.redraw();
    
    app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/trader/feedback/pending'
    }).then(response => {
      this.pendingFeedbacks = response.data || [];
      this.loading = false;
      m.redraw();
    }).catch(error => {
      this.loading = false;
      app.alerts.show({ type: 'error' }, 'Failed to load pending feedbacks');
      m.redraw();
    });
  }
  
  dismissReport(report) {
    if (!confirm('Dismiss this report?')) return;
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/dismiss'
    }).then(() => {
      app.alerts.show({ type: 'success' }, 'Report dismissed');
      this.loadReports();
    });
  }
  
  deleteReportedFeedback(report) {
    if (!confirm('Delete the reported feedback?')) return;
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/reject'
    }).then(() => {
      app.alerts.show({ type: 'success' }, 'Feedback deleted');
      this.loadReports();
    });
  }
  
  approveFeedback(feedback) {
    if (!confirm('Approve this feedback?')) return;
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/approve'
    }).then(() => {
      app.alerts.show({ type: 'success' }, 'Feedback approved');
      this.loadPendingFeedbacks();
    });
  }
  
  rejectFeedback(feedback) {
    if (!confirm('Reject this feedback?')) return;
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/reject'
    }).then(() => {
      app.alerts.show({ type: 'success' }, 'Feedback rejected');
      this.loadPendingFeedbacks();
    });
  }
}