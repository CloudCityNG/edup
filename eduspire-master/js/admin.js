/**
@Page/Module Name/Class: 		admin.js
@Author Name:			 		ben binesh
@Date:					 		Sept,02 2013
@Purpose:		        		Contain functions used in the backend 
@Table referred:				NIL
@Table updated:					MIL
@Most Important Related Files	NIL
 */
 
 
/**
	@Function Name:	checkall
	@Author Name:	binesh
	@Date:		Sept, 02 2013
	@objForm   | Object | html form object 
	@return     void 
	@Purpose:	checked all checkboxes
	
*/
function checkall(objForm){
	len = objForm.elements.length;
	var i=0;
	for( i=0 ; i<len ; i++) {
		if (objForm.elements[i].type=='checkbox') {
			objForm.elements[i].checked=objForm.check_all.checked;
		}
	}
}

/**
	@Function Name:	check
	@Author Name:	binesh
	@Date:		Sept, 02 2013
	@return     void 
	@Purpose:	check wether the atleast on checkbox is checked or not 
	
*/

function check()
{
	var a=new Array();
	a=document.getElementsByName("chk_ids[]");
	var p=0;
	for(i=0;i<a.length;i++){
		if(a[i].checked){
			p=1;
		}
	}
	if (p==0){
		alert('Please select at least one check box.');
		return false;
	}
	return true;
}
function edu_close_popup(){
	jQuery('.popup-div').hide();
}
		
function edu_popup_msg(msg){
	jQuery('.popup-div .popup-msg').text(msg);
}
function edu_show_popup(){
	jQuery('.popup-div').show();
}

jQuery(document).ready(function($) {
	$('.popup-div .close-btn').click(edu_close_popup);
	$('.popup-div #ok-btn').click(edu_close_popup);
	$('.popup-div #cancel-btn').click(edu_close_popup);
	$("[name='chk_ids[]']").click(function(){
		if($(this).prop('checked')) {
          //$("[name='check_all']").prop('checked', true);
        } else {
			if( $("[name='check_all']").prop('checked')) {
		   $("[name='check_all']").prop('checked', false);
		   }
        }
	});
	

});