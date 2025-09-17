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
    includedUsers: Map<string, any> = new Map();
    
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
            // Process included users first
            if (response.included) {
                response.included.forEach(item => {
                    if (item.type === 'users') {
                        // Store user data in map for quick access
                        this.includedUsers.set(item.id, item.attributes);
                        
                        // Also update the store if it exists
                        const storeUser = app.store.getById('users', item.id);
                        if (storeUser) {
                            storeUser.pushData(item.attributes);
                        } else {
                            // Create user in store if doesn't exist
                            app.store.pushPayload({
                                data: item
                            });
                        }
                    }
                });
            }
            
            // Process feedbacks
            this.feedbacks = Array.isArray(response.data) ? response.data : [];
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
                    {/* Başlık kaldırıldı - sadece istatistikler gösteriliyor */}
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
        // Allow negative feedback kontrolü
        const allowNegative = app.forum.attribute('huseyinfiliz.traderfeedback.allowNegative') !== false;
        
        // Filter seçeneklerini oluştur
        const filterOptions: any = {
            all: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.all'),
            positive: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.positive'),
            neutral: app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.neutral')
        };
        
        // Negative seçeneğini sadece izin veriliyorsa ekle
        if (allowNegative) {
            filterOptions.negative = app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.negative');
        }
        
        return (
            <div className="TraderFeedbackPage-filters">
                <Select
                    value={this.filter}
                    options={filterOptions}
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
        const attrs = feedback.attributes || {};
        
        // Get user from multiple sources
        let fromUser = null;
        let displayName = 'Unknown User';
        let avatarColor = '#888';
        let avatarUrl = null;
        
        // 1. Try to get from relationships
        const fromUserRelationship = feedback.relationships?.fromUser?.data;
        if (fromUserRelationship) {
            const userId = fromUserRelationship.id;
            
            // First check our included users map
            const userData = this.includedUsers.get(userId);
            if (userData) {
                displayName = userData.displayName || userData.username || `User #${userId}`;
                avatarUrl = userData.avatarUrl;
                
                // Try to get user from store for color
                fromUser = app.store.getById('users', userId);
                if (fromUser) {
                    avatarColor = fromUser.color() || '#888';
                }
            } else {
                // Try store as fallback
                fromUser = app.store.getById('users', userId);
                if (fromUser) {
                    displayName = fromUser.displayName();
                    avatarColor = fromUser.color() || '#888';
                    avatarUrl = fromUser.avatarUrl();
                }
            }
        }
        
        // 2. Fallback to attributes
        if (!displayName || displayName === 'Unknown User') {
            if (attrs.fromUserId || attrs.from_user_id) {
                const userId = attrs.fromUserId || attrs.from_user_id;
                const userData = this.includedUsers.get(String(userId));
                
                if (userData) {
                    displayName = userData.displayName || userData.username || `User #${userId}`;
                    avatarUrl = userData.avatarUrl;
                } else {
                    fromUser = app.store.getById('users', String(userId));
                    if (fromUser) {
                        displayName = fromUser.displayName();
                        avatarColor = fromUser.color() || '#888';
                        avatarUrl = fromUser.avatarUrl();
                    } else {
                        displayName = `User #${userId}`;
                    }
                }
            }
        }
        
        const feedbackDate = attrs.createdAt || attrs.created_at || new Date().toISOString();
        
        // Show discussion link if available
        const discussionLink = attrs.discussionId || attrs.discussion_id ? (
            <a href={app.route('discussion', { id: attrs.discussionId || attrs.discussion_id })} 
               className="FeedbackItem-discussionLink"
               target="_blank">
                <i className="fas fa-external-link-alt"></i>
                {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.discussion_link')}
            </a>
        ) : null;
        
        return (
            <div className={`FeedbackItem feedback-${attrs.type}`} key={feedback.id}>
                <div className="FeedbackItem-header">
                    <div className="FeedbackItem-user">
                        {avatarUrl ? (
                            <img className="Avatar" src={avatarUrl} alt={displayName} style={{
                                width: '32px',
                                height: '32px',
                                borderRadius: '50%',
                                marginRight: '8px'
                            }} />
                        ) : (
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
                        )}
                        <span className="username">{displayName}</span>
                    </div>
                    <div className="FeedbackItem-date">
                        {humanTime(feedbackDate)}
                        {discussionLink}
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
        
        const attrs = feedback.attributes || {};
        const canReport = attrs.canReport !== false && currentUser.attribute('canReportFeedback') !== false;
        const canDelete = attrs.canDelete || currentUser.attribute('canDeleteFeedback');
        const canModerate = currentUser.attribute('canModerateFeedback');
        const isOwn = fromUser && currentUser.id() === fromUser.id();
        
        // Hiç aksiyon yoksa null döndür
        if (!canReport && !canDelete && !isOwn && !canModerate) {
            return null;
        }
        
        return (
            <div className="FeedbackItem-actions" style={{
                marginTop: '10px',
                paddingTop: '10px',
                borderTop: '1px solid rgba(0,0,0,0.1)'
            }}>
                {!isOwn && canReport && (
                    <Button 
                        className="Button Button--link"
                        onclick={() => this.reportFeedback(feedback)}
                        title={app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.report_button')}
                    >
                        <i className="fas fa-flag" style={{ marginRight: '5px' }}></i>
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.report_button')}
                    </Button>
                )}
                
                {(isOwn || canDelete || canModerate) && (
                    <Button 
                        className="Button Button--link Button--danger"
                        onclick={() => this.deleteFeedback(feedback)}
                        title={app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.delete_button')}
                    >
                        <i className="fas fa-trash" style={{ marginRight: '5px' }}></i>
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.delete_button')}
                    </Button>
                )}
                
                {!canReport && !isOwn && !canDelete && !canModerate && (
                    <span className="text-muted" style={{ fontSize: '0.9em', fontStyle: 'italic' }}>
                        {app.translator.trans('huseyinfiliz-traderfeedback.forum.feedback_item.no_permission_report')}
                    </span>
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