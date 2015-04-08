// Copyright Andrey Okonetchnikov (andrej.okonetschnikow@gmail.com), 2006-2007
if (!window.EWModalbox)
	var EWModalbox = new Object();

EWModalbox.Methods = {
	focusableElements: new Array,
	currFocused: 0,
	initialized: false,
	active: true,
	pExecutor: 0,
	options: {
		title: '&nbsp;', // Title of the ModalBox window
		width: 500, // Default width in px
		height: 90, // Default height in px
		overlay: true, // To overlay or not
		maxTop: 0, // maximum distance from the top
		overlayClose: true, // Close modal box by clicking on overlay
		overlayOpacity: .65, // Default overlay opacity
		overlayDuration: .25, // Default overlay fade in/out duration in seconds
		slideDownDuration: .5, // Default EWModalbox appear slide down effect in seconds
		slideUpDuration: .5, // Default EWModalbox hiding slide up effect in seconds
		resizeDuration: .25, // Default resize duration seconds
		inactiveFade: true, // Fades MB window on inactive state
		transitions: true,
		loadingString: "Please wait. Loading...", // Default loading string message
		closeString: "Close window", // Default title attribute for close window link
		closeValue: "&times;", // Default string for close link in the header
		params: {},
		method: 'get', // Default Ajax request method
		autoFocusing: true // Toggles auto-focusing for form elements. Disable for long text pages.
	},
	_options: new Object,
	
	setOptions: function(options) {
		Object.extend(this.options, options || {});
	},
	
	_init: function(options) {
		// Setting up original options with default options
		Object.extend(this._options, this.options);
		this.setOptions(options);
		
		//Create the overlay
		this.MBoverlay = new Element("div", { id: "EWMOverlay", opacity: "0" });
		if (!this.options.overlay) $(this.MBoverlay).setStyle({display: 'none'});

		//Create DOm for the window
		this.MBwindow = new Element("div", {id: "EWMWindow", style: "display: none"}).update(
			this.MBframe = new Element("div", {id: "EWMFrame"}).update(
				this.EWMHeader = new Element("div", {id: "EWMHeader"}).update(
					this.MBcaption = new Element("div", {id: "EWMCaption"})
				)
			)
		);
		this.MBclose = new Element("a", {id: "EWMClose", title: this.options.closeString, href: "#"}).update("<span>" + this.options.closeValue + "</span>");
		this.EWMHeader.insert({'bottom':this.MBclose});
		
		this.MBcontent = new Element("div", {id: "EWMContent"}).update(
			this.MBloading = new Element("div", {id: "EWMLoading"}).update(this.options.loadingString)
		);
		this.MBframe.insert({'bottom':this.MBcontent});
		
		// Inserting into DOM. If parameter set and form element have been found will inject into it. Otherwise will inject into body as topmost element.
		// Be sure to set padding and marging to null via CSS for both body and (in case of asp.net) form elements. 
		var injectToEl = $(document.body);
		injectToEl.insert({'bottom':this.MBwindow});
		injectToEl.insert({'bottom':this.MBoverlay});
		
		// Initial scrolling position of the window. To be used for remove scrolling effect during ModalBox appearing
		this.initScrollX = window.pageXOffset || document.body.scrollLeft || document.documentElement.scrollLeft;
		this.initScrollY = window.pageYOffset || document.body.scrollTop || document.documentElement.scrollTop;
		
		//Adding event observers
		this.hideObserver = this._hide.bindAsEventListener(this);
		this.kbdObserver = this._kbdHandler.bindAsEventListener(this);
		this._initObservers();

		this.initialized = true; // Mark as initialized
		this.center();
	},
	
	show: function(content, options) {
		if (this._p) this._p.stop();
		if(!this.initialized) this._init(options); // Check for is already initialized
		
		this.content = content;
		this.setOptions(options);
		if(this.options.title) {
			$(this.MBcaption).update(this.options.title);
		}
		
		if(this.MBwindow.style.display == "none") { // First modal box appearing
			this._appear();
			this.event("onShow"); // Passing onShow callback
		}
		else { // If MB already on the screen, update it
			this._update();
			this.resizeModal();
			this.event("onUpdate"); // Passing onUpdate callback
		} 
	},
	
	hide: function(s, options) { // External hide method to use from external HTML and JS
		if (this._p) this._p.stop();
		if (s > 0) {
			this._p = new PeriodicalExecuter(function(p) {
				p.stop(); this.hide(0, options);
			}.bind(this), s);
		} else {
			if (this.pExecutor) {
				this.pExecutor.stop();
				this.pExecutor = 0;
			}
			if(this.initialized) {
				// Reading for options/callbacks except if event given as a pararmeter
				if(options && typeof options.element != 'function') Object.extend(this.options, options); 
				// Passing beforeHide callback
				this.event("beforeHide");
				if(this.options.transitions)
					Effect.SlideUp(this.MBwindow, { duration: this.options.slideUpDuration, transition: Effect.Transitions.sinoidal, afterFinish: this._deinit.bind(this) } );
				else {
					$(this.MBwindow).hide();
					this._deinit();
				}
			}
		}
	},
	
	_hide: function(event) { // Internal hide method to use with overlay and close link
		event.stop(); // Stop event propaganation for link elements
		/* Then clicked on overlay we'll check the option and in case of overlayClose == false we'll break hiding execution [Fix for #139] */
		if(event.element().id == 'EWMOverlay' && !this.options.overlayClose) return false;
		this.hide();
	},
	
	_appear: function() { // First appearing of MB
		if(Prototype.Browser.IE && Prototype.BrowserFeatures.Version < 7) { // Preparing IE 6 for showing modalbox
			window.scrollTo(0,0);
			this._prepareIE("100%", "hidden"); 
		}
		this._setWidth();
		this._setPosition();
		if(this.options.transitions) {
			$(this.MBoverlay).setStyle({opacity: 0});
			new Effect.Fade(this.MBoverlay, {
					from: 0, 
					to: this.options.overlayOpacity, 
					duration: this.options.overlayDuration, 
					afterFinish: function() {
						new Effect.SlideDown(this.MBwindow, {
							duration: this.options.slideDownDuration, 
							transition: Effect.Transitions.sinoidal, 
							afterFinish: function(){ 
								this._setPosition(); 
								this.loadContent();
							}.bind(this)
						});
					}.bind(this)
			});
		} else {
			$(this.MBoverlay).setStyle({opacity: this.options.overlayOpacity});
			$(this.MBwindow).show();
			this._setPosition(); 
			this.loadContent();
		}
		this._setWidthAndPosition = this._setWidthAndPosition.bindAsEventListener(this);
		Event.observe(window, "resize", this._setWidthAndPosition);
	},
	
	resizeModal: function(options) {
		if (!options) options = {};
		options.oheight = $(this.MBcontent).getHeight();
		options.ohheight = $(this.EWMHeader).getHeight();
		options.owidth = $(this.MBwindow).getWidth();
		this.MBcontent.setStyle({height: ''});
		EWModalbox.resize((this.options.width - $(this.MBwindow).getWidth()), $(this.MBcontent).getHeight() - $(this.MBwindow).getHeight() + $(this.EWMHeader).getHeight(), options);
		this.center();
	},
	
	resize: function(byWidth, byHeight, options) { // Change size of MB without loading content
		var b = document.viewport.getDimensions();
		var oWidth = $(this.MBoverlay).getWidth();
		var wHeight = $(this.MBwindow).getHeight();
		var wWidth = $(this.MBwindow).getWidth();
		var hHeight = $(this.EWMHeader).getHeight();
		var cHeight = $(this.MBcontent).getHeight();
		var newHeight = ((wHeight - hHeight + byHeight) < cHeight) ? (cHeight + hHeight) : (wHeight + byHeight);
		if (newHeight > b.height) newHeight = b.height - 5;
		var newWidth = wWidth + byWidth;
		if(options) this.setOptions(options); // Passing callbacks
		
		this.MBwindow.setStyle({width: newWidth + "px", height: newHeight + "px"});
		setTimeout(function() {
			this.event("_afterResize"); // Passing internal callback
			this.event("afterResize"); // Passing callback
			this.MBcontent.setStyle({height: ($(this.MBframe).getHeight() - hHeight - 13) + 'px', overflow: 'auto'});
		}.bind(this), 1);
		
		if (wHeight == newHeight && wWidth == newWidth) {
			if (this.pExecutor) this.pExecutor.stop();
		} else if (!this.pExecutor) {
			this.pExecutor = new PeriodicalExecuter(function(pe) {
				this.resizeModal();
			}.bind(this), 0.25);
		}
	},
	
	center: function() {
		var b = document.viewport.getDimensions();
		var d = parseInt((b.height - $(this.MBwindow).getHeight())/2);
		if (d > this.options.maxTop) {
			d = this.options.maxTop;
		}
		var top = 0;
		if (d > 0) top = d;
		this.MBwindow.setStyle({top: top + "px"});
	},
	
	_update: function() { // Updating MB in case of wizards
		this.MBcontent.setStyle({height: 'auto'});
		$(this.MBcontent).update($(this.MBloading).update(this.options.loadingString));
		this.loadContent();
	},
	
	loadContent: function () {
		if(this.event("beforeLoad") != false) { // If callback passed false, skip loading of the content
			if(typeof this.content == 'string') {
				var htmlRegExp = new RegExp(/<\/?[^>]+>/gi);
				if(htmlRegExp.test(this.content)) {
					this._insertContent(this.content.stripScripts(), function(){
						this.content.extractScripts().map(function(script) {
							try {
								(window.execScript) ? window.execScript(script) : window.setTimeout(script, 0);
							} catch (e) {}
						}.bind(window));
					}.bind(this));
				} else // URL given as a parameter. We'll request it via Ajax
					new Ajax.Request( this.content, { method: this.options.method.toLowerCase(), parameters: this.options.params, 
						onSuccess: function(transport) {
							var response = new String(transport.responseText);
							this._insertContent(transport.responseText.stripScripts(), function(){
								response.extractScripts().map(function(script) {
									try {
										(window.execScript) ? window.execScript(script) : window.setTimeout(script, 0);
									} catch (e) {}
								}.bind(window));
							}.bind(this));
						}.bind(this),
						onException: function(instance, exception){
							EWModalbox.hide();
							throw('EWModalbox Loading Error: ' + exception);
						}
					});
					
			} else if (typeof this.content == 'object') {
				// object
			} else {
				EWModalbox.hide();
			}
		}
	},
	
	_insertContent: function(content, callback){
		$(this.MBcontent).hide().update("");
		if(typeof content == 'string') { // Plain HTML is given
			this.MBcontent.update(new Element("div", { style: "display: none" }).update(content)).down().show();
		} else if (typeof content == 'object') { // HTML Object is given
			var _htmlObj = content.cloneNode(true); // If node already a part of DOM we'll clone it
			// If clonable element has ID attribute defined, modifying it to prevent duplicates
			if(content.id) content.id = "MB_" + content.id;
			/* Add prefix for IDs on all elements inside the DOM node */
			$(content).select('*[id]').each(function(el){ el.id = "MB_" + el.id; });
			this.MBcontent.update(_htmlObj).down('div').show();
			if(Prototype.Browser.IE) // Toggling back visibility for hidden selects in IE
				$$("#EWMContent select").invoke('setStyle', {'visibility': ''});
		}
		
		setTimeout(function(){ // MSIE fix
			this._putContent(callback);
		}.bind(this),1);
	},
	
	_putContent: function(callback){
		this.MBcontent.show();
		this.resizeModal();
		
		this.focusableElements = this._findFocusableElements();
		this._setFocus(); // Setting focus on first 'focusable' element in content (input, select, textarea, link or button)
		if(callback != undefined) callback(); // Executing internal JS from loaded content
		this.event("afterLoad"); // Passing callback
	},
	
	_initObservers: function(){
		$(this.MBclose).observe("click", this.hideObserver);
		if(this.options.overlayClose)
			$(this.MBoverlay).observe("click", this.hideObserver);
		if(Prototype.Browser.Gecko)
			Event.observe(document, "keypress", this.kbdObserver); // Gecko is moving focus a way too fast
		else
			Event.observe(document, "keydown", this.kbdObserver); // All other browsers are okay with keydown
	},
	
	_removeObservers: function(){
		$(this.MBclose).stopObserving("click", this.hideObserver);
		if(this.options.overlayClose)
			$(this.MBoverlay).stopObserving("click", this.hideObserver);
		if(Prototype.Browser.Gecko)
			Event.stopObserving(document, "keypress", this.kbdObserver);
		else
			Event.stopObserving(document, "keydown", this.kbdObserver);
	},
	
	_setFocus: function() { 
		/* Setting focus to the first 'focusable' element which is one with tabindex = 1 or the first in the form loaded. */
		if(this.focusableElements.length > 0 && this.options.autoFocusing == true) {
			var firstEl = this.focusableElements.find(function (el){
				return el.tabIndex == 1;
			}) || this.focusableElements.first();
			this.currFocused = this.focusableElements.toArray().indexOf(firstEl);
			firstEl.focus(); // Focus on first focusable element except close button
		} else if($(this.MBclose).visible())
			$(this.MBclose).focus(); // If no focusable elements exist focus on close button
	},
	
	_findFocusableElements: function(){ // Collect form elements or links from MB content
		this.MBcontent.select('input:not([type~=hidden]), select, textarea, button, a[href]').invoke('addClassName', 'MB_focusable');
		return this.MBcontent.select('.MB_focusable');
	},
	
	_kbdHandler: function(event) {
		var node = event.element();
		switch(event.keyCode) {
			case Event.KEY_TAB:
				event.stop();
				
				/* Switching currFocused to the element which was focused by mouse instead of TAB-key. Fix for #134 */ 
				if(node != this.focusableElements[this.currFocused])
					this.currFocused = this.focusableElements.toArray().indexOf(node);
				
				if(!event.shiftKey) { //Focusing in direct order
					if(this.currFocused == this.focusableElements.length - 1) {
						this.focusableElements.first().focus();
						this.currFocused = 0;
					} else {
						this.currFocused++;
						this.focusableElements[this.currFocused].focus();
					}
				} else { // Shift key is pressed. Focusing in reverse order
					if(this.currFocused == 0) {
						this.focusableElements.last().focus();
						this.currFocused = this.focusableElements.length - 1;
					} else {
						this.currFocused--;
						this.focusableElements[this.currFocused].focus();
					}
				}
				break;			
			case Event.KEY_ESC:
				if(this.active) this._hide(event);
				break;
			case 32:
				this._preventScroll(event);
				break;
			case 0: // For Gecko browsers compatibility
				if(event.which == 32) this._preventScroll(event);
				break;
			case Event.KEY_UP:
			case Event.KEY_DOWN:
			case Event.KEY_PAGEDOWN:
			case Event.KEY_PAGEUP:
			case Event.KEY_HOME:
			case Event.KEY_END:
				// Safari operates in slightly different way. This realization is still buggy in Safari.
				if(Prototype.Browser.WebKit && !["textarea", "select"].include(node.tagName.toLowerCase()))
					event.stop();
				else if( (node.tagName.toLowerCase() == "input" && ["submit", "button"].include(node.type)) || (node.tagName.toLowerCase() == "a") )
					event.stop();
				break;
		}
	},
	
	_preventScroll: function(event) { // Disabling scrolling by "space" key
		if(!["input", "textarea", "select", "button"].include(event.element().tagName.toLowerCase())) 
			event.stop();
	},
	
	_deinit: function()
	{	
		this._removeObservers();
		Event.stopObserving(window, "resize", this._setWidthAndPosition );
		if(this.options.transitions && this.options.overlay) {
			Effect.toggle(this.MBoverlay, 'appear', {duration: this.options.overlayDuration, afterFinish: this._removeElements.bind(this) });
		} else {
			this.MBoverlay.hide();
			this._removeElements();
		}
		$(this.MBcontent).setStyle({overflow: '', height: ''});
	},
	
	_removeElements: function () {
		$(this.MBoverlay).remove();
		$(this.MBwindow).remove();
		if(Prototype.Browser.IE && !navigator.appVersion.match(/\b7.0\b/)) {
			this._prepareIE("", ""); // If set to auto MSIE will show horizontal scrolling
			window.scrollTo(this.initScrollX, this.initScrollY);
		}
		
		/* Replacing prefixes 'MB_' in IDs for the original content */
		if(typeof this.content == 'object') {
			if(this.content.id && this.content.id.match(/MB_/)) {
				this.content.id = this.content.id.replace(/MB_/, "");
			}
			this.content.select('*[id]').each(function(el){ el.id = el.id.replace(/MB_/, ""); });
		}
		/* Initialized will be set to false */
		this.initialized = false;
		this.event("afterHide"); // Passing afterHide callback
		this.setOptions(this._options); //Settings options object into intial state
	},
	
	_setWidth: function () { //Set size
		$(this.MBwindow).setStyle({width: this.options.width + "px", height: this.options.height + "px"});
	},
	
	_setPosition: function () {
		$(this.MBwindow).setStyle({left: (($(this.MBoverlay).getWidth() - $(this.MBwindow).getWidth()) / 2 ) + "px"});
	},
	
	_setWidthAndPosition: function () {
		$(this.MBwindow).setStyle({width: this.options.width + "px"});
		this._setPosition();
		this.resizeModal();
	},
	
	_getScrollTop: function () { //From: http://www.quirksmode.org/js/doctypes.html
		var theTop;
		if (document.documentElement && document.documentElement.scrollTop)
			theTop = document.documentElement.scrollTop;
		else if (document.body)
			theTop = document.body.scrollTop;
		return theTop;
	},
	_prepareIE: function(height, overflow){
		$$('html, body').invoke('setStyle', {width: height, height: height, overflow: overflow}); // IE requires width and height set to 100% and overflow hidden
		$$("select").invoke('setStyle', {'visibility': overflow}); // Toggle visibility for all selects in the common document
	},
	event: function(eventName) {
		if(this.options[eventName]) {
			var returnValue = this.options[eventName](); // Executing callback
			this.options[eventName] = null; // Removing callback after execution
			if(returnValue != undefined) 
				return returnValue;
			else 
				return true;
		}
		return true;
	}
};

Object.extend(EWModalbox, EWModalbox.Methods);