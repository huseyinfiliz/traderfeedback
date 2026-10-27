import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import UserCard from 'flarum/forum/components/UserCard';
import Icon from 'flarum/common/components/Icon';
import Model from 'flarum/common/Model';
import User from 'flarum/common/models/User';
import TraderStats from '../common/models/TraderStats';

export default function addUserCardStats() {
  // Register trader statistics with the store.
  app.store.models['trader-stats'] = TraderStats;

  // Add the trader statistics relationship to users.
  User.prototype.traderStats = Model.hasOne('traderStats');

  // Show statistics on profile cards and hovercards.
  extend(UserCard.prototype, 'infoItems', function (items) {
    const user = this.attrs.user;

    if (!user || !user.traderStats()) return;

    const stats = user.traderStats();
    const score = Math.round(stats.score());
    const total = stats.positiveCount() + stats.neutralCount() + stats.negativeCount();

    if (total > 0) {
      items.add(
        'traderScore',
        <div className="TraderScore">
          <Icon name="fas fa-cart-shopping" />
          <span className="TraderScore-value"> {score}%</span>
        </div>,
        10
      );
    }
  });
}
