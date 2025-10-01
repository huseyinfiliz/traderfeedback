import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import UserCard from 'flarum/forum/components/UserCard';
import Model from 'flarum/common/Model';
import User from 'flarum/common/models/User';
import TraderStats from '../common/models/TraderStats';

export default function addUserCardStats() {
  // TraderStats model'ini store'a kaydet
  app.store.models['trader-stats'] = TraderStats;
  
  // User model'e traderStats relationship ekle
  User.prototype.traderStats = Model.hasOne('traderStats');
  
  // UserCard'a stats ekle (hem profil hem hovercard için)
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
          <i className="fas fa-balance-scale"></i>
          <span className="TraderScore-value">{score}%</span>
        </div>,
        10
      );
    }
  });
}