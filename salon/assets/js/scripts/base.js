function display_notify(typeinfo,msg,process=""){
	// Use toastr for notifications get an parameter from other function
	var infotype=typeinfo;
	var msg=msg;
	toastr.options.positionClass = "toast-top-right";
	toastr.options.progressBar = true;
	toastr.options.debug = false;
	toastr.options.showDuration=800;
	toastr.options.hideDuration=800;
	toastr.options.timeOut = 1000; // time duration
	toastr.options.showMethod="fadeIn";
	toastr.options.hideMethod="fadeOut";

	toastr.options.showEasing = 'swing';
	toastr.options.hideEasing = 'linear';
	toastr.options.closeEasing = 'linear';

	toastr.options.closeButton=true;


	if (infotype=='Success' || infotype=='success'){
		toastr.success(msg,infotype);
	}
	if (infotype=='Info' || infotype=='info'){
		toastr.info(msg,infotype);
	}
	if (infotype=='Warning' || infotype=='warning'){
		toastr.warning(msg,infotype);
	}
	if (infotype=='Error' || infotype=='error'){
		toastr.error(msg,infotype);
	}

}
//function to round 2 decimal places
function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}
function scrolltable(){
  $('.js-pscroll').each(function(){
      var ps = new PerfectScrollbar(this);
    });
}
