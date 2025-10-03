import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import avatar from 'flarum/common/helpers/avatar';
import humanTime from 'flarum/common/helpers/humanTime';

export default class ReportCard extends Component {
  view() {
    const { report, included, onDismiss, onDelete } = this.attrs;
    
    const reporterId = report.relationships?.reporter?.data?.id || report.relationships?.user?.data?.id;
    
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

    const badgeClass = feedback?.attributes.type === 'positive' ? 'Badge--success' : 
                      feedback?.attributes.type === 'negative' ? 'Badge--danger' : 'Badge--warning';

    const typeIcon = feedback?.attributes.type === 'positive' ? 'thumbs-up' : 
                    feedback?.attributes.type === 'negative' ? 'thumbs-down' : 'minus';

    return (
      <div className="ReportCard">
        <div className="ReportCard-header">
          <div className="ReportCard-reporter">
            {reporter && avatar(reporter)}
            <strong>
              {reporter 
                ? reporter.displayName() 
                : (reporterId 
                    ? app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.user_id_format', { id: reporterId })
                    : app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.unknown_user')
                  )
              }
            </strong>
          </div>
          
          {(report.attributes.created_at || report.attributes.updated_at) && (
            <span className="ReportCard-dateBadge">
              <i className="far fa-clock"></i>
              <span>{humanTime(new Date(report.attributes.created_at || report.attributes.updated_at))}</span>
            </span>
          )}
        </div>

        {report.attributes.reason && (
          <div className="ReportCard-reason">
            <strong>{app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.reason_label')}</strong>
            <p>{report.attributes.reason}</p>
          </div>
        )}

        {feedback && (
          <div className="ReportCard-feedback">
            <div className="ReportCard-feedbackHeader">
              <div className="ReportCard-feedbackHeader-left">
                <span>{app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.reported_feedback_label')}</span>
                <span className={`Badge ${badgeClass}`}>
                  <i className={`fas fa-${typeIcon}`}></i>
                </span>
              </div>
            </div>
            
            <div className="ReportCard-feedbackUsers">
              {fromUser && avatar(fromUser)}
              <span>
                {fromUser 
                  ? fromUser.displayName() 
                  : app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.user_id_format', { id: fromUserId })
                }
              </span>
              <i className="fas fa-arrow-right"></i>
              {toUser && avatar(toUser)}
              <span>
                {toUser 
                  ? toUser.displayName() 
                  : app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.user_id_format', { id: toUserId })
                }
              </span>
            </div>

            {feedback.attributes.comment && (
              <div className="ReportCard-feedbackComment">
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
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.dismiss_button')}
          </Button>
          <Button
            className="Button"
            icon="fas fa-trash"
            onclick={() => onDelete(report)}
          >
            {app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.delete_button')}
          </Button>
        </div>
      </div>
    );
  }
}