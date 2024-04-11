/**
 * KeeWeb plugin: Logout_Session
 * @author Elbangkero
 * @license MIT
 */

const Storage = require('storage/index').Storage;
const StorageBase = require('storage/storage-base').StorageBase;
const BaseLocale = require('locales/base');

class LogoutSession extends StorageBase {
    name = 'logout_session';
    icon = 'sign-out-alt';
    enabled = true;
    uipos = 100;

    needShowOpenConfig() {
        localStorage.removeItem('appSettings');
        localStorage.removeItem('fileInfo');
        alert('Change Account!'); 
        window.location.reload(true);
    }


}

BaseLocale.logout_session = 'Change Account';
Storage.logout_session = new LogoutSession();
 
module.exports.uninstall = function () {
};
