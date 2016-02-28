/*
 * MWS Admin v2.1 - Charts Demo JS
 * This file is part of MWS Admin, an Admin template build for sale at ThemeForest.
 * All copyright to this file is hold by Mairel Theafila <maimairel@yahoo.com> a.k.a nagaemas on ThemeForest.
 * Last Updated:
 * December 08, 2012
 *
 */

;(function( $, window, document, undefined ) {

    $(document).ready(function() {
        
        if($.plot) {
            var plot = $.plot(
              $("#mws-line-chart"),
              datachart,
              {
                  axes:{
                      xaxis:{
                          renderer:$.plot.DateAxisRenderer
                      }
                  },
                  tooltip: true, 
                  series: {
                      lines: {
                          show: true
                      },
                      points: {
                          show: true
                      }
                  },
                  grid: {
                      borderWidth: 0, 
                      hoverable: true,
                      clickable: true
                  }
              });
        }
    });

}) (jQuery, window, document);