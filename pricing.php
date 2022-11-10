<!DOCTYPE html>
<html>
<head>
<title>Title of the document</title>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.js" type="text/javascript"></script>
<script>
;(function ( $, window, undefined ) {

    /** Default settings */
    var defaults = {
        active: null,
        event: 'click',
        disabled: [],
        collapsible: 'accordion',
        startCollapsed: false,
        rotate: false,
        setHash: false,
        animation: 'default',
        animationQueue: false,
        duration: 500,
        fluidHeight: true,
        scrollToAccordion: false,
        scrollToAccordionOnLoad: true,
        scrollToAccordionOffset: 0,
        accordionTabElement: '<div></div>',
        navigationContainer: '',
        click: function(){},
        activate: function(){},
        deactivate: function(){},
        load: function(){},
        activateState: function(){},
        classes: {
            stateDefault: 'r-tabs-state-default',
            stateActive: 'r-tabs-state-active',
            stateDisabled: 'r-tabs-state-disabled',
            stateExcluded: 'r-tabs-state-excluded',
            container: 'r-tabs',
            ul: 'r-tabs-nav',
            tab: 'r-tabs-tab',
            anchor: 'r-tabs-anchor',
            panel: 'r-tabs-panel',
            accordionTitle: 'r-tabs-accordion-title'
        }
    };

    /**
     * Responsive Tabs
     * @constructor
     * @param {object} element - The HTML element the validator should be bound to
     * @param {object} options - An option map
     */
    function ResponsiveTabs(element, options) {
        this.element = element; // Selected DOM element
        this.$element = $(element); // Selected jQuery element

        this.tabs = []; // Create tabs array
        this.state = ''; // Define the plugin state (tabs/accordion)
        this.rotateInterval = 0; // Define rotate interval
        this.$queue = $({});

        // Extend the defaults with the passed options
        this.options = $.extend( {}, defaults, options);

        this.init();
    }


    /**
     * This function initializes the tab plugin
     */
    ResponsiveTabs.prototype.init = function () {
        var _this = this;

        // Load all the elements
        this.tabs = this._loadElements();
        this._loadClasses();
        this._loadEvents();

        // Window resize bind to check state
        $(window).on('resize', function(e) {
            _this._setState(e);
            if(_this.options.fluidHeight !== true) {
                _this._equaliseHeights();
            }
        });

        // Hashchange event
        $(window).on('hashchange', function(e) {
            var tabRef = _this._getTabRefBySelector(window.location.hash);
            var oTab = _this._getTab(tabRef);

            // Check if a tab is found that matches the hash
            if(tabRef >= 0 && !oTab._ignoreHashChange && !oTab.disabled) {
                // If so, open the tab and auto close the current one
                _this._openTab(e, _this._getTab(tabRef), true);
            }
        });

        // Start rotate event if rotate option is defined
        if(this.options.rotate !== false) {
            this.startRotation();
        }

        // Set fluid height
        if(this.options.fluidHeight !== true) {
            _this._equaliseHeights();
        }

        // --------------------
        // Define plugin events
        //

        // Activate: this event is called when a tab is selected
        this.$element.bind('tabs-click', function(e, oTab) {
            _this.options.click.call(this, e, oTab);
        });

        // Activate: this event is called when a tab is selected
        this.$element.bind('tabs-activate', function(e, oTab) {
            _this.options.activate.call(this, e, oTab);
        });
        // Deactivate: this event is called when a tab is closed
        this.$element.bind('tabs-deactivate', function(e, oTab) {
            _this.options.deactivate.call(this, e, oTab);
        });
        // Activate State: this event is called when the plugin switches states
        this.$element.bind('tabs-activate-state', function(e, state) {
            _this.options.activateState.call(this, e, state);
        });

        // Load: this event is called when the plugin has been loaded
        this.$element.bind('tabs-load', function(e) {
            var startTab;

            _this._setState(e); // Set state

            // Check if the panel should be collaped on load
            if(_this.options.startCollapsed !== true && !(_this.options.startCollapsed === 'accordion' && _this.state === 'accordion')) {

                startTab = _this._getStartTab();

                // Open the initial tab
                _this._openTab(e, startTab); // Open first tab

                // Call the callback function
                _this.options.load.call(this, e, startTab); // Call the load callback
            }
        });
        // Trigger loaded event
        this.$element.trigger('tabs-load');
    };

    //
    // PRIVATE FUNCTIONS
    //

    /**
     * This function loads the tab elements and stores them in an array
     * @returns {Array} Array of tab elements
     */
    ResponsiveTabs.prototype._loadElements = function() {
        var _this = this;
        var $ul = (_this.options.navigationContainer === '') ? this.$element.children('ul:first') : this.$element.find(_this.options.navigationContainer).children('ul:first');
        var tabs = [];
        var id = 0;

        // Add the classes to the basic html elements
        this.$element.addClass(_this.options.classes.container); // Tab container
        $ul.addClass(_this.options.classes.ul); // List container

        // Get tab buttons and store their data in an array
        $('li', $ul).each(function() {
            var $tab = $(this);
            var isExcluded = $tab.hasClass(_this.options.classes.stateExcluded);
            var $anchor, $panel, $accordionTab, $accordionAnchor, panelSelector;

            // Check if the tab should be excluded
            if(!isExcluded) {

                $anchor = $('a', $tab);
                panelSelector = $anchor.attr('href');
                $panel = $(panelSelector);
                $accordionTab = $(_this.options.accordionTabElement).insertBefore($panel);
                $accordionAnchor = $('<a></a>').attr('href', panelSelector).html($anchor.html()).appendTo($accordionTab);

                var oTab = {
                    _ignoreHashChange: false,
                    id: id,
                    disabled: ($.inArray(id, _this.options.disabled) !== -1),
                    tab: $(this),
                    anchor: $('a', $tab),
                    panel: $panel,
                    selector: panelSelector,
                    accordionTab: $accordionTab,
                    accordionAnchor: $accordionAnchor,
                    active: false
                };

                // 1up the ID
                id++;
                // Add to tab array
                tabs.push(oTab);
            }
        });
        return tabs;
    };


    /**
     * This function adds classes to the tab elements based on the options
     */
    ResponsiveTabs.prototype._loadClasses = function() {
        for (var i=0; i<this.tabs.length; i++) {
            this.tabs[i].tab.addClass(this.options.classes.stateDefault).addClass(this.options.classes.tab);
            this.tabs[i].anchor.addClass(this.options.classes.anchor);
            this.tabs[i].panel.addClass(this.options.classes.stateDefault).addClass(this.options.classes.panel);
            this.tabs[i].accordionTab.addClass(this.options.classes.accordionTitle);
            this.tabs[i].accordionAnchor.addClass(this.options.classes.anchor);
            if(this.tabs[i].disabled) {
                this.tabs[i].tab.removeClass(this.options.classes.stateDefault).addClass(this.options.classes.stateDisabled);
                this.tabs[i].accordionTab.removeClass(this.options.classes.stateDefault).addClass(this.options.classes.stateDisabled);
           }
        }
    };

    /**
     * This function adds events to the tab elements
     */
    ResponsiveTabs.prototype._loadEvents = function() {
        var _this = this;

        // Define activate event on a tab element
        var fActivate = function(e) {
            var current = _this._getCurrentTab(); // Fetch current tab
            var activatedTab = e.data.tab;

            e.preventDefault();

            // Trigger click event for whenever a tab is clicked/touched even if the tab is disabled
            activatedTab.tab.trigger('tabs-click', activatedTab);

            // Make sure this tab isn't disabled
            if(!activatedTab.disabled) {

                // Check if hash has to be set in the URL location
                if(_this.options.setHash) {
                    // Set the hash using the history api if available to tackle Chromes repaint bug on hash change
                    if(history.pushState) {
                        // Fix for missing window.location.origin in IE
                        if (!window.location.origin) {
                            window.location.origin = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port: '');
                        }
                        
                        history.pushState(null, null, window.location.origin + window.location.pathname + window.location.search + activatedTab.selector);
                    } else {
                        // Otherwise fallback to the hash update for sites that don't support the history api
                        window.location.hash = activatedTab.selector;
                    }
                }

                e.data.tab._ignoreHashChange = true;

                // Check if the activated tab isnt the current one or if its collapsible. If not, do nothing
                if(current !== activatedTab || _this._isCollapisble()) {
                    // The activated tab is either another tab of the current one. If it's the current tab it is collapsible
                    // Either way, the current tab can be closed
                    _this._closeTab(e, current);

                    // Check if the activated tab isnt the current one or if it isnt collapsible
                    if(current !== activatedTab || !_this._isCollapisble()) {
                        _this._openTab(e, activatedTab, false, true);
                    }
                }
            }
        };

        // Loop tabs
        for (var i=0; i<this.tabs.length; i++) {
            // Add activate function to the tab and accordion selection element
            this.tabs[i].anchor.on(_this.options.event, {tab: _this.tabs[i]}, fActivate);
            this.tabs[i].accordionAnchor.on(_this.options.event, {tab: _this.tabs[i]}, fActivate);
        }
    };

    /**
     * This function gets the tab that should be opened at start
     * @returns {Object} Tab object
     */
    ResponsiveTabs.prototype._getStartTab = function() {
        var tabRef = this._getTabRefBySelector(window.location.hash);
        var startTab;

        // Check if the page has a hash set that is linked to a tab
        if(tabRef >= 0 && !this._getTab(tabRef).disabled) {
            // If so, set the current tab to the linked tab
            startTab = this._getTab(tabRef);
        } else if(this.options.active > 0 && !this._getTab(this.options.active).disabled) {
            startTab = this._getTab(this.options.active);
        } else {
            // If not, just get the first one
            startTab = this._getTab(0);
        }

        return startTab;
    };

    /**
     * This function sets the current state of the plugin
     * @param {Event} e - The event that triggers the state change
     */
    ResponsiveTabs.prototype._setState = function(e) {
        var $ul = $('ul:first', this.$element);
        var oldState = this.state;
        var startCollapsedIsState = (typeof this.options.startCollapsed === 'string');
        var startTab;

        // The state is based on the visibility of the tabs list
        if($ul.is(':visible')){
            // Tab list is visible, so the state is 'tabs'
            this.state = 'tabs';
        } else {
            // Tab list is invisible, so the state is 'accordion'
            this.state = 'accordion';
        }

        // If the new state is different from the old state
        if(this.state !== oldState) {
            // If so, the state activate trigger must be called
            this.$element.trigger('tabs-activate-state', {oldState: oldState, newState: this.state});

            // Check if the state switch should open a tab
            if(oldState && startCollapsedIsState && this.options.startCollapsed !== this.state && this._getCurrentTab() === undefined) {
                // Get initial tab
                startTab = this._getStartTab(e);
                // Open the initial tab
                this._openTab(e, startTab); // Open first tab
            }
        }
    };

    /**
     * This function opens a tab
     * @param {Event} e - The event that triggers the tab opening
     * @param {Object} oTab - The tab object that should be opened
     * @param {Boolean} closeCurrent - Defines if the current tab should be closed
     * @param {Boolean} stopRotation - Defines if the tab rotation loop should be stopped
     */
    ResponsiveTabs.prototype._openTab = function(e, oTab, closeCurrent, stopRotation) {
        var _this = this;
        var scrollOffset;

        // Check if the current tab has to be closed
        if(closeCurrent) {
            this._closeTab(e, this._getCurrentTab());
        }

        // Check if the rotation has to be stopped when activated
        if(stopRotation && this.rotateInterval > 0) {
            this.stopRotation();
        }

        // Set this tab to active
        oTab.active = true;
        // Set active classes to the tab button and accordion tab button
        oTab.tab.removeClass(_this.options.classes.stateDefault).addClass(_this.options.classes.stateActive);
        oTab.accordionTab.removeClass(_this.options.classes.stateDefault).addClass(_this.options.classes.stateActive);

        // Run panel transiton
        _this._doTransition(oTab.panel, _this.options.animation, 'open', function() {
            var scrollOnLoad = (e.type !== 'tabs-load' || _this.options.scrollToAccordionOnLoad);

            // When finished, set active class to the panel
            oTab.panel.removeClass(_this.options.classes.stateDefault).addClass(_this.options.classes.stateActive);

            // And if enabled and state is accordion, scroll to the accordion tab
            if(_this.getState() === 'accordion' && _this.options.scrollToAccordion && (!_this._isInView(oTab.accordionTab) || _this.options.animation !== 'default') && scrollOnLoad) {

                // Add offset element's height to scroll position
                scrollOffset = oTab.accordionTab.offset().top - _this.options.scrollToAccordionOffset;

                // Check if the animation option is enabled, and if the duration isn't 0
                if(_this.options.animation !== 'default' && _this.options.duration > 0) {
                    // If so, set scrollTop with animate and use the 'animation' duration
                    $('html, body').animate({
                        scrollTop: scrollOffset
                    }, _this.options.duration);
                } else {
                    //  If not, just set scrollTop
                    $('html, body').scrollTop(scrollOffset);
                }
            }
        });

        this.$element.trigger('tabs-activate', oTab);
    };

    /**
     * This function closes a tab
     * @param {Event} e - The event that is triggered when a tab is closed
     * @param {Object} oTab - The tab object that should be closed
     */
    ResponsiveTabs.prototype._closeTab = function(e, oTab) {
        var _this = this;
        var doQueueOnState = typeof _this.options.animationQueue === 'string';
        var doQueue;

        if(oTab !== undefined) {
            if(doQueueOnState && _this.getState() === _this.options.animationQueue) {
                doQueue = true;
            } else if(doQueueOnState) {
                doQueue = false;
            } else {
                doQueue = _this.options.animationQueue;
            }

            // Deactivate tab
            oTab.active = false;
            // Set default class to the tab button
            oTab.tab.removeClass(_this.options.classes.stateActive).addClass(_this.options.classes.stateDefault);

            // Run panel transition
            _this._doTransition(oTab.panel, _this.options.animation, 'close', function() {
                // Set default class to the accordion tab button and tab panel
                oTab.accordionTab.removeClass(_this.options.classes.stateActive).addClass(_this.options.classes.stateDefault);
                oTab.panel.removeClass(_this.options.classes.stateActive).addClass(_this.options.classes.stateDefault);
            }, !doQueue);

            this.$element.trigger('tabs-deactivate', oTab);
        }
    };

    /**
     * This function runs an effect on a panel
     * @param {Element} panel - The HTML element of the tab panel
     * @param {String} method - The transition method reference
     * @param {String} state - The state (open/closed) that the panel should transition to
     * @param {Function} callback - The callback function that is called after the transition
     * @param {Boolean} dequeue - Defines if the event queue should be dequeued after the transition
     */
    ResponsiveTabs.prototype._doTransition = function(panel, method, state, callback, dequeue) {
        var effect;
        var _this = this;

        // Get effect based on method
        switch(method) {
            case 'slide':
                effect = (state === 'open') ? 'slideDown' : 'slideUp';
                break;
            case 'fade':
                effect = (state === 'open') ? 'fadeIn' : 'fadeOut';
                break;
            default:
                effect = (state === 'open') ? 'show' : 'hide';
                // When default is used, set the duration to 0
                _this.options.duration = 0;
                break;
        }

        // Add the transition to a custom queue
        this.$queue.queue('responsive-tabs',function(next){
            // Run the transition on the panel
            panel[effect]({
                duration: _this.options.duration,
                complete: function() {
                    // Call the callback function
                    callback.call(panel, method, state);
                    // Run the next function in the queue
                    next();
                }
            });
        });

        // When the panel is openend, dequeue everything so the animation starts
        if(state === 'open' || dequeue) {
            this.$queue.dequeue('responsive-tabs');
        }

    };

    /**
     * This function returns the collapsibility of the tab in this state
     * @returns {Boolean} The collapsibility of the tab
     */
    ResponsiveTabs.prototype._isCollapisble = function() {
        return (typeof this.options.collapsible === 'boolean' && this.options.collapsible) || (typeof this.options.collapsible === 'string' && this.options.collapsible === this.getState());
    };

    /**
     * This function returns a tab by numeric reference
     * @param {Integer} numRef - Numeric tab reference
     * @returns {Object} Tab object
     */
    ResponsiveTabs.prototype._getTab = function(numRef) {
        return this.tabs[numRef];
    };

    /**
     * This function returns the numeric tab reference based on a hash selector
     * @param {String} selector - Hash selector
     * @returns {Integer} Numeric tab reference
     */
    ResponsiveTabs.prototype._getTabRefBySelector = function(selector) {
        // Loop all tabs
        for (var i=0; i<this.tabs.length; i++) {
            // Check if the hash selector is equal to the tab selector
            if(this.tabs[i].selector === selector) {
                return i;
            }
        }
        // If none is found return a negative index
        return -1;
    };

    /**
     * This function returns the current tab element
     * @returns {Object} Current tab element
     */
    ResponsiveTabs.prototype._getCurrentTab = function() {
        return this._getTab(this._getCurrentTabRef());
    };

    /**
     * This function returns the next tab's numeric reference
     * @param {Integer} currentTabRef - Current numeric tab reference
     * @returns {Integer} Numeric tab reference
     */
    ResponsiveTabs.prototype._getNextTabRef = function(currentTabRef) {
        var tabRef = (currentTabRef || this._getCurrentTabRef());
        var nextTabRef = (tabRef === this.tabs.length - 1) ? 0 : tabRef + 1;
        return (this._getTab(nextTabRef).disabled) ? this._getNextTabRef(nextTabRef) : nextTabRef;
    };

    /**
     * This function returns the previous tab's numeric reference
     * @returns {Integer} Numeric tab reference
     */
    ResponsiveTabs.prototype._getPreviousTabRef = function() {
        return (this._getCurrentTabRef() === 0) ? this.tabs.length - 1 : this._getCurrentTabRef() - 1;
    };

    /**
     * This function returns the current tab's numeric reference
     * @returns {Integer} Numeric tab reference
     */
    ResponsiveTabs.prototype._getCurrentTabRef = function() {
        // Loop all tabs
        for (var i=0; i<this.tabs.length; i++) {
            // If this tab is active, return it
            if(this.tabs[i].active) {
                return i;
            }
        }
        // No tabs have been found, return negative index
        return -1;
    };

    /**
     * This function gets the tallest tab and applied the height to all tabs
     */
    ResponsiveTabs.prototype._equaliseHeights = function() {
        var maxHeight = 0;

        $.each($.map(this.tabs, function(tab) {
            maxHeight = Math.max(maxHeight, tab.panel.css('minHeight', '').height());
            return tab.panel;
        }), function() {
            this.css('minHeight', maxHeight);
        });
    };

    //
    // HELPER FUNCTIONS
    //

    ResponsiveTabs.prototype._isInView = function($element) {
        var docViewTop = $(window).scrollTop(),
            docViewBottom = docViewTop + $(window).height(),
            elemTop = $element.offset().top,
            elemBottom = elemTop + $element.height();
        return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    };

    //
    // PUBLIC FUNCTIONS
    //

    /**
     * This function activates a tab
     * @param {Integer} tabRef - Numeric tab reference
     * @param {Boolean} stopRotation - Defines if the tab rotation should stop after activation
     */
    ResponsiveTabs.prototype.activate = function(tabRef, stopRotation) {
        var e = jQuery.Event('tabs-activate');
        var oTab = this._getTab(tabRef);
        if(!oTab.disabled) {
            this._openTab(e, oTab, true, stopRotation || true);
        }
    };

    /**
     * This function deactivates a tab
     * @param {Integer} tabRef - Numeric tab reference
     */
    ResponsiveTabs.prototype.deactivate = function(tabRef) {
        var e = jQuery.Event('tabs-dectivate');
        var oTab = this._getTab(tabRef);
        if(!oTab.disabled) {
            this._closeTab(e, oTab);
        }
    };

    /**
     * This function enables a tab
     * @param {Integer} tabRef - Numeric tab reference
     */
    ResponsiveTabs.prototype.enable = function(tabRef) {
        var oTab = this._getTab(tabRef);
        if(oTab){
            oTab.disabled = false;
            oTab.tab.addClass(this.options.classes.stateDefault).removeClass(this.options.classes.stateDisabled);
            oTab.accordionTab.addClass(this.options.classes.stateDefault).removeClass(this.options.classes.stateDisabled);
        }
    };

    /**
     * This function disable a tab
     * @param {Integer} tabRef - Numeric tab reference
     */
    ResponsiveTabs.prototype.disable = function(tabRef) {
        var oTab = this._getTab(tabRef);
        if(oTab){
            oTab.disabled = true;
            oTab.tab.removeClass(this.options.classes.stateDefault).addClass(this.options.classes.stateDisabled);
            oTab.accordionTab.removeClass(this.options.classes.stateDefault).addClass(this.options.classes.stateDisabled);
        }
    };

    /**
     * This function gets the current state of the plugin
     * @returns {String} State of the plugin
     */
    ResponsiveTabs.prototype.getState = function() {
        return this.state;
    };

    /**
     * This function starts the rotation of the tabs
     * @param {Integer} speed - The speed of the rotation
     */
    ResponsiveTabs.prototype.startRotation = function(speed) {
        var _this = this;
        // Make sure not all tabs are disabled
        if(this.tabs.length > this.options.disabled.length) {
            this.rotateInterval = setInterval(function(){
                var e = jQuery.Event('rotate');
                _this._openTab(e, _this._getTab(_this._getNextTabRef()), true);
            }, speed || (($.isNumeric(_this.options.rotate)) ? _this.options.rotate : 4000) );
        } else {
            throw new Error("Rotation is not possible if all tabs are disabled");
        }
    };

    /**
     * This function stops the rotation of the tabs
     */
    ResponsiveTabs.prototype.stopRotation = function() {
        window.clearInterval(this.rotateInterval);
        this.rotateInterval = 0;
    };

    /**
     * This function can be used to get/set options
     * @return {any} Option value
     */
    ResponsiveTabs.prototype.option = function(key, value) {
        if(value) {
            this.options[key] = value;
        }
        return this.options[key];
    };

    /** jQuery wrapper */
    $.fn.responsiveTabs = function ( options ) {
        var args = arguments;
        var instance;

        if (options === undefined || typeof options === 'object') {
            return this.each(function () {
                if (!$.data(this, 'responsivetabs')) {
                    $.data(this, 'responsivetabs', new ResponsiveTabs( this, options ));
                }
            });
        } else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {
            instance = $.data(this[0], 'responsivetabs');

            // Allow instances to be destroyed via the 'destroy' method
            if (options === 'destroy') {
                // TODO: destroy instance classes, etc
                $.data(this, 'responsivetabs', null);
            }

            if (instance instanceof ResponsiveTabs && typeof instance[options] === 'function') {
                return instance[options].apply( instance, Array.prototype.slice.call( args, 1 ) );
            } else {
                return this;
            }
        }
    };

}(jQuery, window));

</script>
<script>
  
    jQuery(document).on('ready', function() {
      $('#responsiveTabsDemo').responsiveTabs({
      startCollapsed: 'tabs'
  });
      $('#mobileTabs').responsiveTabs({
      startCollapsed: 'tabs'
  });
    });
    
  
  </script>
  <script>
    //The pixel amount scrolled before back to top button appears
    var scrollAmount = 120;
    
    $(window).scroll(function() {
    
        if ($(window).scrollTop() > scrollAmount) {
            $("#go-to-pricing-section").addClass('scrolled');
        } else {
            $("#go-to-pricing-section").removeClass('scrolled');
        }
    });
    </script>
    <script>
        jQuery($ => {
          // The speed of the scroll in milliseconds
          const speed = 1000;
      
          $('a.scroll-link')
            .filter((i, a) => a.getAttribute('href').startsWith('#') || a.href.startsWith(`${location.href}#`))
            .unbind('click.smoothScroll')
            .bind('click.smoothScroll', event => {
              const targetId = event.currentTarget.getAttribute('href').split('#')[1];
              const targetElement = document.getElementById(targetId);
      
              if (targetElement) {
                event.preventDefault();
                $('html, body').animate({ scrollTop: $(targetElement).offset().top - 50 }, speed);
              }
            });
        });
      </script>


<script>
    $(function() {
      var taeb = $(".taeb-switch");
      taeb.find(".taeb").on("click", function() {
        var $this = $(this);
    
        if ($this.hasClass("active")) return;
    
        var direction = $this.attr("taeb-direction");
    
        taeb.removeClass("left right").addClass(direction);
        taeb.find(".taeb.active").removeClass("active");
        $this.addClass("active");
      });
    });
    </script>

<script>
    $('a.view-more-btn-monthly-basic').click(function() {
        $('.view-more-box-monthly-basic').slideToggle();
          $('.view-more-box-monthly-plus').hide();
        $('.view-more-box-monthly-premium').hide();  
      
    });
      
    $('a.view-more-btn-monthly-plus').click(function() {
        $('.view-more-box-monthly-plus').slideToggle();
          $('.view-more-box-monthly-basic').hide();
        $('.view-more-box-monthly-premium').hide();  
      
    });
      
    $('a.view-more-btn-monthly-premium').click(function() {
        $('.view-more-box-monthly-premium').slideToggle();
          $('.view-more-box-monthly-plus').hide();
        $('.view-more-box-monthly-basic').hide();  
    });
    
    
      
    $('a.view-more-btn-yearly-basic').click(function() {
        $('.view-more-box-yearly-basic').slideToggle();
          $('.view-more-box-yearly-plus').hide();
        $('.view-more-box-yearly-premium').hide();  
      
    });
      
    $('a.view-more-btn-yearly-plus').click(function() {
        $('.view-more-box-yearly-plus').slideToggle();
          $('.view-more-box-yearly-basic').hide();
        $('.view-more-box-yearly-premium').hide();  
      
    });
      
    $('a.view-more-btn-yearly-premium').click(function() {
        $('.view-more-box-yearly-premium').slideToggle();
          $('.view-more-box-yearly-plus').hide();
        $('.view-more-box-yearly-basic').hide();  
    });
    
      
        
    $('a.test').click(function() {
        $('.view-more-box-monthly-premium').slideToggle();
          $('.view-more-box-monthly-plus').hide();
        $('.view-more-box-monthly-basic').hide();
    if ($('a.view-more-btn-monthly-premium').text() == "+ View More") {
        $(this).text("- View Less")
      } else {
        $(this).text("+ View More")
    }
      
    });
    </script>
    
    <style>

        #responsiveTabsDemo{
            max-width: 1500px;
            width: 100%;
            margin: 0 auto;
        }

        .r-tabs .r-tabs-nav {
            margin: 0;
            padding: 0;
            font-size: 0;
        }
        
        .r-tabs .r-tabs-tab {
            display: inline-block;
            margin: 0;
            list-style: none;
        }
        
        .r-tabs .r-tabs-panel {
            padding: 15px;
            display: none;
        }
        
        .r-tabs .r-tabs-accordion-title {
            display: none;
        }
        
        .r-tabs .r-tabs-panel.r-tabs-state-active {
            display: block;
        }
        
        /* Accordion responsive breakpoint */
        @media only screen and (max-width: 768px) {
            .r-tabs .r-tabs-nav {
                display: flex;
            }
        
            .r-tabs .r-tabs-accordion-title {
                display: none;
            }
        }
        </style>

<style>
 
    .r-tabs-nav{
        text-align: center;
        display: flex;
        max-width: 418px;
        width: 100%;
        margin: 0 auto 30px!important;
    }
      
    .r-tabs .r-tabs-tab {
        
        font-size: 18px;
        font-family: Poppins;
        line-height: 1;
        background: #eee;
        transition: 0.2s all ease-in;
        flex: 0 1 50%;
    }
      
    .r-tabs-tab:first-child{
          border-top-left-radius:27.5px;
        border-bottom-left-radius:27.5px;;
    }
      
    .r-tabs-tab:last-child{
        border-top-right-radius:27.5px;
        border-bottom-right-radius:27.5px;
    }
      
    .r-tabs .r-tabs-tab a{
        color: #010101;
            padding: 13px 16px;
        display: block;
        text-decoration: none;
    }
    
    li.r-tabs-state-active{
          color:#fff;
    }
      
    .r-tabs-state-active a{
          color:#fff!important;
      transition:0.2s all ease-in;
    }
      
    .save{
        background: red;
        color: #fff;
        text-align: center;
        border-radius: 12px;
          font-size: 13px;
        padding: 2px 8px;
    }
        
      @media screen and (max-width:767px){
        
          .r-tabs-nav {
        max-width: 320px;
        }
        .r-tabs .r-tabs-tab {
        font-size: 14px;
        }
        
        .r-tabs .r-tabs-panel {
        padding: 0;
        }
        
        .save,
        .taeb-switch .taeb {
        font-size: 12px!important;
        }
        
        .r-tabs .r-tabs-tab a {
        padding: 15px 4px;
        }
        
      }
      
    </style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
 
  
   .pricing-table-features__link{
  	color:blue!important;
    text-decoration:underline;
  }
  
   .price{
  color:#ea0000!important;
  }
  
  .price s{
  color:#010101!important;
  }
  
  .pricing-table-container .sub-price-label--black{
  	color:#010101;
    font-weight:400;
  }
  
   sup {
    vertical-align: top!important;
	}
  
  .pointer-events{
  	pointer-events: none;
    cursor:not-allowed;
  }
  
  
  .cta-button{
    position:relative;
    z-index:56;
  }
  
  .pricing-table-header .pricing-table-plus,
  .pricing-table-header .pricing-table-premium{
  position:relative;
  }
  
  .pricing-table-header .pricing-table-plus:after{
  	content:'';
    position:absolute;
    display:inline-block;
    top:0;
    right:-165px;
    width:100%;
    height:100%;
    background-size:120px 82px;
    background-repeat:no-repeat;
    background-image:url('https://combativewingchun.com/images/unbounce/popular-badge.png');
  }
  
  .pricing-table-header .pricing-table-premium:after{
  	content:'';
    position:absolute;
    display:inline-block;
    top:0;
    right:-165px;
    width:100%;
    height:100%;
    background-size:120px 82px;
    background-repeat:no-repeat;
    background-image:url('https://combativewingchun.com/images/unbounce/best-value.png');
  }
   
   .pricing-table-wrapper{
       display: flex;
       justify-content: space-around;
   }

   .pricing-table-features{
        flex: 0 1 15%;
   }

   .pricing-table-basic{
       flex: 0 1 17%;
   }

   .pricing-table-premium{
       flex: 0 1 17%;
   }
  
  .pricing-table-wrapper{
       border-bottom: 2px solid #efefef;
   }
  
   .pricing-table-header{
       border-bottom: 4px solid #e0efff;
   }
  	

   .pricing-table-basic,
   .pricing-table-premium{
      text-align: center;
   }

   .pricing-table-features,
   .pricing-table-basic,
   .pricing-table-plus,
   .pricing-table-premium{
      padding:12px 8px;
   }


   .pricing-table-basic p,
   .pricing-table-plus p,
   .pricing-table-premium p{
    font-size: 18px;
    font-family: 'Poppins',Arial, Helvetica, sans-serif;
    color: #4a4a4a;
    line-height: 1.625;
   }
  
  .pricing-table-basic ul li,
  .pricing-table-plus ul li,
   .pricing-table-premium ul li{
    font-size: 18px;
    font-family: 'Poppins',Arial, Helvetica, sans-serif;
    color: #4a4a4a;
    line-height: 1.625;
    padding-bottom:10px;
   }

   .pricing-table-features p{
    font-size: 15px;
    font-family: 'Poppins',Arial, Helvetica, sans-serif;
    color: #4a4a4a;
    line-height: 1.625;
   }


   .pricing-table-wrapper i{
       font-size: 16px;
   }


   .pricing-table-wrapper i.fa-check{
    color: #0070F0;
    width: 36px;
    height: 26px;
    border-radius: 100%;
    border: 1px solid #0070F0;
    padding-top: 10px;
   }

   .pricing-table-wrapper i.fa-times{
    color:#4a4a4a;
    width: 36px;
    height: 26px;
    border-radius: 100%;
    border: 1px solid #4a4a4a;
    padding-top: 10px;
   }

   h2,h3,h4{
        font-weight: bold;
        font-family: 'Poppins',Arial, Helvetica, sans-serif;
     	line-height:1.2;
     	margin-bottom:8px;
   }

   h2{
        font-size: 26px;
   }

   h3{
        font-size: 24px;
        color:#0070F0;
   }

   h4{
        font-size: 16px;
   }



    .price{
        color:#010101;
        font-weight: bold;
        font-family: 'Poppins',Arial, Helvetica, sans-serif;
        font-size: 24px;
        display: block;
        margin-bottom: 6px;
    }
    .pricing-table-container .label{
        font-size: 18px;
    }

    .hide-in-desktop{
        display: none;
    }
  
    #responsiveTabsDemo .basic-button{
      background: rgba(241,209,0,0.20);
      display:block;
      max-width:166px;
      color:#010101!important;
      width:100%;
      margin:14px auto;
      text-align:center;
      padding:12px 24px;
      border:2px solid rgba(241,209,0,1);
      font-size:18px;
      font-weight:bold;
      font-family:'Poppins',Arial;
      transition: 0.2s all ease-in;
      border-radius:4px;
      text-decoration: none;
    }
  
    #responsiveTabsDemo .premium-button{
      background: rgba(241,209,0,1);
      display:block;
      max-width:166px;
      width:100%;
      color:#010101!important;
      margin:14px auto;
      text-align:center;
      padding:12px 24px;
      border:2px solid rgba(241,209,0,1);
      font-size:18px;
      font-weight:bold;
      font-family:'Poppins',Arial;
      transition: 0.2s all ease-in;
      border-radius:4px;
      text-decoration: none;
    }
  
    #responsiveTabsDemo .premium-button:hover,
    #responsiveTabsDemo .basic-button:hover{
      border:2px solid rgba(229,193,0,1);
      background-color:rgba(229,193,0,1);
    }
  
  	.sub-price-label{
    font-size: 13px;
    font-weight: bold;
    font-family: 'Poppins', Arial;
    line-height: 1.2;
    margin-top: 12px;
    display: block;
    color: #0070F0;
    }
  
  #lp-code-478{
    display:none!important;
  }
  
  #go-to-pricing-section{
    display:none;  
    position: fixed!important;
    bottom: 10px!important;
    right: 10px!important;
    color: #0070F0;
    font-size: 36px;  
    
  }
    
  .scrolled{
  	display:block!important;
  }
  
  .lp-pom-body .iframe-popup {
    color: #000 !important;
    font-size: inherit!important;
    font-family: 'Poppins'!important;
    line-height: 0 !important;
    text-decoration: underline!important;
    padding: 0;
    width: 100%!important;
    display: inline-block!important;
    height: auto!important;
    position: unset!important;
    line-height: 1.35!important;
    background: transparent!important;
    text-align: center!important;
  }
  
  .lp-pom-body .iframe-popup:hover {
    text-decoration: underline;
  }
  
  .qe-albums{
  	display: grid;
    grid-template-columns: auto auto auto;
    grid-gap: 10px;
    padding:0;
    margin:0;
    list-style: none;
  }
  
  .qe-albums li{
  	padding:0 1%;
    flex:0 1 31.33%;
    margin:0 auto;
  }

  .qe-albums li img{
  	width: 100%;
  }
  
  .sublabel-price--black,
  .sub-price-label--black{
  	color:#010101;
  }
  
  .qe-albums-3{
    height: 40px;
    object-fit: contain;
    margin: 0;
    width: 120px;
  }
  
   .bold-blue-text {
    font-weight: bold;
    color: #0070F0;
   }



  @media screen and (min-width:767px) {
  
  	.align-items-center {
    display: flex;
    align-items: center;
    justify-content: center;
   }
    
  }
   

@media screen and (max-width:767px) {

  

    .hide-in-desktop{
        display: block;
    }
   h2{
        font-size: 22px;
   }

   h3{
        font-size: 20px;
   }

   h4{
        font-size: 18px;
   }
  

  
  .pricing-table-features h4{
  	margin-bottom:0;
  }
  
  
  .pricing-table-basic h4,.pricing-table-plus h4,.pricing-table-premium h4{
    font-size: 14px;
    margin-bottom: 2px;
  }
  
  .pricing-table-basic,.pricing-table-plus,.pricing-table-premium{
    padding: 12px 0;
    text-align:left;
  }
  
  .pricing-table-basic,
  .pricing-table-plus,
  .pricing-table-premium{
  	flex: 0 1 32.33%;
    padding-right: 0;
  }
  
  .pricing-table-premium{
  	flex: 0 1 32.33%;
    padding-left: 1%;
  }

  
  .pricing-table-features p {
    font-size: 14px;
  }
  
  .pricing-table-basic ul li,
  .pricing-table-plus ul li,
  .pricing-table-premium ul li,
  .pricing-table-basic p,
  .pricing-table-plus p,
  .pricing-table-premium p{
    font-size: 15px;
   }
  
  .pricing-table-wrapper i.fa-times,
  .pricing-table-wrapper i.fa-check {
    text-align: center;
  }
  
  .cta-mobile-buttons .cta-button{
    margin: 12px 0;
    max-width: 63px;
    font-size: 12px;
    padding: 10px 12px;
    line-height: 1.2;
  }
  
  .cta-mobile-buttons h3 {
    font-size: 16px;
   }
  
  .cta-mobile-buttons h3 {
    font-size: 16px;
   }
  
  .cta-mobile-buttons price {
    font-size: 20px;
  }
  
  .cta-mobile-buttons{
    display:flex;
  }
  
  #lp-code-478{
    display:block!important;
  }
  

  h6.subs{
    text-transform: uppercase;
    font-family: 'Poppins';
    font-size: 12px;
  }
 

.pricing-table-container .label {
    font-size: 12px;
	}
  
  .qe-albums li {
    margin: 0;
}

}

@media screen and (max-width:600px) {
  .lp-pom-body .iframe-popup {
    text-align: left!important;
	}
  
    
  .mobile-price .sublabel-price--black{
    font-size: 12px;
    line-height: 1.2;
  }
  	
  }


    
</style>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
  
   .list-style-type-none ul li{
  	list-style-type: none!important;
  }

.ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active, a.ui-button:active, .ui-button:active, .ui-button.ui-state-active:hover {
    border: 1px solid #0070F0;
    background: #fafcff;
    font-weight: normal;
    color: #666;
}
 
.ui-widget-content {
    border: none;
    color: #333333;
  	margin-bottom:12px;
}
  
.ui-state-active {
    border: 1px solid #0070F0;
    color: #fff;
    background: #0070F0!important;
}
  
.ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active, a.ui-button:active, .ui-button:active, .ui-button.ui-state-active:hover {
  border:none;  
  border-bottom: 1px solid #0070F0;
}
  .ui-corner-top,.ui-corner-all{
    border-radius:0!important;
  }
.ui-accordion .ui-accordion-header {
    font-family: "Poppins",sans-serif;
    font-weight: 700;
    border-radius: 0px!important;
    border: 1px solid #bddcff;
    padding: 12px 6px;
}

.ui-accordion .ui-accordion-content {
    font-weight: 400;
    font-family: "Poppins",sans-serif;
}
  
.ui-accordion-content li {
    list-style: disc;
}
  
.ui-widget-content p a{
  	color:blue!important;
  	text-decoration:underline;
}

</style>

<style>
    .taeb-switch {
    position: relative;
  }
  
  .taeb-switch:after {
    content: "";
    position: absolute;
    width: 50%;
    top: 0;
    transition: left cubic-bezier(.88, -.35, .565, 1.35) .4s;
    border-radius: 27.5px;
    box-shadow: 0 2px 15px 0 rgba(0, 0, 0, .1);
    background-color: #0070F0;
    height: 100%;
    z-index: 0;
  }
  
  .taeb-switch.left:after {
    left: 0;
  }
  
  .taeb-switch.right:after {
    left: 50%;
  }
  
  .taeb-switch .taeb {
    z-index: 1;
    position: relative;
    cursor: pointer;
    transition: color 200ms;
    font-size: 16px;
    font-weight: bold;
    line-height: normal;
    user-select: none;
  }
  
  .taeb-switch .taeb.active {
    color: #ffffff;
  }
  
  </style>

<style>
  
 
    .small-text{
        font-size:13px;
    }
    
    .pointer-events{
        pointer-events: none;
      cursor:not-allowed;
    }
     
  
    .view-more-box{
        display:none;
    }
    
     .view-more-btn{
        color: #0070F0!important;
      display: block;
      font-size: 12px;
      text-align: center;
      border: 1px solid #0070F0!important;
      max-width: 120px;
      width: 100%;
      margin: 20px auto 0;
      border-radius: 4px;
      padding: 6px 0;
    }
    
     .view-more-btn span{
        text-decoration:underline;
    }
    
     .view-more-btn:hover span{
        text-decoration:none;
    }
    
    .pricing-table-basic, .pricing-table-plus, .pricing-table-premium{
        text-align:center;
    }
    
    .mobile-price-container{
        border: 1px solid #dee2eb;
      max-width:500px;
      width:100%;
      margin:0 auto 32px;
      display:block;
      background:#fff;
      position:relative;
    }
    
    .mobile-price-container:before{
            content:'';
          position: absolute;
          display: inline-block;
          top: 0;
          right: -190px;
          width: 100%;
          height: 100%;
          background-size: 120px 82px;
          background-repeat: no-repeat;
    }
    
    .mobile-price-container-plus:before{
       background-image: url("https://combativewingchun.com/images/unbounce/popular-badge.png");
    }
    
    .mobile-price-container-premium:before{
       background-image: url("https://combativewingchun.com/images/unbounce/best-value.png");
    }
    
    .mobile-price-container .cta-button{
        padding: 12px 20px;
      font-size:15px;
      max-width:150px;
    }
    
    .fa-check{
        color:#0070F0;
    }
    
    .fa-times{
        color:#4a4a4a;
    }
    
    .detail{
        display: grid;
      font-size: 15px;
      grid-template-columns: 16px auto;
      grid-gap: 10px;
    }
    
    .mobile-price{
        padding:20px;
    }
    
    #mobileTabs .r-tabs-panel {
      padding: 5px;
     }
    
    .mobile-container-details{
      font-family: 'Poppins',Arial, Helvetica, sans-serif;
      color: #4a4a4a;
      line-height: 1.625;
      padding-top: 20px;
      border-top: 5px solid #dee2eb;
     }
    
    .cross-out{
        opacity:0.25;
    }

    .white-popup {
  position: relative;
  background: #FFF;
  padding: 20px;
  width: auto;
  max-width: 500px;
  margin: 20px auto;
}
      
  
    
  </style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js" integrity="sha512-IsNh5E3eYy3tr/JiX2Yx4vsCujtkhwl7SLqgnwLNgf04Hrt9BT9SXlLlZlWx+OK4ndzAoALhsMNcCmkggjZB1w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" integrity="sha512-+EoPw+Fiwh6eSeRK7zwIKG2MA8i3rV/DGa3tdttQGgWyatG/SkncT53KHQaS5Jh9MNOT3dmFL0FjTY08And/Cw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script>

  jQuery(document).ready(function() {
    jQuery('.open-popup-link').magnificPopup({
        type: 'inline',
        preloader: true,
        closeBtnInside: true,
        showCloseBtn: true,
        closeOnBgClick: true
    });
});


</script>


</head>

<body>

    <div id="responsiveTabsDemo">
        <ul class="taeb-switch left">
          <li><a class="taeb " taeb-direction="left" href="#monthly">1 Month</a></li>
          <li><a class="taeb" taeb-direction="right" href="#yearly">1 Year <span class="save">Save Up To 15%</span></a></li>
        </ul>
      
        <div id="monthly">
        <div class="pricing-table-container">
      
        <div class="pricing-table-wrapper pricing-table-header">
            <div class="pricing-table-features">
                <h2>Features</h2>
            </div>

            <div class="pricing-table-basic hide-in-mobile">
                <h3>FREE</h3>
              <span class="price">FREE</span>
                <a href="https://qienergy.ai/membership-account/membership-checkout/?level=94" class="cta-button basic-button">Activate This Plan</a>
              <span class="sub-price-label">60 Day Money Back Guarantee<br/>Cancel Anytime</span>
            </div>

            <div class="pricing-table-basic hide-in-mobile">
                <h3>Rife</h3>
                <span class="price">$9.99 <span class="label">/mo</span></span>
                <a href="https://qienergy.ai/membership-account/membership-checkout/?level=94" class="cta-button basic-button">Activate This Plan</a>
              <span class="sub-price-label">60 Day Money Back Guarantee<br/>Cancel Anytime</span>
            </div>


            <div class="pricing-table-basic hide-in-mobile">
                <h3>Master/Quantum</h3>
                <span class="price">$14.99 <span class="label">/mo</span></span>
                <a href="https://qienergy.ai/membership-account/membership-checkout/?level=94" class="cta-button basic-button">Activate This Plan</a>
              <span class="sub-price-label">60 Day Money Back Guarantee<br/>Cancel Anytime</span>
            </div>
            <div class="pricing-table-basic pricing-table-plus hide-in-mobile">
                <h3>Higher quantum</h3>
                <span class="price">$97 <span class="label">/mo</span></span>
                <a href="https://qienergy.ai/membership-account/membership-checkout/?level=95" class="cta-button basic-button">Activate This Plan</a>
              <span class="sub-price-label">60 Day Money Back Guarantee<br/>Cancel Anytime</span>
            </div>
            <div class="pricing-table-premium hide-in-mobile">
                <h3>Inner circle</h3>
                <span class="price">$297 <span class="label">/mo</span></span>
                <a href="https://qienergy.ai/membership-account/membership-checkout/?level=96" class="cta-button premium-button">Activate This Plan</a>
              <span class="sub-price-label">60 Day Money Back Guarantee<br/>Cancel Anytime</span>
            </div>
        </div>
      
        <div class="pricing-table-wrapper">
            <div class="pricing-table-features">
                <h4>EMF Protection in every signature</h4>
            </div>

            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>

            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>


            
            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>
            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>
            <div class="pricing-table-premium align-items-center">
                 
                <i class="fas fa-check"></i>
            </div>
        </div>
              
        <div class="pricing-table-wrapper">
            <div class="pricing-table-features">
                <h4>Silent Energy Broadcasts</h4>
            </div>

            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>

            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>

            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>
            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>
            <div class="pricing-table-premium align-items-center">
                 
                <i class="fas fa-check"></i>
            </div>
        </div>
      
        <div class="pricing-table-wrapper">
            <div class="pricing-table-features">
                <h4>Unlimited Duration</h4>
            </div>

            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>

            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>


            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>


            <div class="pricing-table-basic align-items-center">
                
                <i class="fas fa-check"></i>
            </div>
            <div class="pricing-table-premium align-items-center">
                 
                <i class="fas fa-check"></i>
            </div>
        </div>
      

      
        <div class="pricing-table-wrapper">
            <div class="pricing-table-features">
                <h4>Energy Signatures Included</h4>
                <p>*all signatures include EMF protection</p>
            </div>

            <div class="pricing-table-basic">
                
                <ul class="qe-albums">
       <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-p-alt.png" width="75"></a></li>
      <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-alt.jpg" width="75"></a></li>
      <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-r-alt.jpg" width="75"></a></li>
      <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-c-alt.jpg" width="75"></a></li>

      </ul>
      <a href="#test-popup" class="open-popup-link">View Details</a>
  </div>

            <div class="pricing-table-basic">
                
                          <ul class="qe-albums">
                 <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-p-alt.png" width="75"></a></li>
                <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-alt.jpg" width="75"></a></li>
                <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-r-alt.jpg" width="75"></a></li>
                <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-c-alt.jpg" width="75"></a></li>
                </ul>
                <a href="#test-popup" class="open-popup-link">View Details</a>
            </div>
            <div class="pricing-table-basic">
                
                        <ul class="qe-albums">
                 <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-e-p-alt.png" width="75"></a></li>
                 <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-b-alt.jpg" width="75"></a></li>
                <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-e-alt.jpg" width="75"></a></li>
                <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-b-e-alt.jpg" width="75"></a></li>
                 <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-m-alt.jpg" width="75"></a></li>
                <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-r-alt.jpg" width="75"></a></li>
                <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-c-alt.jpg" width="75"></a></li>
                </ul>
                <a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup">View Details</a>
            </div>
        <div class="pricing-table-basic">
                
                          <ul class="qe-albums">
                 <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-p-alt.png" width="75"></a></li>
                <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-alt.jpg" width="75"></a></li>
                <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-r-alt.jpg" width="75"></a></li>
                <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-c-alt.jpg" width="75"></a></li>
      
                </ul>
                <a href="#test-popup" class="open-popup-link">View Details</a>
            </div>


            <div class="pricing-table-basic">
                
                <ul class="qe-albums">
       <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-p-alt.png" width="75"></a></li>
      <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-alt.jpg" width="75"></a></li>
      <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-r-alt.jpg" width="75"></a></li>
      <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-c-alt.jpg" width="75"></a></li>

      </ul>
      <a href="#test-popup" class="open-popup-link">View Details</a>
  </div>


        </div>
      

              

             
      
      
      </div>
        </div>
        <div id="yearly">

            <h4>This is yearly tab</h4>

            <div class="pricing-table-container">
          
            <div class="pricing-table-wrapper pricing-table-header">
                <div class="pricing-table-features">
                    <h2>Features</h2>
                </div>
    
                <div class="pricing-table-basic hide-in-mobile">
                    <h3>FREE</h3>
                  <span class="price">FREE</span>
                    <a href="https://qienergy.ai/membership-account/membership-checkout/?level=94" class="cta-button basic-button">Activate This Plan</a>
                  <span class="sub-price-label">60 Day Money Back Guarantee<br/>Cancel Anytime</span>
                </div>
    
                <div class="pricing-table-basic hide-in-mobile">
                    <h3>Rife</h3>
                    <span class="price">$9.99 <span class="label">/mo</span></span>
                    <a href="https://qienergy.ai/membership-account/membership-checkout/?level=94" class="cta-button basic-button">Activate This Plan</a>
                  <span class="sub-price-label">60 Day Money Back Guarantee<br/>Cancel Anytime</span>
                </div>
    
    
                <div class="pricing-table-basic hide-in-mobile">
                    <h3>Master/Quantum</h3>
                    <span class="price">$14.99 <span class="label">/mo</span></span>
                    <a href="https://qienergy.ai/membership-account/membership-checkout/?level=94" class="cta-button basic-button">Activate This Plan</a>
                  <span class="sub-price-label">60 Day Money Back Guarantee<br/>Cancel Anytime</span>
                </div>
                <div class="pricing-table-basic pricing-table-plus hide-in-mobile">
                    <h3>Higher quantum</h3>
                    <span class="price">$97 <span class="label">/mo</span></span>
                    <a href="https://qienergy.ai/membership-account/membership-checkout/?level=95" class="cta-button basic-button">Activate This Plan</a>
                  <span class="sub-price-label">60 Day Money Back Guarantee<br/>Cancel Anytime</span>
                </div>
                <div class="pricing-table-premium hide-in-mobile">
                    <h3>Inner circle</h3>
                    <span class="price">$297 <span class="label">/mo</span></span>
                    <a href="https://qienergy.ai/membership-account/membership-checkout/?level=96" class="cta-button premium-button">Activate This Plan</a>
                  <span class="sub-price-label">60 Day Money Back Guarantee<br/>Cancel Anytime</span>
                </div>
            </div>
          
            <div class="pricing-table-wrapper">
                <div class="pricing-table-features">
                    <h4>EMF Protection in every signature</h4>
                </div>
    
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
    
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
    
    
                
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
                <div class="pricing-table-premium align-items-center">
                     
                    <i class="fas fa-check"></i>
                </div>
            </div>
                  
            <div class="pricing-table-wrapper">
                <div class="pricing-table-features">
                    <h4>Silent Energy Broadcasts</h4>
                </div>
    
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
    
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
    
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
                <div class="pricing-table-premium align-items-center">
                     
                    <i class="fas fa-check"></i>
                </div>
            </div>
          
            <div class="pricing-table-wrapper">
                <div class="pricing-table-features">
                    <h4>Unlimited Duration</h4>
                </div>
    
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
    
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
    
    
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
    
    
                <div class="pricing-table-basic align-items-center">
                    
                    <i class="fas fa-check"></i>
                </div>
                <div class="pricing-table-premium align-items-center">
                     
                    <i class="fas fa-check"></i>
                </div>
            </div>
          
    
          
            <div class="pricing-table-wrapper">
                <div class="pricing-table-features">
                    <h4>Energy Signatures Included</h4>
                    <p>*all signatures include EMF protection</p>
                </div>
    
                <div class="pricing-table-basic">
                    
                    <ul class="qe-albums">
           <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-p-alt.png" width="75"></a></li>
          <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-alt.jpg" width="75"></a></li>
          <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-r-alt.jpg" width="75"></a></li>
          <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-c-alt.jpg" width="75"></a></li>
    
          </ul>
          <a href="#test-popup" class="open-popup-link">View Details</a>
      </div>
    
                <div class="pricing-table-basic">
                    
                              <ul class="qe-albums">
                     <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-p-alt.png" width="75"></a></li>
                    <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-alt.jpg" width="75"></a></li>
                    <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-r-alt.jpg" width="75"></a></li>
                    <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-c-alt.jpg" width="75"></a></li>
          
                    </ul>
                    <a href="#test-popup" class="open-popup-link">View Details</a>
                </div>
                <div class="pricing-table-basic">
                    
                            <ul class="qe-albums">
                     <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-e-p-alt.png" width="75"></a></li>
                     <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-b-alt.jpg" width="75"></a></li>
                    <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-e-alt.jpg" width="75"></a></li>
                    <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-b-e-alt.jpg" width="75"></a></li>
                     <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-m-alt.jpg" width="75"></a></li>
                    <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-r-alt.jpg" width="75"></a></li>
                    <li><a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup"><img src="https://qienergy.ai/alt-energies/q-c-alt.jpg" width="75"></a></li>
                    </ul>
                    <a id="lp-pom-button-481" href="/pricing/ab-2-lightbox.html" target="_blank" data-params="true" class="iframe-popup">View Details</a>
                </div>
            <div class="pricing-table-basic">
                    
                              <ul class="qe-albums">
                     <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-p-alt.png" width="75"></a></li>
                    <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-alt.jpg" width="75"></a></li>
                    <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-r-alt.jpg" width="75"></a></li>
                    <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-c-alt.jpg" width="75"></a></li>
          
                    </ul>
                    <a href="#test-popup" class="open-popup-link">View Details</a>
                </div>
    
    
                <div class="pricing-table-basic">
                    
                    <ul class="qe-albums">
           <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-p-alt.png" width="75"></a></li>
          <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-e-alt.jpg" width="75"></a></li>
          <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-r-alt.jpg" width="75"></a></li>
          <li><a href="#test-popup" class="open-popup-link"><img src="https://qienergy.ai/alt-energies/q-c-alt.jpg" width="75"></a></li>
    
          </ul>
          <a href="#test-popup" class="open-popup-link">View Details</a>
      </div>
    
    
            </div>
          
    
                  
    
                 
          
          
          </div>
            </div>
      
      </div>

      <div id="test-popup" class="white-popup mfp-hide">
        Popup content
      </div>

      

</body>

</html>