(function() {
    tinymce.create('tinymce.plugins.TextbloxShortcode', {

        init : function(ed, url) {

        	//var popUpURL = url + '/textbloxtinymce.php';

			ed.addCommand('TextbloxShortcodePopup', function() {
				ed.windowManager.open({
					file : ajaxurl + '?action=textblox_tinymce',
					//url : popUpURL,
					width : 400,
					height : 200,
					inline : 1
				});
			});

			ed.addButton('TextbloxShortcodeButton', {
				title : 'TextBlox',
				image : url + '/textblox_16.png',
				cmd : 'TextbloxShortcodePopup'
			});
		}
    });
    tinymce.PluginManager.add('TextbloxShortcode', tinymce.plugins.TextbloxShortcode);
}());