<?php
	// Set the path to the de folder
	SetDevEditPath("devedit-5.0.4");

	// These are the functions that you can call to hide varions buttons,
	// lists and tab buttons. By default, everything is enabled

	//$myDE->HideFullScreenButton();
	//$myDE->HideBoldButton();
	$myDE->HideUnderlineButton();
	//$myDE->HideItalicButton();
	//$myDE->HideStrikethroughButton();
	//$myDE->HideNumberListButton();
	//$myDE->HideBulletListButton();
	//$myDE->HideDecreaseIndentButton();
	//$myDE->HideIncreaseIndentButton();
	//$myDE->HideLeftAlignButton();
	//$myDE->HideCenterAlignButton();
	//$myDE->HideRightAlignButton();
	//$myDE->HideJustifyButton();
	//$myDE->HideHorizontalRuleButton();
	//$myDE->HideLinkButton();
	//$myDE->HideAnchorButton();
	//$myDE->HideMailLinkButton();
	//$myDE->HideHelpButton();
	$myDE->HideFontList();
	$myDE->HideSizeList();
	//$myDE->HideFormatList();
	//$myDE->HideStyleList();
	//$myDE->HideForeColorButton();
	//$myDE->HideBackColorButton();
	//$myDE->HideTableButton();
	$myDE->HideFormButton();
	//$myDE->HideImageButton();
	$myDE->HideFlashButton();
	$myDE->DisableFlashUploading();
	$myDE->DisableFlashDeleting();
	$myDE->DisableInsertFlashFromWeb();
	//$myDE->HideTextBoxButton();
	//$myDE->HideSymbolButton();
	//$myDE->HidePropertiesButton();
	//$myDE->HideCleanHTMLButton();
	$myDE->HidePositionAbsoluteButton();
	//$myDE->HideSpellingButton();
	//$myDE->HideRemoveTextFormattingButton();
	//$myDE->HideSuperScriptButton();
	//$myDE->HideSubScriptButton();
	//$myDE->HideGuidelinesButton();
	//$myDE->DisableSourceMode();
	//$myDE->DisablePreviewMode();
	//$myDE->DisableImageUploading();
	$myDE->DisableImageDeleting();
	//$myDE->DisableXHTMLFormatting();
	$myDE->DisableSingleLineReturn();
	//$myDE->DisableInsertImageFromWeb();
	
	//If you want to use the spell checker, then you can set
	//the spelling language to DE_AMERICAN, DE_BRITISH or DE_CANADIAN,
	//DE_FRENCH, DE_SPANISH, DE_GERMAN, DE_ITALIAN, DE_PORTUGESE,
	//DE_DUTCH, DE_NORWEGIAN, DE_SWEDISH or DE_DANISH
	$myDE->SetLanguage(DE_BRITISH);

	//We can specify a list of fonts for the font drop down. If we don't,
	//then a default list will show
	//$myDE->SetFontList("Arial,Verdana");

	//We can specify a list of font sizes for the font size drop down. If we don't,
	//then a default list will show
	//$myDE->SetFontSizeList("8,10");

	//How do we want images to be inserted into our HTML content?
	//DE_PATH_TYPE_FULL will insert a image in this format: http://www.mysite.com/test.html
	//DE_PATH_TYPE_ABSOLUTE will insert a image in this format: /myimage.gif
	$myDE->SetPathType(DE_PATH_TYPE_ABSOLUTE);
	
	//Are we editing a full HTML page, or just a snippet of HTML?
	//DE_DOC_TYPE_HTML_PAGE means we're editing a complete HTML page
	//DE_DOC_TYPE_SNIPPET means we're editing a snippet of HTML
	$myDE->SetDocumentType(DE_DOC_TYPE_SNIPPET);
	
	//Do we want images to appear in the image manager as thumbnails or just in rows?
	//DE_IMAGE_TYPE_ROW means just list in a tabular format, without a thumbnail
	//DE_IMAGE_TYPE_THUMBNAIL means list in 4-per-line thumbnail mode
	$myDE->SetImageDisplayType(DE_IMAGE_TYPE_THUMBNAIL);
	
	//Do we want flash files to appear in the flash manager as thumbnails or just in rows?
	//DE_FLASH_TYPE_ROW means just list in a tabular format, without a thumbnail
	//DE_FLASH_TYPE_THUMBNAIL means list in 4-per-line thumbnail mode
	$myDE->SetFlashDisplayType(DE_FLASH_TYPE_THUMBNAIL);
	
	//Show table guidelines as dashed
	$myDE->EnableGuidelines();
	
	//If the user isnt running Internet Explorer, then a <textarea> tag will be shown.
	//This function will set the rows and cols of that <textarea>
	$myDE->SetTextAreaDimensions(60, 90);

	// Add some custom links that will appear in the link manager
	$myDE->AddCustomLink("ClacksWeb", "http://www.clacksweb.org.uk");

	// Use the AddCustomInsert function to add some custom inserts
	$myDE->AddCustomInsert("Red Text", "<font face='verdana' color='red' size='3'><b>Red Text</b></font>");
	$myDE->AddCustomInsert("Right image div", "<div class='right figure' style='TEXT-ALIGN: center'><img alt='Alt text' src='/path/to/images' /><br /><span style='FONT-SIZE: 0.8em'>Caption</span></div>");
	$myDE->AddCustomInsert("Section FP banner", "<div id='sectionfpbanner'><img alt='Business montage' src='/images/sections/business.gif'/></div>");


	// Use the AddImageLibrary function to add image libraries
	$myDE->AddImageLibrary("Content images", "/images/content");
	$myDE->AddImageLibrary("Councillors", "/images/councillors");
	$myDE->AddImageLibrary("Library Service", "/images/library");
	$myDE->AddImageLibrary("Logos", "/images/logos");
	$myDE->AddImageLibrary("Management", "/images/management");
	$myDE->AddImageLibrary("Maps", "/images/maps");
	$myDE->AddImageLibrary("Museum", "/images/museum");
	$myDE->AddImageLibrary("Schools", "/images/schools");
	$myDE->AddImageLibrary("Sport", "/images/sport");
?>