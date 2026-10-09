import app from 'flarum/forum/app';
import username from 'flarum/common/helpers/username';
import Avatar from 'flarum/common/components/Avatar';
import Link from 'flarum/common/components/Link';

export interface FeedbackUserDisplayProps {
  userId: string | number;
  displayName?: string;
  avatarUrl?: string;
  avatarColor?: string;
  includedUsers?: Map<string, any>;
}

export default class FeedbackUserDisplay {
  static getUserInfo(props: FeedbackUserDisplayProps) {
    let displayName = props.displayName || app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.unknown_user');
    let avatarColor = props.avatarColor || '#888';
    let avatarUrl = props.avatarUrl || null;
    let fromUser = null;

    // Try to get from included users first
    if (props.userId) {
      if (props.includedUsers) {
        const userData = props.includedUsers.get(String(props.userId));
        if (userData) {
          displayName =
            userData.displayName ||
            userData.username ||
            app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.user_id_format', { id: props.userId });
          avatarUrl = userData.avatarUrl;
        }
      }

      // Try to get from store
      fromUser = app.store.getById('users', String(props.userId));
      if (fromUser) {
        displayName = fromUser.displayName() || displayName;
        avatarColor = fromUser.color() || avatarColor;
        avatarUrl = fromUser.avatarUrl() || avatarUrl;
      }
    }

    return { displayName, avatarColor, avatarUrl, fromUser };
  }

  static view(props: FeedbackUserDisplayProps) {
    const { displayName, avatarColor, avatarUrl, fromUser } = this.getUserInfo(props);

    const userProfileLink = fromUser ? app.route.user(fromUser) : null;

    const avatarElement = fromUser ? (
      <Avatar user={fromUser} className="FeedbackItem-avatar" />
    ) : avatarUrl ? (
      <img className="Avatar FeedbackItem-avatar" src={avatarUrl} alt={displayName} />
    ) : (
      <span className="Avatar FeedbackItem-avatar" style={{ '--avatar-bg': avatarColor }}>
        {displayName[0]?.toUpperCase() || '?'}
      </span>
    );

    const usernameElement = <span className="FeedbackItem-username">{displayName}</span>;

    // Eğer user link varsa, tıklanabilir yap
    if (userProfileLink) {
      return (
        <div className="FeedbackItem-user">
          <Link href={userProfileLink} className="FeedbackItem-userLink">
            {avatarElement}
          </Link>
          <Link href={userProfileLink} className="FeedbackItem-userLink">
            {usernameElement}
          </Link>
        </div>
      );
    }

    // User link yoksa normal göster
    return (
      <div className="FeedbackItem-user">
        {avatarElement}
        {usernameElement}
      </div>
    );
  }
}
