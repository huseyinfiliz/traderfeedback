import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import Icon from 'flarum/common/components/Icon';
import Avatar from 'flarum/common/components/Avatar';
import Badge from 'flarum/common/components/Badge';
import Link from 'flarum/common/components/Link';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import humanTime from 'flarum/common/helpers/humanTime';
import username from 'flarum/common/helpers/username';
import app from 'flarum/forum/app';

export default class ModerateUserFeedbackModal extends Modal {
  user: any;
  activeTab: string = 'pending';
  loadingPending: boolean = true;
  loadingReports: boolean = true;
  actionLoading: boolean = false;
  pendingFeedbacks: any[] = [];
  pendingIncluded: any[] = [];
  reports: any[] = [];
  reportsIncluded: any[] = [];

  oninit(vnode: any) {
    super.oninit(vnode);

    this.user = this.attrs.user;
    const pendingCount = (this.user && this.user.attribute('pendingFeedbackCount')) || 0;
    const reportCount = (this.user && this.user.attribute('pendingReportCount')) || 0;

    this.activeTab = pendingCount === 0 && reportCount > 0 ? 'reports' : 'pending';

    this.loadPendingFeedbacks();
    this.loadReports();
  }

  className() {
    return 'Modal Modal--large ModerateUserFeedbackModal';
  }

  title() {
    return app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.title', {
      username: this.user ? this.user.displayName() : '',
    });
  }

  loadPendingFeedbacks() {
    if (!this.user) return;

    this.loadingPending = true;

    app
      .request({
        method: 'GET',
        url: app.forum.attribute('apiUrl') + '/trader/feedback/pending',
        params: {
          filter: {
            user: this.user.id(),
          },
        },
      })
      .then((response: any) => {
        this.pendingFeedbacks = response.data || [];
        this.pendingIncluded = response.included || [];
        this.loadingPending = false;
        m.redraw();
      })
      .catch(() => {
        this.pendingFeedbacks = [];
        this.loadingPending = false;
        m.redraw();
      });
  }

  loadReports() {
    if (!this.user) return;

    this.loadingReports = true;

    app
      .request({
        method: 'GET',
        url: app.forum.attribute('apiUrl') + '/trader/reports',
        params: {
          filter: {
            user: this.user.id(),
          },
        },
      })
      .then((response: any) => {
        this.reports = response.data || [];
        this.reportsIncluded = response.included || [];
        this.loadingReports = false;
        m.redraw();
      })
      .catch(() => {
        this.reports = [];
        this.loadingReports = false;
        m.redraw();
      });
  }

  getUser(userId: string | number, included: any[]) {
    if (!userId) return null;
    const idStr = String(userId);
    let user = app.store.getById('users', idStr);
    if (!user && included && Array.isArray(included)) {
      const data = included.find((item: any) => item.type === 'users' && String(item.id) === idStr);
      if (data) {
        user = app.store.pushPayload({ data });
      }
    }
    return user;
  }

  renderUser(user: any, userId: any) {
    if (user) {
      return (
        <Link href={app.route.user(user)} className="ModerateUserFeedbackModal-userLink" target="_blank">
          <Avatar user={user} />
          <strong>{username(user)}</strong>
        </Link>
      );
    }

    return (
      <span className="ModerateUserFeedbackModal-userPlaceholder">
        <Avatar user={null} />
        <strong>{app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.user_id_format', { id: userId })}</strong>
      </span>
    );
  }

  approveFeedback(feedback: any) {
    if (!confirm(app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.confirm_approve'))) {
      return;
    }

    this.actionLoading = true;
    m.redraw();

    app
      .request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/approve',
      })
      .then(() => {
        this.actionLoading = false;
        this.pendingFeedbacks = this.pendingFeedbacks.filter((f) => f.id !== feedback.id);
        app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.approved_success'));
        this.attrs.onAction?.();
        m.redraw();
      })
      .catch(() => {
        this.actionLoading = false;
        app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.error'));
        m.redraw();
      });
  }

  rejectFeedback(feedback: any) {
    if (!confirm(app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.confirm_reject'))) {
      return;
    }

    this.actionLoading = true;
    m.redraw();

    app
      .request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/reject',
      })
      .then(() => {
        this.actionLoading = false;
        this.pendingFeedbacks = this.pendingFeedbacks.filter((f) => f.id !== feedback.id);
        app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.rejected_success'));
        this.attrs.onAction?.();
        m.redraw();
      })
      .catch(() => {
        this.actionLoading = false;
        app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.error'));
        m.redraw();
      });
  }

  dismissReport(report: any) {
    if (!confirm(app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.confirm_dismiss'))) {
      return;
    }

    this.actionLoading = true;
    m.redraw();

    app
      .request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/dismiss',
      })
      .then(() => {
        this.actionLoading = false;
        this.reports = this.reports.filter((r) => r.id !== report.id);
        app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.dismissed_success'));
        this.attrs.onAction?.();
        m.redraw();
      })
      .catch(() => {
        this.actionLoading = false;
        app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.error'));
        m.redraw();
      });
  }

  deleteReportFeedback(report: any) {
    if (!confirm(app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.confirm_delete'))) {
      return;
    }

    this.actionLoading = true;
    m.redraw();

    app
      .request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/reject',
      })
      .then(() => {
        this.actionLoading = false;
        this.reports = this.reports.filter((r) => r.id !== report.id);
        app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.deleted_success'));
        this.attrs.onAction?.();
        m.redraw();
      })
      .catch(() => {
        this.actionLoading = false;
        app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.error'));
        m.redraw();
      });
  }

  content() {
    return (
      <div className="Modal-body ModerateUserFeedbackModal-body">
        <div className="TraderFeedbackTabs ModerateUserFeedbackModal-tabs">
          <button
            type="button"
            className={'TabButton' + (this.activeTab === 'pending' ? ' active' : '')}
            onclick={() => {
              this.activeTab = 'pending';
            }}
          >
            <Icon name="fas fa-circle-check" />
            <span>{app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.pending_tab')}</span>
            {this.pendingFeedbacks.length > 0 && <span className="TabButton-badge">{this.pendingFeedbacks.length}</span>}
          </button>
          <button
            type="button"
            className={'TabButton' + (this.activeTab === 'reports' ? ' active' : '')}
            onclick={() => {
              this.activeTab = 'reports';
            }}
          >
            <Icon name="fas fa-flag" />
            <span>{app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.reports_tab')}</span>
            {this.reports.length > 0 && <span className="TabButton-badge TabButton-badge--warning">{this.reports.length}</span>}
          </button>
        </div>

        <div className="ModerateUserFeedbackModal-content">
          {this.activeTab === 'pending' ? this.pendingContent() : this.reportsContent()}
        </div>
      </div>
    );
  }

  pendingContent() {
    if (this.loadingPending) {
      return (
        <div className="ModerateUserFeedbackModal-loading">
          <LoadingIndicator />
        </div>
      );
    }

    if (this.pendingFeedbacks.length === 0) {
      return (
        <div className="ModerateUserFeedbackModal-empty">
          <div className="ModerateUserFeedbackModal-emptyIcon">
            <Icon name="fas fa-circle-check" />
          </div>
          <p>{app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.no_pending')}</p>
        </div>
      );
    }

    return (
      <div className="ModerateUserFeedbackModal-list">
        {this.pendingFeedbacks.map((feedback) => {
          const attrs = feedback.attributes || {};
          const fromUserId = feedback.relationships?.fromUser?.data?.id || feedback.relationships?.from?.data?.id || attrs.fromUserId || attrs.from_user_id;
          const toUserId = feedback.relationships?.toUser?.data?.id || feedback.relationships?.to?.data?.id || attrs.toUserId || attrs.to_user_id;

          const fromUser = this.getUser(fromUserId, this.pendingIncluded);
          const toUser = this.getUser(toUserId, this.pendingIncluded);

          const type = attrs.type || 'positive';
          const typeIcon = type === 'positive' ? 'thumbs-up' : type === 'negative' ? 'thumbs-down' : 'minus';
          const role = attrs.role || 'buyer';
          const roleIcon = role === 'buyer' ? 'cart-shopping' : role === 'seller' ? 'store' : 'right-left';
          const date = attrs.created_at || attrs.createdAt;

          return (
            <div className={`FeedbackCard FeedbackCard--${type}`} key={feedback.id}>
              <div className="FeedbackCard-header">
                <div className="FeedbackCard-users">
                  <div className="FeedbackCard-user">{this.renderUser(fromUser, fromUserId)}</div>
                  <Icon name="fas fa-arrow-right" className="FeedbackCard-arrow" />
                  <div className="FeedbackCard-user">{this.renderUser(toUser, toUserId)}</div>
                </div>

                <div className="FeedbackCard-meta">
                  <Badge
                    type={type === 'positive' ? 'success' : type === 'negative' ? 'danger' : 'warning'}
                    icon={`fas fa-${typeIcon}`}
                  />

                  <span className="FeedbackCard-roleBadge">
                    <Icon name={`fas fa-${roleIcon}`} />
                    <span>{app.translator.trans(`huseyinfiliz-traderfeedback.forum.form.role_${role}`)}</span>
                  </span>

                  {date && (
                    <span className="FeedbackCard-dateBadge">
                      <Icon name="far fa-clock" />
                      <span>{humanTime(new Date(date))}</span>
                    </span>
                  )}
                </div>
              </div>

              {attrs.comment && (
                <div className="FeedbackCard-comment">
                  <p>{attrs.comment}</p>
                </div>
              )}

              {attrs.discussion_id && (
                <a
                  href={app.route('discussion', { id: attrs.discussion_id })}
                  className="FeedbackCard-discussion"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  <Icon name="fas fa-comments" />
                  <span>{app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.discussion_link')}</span>
                </a>
              )}

              <div className="FeedbackCard-actions">
                <Button
                  className="Button Button--primary"
                  icon="fas fa-check"
                  disabled={this.actionLoading}
                  onclick={() => this.approveFeedback(feedback)}
                >
                  {app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.approve_button')}
                </Button>
                <Button
                  className="Button Button--danger"
                  icon="fas fa-xmark"
                  disabled={this.actionLoading}
                  onclick={() => this.rejectFeedback(feedback)}
                >
                  {app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.reject_button')}
                </Button>
              </div>
            </div>
          );
        })}
      </div>
    );
  }

  reportsContent() {
    if (this.loadingReports) {
      return (
        <div className="ModerateUserFeedbackModal-loading">
          <LoadingIndicator />
        </div>
      );
    }

    if (this.reports.length === 0) {
      return (
        <div className="ModerateUserFeedbackModal-empty">
          <div className="ModerateUserFeedbackModal-emptyIcon">
            <Icon name="fas fa-flag" />
          </div>
          <p>{app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.no_reports')}</p>
        </div>
      );
    }

    return (
      <div className="ModerateUserFeedbackModal-list">
        {this.reports.map((report) => {
          const reportAttrs = report.attributes || {};
          const reporterId = report.relationships?.reporter?.data?.id || report.relationships?.user?.data?.id;
          const reporter = this.getUser(reporterId, this.reportsIncluded);
          const reportDate = reportAttrs.created_at || reportAttrs.createdAt;

          const feedbackId = report.relationships?.feedback?.data?.id;
          const feedback = this.reportsIncluded.find((item: any) => item.type === 'trader-feedbacks' && String(item.id) === String(feedbackId));

          let fromUser = null;
          let toUser = null;
          let fromUserId = null;
          let toUserId = null;
          let fbAttrs: any = {};
          let fbType = 'positive';
          let fbTypeIcon = 'thumbs-up';

          if (feedback) {
            fbAttrs = feedback.attributes || {};
            fromUserId = feedback.relationships?.fromUser?.data?.id || feedback.relationships?.from?.data?.id || fbAttrs.fromUserId || fbAttrs.from_user_id;
            toUserId = feedback.relationships?.toUser?.data?.id || feedback.relationships?.to?.data?.id || fbAttrs.toUserId || fbAttrs.to_user_id;
            fromUser = this.getUser(fromUserId, this.reportsIncluded);
            toUser = this.getUser(toUserId, this.reportsIncluded);
            fbType = fbAttrs.type || 'positive';
            fbTypeIcon = fbType === 'positive' ? 'thumbs-up' : fbType === 'negative' ? 'thumbs-down' : 'minus';
          }

          return (
            <div className="ReportCard" key={report.id}>
              <div className="ReportCard-header">
                <div className="ReportCard-reporter">
                  {this.renderUser(reporter, reporterId)}
                </div>

                {reportDate && (
                  <span className="ReportCard-dateBadge">
                    <Icon name="far fa-clock" />
                    <span>{humanTime(new Date(reportDate))}</span>
                  </span>
                )}
              </div>

              {reportAttrs.reason && (
                <div className="ReportCard-reason">
                  <strong>{app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.reason_label')}:</strong>
                  <p>{reportAttrs.reason}</p>
                </div>
              )}

              {feedback && (
                <div className="ReportCard-feedback">
                  <div className="ReportCard-feedbackHeader">
                    <div className="ReportCard-feedbackHeader-left">
                      <span>{app.translator.trans('huseyinfiliz-traderfeedback.admin.reports.reported_feedback_label')}</span>
                      <Badge
                        type={fbType === 'positive' ? 'success' : fbType === 'negative' ? 'danger' : 'warning'}
                        icon={`fas fa-${fbTypeIcon}`}
                      />
                    </div>
                  </div>

                  <div className="ReportCard-feedbackUsers">
                    <div className="FeedbackCard-user">{this.renderUser(fromUser, fromUserId)}</div>
                    <Icon name="fas fa-arrow-right" className="FeedbackCard-arrow" />
                    <div className="FeedbackCard-user">{this.renderUser(toUser, toUserId)}</div>
                  </div>

                  {fbAttrs.comment && (
                    <div className="ReportCard-feedbackComment">
                      <p>{fbAttrs.comment}</p>
                    </div>
                  )}
                </div>
              )}

              <div className="ReportCard-actions">
                <Button
                  className="Button Button--primary"
                  icon="fas fa-check"
                  disabled={this.actionLoading}
                  onclick={() => this.dismissReport(report)}
                >
                  {app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.dismiss_button')}
                </Button>
                <Button
                  className="Button Button--danger"
                  icon="fas fa-trash"
                  disabled={this.actionLoading}
                  onclick={() => this.deleteReportFeedback(report)}
                >
                  {app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.delete_button')}
                </Button>
              </div>
            </div>
          );
        })}
      </div>
    );
  }
}
