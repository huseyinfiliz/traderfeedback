import Component from 'flarum/common/Component';
import FeedbackItem from './FeedbackItem';

export default class FeedbackList extends Component {
  view() {
    return (
      <div className="FeedbackList">
        {this.attrs.feedbacks.map(feedback => (
          <FeedbackItem feedback={feedback} key={feedback.id()} />
        ))}
      </div>
    );
  }
}