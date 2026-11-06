/*
 * Load remote HTML
 */

/* If the app allows to use different account then check URL values to get the account information */
window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(str,key,value) {
	if (config[key] === undefined || config[key] === "") {
		config[key] = value;
	}
});

$('#wrapper').load(
	'https://' + config.domain + '/supplementary/webrtc/body.mcomp?embed=1&i_env=' + config.i_env
	+ (config.id            ? '&id='            + config.id            : '')
	+ (config.h323_password ? '&h323_password=' + config.h323_password : '')
	+ (config.destination   ? '&destination='   + config.destination   : '')
	+ (config.autodial      ? '&autodial='      + config.autodial      : '')
	+ (config.mode          ? '&mode='          + config.mode          : '')
	+ (config.wss           ? '&wss='           + config.wss           : '')
);
