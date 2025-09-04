 import app from 'flarum/forum/app';
 import avatar from 'flarum/common/helpers/avatar';
 import Button from 'flarum/common/components/Button';
 import username from 'flarum/common/helpers/username';
 import UserPage from 'flarum/forum/components/UserPage';
 import Stream from 'flarum/common/utils/Stream';
 import Placeholder from 'flarum/common/components/Placeholder';
 import LoadingIndicator from 'flarum/common/components/LoadingIndicator';

 export default class ProfilePage extends UserPage {
	oninit(vnode) {
    super.oninit(vnode);
    this.loading = true;

    this.loadUser(this.attrs.username); // Profile ait kullanıcının adını al, giriş yapanın değil

    this.loading = false;
  }

  content() {
    if (this.loading) {
      return (
        <div className="DiscussionList">
          <LoadingIndicator />
        </div>
      );
    }

    return (
        <div> {/* Ana kapsayıcı div */}
          <div style={{ marginBottom: '20px', paddingBottom: '10px', borderBottom: '1px solid #ccc' }}> {/* İlk geri bildirim bloğu */}
            <div style={{ display: 'flex', alignItems: 'center', marginBottom: '10px' }}> {/* Avatar ve kullanıcı adı */}
              <div>[Avatar Burada]</div> {/* Avatar için yer tutucu */}
              <h3>Kullanıcı Adı 1</h3> {/* Kullanıcı adı için yer tutucu */}
            </div>
            <h4>Geri Bildirim Başlığı 1</h4> {/* Geri bildirim başlığı için yer tutucu */}
            <p>Bu alana geri bildirim içeriği gelecek. Bu sadece örnek bir geri bildirim yazısıdır.</p> {/* Geri bildirim içeriği için yer tutucu */}
          </div>

          <div style={{ marginBottom: '20px', paddingBottom: '10px', borderBottom: '1px solid #ccc' }}> {/* İkinci geri bildirim bloğu */}
            <div style={{ display: 'flex', alignItems: 'center', marginBottom: '10px' }}> {/* Avatar ve kullanıcı adı */}
              <div>[Avatar Burada]</div> {/* Avatar için yer tutucu */}
              <h3>Kullanıcı Adı 2</h3> {/* Kullanıcı adı için yer tutucu */}
            </div>
            <h4>Geri Bildirim Başlığı 2</h4> {/* Geri bildirim başlığı için yer tutucu */}
            <p>Bu da ikinci bir örnek geri bildirim içeriğidir. HTML yapısını göstermek için statik olarak eklendi.</p> {/* Geri bildirim içeriği için yer tutucu */}
          </div>

          {/* İhtiyaç duyarsanız daha fazla geri bildirim bloğu ekleyebilirsiniz */}

        </div>
    );
  }
 }
