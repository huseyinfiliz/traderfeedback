import Component from 'flarum/common/Component';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import app from 'flarum/forum/app';

export default class FeedbackStats extends Component {
    view() {
        const { stats, loading } = this.attrs;

        if (loading) {
            return (
                <div className="TraderFeedbackPage-stats">
                    <LoadingIndicator size="small" />
                </div>
            );
        }

        if (!stats) {
            return null;
        }

        const total = stats.positive_count + stats.neutral_count + stats.negative_count;
        const scorePercentage = total > 0 ? Math.round(stats.score) : 0;

        return (
            <div className="TraderFeedbackPage-stats">
                <div className="TraderFeedbackPage-stat TraderFeedbackPage-stat--score">
                    <div className="TraderFeedbackPage-stat-value">{scorePercentage}%</div>
                    <div className="TraderFeedbackPage-stat-label">
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.score')}
                    </div>
                </div>

                <div className="TraderFeedbackPage-stat TraderFeedbackPage-stat--positive">
                    <div className="TraderFeedbackPage-stat-value">{stats.positive_count}</div>
                    <div className="TraderFeedbackPage-stat-label">
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.positive')}
                    </div>
                </div>

                <div className="TraderFeedbackPage-stat TraderFeedbackPage-stat--neutral">
                    <div className="TraderFeedbackPage-stat-value">{stats.neutral_count}</div>
                    <div className="TraderFeedbackPage-stat-label">
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.neutral')}
                    </div>
                </div>

                <div className="TraderFeedbackPage-stat TraderFeedbackPage-stat--negative">
                    <div className="TraderFeedbackPage-stat-value">{stats.negative_count}</div>
                    <div className="TraderFeedbackPage-stat-label">
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.negative')}
                    </div>
                </div>
            </div>
        );
    }
}