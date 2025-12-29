/* global CtAjax, Mustache */

// Modified http://paulirish.com/2009/markup-based-unobtrusive-comprehensive-dom-ready-execution/
// Only fires on body class (working off strictly WordPress body_class)

jQuery.noConflict();

var ChanceTheater = {
  // All pages
  common: {
    init: function() {
      // Enable Tooltips in Main
      jQuery('[data-toggle="tooltip"]').tooltip();

      jQuery('.entry-meta a').tooltip();

      jQuery('abbr').tooltip();

      jQuery('.widget_ct_event_calendar .calendar-format-calendar .calendar-day-hasevents').popover({
        trigger: 'manual',
        delay: { show: 200, hide: 100 },
        placement: 'auto',
        title: function () {
          return jQuery(this).find('.calendar-day-date').html();
        },
        content: function () {
          return jQuery(this).find('.calendar-events').html();
        },
        html: true,
        container: '.widget_ct_event_calendar'
      }).on('click', function () {
        var trigger = jQuery(this);
        var widget = trigger.parents('.widget');
        // Show Popover
        trigger.popover('show');
        // Hide Popover if Leave Widget
        widget.parent().on('mouseleave', function () {
          trigger.popover('hide');
        });
        // Hide Popover if Leave Popover
        widget.find('.popover').on('mouseleave', function () {
          trigger.popover('hide');
        });
        // Hide Any Other Popovers
        widget.find('.calendar-day-hasevents').not(trigger).popover('hide');
      });

      jQuery('.sidebar-primary .widget_ct_productions .production').has('.production-performance')
      .popover({
        trigger: 'hover',
        delay: { show: 500, hide: 100 },
        placement: 'left',
        title: 'Next Performance',
        content: function () {
          return jQuery(this).find('.production-event').html();
        },
        html: true
      });

      jQuery(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
          event.preventDefault();
          jQuery(this).ekkoLightbox({
            left_arrow_class: '.fa .fa-chevron-left',
            right_arrow_class: '.fa .fa-chevron-right'
          });
      });

      jQuery('.modal-open a, a.modal-open').click(function (event) {
        event.preventDefault();
        jQuery(this.hash).modal('show');
      });

      jQuery('a.popup').click(function (event) {
        event.preventDefault();
        var popup = jQuery('#modal_popup');
        var link = this;
        popup.find('.modal-title').text(jQuery(link).attr('alt'));
        popup.find('.modal-body iframe').attr('src', link.href);
        popup.modal('show');
      });

      jQuery('a.closewindow').click(function (event) {
        event.preventDefault();
        window.close();
      });

      /**
       * Linkable Tabs
       */

      // Link to tab
      var hash = document.location.hash;
      var prefix = "tab_";
      if (hash) {
        jQuery('.nav-tabs a[href$="' + hash.replace(prefix,"") + '"]').tab('show');
      }

      // Change hash for page-reload
      jQuery('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash.replace("#", "#" + prefix);
      });

    },
    finalize: function() { }
  },
  // Home page
  home: {
    init: function() {
      // JS here
    }
  },
  // About page
  'post_type_archive_ct_event': {
    init: function() {

      // Tooltips on Calendar Event Keys
      jQuery('.calendar-event-key-item').tooltip({
        delay: { show: 200, hide: 100 },
        placement: 'bottom'
      });

      // Popovers on Calendar Event Titles
      jQuery('.calendar-event-title').popover({
        delay: { show: 300, hide: 1000 },
        trigger: 'hover',
        placement: 'auto top',
        html: true,
        template: '<div class="popover"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"><p></p></div></div></div>',
        content: function () {
          var calEventEl = jQuery(this).parents('.calendar-event');
          var output;
          var request = {
            action: 'get_event',
            security: CtAjax.nonce,
            id: calEventEl.data('id')
          };
          var response = jQuery.ajax({
            type: 'POST',
            url: CtAjax.ajaxurl,
            data: request,
            async: false
          })
          .success(function (calEvent) {
            output = Mustache.render(
              '<article>' +
              '{{#masthead_url}}<img src="{{masthead_url}}">{{/masthead_url}}' +
              '<h1>{{name}}</h1>' +
              '<div class="event-date">{{date_start.date}}</div>' +
              '<div class="event-timespan">{{date_start.time}}&mdash;{{date_end.time}}</div>' +
              '<div class="event-summary">{{summary}}</div>' +
              '<div class="production-summary">{{production.summary}}</div>' +
              '<a href="{{permalink}}" class="learn-more">Click to read details about this event.</a>' +
              '</article>',
              calEvent
            );
          });

          return output;
        }
      });
    }
  },
};

var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = ChanceTheater;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {

    UTIL.fire('common');

    jQuery.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });

    UTIL.fire('common', 'finalize');
  }
};

jQuery(document).ready(UTIL.loadEvents);
