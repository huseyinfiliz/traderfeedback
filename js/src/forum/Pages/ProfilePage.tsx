import app from 'flarum/forum/app';
import UserPage from 'flarum/forum/components/UserPage';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import Button from 'flarum/common/components/Button';
import avatar from 'flarum/common/helpers/avatar';
import username from 'flarum/common/helpers/username';
import humanTime from 'flarum/common/helpers/humanTime';
import Select from 'flarum/common/components/Select';
import Placeholder from 'flarum/common/components/Placeholder';
import FeedbackModal from '../modals/FeedbackModal';

export default class ProfilePage extends UserPage {
    feedbacks: any[] = [];
    stats: any = null;
    loading: boolean = false;
    statsLoading: boolean = false;
    filter: string = 'all';
    
    oninit(vnode) {
        super.oninit(vnode);
        this.loading = true;
        this.statsLoading = true;
        this.loadUser(this.attrs.username);
    }
    
    oncreate(vnode) {
        super.oncreate(vnode);
        this.loadFeedbacks();
        this.loadStats();
    }
    
    loadFeedbacks() {
        this.loading = true;
        
        app.request({
            method: 'GET',
            url: app.forum.attribute('apiUrl') + '/trader/feedback',
            params: {
                filter: {
                    user: this.user.id(),
                    type: this.filter === 'all' ? null : this.filter
                }
            }
        }).then(response => {
            this.feedbacks = Array.isArray(response) ? response : (response.data || []);
            this.loading = false;
            m.redraw();
        }).catch(error => {
            this.feedbacks = [];
            this.loading = false;
            m.redraw();
        });
    }
    
    loadStats() {
        this.statsLoading = true;
        
        app.request({
            method: 'GET',
            url: app.forum.attribute('apiUrl') + '/trader/stats/' + this.user.id()
        }).then(response => {
            if (response && response.data) {
                const data = response.data.attributes || response.data;
                this.stats = {
                    score: data.score || 0,
                    positive_count: data.positive_count || 0,
                    neutral_count: data.neutral_count || 0,
                    negative_count: data.negative_count || 0
                };
            } else {
                this.stats = {
                    score: 0,
                    positive_count: 0,
                    neutral_count: 0,
                    negative_count: 0
                };
            }
            this.statsLoading = false;
            m.redraw();
        }).catch(error => {
            this.stats = {
                score: 0,
                positive_count: 0,
                neutral_count: 0,
                negative_count: 0
            };
            this.statsLoading = false;
            m.redraw();
        });
    }
    
    content() {
        if (this.loading || !this.user) {
            return (
                <div className="TraderFeedbackPage">
                    <LoadingIndicator />
                </div>
            );
        }
        
        return (
            <div className="TraderFeedbackPage">
                <div className="TraderFeedbackPage-header">
                    <h2>{app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.title', {
                        username: this.user.displayName()
                    })}</h2>
                    {this.statsSection()}
                </div>
                {this.filterSection()}
                {this.feedbacksList()}
            </div>
        );
    }
    
    statsSection() {
        if (this.statsLoading) {
            return (
                <div className="TraderFeedbackPage-stats">
                    <LoadingIndicator size="small" />
                </div>
            );
        }
        
        if (!this.stats) {
            return null;
        }
        
        const total = this.stats.positive_count + this.stats.neutral_count + this.stats.negative_count;
        const scorePercentage = total > 0 ? Math.round(this.stats.score) : 0;
        
        return (
            <div className="TraderFeedbackPage-stats">
                <div className="TraderFeedbackPage-stat score">
                    <div className="TraderFeedbackPage-stat-value">
                        {scorePercentage}%
                    </div>
                    <div className="TraderFeedbackPage-stat-label">
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.score')}
                    </div>
                </div>
                
                <div className="TraderFeedbackPage-stat positive">
                    <div className="TraderFeedbackPage-stat-value">
                        {this.stats.positive_count}
                    </div>
                    <div className="TraderFeedbackPage-stat-label">
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.positive')}
                    </div>
                </div>
                
                <div className="TraderFeedbackPage-stat neutral">
                    <div className="TraderFeedbackPage-stat-value">
                        {this.stats.neutral_count}
                    </div>
                    <div className="TraderFeedbackPage-stat-label">
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.neutral')}
                    </div>
                </div>
                
                <div className="TraderFeedbackPage-stat negative">
                    <div className="TraderFeedbackPage-stat-value">
                        {this.stats.negative_count}
                    </div>
                    <div className="TraderFeedbackPage-stat-label">
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.negative')}
                    </div>
                </div>
            </div>
        );
    }
    
    filterSection() {
        return (
            <div className="TraderFeedbackPage-filters">
                <Select
                    value={this.filter}
                    options={{
                        all: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.all'),
                        positive: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.positive'),
                        neutral: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.neutral'),
                        negative: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.negative')
                    }}
                    onchange={(value) => {
                        this.filter = value;
                        this.loadFeedbacks();
                    }}
                />
                
                {app.session.user && app.session.user.id() !== this.user.id() && (
                    <Button
                        className="Button Button--primary"
                        onclick={() => {
                            app.modal.show(FeedbackModal, {
                                user: this.user
                            });
                        }}
                    >
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.give_feedback_button')}
                    </Button>
                )}
            </div>
        );
    }
    
    feedbacksList() {
        if (!this.feedbacks || !Array.isArray(this.feedbacks) || this.feedbacks.length === 0) {
            return (
                <Placeholder text={app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.no_feedback')} />
            );
        }
        
        return (
            <div className="TraderFeedbackPage-list">
                {this.feedbacks.map(feedback => this.feedbackItem(feedback))}
            </div>
        );
    }
    
    feedbackItem(feedback) {
        // Access data from the original API response structure
        const attrs = feedback.attributes || {};
        const fromUserId = attrs.from_user_id;
        
        console.log('Feedback object:', feedback);
        console.log('Attributes:', attrs);
        console.log('Looking for user ID:', fromUserId);
        
        // Get user from store
        let fromUser = fromUserId ? app.store.getById('users', String(fromUserId)) : null;
        console.log('Found user in store:', fromUser);
        
        // Fallback to placeholder if user not found
        const displayUser = fromUser || {
            displayName: () => `User #${fromUserId || 'Unknown'}`,
            username: () => 'anonymous',
            color: () => '#888'
        };
        
        // Fix for created_at being null
        const feedbackDate = attrs.created_at || new Date().toISOString();
        
        return (
            <div className={`FeedbackItem feedback-${attrs.type}`} key={feedback.id}>
                <div className="FeedbackItem-header">
                    <div className="FeedbackItem-user">
                        <span className="Avatar" style={{
                            background: displayUser.color ? displayUser.color() : '#888',
                            width: '32px',
                            height: '32px',
                            borderRadius: '50%',
                            display: 'inline-flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            marginRight: '8px',
                            color: 'white',
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }}>
                            {displayUser.displayName()[0]?.toUpperCase() || '?'}
                        </span>
                        <span className="username">{displayUser.displayName()}</span>
                    </div>
                    <div className="FeedbackItem-date">
                        {feedbackDate ? humanTime(feedbackDate) : 'Unknown date'}
                    </div>
                </div>
                
                <div className="FeedbackItem-meta">
                    <span className={`FeedbackItem-type feedback-type-${attrs.type}`} style={{
                        padding: '2px 8px',
                        borderRadius: '4px',
                        fontSize: '0.9em',
                        background: attrs.type === 'positive' ? '#4CAF5020' : 
                                   attrs.type === 'negative' ? '#f4433620' :
                                   '#FF980020',
                        color: attrs.type === 'positive' ? '#2E7D32' : 
                               attrs.type === 'negative' ? '#C62828' :
                               '#E65100'
                    }}>
                        {app.translator.trans(`huseyinfiliz-traderfeedback.forum.form.type_${attrs.type}`)}
                    </span>
                    <span className="FeedbackItem-role" style={{
                        marginLeft: '8px',
                        padding: '2px 8px',
                        borderRadius: '4px',
                        fontSize: '0.9em',
                        background: 'rgba(0,0,0,0.1)'
                    }}>
                        {app.translator.trans(`huseyinfiliz-traderfeedback.forum.feedback_item.as_${attrs.role}`)}
                    </span>
                </div>
                
                <div className="FeedbackItem-comment" style={{
                    marginTop: '10px',
                    padding: '10px',
                    background: 'rgba(0,0,0,0.02)',
                    borderRadius: '4px'
                }}>
                    {attrs.comment || 'No comment provided'}
                </div>
            </div>
        );
    }
}