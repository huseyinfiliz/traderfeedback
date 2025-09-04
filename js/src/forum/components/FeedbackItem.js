import Component from 'flarum/common/Component';
import avatar from 'flarum/common/helpers/avatar';
import username from 'flarum/common/helpers/username';
import humanTime from 'flarum/common/helpers/humanTime';
import Button from 'flarum/common/components/Button';
import Dropdown from 'flarum/common/components/Dropdown';
import ReportModal from './ReportModal';
import app from 'flarum/forum/app';

export default class FeedbackItem extends Component {
  view() {
    const feedback = this.attrs.feedback;
    const user = feedback.fromUser();
    
    return (
      <div className={`FeedbackItem ${feedback.type()}`}>
        <div className="FeedbackItem-header">
          <div className="FeedbackItem-user">
            {avatar(user)}
            {username(user)}
          </div>
          <div className="FeedbackItem-date">
            {humanTime(feedback.createdAt())}
          </div>
        </div>
        
        <div className="FeedbackItem-meta">
          <span className={`FeedbackItem-type ${feedback.type()}`}>
            {app.translator.trans(`huseyinfiliz-traderfeedback.forum.form.type_${feedback.type()}`)}
          </span>
          <span className="FeedbackItem-role">
            {app.translator.trans(`huseyinfiliz-traderfeedback.forum.feedback_item.as_${feedback.role()}`)}
          </span>
        </div>
        
        <div className="FeedbackItem-comment">
          {feedback.comment()}
        </div>
        
        <div className="FeedbackItem-actions">
          {this.actionButtons(feedback)}
        </div>
      </div>
    );
  }
  
  actionButtons(feedback) {
    const currentUser = app.session.user;
    
    if (!currentUser) return null;
    
    const canModerate = currentUser.attribute('canModerateFeedback');
    const isOwn = currentUser.id() === feedback.fromUser().id();
    
    const items = [];
    
    if (!isOwn && !canModerate) {
      items.push(
        <Button 
          className="Button Button--link" 
          onclick={() => app.modal.show(ReportModal, { feedback })}
        >
          {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.report_button')}
        </Button>
      );
    }
    
    if (isOwn || canModerate) {
      items.push(
        <Button 
          className="Button Button--link" 
          onclick={() => {
            if (confirm(app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.confirm_delete'))) {
              feedback.delete().then(() => {
                window.location.reload();
              });
            }
          }}
        >
          {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.delete_button')}
        </Button>
      );
    }
    
    if (isOwn) {
      items.push(
        <Button 
          className="Button Button--link" 
          onclick={() => {
            // Edit functionality would be implemented here
            // For now, just show a message
            alert('Edit functionality not implemented yet');
          }}
        >
          {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.edit_button')}
        </Button>
      );
    }
    
    return items;
  }
}