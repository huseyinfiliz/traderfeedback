import app from 'flarum/forum/app';
import addUserProfilePage from './addUserProfilePage';
import addUserControls from './addUserControls';

export {default as extend} from './extend';

app.initializers.add('huseyinfiliz-traderfeedback', () => {
  addUserProfilePage();
  addUserControls();
});
