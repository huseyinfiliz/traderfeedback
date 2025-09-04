import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import avatar from 'flarum/common/helpers/avatar';
import username from 'flarum/common/helpers/username';
import humanTime from 'flarum/common/helpers/humanTime';
import app from 'flarum/admin/app';

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
      return m('div', {className: 'TraderFeedbackReportsPage'}, 
        m(LoadingIndicator)
      );
    }
    
    return m('div', {className: 'TraderFeedbackReportsPage'},
      m('div', {className: 'container'},
        m('h2', app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.title')),
        
        this.reports.length === 0
          ? m('div', {className: 'TraderFeedbackReportsPage-empty'},
              app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.no_reports')
            )
          : m('div', {className: 'TraderFeedbackReportsPage-list'},
              this.reports.map(report => this.reportItem(report))
            )
      )
    );
  }
  
  reportItem(report) {
    const feedback = report.feedback;
    const reporter = report.user;
    
    return m('div', {
      className: 'TraderFeedbackReportsPage-item',
      key: report.id
    }, [
      m('div', {className: 'TraderFeedbackReportsPage-item-header'}, [
        m('div', {className: 'TraderFeedbackReportsPage-item-user'},
          app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.user_reported', {
            username: username(reporter)
          })
        ),
        m('div', {className: 'TraderFeedbackReportsPage-item-date'},
          humanTime(report.created_at)
        )
      ]),
      
      m('div', {className: 'TraderFeedbackReportsPage-item-reason'}, [
        m('strong', app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.reason_label') + ':'),
        ' ',
        report.reason
      ]),
      
      m('div', {className: 'TraderFeedbackReportsPage-item-feedback'}, [
        m('div', {className: 'TraderFeedbackReportsPage-item-feedback-header'}, [
          m('div', {className: 'TraderFeedbackReportsPage-item-feedback-user'}, [
            avatar(feedback.fromUser),
            username(feedback.fromUser),
            ' → ',
            username(feedback.toUser)
          ]),
          m('div', {className: 'TraderFeedbackReportsPage-item-feedback-type'},
            feedback.type
          )
        ]),
        
        m('div', {className: 'TraderFeedbackReportsPage-item-feedback-comment'},
          feedback.comment
        )
      ]),
      
      m('div', {className: 'TraderFeedbackReportsPage-item-actions'}, [
        Button.component({
          className: 'Button Button--primary',
          onclick: () => this.approveReport(report)
        }, app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.approve_button')),
        
        Button.component({
          className: 'Button Button--danger',
          onclick: () => this.rejectReport(report)
        }, app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.reject_button')),
        
        Button.component({
          className: 'Button',
          onclick: () => this.dismissReport(report)
        }, app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.dismiss_button'))
      ])
    ]);
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