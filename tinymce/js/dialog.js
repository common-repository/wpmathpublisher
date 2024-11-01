tinyMCEPopup.requireLangPack();

var WpmpDialog = {
	init : function() {
		var myForm = document.forms[0];
		// Get the selected contents as text and place it in the input
		myForm.formula.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		myForm.size.value = tinyMCEPopup.getWindowArg('size');
		
		WpmpDialog.preview();
	},

	insert : function() {
		// Insert the contents from the input into the document
		var myForm = document.forms[0];
		var size = myForm.size.value;
		var parse = myForm.parse.checked;
		
		var mathText =
			'[math' +
			((size != 'default') ? ' size="'+size+'"' : '') +
			((!parse) ? ' noparse="true"' : '') +
			']' +
			myForm.formula.value +
			'[/math]';
				
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, mathText);
		tinyMCEPopup.close();
	},
	
	preview : function() {
		var codePreview = document.getElementById('codePreview');
		var myForm = document.forms[0];
		var size = myForm.size.value;
		var parse = myForm.parse.checked;
		
		var mathText =
			'[math' +
			((size != 'default') ? ' size="'+size+'"' : '') +
			((!parse) ? ' noparse="true"' : '') +
			']' +
			myForm.formula.value +
			'[/math]';
		
		codePreview.innerHTML = mathText;
	}
};

tinyMCEPopup.onInit.add(WpmpDialog.init, WpmpDialog);