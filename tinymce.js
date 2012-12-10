function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function insertTBshortcode() {
	var tagtext;
    var textblox_id = document.getElementById('tbid').value;

    //alert(textblox_id);

	if (textblox_id != "0" ) {
		tagtext = "[textblox id=" + textblox_id + "]";
	} else {
		tinyMCEPopup.close();
	}

	if(window.tinyMCE) {
		//TODO: For QTranslate we should use here 'qtrans_textarea_content' instead 'content'
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML.
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches.
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}

	return;
}