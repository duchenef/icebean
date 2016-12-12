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
              jQuery('#exampleXtra').html("&nbsp; Facet <b>"+ getTypeFromTag(ui.item.tag)+ "</b>");
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
