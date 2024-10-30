/**
 * global ltb_settings
 */

function lemmToolbar(){

    "use strict";

    /**
     *
     * @type {lemmToolbar}
     */
    const that = this;

    this.init   = function()    {
        that.setConfig();
        that.setLtbStyles();
        that.bindOpenBtn();
        that.upScroll();
        that.getHighestZ( 'div' );
        that.isMobile();
    };

    this.setConfig  = function()    {
        this.ltb_settings   = ltb_settings;
    };

    this.bindOpenBtn    = function()    {
        jQuery(".open-close").on("click", function()    {
           jQuery("#scroll-area").toggleClass("opened");
        });
    };

    this.upScroll       = function ()   {
        jQuery("a.up").click(function() {
            jQuery("html, body").stop().animate({ scrollTop: 0 }, 500);
            return false;
        });
    };

    this.setLtbStyles = function () {
        let style = jQuery('<style>'+'\
            div.scroll-area {\
            z-index: ' + that.zIndex() + ' ;\
            }\
            div.scroll-area > div > a{\
            background-color: '+that.ltb_settings.primaerFarbe+';\
            color: '+that.ltb_settings.schriftFarbe+';\
            border-radius: '+that.ltb_settings.borderRadius+that.ltb_settings.radiusEinheit+';\
            }\
            div.scroll-area > div > a:hover{\
            background-color: '+that.ltb_settings.sekundaerFarbe+';\
            color: '+that.ltb_settings.schriftFarbe+';\
            ' + that.isMobile() + '\
            }\
            div.scroll-area > div > a > div.icon{\
            background-color: '+that.ltb_settings.primaerFarbe+';\
            }\
            div.scroll-area > div > a.open-close,\
            div.scroll-area > div > a.up{\
            background-color: '+that.ltb_settings.primaerFarbe+';\
            color: '+that.ltb_settings.schriftFarbe+';\
            border: 1px solid '+that.ltb_settings.primaerFarbe+';\
            }\
            div.scroll-area > div > a.up{\
            color: '+that.ltb_settings.schriftFarbe+';\
            }\
            div.scroll-area > div > a.open-close{\
            background-color: '+that.ltb_settings.primaerFarbe+';\
            }\
            div.scroll-area > div > a.open-close:after,\
            div.scroll-area.opened > div > a.open-close:after{\
            color: '+that.ltb_settings.schriftFarbe+';\
            }\
            div.scroll-area.opened > div > a.open-close{\
            background-color: '+that.ltb_settings.sekundaerFarbe+';\
            }\
            '+'</style>');
        jQuery( 'html > head' ).append( style );
    };

    this.template = '\
    \
    ';

    /**
     * Validation for zIndex
     * @returns {number}
     */
    this.zIndex = function () {
        if ( that.ltb_settings.zIndex ){
            return that.ltb_settings.zIndex;
        } else {
            return that.getHighestZ( 'div' );
        }
    };

    /**
     *
     * @param {string} elem
     * @returns {number}
     */
    this.getHighestZ   = function( elem ){
        let elems	= document.getElementsByTagName( elem );
        let highest	= 999;
        for ( let i = 0; i < elems.length; i++ ) {
            let zIndex	= document.defaultView.getComputedStyle(elems[i],null).getPropertyValue("z-index");
            if (( zIndex > highest ) && ( zIndex !== 'auto' )) {
                highest = parseInt(zIndex) + 1;
            }
        }
        return highest;
    };

    /**
     * CHECKS IF MOBILE DEVICE OR DESKTOP IS USED
     * @returns {string} mobileDevice
     */
    this.isMobile   = function(){
        let mobileDevice    = '';
        if( navigator.userAgent.match(/Android/i)
            || navigator.userAgent.match(/webOS/i)
            || navigator.userAgent.match(/iPhone/i)
            || navigator.userAgent.match(/iPad/i)
            || navigator.userAgent.match(/iPod/i)
            || navigator.userAgent.match(/BlackBerry/i)
            || navigator.userAgent.match(/Windows Phone/i)) {
            return mobileDevice;
        } else {
            mobileDevice    = 'width: 200px;';
            return mobileDevice;
        }
    };

    this.init();
}


/******************************************
 *** Cookie for First Toolbar Collapse
 ******************************************/

/**
 *
 * @constructor
 */
function LemmToolbarCookie() {
    "use strict";

    /**
     *
     * @type {LemmToolbarCookie}
     */
    const that    = this;

    this.init   = function() {
        if( ltb_settings.useCookie[0] ) {
            this.setConfig();

            if (that.ltb_settings.cookieExpires === '') {
                let isCookie = that.getCookie('visited');
                if (isCookie === undefined) {
                    that.scrollbarCollapse();
                    that.createCookie(that.cookieExp());
                }
            }
        }
    };

    this.setConfig  = function()    {
        this.ltb_settings   = ltb_settings;
    };

    /**
     *
     * @param expires
     */
    this.createCookie	= function( expires ){
        let cookie = "visited=true;";
        if( expires ) {
            if( expires instanceof Date ) {
                if( isNaN(expires.getTime())) {
                    expires = new Date();
                }
            }
            else {
                expires = new Date( new Date().getTime() + parseInt( expires ) * 1000 * 60 * 60 * 24 );
            }
            cookie	+= "expires=" + expires.toGMTString() + ";";
        }
        document.cookie	= cookie;
    };

    /**
     * GETS COOKIE FROM NAME
     * @param name
     * @returns {string}
     */
    this.getCookie	= function( name ){
        let value = "; " + document.cookie;
        let parts = value.split("; " + name + "=");
        if (parts.length === 2) return parts.pop().split(";").shift();
    };

    /**
     *
     * @returns {number}
     */
    this.cookieExp	= function () {
        if( that.ltb_settings.cookieExpires == null ) {
            return 30;
        } else {
            return that.ltb_settings.cookieExpires;
        }
    };

    this.scrollbarCollapse  = function () {
        jQuery( window ).scroll( function() {
            if( jQuery( window ).scrollTop() <= 100 ) {
                that.scrollbarOpen();
            } else {
                that.scrollbarClose();
            }
        });
    };

    this.scrollbarOpen  = function () {
        if( !jQuery( '#scroll-area' ).hasClass( 'opened' )) {
            jQuery( '#scroll-area' ).addClass( 'opened' );
        }
    };

    this.scrollbarClose = function () {
        if( jQuery( '#scroll-area' ).hasClass( 'opened' )) {
            jQuery( '#scroll-area' ).removeClass( 'opened' );
        }
    };

    this.init();
}

var ltb_cookie_object;
window.onload   = function () {
    ltb_cookie_object   = new LemmToolbarCookie();
};

var ltb_script_object;
jQuery( document ).ready( function(){
    ltb_script_object = new lemmToolbar();
} );