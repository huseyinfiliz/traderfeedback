import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Button from 'flarum/common/components/Button';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import humanTime from 'flarum/common/helpers/humanTime';
import app from 'flarum/admin/app';

export default class TraderFeedbackApprovalsPage extends ExtensionPage {
  oninit(vnode) {
    super.oninit(vnode);
    
    this.loading = false;
    this.feedbacks = [];
    
    this.loadPendingFeedbacks();
  }
  
  content() {
    return (
      <div className="TraderFeedbackApprovalsPage">
        <div className="ExtensionPage-header">
          <div className="container">
            <h2>{app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.title')}</h2>
            <Button
              className="Button Button--primary"
              onclick={() => this.loadPendingFeedbacks()}
              loading={this.loading}
            >
              <i className="fas fa-sync"></i> Refresh
            </Button>
          </div>
        </div>
        
        <div className="ExtensionPage-content">
          <div className="container">
            {this.feedbacksList()}
          </div>
        </div>
      </div>
    );
  }
  
  feedbacksList() {
    if (this.loading) {
      return <LoadingIndicator />;
    }
    
    if (this.feedbacks.length === 0) {
      return (
        <div className="TraderFeedbackApprovalsPage-empty">
          <i className="fas fa-check-circle fa-3x"></i>
          <p>{app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.no_approvals')}</p>
        </div>
      );
    }
    
    return (
      <div className="TraderFeedbackApprovalsList">
        {this.feedbacks.map(feedback => this.feedbackItem(feedback))}
      </div>
    );
  }
  
  feedbackItem(feedback) {
    const attrs = feedback.attributes || {};
    const fromUser = feedback.relationships?.fromUser?.data;
    const toUser = feedback.relationships?.toUser?.data;
    
    return (
      <div className={`TraderFeedbackApprovalItem feedback-${attrs.type}`} key={feedback.id}>
        <div className="TraderFeedbackApprovalItem-header">
          <div className="TraderFeedbackApprovalItem-users">
            <strong>
              {fromUser ? `User #${fromUser.id}` : 'Unknown'} 
              {' → '}
              {toUser ? `User #${toUser.id}` : 'Unknown'}
            </strong>
          </div>
          <div className="TraderFeedbackApprovalItem-date">
            {humanTime(attrs.created_at)}
          </div>
        </div>
        
        <div className="TraderFeedbackApprovalItem-meta">
          <span className={`feedback-type feedback-type-${attrs.type}`}>
            {app.translator.trans(`huseyinfiliz-traderfeedback.forum.form.type_${attrs.type}`)}
          </span>
          <span className="feedback-role">
            {app.translator.trans(`huseyinfiliz-traderfeedback.forum.feedback_item.as_${attrs.role}`)}
          </span>
        </div>
        
        <div className="TraderFeedbackApprovalItem-comment">
          <p>{attrs.comment}</p>
        </div>
        
        <div className="TraderFeedbackApprovalItem-actions">
          <Button
            className="Button Button--success"
            onclick={() => this.approveFeedback(feedback)}
            loading={this.loading}
          >
            <i className="fas fa-check"></i> 
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.approve_button')}
          </Button>
          
          <Button
            className="Button Button--danger"
            onclick={() => this.rejectFeedback(feedback)}
            loading={this.loading}
          >
            <i className="fas fa-times"></i> 
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.reject_button')}
          </Button>
        </div>
      </div>
    );
  }
  
  loadPendingFeedbacks() {
    this.loading = true;
    m.redraw();
    
    app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/trader/feedback/pending'
    })
    .then(response => {
      this.feedbacks = response.data || [];
      this.loading = false;
      m.redraw();
    })
    .catch(error => {
      this.loading = false;
      app.alerts.show({ type: 'error' }, 'Failed to load pending feedbacks');
      m.redraw();
    });
  }
  
  approveFeedback(feedback) {
    if (!confirm(app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.confirm_approve'))) return;
    
    this.loading = true;
    m.redraw();
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/approve'
    })
    .then(() => {
      app.alerts.show({ type: 'success' }, 'Feedback approved');
      this.loadPendingFeedbacks();
    })
    .catch(error => {
      this.loading = false;
      app.alerts.show({ type: 'error' }, 'Failed to approve feedback');
      m.redraw();
    });
  }
  
  rejectFeedback(feedback) {
    if (!confirm(app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.confirm_reject'))) return;
    
    this.loading = true;
    m.redraw();
    
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/reject'
    })
    .then(() => {
      app.alerts.show({ type: 'success' }, 'Feedback rejected');
      this.loadPendingFeedbacks();
    })
    .catch(error => {
      this.loading = false;
      app.alerts.show({ type: 'error' }, 'Failed to reject feedback');
      m.redraw();
    });
  }
}