define(["Language"], function(Language) {
	"use strict";

	function ServerAdd() {
		this.init(); 
	}
	ServerAdd.prototype = {
		init: function() {
			elById("queryType").addEventListener("change", this._changeQueryType.bind(this));
		},

		_changeQueryType: function(e) {
			if (e.target.value == 'http' || e.target.value == 'https') {
				elHide(elById('usernameWrapper'));
				elHide(elById('displayNameWrapper'));
				elById('virtualServerPortLanguage').innerHTML = Language.get('wcf.page.teamspeakAdd.virtualServerID');
				elById('virtualServerPortDescriptionLanguage').innerHTML = Language.get('wcf.page.teamspeakAdd.virtualServerID.description');
				elById('passwordLanguage').innerHTML = Language.get('wcf.page.teamspeakAdd.apiKey');
				elById('virtualServerPort').value = '1';
			} else {
				elShow(elById('usernameWrapper'));
				elShow(elById('displayNameWrapper'));
				elById('virtualServerPortLanguage').innerHTML = Language.get('wcf.page.teamspeakAdd.virtualServerPort');
				elById('virtualServerPortDescriptionLanguage').innerHTML = Language.get('wcf.page.teamspeakAdd.virtualServerPort.description');
				elById('passwordLanguage').innerHTML = Language.get('wcf.page.teamspeakAdd.password');
				elById('virtualServerPort').value = '9987';
			}

			if (e.target.value == 'raw') {
				elById('queryPort').value = '9987';
			} else if (e.target.value == 'ssh') {
				elById('queryPort').value = '10022';
			} else if (e.target.value == 'http') {
				elById('queryPort').value = '10080';
			} else if (e.target.value == 'https') {
				elById('queryPort').value = '10443';
			}
		}
	}

	return ServerAdd;
});
