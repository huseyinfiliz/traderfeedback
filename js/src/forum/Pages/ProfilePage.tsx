import app from 'flarum/forum/app';
import UserPage from 'flarum/forum/components/UserPage';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import FeedbackModal from '../modals/FeedbackModal';
import ModerateUserFeedbackModal from '../modals/ModerateUserFeedbackModal';
import FeedbackStats from '../components/TraderFeedback/FeedbackStats';
import FeedbackFilters from '../components/TraderFeedback/FeedbackFilters';
import FeedbackList from '../components/TraderFeedback/FeedbackList';
import ReportModal from '../components/ReportModal';
import type Mithril from 'mithril';
import type User from 'flarum/common/models/User';
import type { FeedbackData } from '../../common/types';

export default class ProfilePage extends UserPage {
  feedbacks: FeedbackData[] = [];
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
        if (this.user?.id()) {
          app.store.find('users', this.user.id()!).then(() => m.redraw());
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
        if (this.user?.id()) {
          app.store.find('users', this.user.id()!).then(() => m.redraw());
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
        app.store.pushPayload(response);
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
          app.store.pushPayload(response);
          const data = response.data.attributes || response.data;
          this.stats = {
            score: data.score || 0,
            positive_count: data.positiveCount || 0,
            neutral_count: data.neutralCount || 0,
            negative_count: data.negativeCount || 0,
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
      response.included.forEach((item: any) => {
        if (item.type === 'users') {
          this.includedUsers.set(item.id, item.attributes);
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

  reportFeedback(feedback: FeedbackData) {
    app.modal.show(ReportModal, { feedback });
  }

  deleteFeedback(feedback: FeedbackData) {
    if (!confirm(String(app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.confirm_delete')))) {
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
        const record = app.store.getById('trader-feedbacks', feedback.id);
        if (record) app.store.remove(record);
        this.loadStats();
        if (this.user?.id()) app.store.find('users', this.user.id()!).then(() => m.redraw());
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
