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
        // Get relationships from the API response
        const relationships = feedback.relationships || {};
        const fromUserData = relationships.fromUser?.data;
        const attrs = feedback.attributes || {};
        
        let fromUser = null;
        let displayName = 'Unknown User';
        let avatarColor = '#888';
        
        // Try to get user from the relationship
        if (fromUserData) {
            fromUser = app.store.getById('users', fromUserData.id);
        }
        
        // If still no user, try from attributes
        if (!fromUser && attrs.from_user_id) {
            fromUser = app.store.getById('users', String(attrs.from_user_id));
        }
        
        // If we have a user object
        if (fromUser) {
            displayName = fromUser.displayName();
            avatarColor = fromUser.color() || '#888';
        } else if (attrs.from_user_id) {
            displayName = `User #${attrs.from_user_id}`;
        }
        
        const feedbackDate = attrs.created_at || new Date().toISOString();
        
        return (
            <div className={`FeedbackItem feedback-${attrs.type}`} key={feedback.id}>
                <div className="FeedbackItem-header">
                    <div className="FeedbackItem-user">
                        <span className="Avatar" style={{
                            background: avatarColor,
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
                            {displayName[0]?.toUpperCase() || '?'}
                        </span>
                        <span className="username">{displayName}</span>
                    </div>
                    <div className="FeedbackItem-date">
                        {humanTime(feedbackDate)}
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
                
                {this.feedbackActions(feedback, fromUser)}
            </div>
        );
    }
    
    feedbackActions(feedback, fromUser) {
        const currentUser = app.session.user;
        if (!currentUser) return null;
        
        const canModerate = currentUser.attribute('canModerateFeedback');
        const isOwn = fromUser && currentUser.id() === fromUser.id();
        
        return (
            <div className="FeedbackItem-actions" style={{
                marginTop: '10px',
                paddingTop: '10px',
                borderTop: '1px solid rgba(0,0,0,0.1)'
            }}>
                {!isOwn && (
                    <Button 
                        className="Button Button--link"
                        onclick={() => this.reportFeedback(feedback)}
                    >
                        <i className="fas fa-flag" style={{ marginRight: '5px' }}></i>
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.report_button')}
                    </Button>
                )}
                
                {(isOwn || canModerate) && (
                    <Button 
                        className="Button Button--link Button--danger"
                        onclick={() => this.deleteFeedback(feedback)}
                    >
                        <i className="fas fa-trash" style={{ marginRight: '5px' }}></i>
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.delete_button')}
                    </Button>
                )}
            </div>
        );
    }
    
    reportFeedback(feedback) {
        const reason = prompt('Please provide a reason for reporting this feedback:');
        if (!reason || !reason.trim()) return;
        
        this.loading = true;
        m.redraw();
        
        app.request({
            method: 'POST',
            url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/report',
            body: {
                data: {
                    attributes: {
                        reason: reason.trim()
                    }
                }
            }
        }).then(() => {
            this.loading = false;
            app.alerts.show({ type: 'success' }, app.translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.success'));
            m.redraw();
        }).catch(error => {
            this.loading = false;
            app.alerts.show({ type: 'error' }, 'Failed to report feedback');
            m.redraw();
        });
    }
    
    deleteFeedback(feedback) {
        if (!confirm(app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.confirm_delete'))) {
            return;
        }
        
        this.loading = true;
        m.redraw();
        
        app.request({
            method: 'DELETE',
            url: app.forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id
        }).then(() => {
            this.loading = false;
            // Remove the deleted feedback from the list
            this.feedbacks = this.feedbacks.filter(f => f.id !== feedback.id);
            // Reload stats as they might have changed
            this.loadStats();
            app.alerts.show({ type: 'success' }, 'Feedback deleted successfully');
            m.redraw();
        }).catch(error => {
            this.loading = false;
            app.alerts.show({ type: 'error' }, 'Failed to delete feedback');
            m.redraw();
        });
    }
}