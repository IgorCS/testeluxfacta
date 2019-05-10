//variavel de requisicao
var ajax_w;
function sendForm(formid,div, cancelar = true) {
	//$("#"+formid+" [type='button']").attr('disabled',true);
	carregando(1);
	jQuery("#"+formid).ajaxSubmit(function(resposta){ 
		jQuery('#'+div).html(resposta);
		if(cancelar) {
			carregando(0);	
		}
	});
}

function carregando(n){	
	var display = "none";
	if(n==1){
		display = "block";		
	}else{
		lnk = document.querySelector('link[rel="icon"]');
		lnk.href= '/images/favicon.ico';
	}
	$(".progress-indicator").css('display',display);
	$("#fundo_carregando").css('display',display);
}