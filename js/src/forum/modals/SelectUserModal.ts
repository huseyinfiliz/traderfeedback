import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import username from 'flarum/common/helpers/username';
import avatar from 'flarum/common/helpers/avatar';
import KeyboardNavigatable from 'flarum/common/utils/KeyboardNavigatable';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import FeedbackModal from './FeedbackModal';
import app from 'flarum/forum/app';

export default class SelectUserModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);
    
    this.discussion = this.attrs.discussion;
    this.users = [];
    this.loading = true;
    this.filter = '';
    this.selectedIndex = 0;
    this.navigator = new KeyboardNavigatable();
    
    // ✅ Her zaman ilk 5 kullanıcı limiti
    this.displayLimit = 5;
    
    this.navigator
      .onUp(() => { 
        this.selectedIndex = Math.max(0, this.selectedIndex - 1);
        m.redraw();
      })
      .onDown(() => { 
        this.selectedIndex = Math.min(this.displayUsers().length - 1, this.selectedIndex + 1);
        m.redraw();
      })
      .onSelect(() => this.selectUser(this.displayUsers()[this.selectedIndex]));

    this.loadParticipants();
  }

  async loadParticipants() {
    try {
      const response = await app.request({
        method: 'GET',
        url: app.forum.attribute('apiUrl') + `/trader/discussions/${this.discussion.id()}/participants`,
      });

      const users = [];
      
      if (response.data && Array.isArray(response.data)) {
        response.data.forEach((userData) => {
          let user = app.store.getById('users', userData.id);
          if (!user) {
            user = app.store.pushPayload({ data: userData });
          }
          users.push(user);
        });
      }

      this.users = users;
      this.loading = false;

      if (users.length === 1) {
        this.hide();
        setTimeout(() => {
          app.modal.show(FeedbackModal, {
            user: users[0],
            discussionId: this.discussion.id(),
            autoFillDiscussion: true,
          });
        }, 200);
        return;
      }

      if (users.length === 0) {
        this.hide();
        app.alerts.show({ 
          type: 'error' 
        }, app.translator.trans('huseyinfiliz-traderfeedback.forum.discussion_actions.no_users_found'));
        return;
      }

      m.redraw();

    } catch (error) {
      console.error('Error loading participants:', error);
      this.loading = false;
      this.hide();
      app.alerts.show({ 
        type: 'error' 
      }, 'Could not load participants');
    }
  }

  filteredUsers() {
    if (!this.filter) return this.users;
    const query = this.filter.toLowerCase();
    return this.users.filter(u => {
      const userName = u.username ? u.username() : '';
      const displayName = u.displayName ? u.displayName() : '';
      return userName.toLowerCase().includes(query) || displayName.toLowerCase().includes(query);
    });
  }

  // ✅ Her zaman ilk 5'i göster
  displayUsers() {
    const filtered = this.filteredUsers();
    return filtered.slice(0, this.displayLimit);
  }

  selectUser(user) {
    this.hide();
    
    setTimeout(() => {
      app.modal.show(FeedbackModal, {
        user,
        discussionId: this.discussion.id(), // ✅ URL yerine ID
        autoFillDiscussion: true
      });
    }, 200);
  }

  className() {
    return 'SelectUserModal Modal--small';
  }

  title() {
    return app.translator.trans('huseyinfiliz-traderfeedback.forum.discussion_actions.select_user_title');
  }

  content() {
    if (this.loading) {
      return m('.Modal-body', [
        m('.LoadingContainer', {
          style: 'text-align: center; padding: 50px;'
        }, LoadingIndicator.component({ size: 'large' }))
      ]);
    }

    const filteredUsers = this.filteredUsers();
    const displayUsers = this.displayUsers();
    const hasMoreResults = filteredUsers.length > this.displayLimit;
    
    return m('.Modal-body', [
      m('.Form-group', [
        m('input.FormControl', {
          placeholder: app.translator.trans('huseyinfiliz-traderfeedback.forum.discussion_actions.search_user_placeholder') || 'Search users...',
          value: this.filter,
          oninput: e => { 
            this.filter = e.target.value; 
            this.selectedIndex = 0; 
            m.redraw();
          },
          onkeydown: this.navigator.navigate.bind(this.navigator),
          oncreate: vnode => vnode.dom.focus()
        }),
        // ✅ Bilgilendirme mesajı - her zaman
        hasMoreResults && m('.helpText', {
          style: 'margin-top: 8px; color: #888; font-size: 12px;'
        }, this.filter 
          ? (app.translator.trans(
              'huseyinfiliz-traderfeedback.forum.discussion_actions.showing_limited_results',
              { shown: this.displayLimit, total: filteredUsers.length }
            ) || `Showing first ${this.displayLimit} of ${filteredUsers.length} matching users. Refine your search.`)
          : (app.translator.trans(
              'huseyinfiliz-traderfeedback.forum.discussion_actions.showing_limited_users',
              { shown: this.displayLimit, total: filteredUsers.length }
            ) || `Showing first ${this.displayLimit} of ${filteredUsers.length} users. Use search to find others.`)
        )
      ]),
      m('.UserList', displayUsers.length ? 
        displayUsers.map((user, index) => 
          m('.UserListItem', {
            className: index === this.selectedIndex ? 'active' : '',
            onclick: () => this.selectUser(user)
          }, [
            avatar(user, {className: 'UserListItem-avatar'}),
            m('.UserListItem-info', username(user))
          ])
        ) : 
        m('.UserList-empty', 
          this.filter 
            ? (app.translator.trans('huseyinfiliz-traderfeedback.forum.discussion_actions.no_matching_users') || 'No matching users found')
            : app.translator.trans('huseyinfiliz-traderfeedback.forum.discussion_actions.no_users_found')
        )
      )
    ]);
  }
}