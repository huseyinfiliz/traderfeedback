import app from 'flarum/forum/app';
import UserPage from 'flarum/forum/components/UserPage';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import FeedbackModal from '../modals/FeedbackModal';
import ModerateUserFeedbackModal from '../modals/ModerateUserFeedbackModal';
import FeedbackStats from '../components/TraderFeedback/FeedbackStats';
import FeedbackFilters from '../components/TraderFeedback/FeedbackFilters';
import FeedbackList from '../components/TraderFeedback/FeedbackList';
import type Mithril from 'mithril';
import type User from 'flarum/common/models/User';

export default class ProfilePage extends UserPage {
  feedbacks: any[] = [];
  stats: any = null;
  loading: boolean = true;
  statsLoading: boolean = true;
  filter: string = 'all';
  includedUsers: Map<string, any> = new Map();

  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);
    this.loading = true;
    this.statsLoading = true;
    this.loadUser(m.route.param('username'));
  }

  show(user: User): void {
    super.show(user);
    this.loadFeedbacks();
    this.loadStats();
  }

  content(): Mithril.Children {
    if (this.loading) {
      return (
        <div className="TraderFeedbackPage TraderFeedbackPage--loading">
          <LoadingIndicator />
        </div>
      );
    }

    return (
      <div className="TraderFeedbackPage">
        <div className="TraderFeedbackPage-header">
          <FeedbackStats stats={this.stats} loading={this.statsLoading} />
        </div>

        <FeedbackFilters
          filter={this.filter}
          user={this.user}
          onFilterChange={(value: string) => this.handleFilterChange(value)}
          onGiveFeedback={() => this.showFeedbackModal()}
          onModerate={() => this.showModerationModal()}
        />

        <FeedbackList
          feedbacks={this.feedbacks}
          includedUsers={this.includedUsers}
          onDelete={(feedback: any) => this.deleteFeedback(feedback)}
          onReport={(feedback: any) => this.reportFeedback(feedback)}
        />
      </div>
    );
  }

  handleFilterChange(value: string) {
    this.filter = value;
    this.loadFeedbacks();
  }

  showFeedbackModal() {
    app.modal.show(FeedbackModal, {
      user: this.user,
      onSubmit: () => {
        this.loadFeedbacks();
        this.loadStats();
        if (this.user) {
          app.store.find('users', this.user.id()).then(() => m.redraw());
        }
      },
    });
  }

  showModerationModal() {
    app.modal.show(ModerateUserFeedbackModal, {
      user: this.user,
      onAction: () => {
        this.loadFeedbacks();
        this.loadStats();
        if (this.user) {
          app.store.find('users', this.user.id()).then(() => m.redraw());
        }
      },
    });
  }

  loadFeedbacks() {
    if (!this.user) return;

    this.loading = true;

    app
      .request({
        method: 'GET',
        url: app.forum.attribute('apiUrl') + '/trader/feedback',
        params: {
          filter: {
            user: this.user.id(),
            type: this.filter === 'all' ? null : this.filter,
          },
        },
      })
      .then((response: any) => {
        this.processIncludedUsers(response);
        this.feedbacks = Array.isArray(response.data) ? response.data : [];
        this.loading = false;
        m.redraw();
      })
      .catch(() => {
        this.feedbacks = [];
        this.loading = false;
        m.redraw();
      });
  }

  loadStats() {
    if (!this.user) return;

    this.statsLoading = true;

    app
      .request({
        method: 'GET',
        url: app.forum.attribute('apiUrl') + '/trader/stats/' + this.user.id(),
      })
      .then((response: any) => {
        if (response && response.data) {
          const data = response.data.attributes || response.data;
          this.stats = {
            score: data.score || 0,
            positive_count: data.positive_count || 0,
            neutral_count: data.neutral_count || 0,
            negative_count: data.negative_count || 0,
          };
        } else {
          this.stats = this.getDefaultStats();
        }
        this.statsLoading = false;
        m.redraw();
      })
      .catch(() => {
        this.stats = this.getDefaultStats();
        this.statsLoading = false;
        m.redraw();
      });
  }

  processIncludedUsers(response: any) {
    if (response.included) {
      response.included.forEach((item) => {
        if (item.type === 'users') {
          this.includedUsers.set(item.id, item.attributes);

          const storeUser = app.store.getById('users', item.id);
          if (storeUser) {
            storeUser.pushAttributes(item.attributes);
          } else {
            app.store.pushPayload({ data: item });
          }
        }
      });
    }
  }

  getDefaultStats() {
    return {
      score: 0,
      positive_count: 0,
      neutral_count: 0,
      negative_count: 0,
    };
  }

  reportFeedback(feedback) {
    const reason = prompt(app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.reason_placeholder'));
    if (!reason || !reason.trim()) return;

    this.loading = true;
    m.redraw();

    app
      .request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/report',
        body: {
          data: {
            attributes: {
              reason: reason.trim(),
            },
          },
        },
      })
      .then(() => {
        this.loading = false;
        app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.success'));
        m.redraw();
      })
      .catch(() => {
        this.loading = false;
        app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.error'));
        m.redraw();
      });
  }

  deleteFeedback(feedback) {
    if (!confirm(app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.confirm_delete'))) {
      return;
    }

    this.loading = true;
    m.redraw();

    app
      .request({
        method: 'DELETE',
        url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id,
      })
      .then(() => {
        this.loading = false;
        this.feedbacks = this.feedbacks.filter((f) => f.id !== feedback.id);
        this.loadStats();
        app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.delete_success'));
        m.redraw();
      })
      .catch(() => {
        this.loading = false;
        app.alerts.show({ type: 'error' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.delete_error'));
        m.redraw();
      });
  }
}
