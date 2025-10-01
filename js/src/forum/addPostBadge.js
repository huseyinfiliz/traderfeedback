import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import CommentPost from 'flarum/forum/components/CommentPost';

export default function addPostBadge() {
  extend(CommentPost.prototype, 'headerItems', function (items) {
    // Ayar kapalıysa gösterme
    if (!app.forum.attribute('huseyinfiliz.traderfeedback.showBadgeInPosts')) {
      return;
    }

    const post = this.attrs.post;
    const user = post.user();
    
    if (!user || !user.traderStats()) return;
    
    const stats = user.traderStats();
    const score = Math.round(stats.score());
    const total = stats.positiveCount() + stats.neutralCount() + stats.negativeCount();
    
    // En az 1 feedback olmalı
    if (total === 0) return;
    
    items.add(
      'traderBadge',
      <span className="TraderBadge TraderBadge--inline">
        <i className="fas fa-balance-scale"></i>
        <span className="TraderBadge-score">{score}%</span>
      </span>,
      -5 // Kullanıcı adından sonra gelmesi için negatif priority
    );
  });
}