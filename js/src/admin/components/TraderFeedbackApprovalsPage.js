import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import avatar from 'flarum/common/helpers/avatar';
import username from 'flarum/common/helpers/username';
import humanTime from 'flarum/common/helpers/humanTime';
import app from 'flarum/admin/app';
//import m from 'mithril'; // Import mithril

export default class TraderFeedbackApprovalsPage extends Component {
  oninit(vnode) {
    super.oninit(vnode);
    
    this.loading = true;
    this.feedbacks = [];
    
    this.loadFeedbacks();
  }
  
  loadFeedbacks() {
    this.loading = true;
    
    app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/trader/feedback/pending'
    })
    .then(response => {
      this.feedbacks = response.data;
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
      return <div className="TraderFeedbackApprovalsPage"><LoadingIndicator /></div>;
    }
    
    return (
      <div className="TraderFeedbackApprovalsPage">
        <div className="container">
          <h2>{app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.title')}</h2>
          
          {this.feedbacks.length === 0 ? (
            <div className="TraderFeedbackApprovalsPage-empty">
              {app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.no_approvals')}
            </div>
          ) : (
            <div className="TraderFeedbackApprovalsPage-list">
              {this.feedbacks.map(feedback => this.feedbackItem(feedback))}
            </div>
          )}
        </div>
      </div>
    );
  }
  
  feedbackItem(feedback) {
    return (
      <div className={`TraderFeedbackApprovalsPage-item ${feedback.type}`} key={feedback.id}>
        <div className="TraderFeedbackApprovalsPage-item-header">
          <div className="TraderFeedbackApprovalsPage-item-user">
            {avatar(feedback.fromUser)}
            {username(feedback.fromUser)} → {username(feedback.toUser)}
          </div>
          <div className="TraderFeedbackApprovalsPage-item-date">
            {humanTime(feedback.created_at)}
          </div>
        </div>
        
        <div className="TraderFeedbackApprovalsPage-item-meta">
          <span className={`TraderFeedbackApprovalsPage-item-type ${feedback.type}`}>
            {app.translator.trans(`huseyinfiliz-traderfeedback.forum.form.type_${feedback.type}`)}
          </span>
          <span className="TraderFeedbackApprovalsPage-item-role">
            {app.translator.trans(`huseyinfiliz-traderfeedback.forum.feedback_item.as_${feedback.role}`)}
          </span>
        </div>
        
        <div className="TraderFeedbackApprovalsPage-item-comment">
          {feedback.comment}
        </div>
        
        <div className="TraderFeedbackApprovalsPage-item-actions">
          <Button
            className="Button Button--primary"
            onclick={() => this.approveFeedback(feedback)}
          >
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.approve_button')}
          </Button>
          
          <Button
            className="Button Button--danger"
            onclick={() => this.rejectFeedback(feedback)}
          >
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.reject_button')}
          </Button>
        </div>
      </div>
    );
  }
  
  approveFeedback(feedback) {
    if (confirm(app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.confirm_approve'))) {
      app.request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/approve'
      })
      .then(() => {
        this.loadFeedbacks();
      });
    }
  }
  
  rejectFeedback(feedback) {
    if (confirm(app.translator.trans('huseyinfiliz-traderfeedback.admin.approvals.confirm_reject'))) {
      app.request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/reject'
      })
      .then(() => {
        this.loadFeedbacks();
      });
    }
  }
}