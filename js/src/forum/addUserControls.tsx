import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import Button from 'flarum/common/components/Button';
import UserControls from 'flarum/forum/utils/UserControls';
import FeedbackModal from './modals/FeedbackModal';  // Modal'ı import et

export default function addUserControls() {
    extend(UserControls, 'userControls', (items, user) => {
        // Kendine feedback veremez
        if (app.session.user && app.session.user.id() !== user.id()) {
            items.add('giveFeedback', 
                Button.component({
                    icon: 'fas fa-exchange-alt',
                    onclick() {
                        app.modal.show(FeedbackModal, {
                            user: user,
                        });
                    },
                }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.submit_button')),
                100
            );
        }
    });
}