jQuery.noConflict();  


/*
    javascript in this file controls the html page demonstrating the autosubject functionality

*/



/**************************************************************************************/
/*              Set up and initialization */
/**************************************************************************************/
/*
initial setup - called from onLoad
  attaches the autocomplete function to the search box
*/


var currentSuggestIndexDefault = "suggestall";  //initial default value

function setUpPage() {
// connect the autoSubject to the input areas
    jQuery('#examplebox').autocomplete(	  {
          source: autoSubjectExample, 
          minLength: 1,
   		 select: function(event, ui) {
              /*jQuery('#exampleXtra').html("&nbsp;"+ getTypeFromTag(ui.item.tag)+ " / "+ ui.item.idroot);*/
              var marcfield = String.fromCharCode(30) + String.fromCharCode(9) + ui.item.tag;
              marcfield = marcfield.replace('1', '6');
              marcfield += String.fromCharCode(9) + ui.item.indicator + String.fromCharCode(9) + '7' + String.fromCharCode(10)
                                                  + 'a' + String.fromCharCode(9);
              if (ui.item.raw == '') var fastcontent = ui.item.auth;
              else var fastcontent = ui.item.raw;
              fastcontent = fastcontent.replace(/\$b/g, String.fromCharCode(10) + 'b' + String.fromCharCode(9));
              fastcontent = fastcontent.replace(/\$c/g, String.fromCharCode(10) + 'c' + String.fromCharCode(9));
              fastcontent = fastcontent.replace(/\$d/g, String.fromCharCode(10) + 'd' + String.fromCharCode(9));
              fastcontent = fastcontent.replace(/\$q/g, String.fromCharCode(10) + 'q' + String.fromCharCode(9));
              fastcontent = fastcontent.replace(/\$x/g, String.fromCharCode(10) + 'x' + String.fromCharCode(9));
              fastcontent = fastcontent.replace(/\$y/g, String.fromCharCode(10) + 'y' + String.fromCharCode(9));
              fastcontent = fastcontent.replace(/\$z/g, String.fromCharCode(10) + 'z' + String.fromCharCode(9));
              
              if (fastcontent.substring(fastcontent.length-1) != ')') fastcontent += '.';
              marcfield += fastcontent + String.fromCharCode(10) + '2' + String.fromCharCode(9) + 'fast' + String.fromCharCode(10) + String.fromCharCode(0);
              marcfield = JSON.stringify(marcfield);
              /*console.log(marcfield);*/
              jQuery('#exampleXtra').html("&nbsp" + getTypeFromTag(ui.item.tag) + "&nbsp<button class='buttons' id='copy-buttonAssign' data-clipboard-target= '#fastAssign'>Copy</button> <div class ='hidden' id='fastAssign' style='display: none;'>" + marcfield + "</div>");
              /*jQuery('#exampleXtra').html("&nbsp;"+ getTypeFromTag(ui.item.tag) + " / Marc: " + marcfield);*/
              
          } //end select
      } 
   ).data( "autocomplete" )._renderItem = function( ul, item ) { formatSuggest(ul, item);};
}  //end setUpPage()

/*  
    example style - simple reformatting
*/
function autoSubjectExample(request, response) {
  currentSuggestIndex = currentSuggestIndexDefault;
  autoSubject(request, response, exampleStyle);
}

/*
  For this example, replace the common subfield break of -- with  /
  */
  
function exampleStyle(res) {
  return res["auth"].replace("--","/"); 
   
}