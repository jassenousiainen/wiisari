// Use this file to load tablesorter after the table has loaded

$(function() {
  var sort;

  if ($("table").hasClass("sort-desc")) { sort = [0,1]}
  else {sort=[0,0]}


  $("table").tablesorter({
    theme : "wiisari",

    sortList: [sort],

    widthFixed: true,
    // widget code contained in the jquery.tablesorter.widgets.js file
    // use the zebra stripe widget if you plan on hiding any rows (filter widget)
    widgets : [ "filter", "zebra" ],

    widgetOptions : {
      // using the default zebra striping class name, so it actually isn't included in the theme variable above
      // this is ONLY needed for materialize theming if you are using the filter widget, because rows are hidden
      zebra : ["even", "odd"],

      // reset filters button
      filter_reset : ".reset",

      // extra css class name (string or array) added to the filter element (input or select)
      // select needs a "browser-default" class or it gets hidden
      filter_cssFilter: ["", "", "browser-default"]
    },

    // shows the loading -icon while tablesorter is loading
    initialized : function(){
      $('.tableWrapper').removeClass('loading');
    }
  })
  .tablesorterPager({

    // target the pager markup - see the HTML block below
    container: $(".ts-pager"),

    // target the pager page select dropdown - choose a page
    cssGoto  : ".pagenum",

    // remove rows from the table to speed up the sort of large tables.
    // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
    removeRows: false,

    // output string - default is '{page}/{totalPages}';
    // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
    output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'
  });

});
