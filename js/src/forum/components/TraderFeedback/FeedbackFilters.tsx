import Component from 'flarum/common/Component';
import Select from 'flarum/common/components/Select';
import Button from 'flarum/common/components/Button';
import Icon from 'flarum/common/components/Icon';
import app from 'flarum/forum/app';

export default class FeedbackFilters extends Component {
  view() {
    const { filter, user, onFilterChange, onGiveFeedback, onModerate } = this.attrs;
    const allowNegative = app.forum.attribute('huseyinfiliz.traderfeedback.allowNegative') !== false;

    const filterOptions: any = {
      all: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.all'),
      positive: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.positive'),
      neutral: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.neutral'),
    };

    if (allowNegative) {
      filterOptions.negative = app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.negative');
    }

    const actor = app.session.user;
    const canModerate = Boolean(
      (actor && (actor.attribute('canModerateFeedback') || actor.attribute('huseyinfilizTraderAdmin'))) ||
      (user && user.attribute('canModerateFeedback'))
    );
    const pendingFeedbackCount = (user && user.attribute('pendingFeedbackCount')) || 0;
    const pendingReportCount = (user && user.attribute('pendingReportCount')) || 0;
    const totalPending = pendingFeedbackCount + pendingReportCount;

    return (
      <div className="TraderFeedbackPage-filters">
        <div className="TraderFeedbackPage-filterSelect">
          <Select value={filter} options={filterOptions} onchange={onFilterChange} />
        </div>

        <div className="TraderFeedbackPage-actions">
          {canModerate && (
            <Button className="Button Button--danger TraderFeedbackPage-modBtn" onclick={onModerate}>
              <Icon name="fas fa-gavel" className="TraderFeedbackPage-modBtnIcon" />
              <span className="TraderFeedbackPage-modBtnText">
                {app.translator.trans('huseyinfiliz-traderfeedback.forum.moderation.actions_button')}
              </span>
              {totalPending > 0 && <span className="Button-badge">{totalPending}</span>}
            </Button>
          )}

          {app.session.user && user && app.session.user.id() !== user.id() && (
            <Button className="Button Button--primary TraderFeedbackPage-giveBtn" onclick={onGiveFeedback}>
              <Icon name="fas fa-plus" className="TraderFeedbackPage-giveBtnIcon" />
              <span className="TraderFeedbackPage-giveBtnText">
                {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.give_feedback_button')}
              </span>
              <span className="TraderFeedbackPage-giveBtnTextShort">
                {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.give_feedback_button_short')}
              </span>
            </Button>
          )}
        </div>
      </div>
    );
  }
}
