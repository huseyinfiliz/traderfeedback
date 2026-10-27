import Component from 'flarum/common/Component';
import Placeholder from 'flarum/common/components/Placeholder';
import app from 'flarum/forum/app';
import FeedbackItem from './FeedbackItem';
import type { FeedbackItemAttrs } from './FeedbackItem';
import type { FeedbackData } from '../../../common/types';

interface FeedbackListAttrs extends Omit<FeedbackItemAttrs, 'feedback'> {
  feedbacks: FeedbackData[];
}

export default class FeedbackList extends Component<FeedbackListAttrs> {
  view() {
    const { feedbacks, includedUsers, onDelete, onReport } = this.attrs;

    if (!feedbacks || !Array.isArray(feedbacks) || feedbacks.length === 0) {
      return <Placeholder text={app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.no_feedback')} />;
    }

    return (
      <div className="TraderFeedbackPage-list">
        {feedbacks.map((feedback) => (
          <FeedbackItem feedback={feedback} includedUsers={includedUsers} onDelete={onDelete} onReport={onReport} key={feedback.id} />
        ))}
      </div>
    );
  }
}
