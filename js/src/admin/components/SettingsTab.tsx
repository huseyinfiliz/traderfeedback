import Component from 'flarum/common/Component';

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
          icon: 'fas fa-exchange-alt',
          value: stats.total,
          label: 'Total Feedbacks',
          type: 'total'
        })}

        {this.card({
          icon: 'fas fa-thumbs-up',
          value: stats.positive,
          label: 'Positive',
          percentage: stats.total > 0 ? Math.round((stats.positive / stats.total) * 100) : 0,
          type: 'positive'
        })}

        {this.card({
          icon: 'fas fa-minus-circle',
          value: stats.neutral,
          label: 'Neutral',
          percentage: stats.total > 0 ? Math.round((stats.neutral / stats.total) * 100) : 0,
          type: 'neutral'
        })}

        {this.card({
          icon: 'fas fa-thumbs-down',
          value: stats.negative,
          label: 'Negative',
          percentage: stats.total > 0 ? Math.round((stats.negative / stats.total) * 100) : 0,
          type: 'negative'
        })}
      </div>
    );
  }

  card(data: any) {
    return (
      <div className={'StatsCard StatsCard--' + data.type}>
        <div className="StatsCard-icon">
          <i className={data.icon}></i>
        </div>
        <div className="StatsCard-content">
          <div className="StatsCard-value">{data.value}</div>
          <div className="StatsCard-label">{data.label}</div>
          {data.percentage !== undefined && (
            <div className="StatsCard-percentage">{data.percentage}%</div>
          )}
        </div>
      </div>
    );
  }
}