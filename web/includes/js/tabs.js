var Tabs = new Class({
	Implements: Options,
	togglers: [],
	elements: [],
	currentRequest: false,
	options: {
		'defaultTab': false,
		'loadingImage': false,
		'game': null,
		'mode': null,
		'extra': []
	},
	initialize: function(container, togglers, options) {
		this.setOptions(options);
		this.container = $(container);
		$splat(togglers).map(this.addTab, this);
	},
	addTab: function(toggler) {
		this.togglers.include(toggler);
		toggler.addEvent('click', this.loadTab.bind(this, [toggler, this.togglers.indexOf(toggler)]));
		if ( toggler.id.replace(/^tab_/, '') == this.options.defaultTab )
		{
			this.loadTab(toggler, this.togglers.indexOf(toggler));
		}
	},
	updateTab: function(txt) {
		$(this.loading).destroy();
		this.elements[this.currentRequest.options.currentTab] = new Element('div').set('html', txt).injectInside(this.container);
		//Evaluate the response AFTER it's been created
		txt.stripScripts(true);
		this.currentRequest = false;
	},
	refreshTab: function(change) {
		this.options.extra = $merge(this.options.extra, change);
		for ( i=0; i<this.togglers.length; i++)
		{
			if ( this.togglers[i].id.replace(/^tab_/, '') == this.currentTab )
			{
				this.elements[i].destroy();
				this.loadTab(this.togglers[i], i);
				return;
			}
		}
	},
	loadTab: function(toggler, idx) {

                // chrome has its own version of .bind() which passes the values as an array rather than arguments
                // so we only want to do this if we don't have normal looking parameters 
                if(typeof toggler == 'object' && typeof idx != 'number') {
                        idx = toggler[1];
                        toggler = toggler[0];
                }

		var tab = toggler.id.replace(/^tab_/, '');
		//Current tab?  Just return
		if ( this.currentTab == tab && this.container.hasChild(this.elements[idx]) )
			return;
		
		//Set the current tab
		for ( i=0; i<this.togglers.length; i++) this.togglers[i].set('class', '');
		toggler.set('class', 'active');
		this.currentTab = tab;
		
		//Current Request?  Lets cancel it
		if ( $chk(this.currentRequest) )
		{
			if ( $chk(this.loading) )
				this.loading.destroy();
			this.currentRequest.cancel();
		}
		
		//Hide the current Tabs
		$(this.container).getChildren().each(function(el){
			el.setStyle('display', 'none');
		});
		
		//Have we already cached this tab?
		if ( this.container.hasChild(this.elements[idx]) )
		{
			this.elements[idx].setStyle('display', 'block');
			return;
		}
		
		//Create the loading image (May change in the future)
		this.loading = new Element('div').set('html', '<br /><br /><center><b>Loading...</b><br /><img src="' + this.options.loadingImage + '" alt="Loading..." /></center>').injectInside($(this.container));
		//AJAX FTW
		this.currentRequest = new Request({
			url: 'hlstats.php?mode=' + this.options.mode + '&type=ajax&game=' + this.options.game + '&tab=' + tab + '&' + Hash.toQueryString(this.options.extra),
			currentTab: idx,
			onSuccess: this.updateTab.bind(this)
		}).send();
	}
});
