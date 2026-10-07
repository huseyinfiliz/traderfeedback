import Component from 'flarum/common/Component';
import Icon from 'flarum/common/components/Icon';
import app from 'flarum/admin/app';

export default class StatsCards extends Component {
  view() {
    const stats = this.attrs.stats || {
      total: 0,
      positive: 0,
      neutral: 0,
      negative: 0,
    };

    return (
      <div className="TraderFeedbackStats">
        {this.card({
          icon: 'fas fa-right-left',
          value: stats.total,
          label: app.translator.trans('huseyinfiliz-traderfeedback.admin.dashboard.total_feedbacks'),
          type: 'total',
        })}

        {this.card({
          icon: 'fas fa-thumbs-up',
          value: stats.positive,
          label: app.translator.trans('huseyinfiliz-traderfeedback.admin.dashboard.positive_feedbacks'),
          percentage: stats.total > 0 ? Math.round((stats.positive / stats.total) * 100) : 0,
          type: 'positive',
        })}

        {this.card({
          icon: 'fas fa-circle-minus',
          value: stats.neutral,
          label: app.translator.trans('huseyinfiliz-traderfeedback.admin.dashboard.neutral_feedbacks'),
          percentage: stats.total > 0 ? Math.round((stats.neutral / stats.total) * 100) : 0,
          type: 'neutral',
        })}

        {this.card({
          icon: 'fas fa-thumbs-down',
          value: stats.negative,
          label: app.translator.trans('huseyinfiliz-traderfeedback.admin.dashboard.negative_feedbacks'),
          percentage: stats.total > 0 ? Math.round((stats.negative / stats.total) * 100) : 0,
          type: 'negative',
        })}
      </div>
    );
  }

  card(data: any) {
    return (
      <div className={'StatsCard StatsCard--' + data.type}>
        <div className="StatsCard-icon">
          <Icon name={data.icon} />
        </div>
        <div className="StatsCard-content">
          <div className="StatsCard-value">{data.value}</div>
          <div className="StatsCard-label">{data.label}</div>
          {data.percentage !== undefined && <div className="StatsCard-percentage">{data.percentage}%</div>}
        </div>
      </div>
    );
  }
}
