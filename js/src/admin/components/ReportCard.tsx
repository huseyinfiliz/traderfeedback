import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import avatar from 'flarum/common/helpers/avatar';
import humanTime from 'flarum/common/helpers/humanTime';

export default class ReportCard extends Component {
  view() {
    const { report, included, onDismiss, onDelete } = this.attrs;
    
    const reporterId = report.relationships?.reporter?.data?.id || report.relationships?.user?.data?.id;
    
    // Get or push to store
    let reporter = reporterId ? app.store.getById('users', reporterId) : null;
    if (!reporter && reporterId) {
      const reporterData = included?.find((item: any) => item.type === 'users' && item.id === reporterId);
      if (reporterData) {
        reporter = app.store.pushPayload({ data: reporterData });
      }
    }
    
    const feedbackId = report.relationships?.feedback?.data?.id;
    const feedback = included?.find((item: any) => 
      item.type === 'trader-feedbacks' && item.id === feedbackId
    );

    const fromUserId = feedback?.relationships?.fromUser?.data?.id || feedback?.relationships?.from?.data?.id;
    const toUserId = feedback?.relationships?.toUser?.data?.id || feedback?.relationships?.to?.data?.id;
    
    // Get or push to store
    let fromUser = fromUserId ? app.store.getById('users', fromUserId) : null;
    let toUser = toUserId ? app.store.getById('users', toUserId) : null;
    
    if (!fromUser && fromUserId) {
      const fromData = included?.find((item: any) => item.type === 'users' && item.id === fromUserId);
      if (fromData) {
        fromUser = app.store.pushPayload({ data: fromData });
      }
    }
    
    if (!toUser && toUserId) {
      const toData = included?.find((item: any) => item.type === 'users' && item.id === toUserId);
      if (toData) {
        toUser = app.store.pushPayload({ data: toData });
      }
    }

    const typeClass = feedback?.attributes.type === 'positive' ? 'success' : 
                     feedback?.attributes.type === 'negative' ? 'danger' : 'warning';

    return (
      <div className="ReportCard">
        <div className="ReportCard-header">
          <div className="ReportCard-reporter">
            {reporter && avatar(reporter, { className: 'Avatar--small' })}
            <div className="ReportCard-reporterInfo">
              <span className="ReportCard-label">Reported by:</span>
              <strong>{reporter ? reporter.displayName() : (reporterId ? `User #${reporterId}` : 'Unknown')}</strong>
              {report.attributes.created_at && (
                <span className="ReportCard-date">
                  {humanTime(new Date(report.attributes.created_at))}
                </span>
              )}
            </div>
          </div>
          
          <span className="Badge Badge--danger">
            <i className="fas fa-flag"></i>
            Report
          </span>
        </div>

        {report.attributes.reason && (
          <div className="ReportCard-reason">
            <strong>Reason:</strong>
            <p>{report.attributes.reason}</p>
          </div>
        )}

        {feedback && (
          <div className="ReportCard-feedback">
            <div className="ReportCard-feedbackHeader">
              <span>Reported Feedback:</span>
              <span className={`Badge Badge--${typeClass}`}>
                {feedback.attributes.type}
              </span>
            </div>
            
            <div className="ReportCard-feedbackUsers">
              <div className="ReportCard-user">
                {fromUser && avatar(fromUser, { className: 'Avatar--small' })}
                <span>{fromUser ? fromUser.displayName() : `User #${fromUserId}`}</span>
              </div>
              <i className="fas fa-arrow-right"></i>
              <div className="ReportCard-user">
                {toUser && avatar(toUser, { className: 'Avatar--small' })}
                <span>{toUser ? toUser.displayName() : `User #${toUserId}`}</span>
              </div>
            </div>

            {feedback.attributes.comment && (
              <div className="ReportCard-feedbackComment">
                <i className="fas fa-quote-left"></i>
                <p>{feedback.attributes.comment}</p>
              </div>
            )}
          </div>
        )}

        <div className="ReportCard-actions">
          <Button
            className="Button Button--primary"
            icon="fas fa-check"
            onclick={() => onDismiss(report)}
          >
            Dismiss Report
          </Button>
          <Button
            className="Button Button--danger"
            icon="fas fa-trash"
            onclick={() => onDelete(report)}
          >
            Delete Feedback
          </Button>
        </div>
      </div>
    );
  }
}