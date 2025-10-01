import Component from 'flarum/common/Component';
import Select from 'flarum/common/components/Select';
import Button from 'flarum/common/components/Button';
import app from 'flarum/forum/app';

export default class FeedbackFilters extends Component {
    view() {
        const { filter, user, onFilterChange, onGiveFeedback } = this.attrs;
        const allowNegative = app.forum.attribute('huseyinfiliz.traderfeedback.allowNegative') !== false;

        const filterOptions: any = {
            all: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.all'),
            positive: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.positive'),
            neutral: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.neutral'),
        };

        if (allowNegative) {
            filterOptions.negative = app.translator.trans(
                'huseyinfiliz-traderfeedback.forum.feedback_page.filter.negative'
            );
        }

        return (
            <div className="TraderFeedbackPage-filters">
                <div className="TraderFeedbackPage-filterSelect">
                    <Select value={filter} options={filterOptions} onchange={onFilterChange} />
                </div>

                {app.session.user && app.session.user.id() !== user.id() && (
                    <Button className="Button Button--primary TraderFeedbackPage-giveBtn" onclick={onGiveFeedback}>
                        <i className="fas fa-plus TraderFeedbackPage-giveBtnIcon"></i>
                        <span className="TraderFeedbackPage-giveBtnText">
                            {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.give_feedback_button')}
                        </span>
                        <span className="TraderFeedbackPage-giveBtnTextShort">
                            Give
                        </span>
                    </Button>
                )}
            </div>
        );
    }
}