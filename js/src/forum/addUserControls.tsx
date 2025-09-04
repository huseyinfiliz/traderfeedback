import {extend} from 'flarum/common/extend';
import app from 'flarum/forum/app';
import UserControls from 'flarum/forum/utils/UserControls';
import FeedbackUserModal from './components/FeedbackForm';

export default function addUserControls() {
	extend(UserControls, 'moderationControls', (items, user) => {
        if (app.forum.huseyinfilizTraderUser()) {
            items.add('huseyinfiliz-traderfeedbacks', Button.component({
                icon: 'fas fa-exchange-alt',
                onclick() {
                    app.modal.show(FeedbackForm, {
                        user,
                    });
                },
            }, app.translator.trans('huseyinfiliz-traderfeedback.forum.form.submit_button')));
        }
    });
}