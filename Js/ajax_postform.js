function getXmlHttp(){
	var xmlhttp;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
	try {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	} catch (E) {
		xmlhttp = false;
	}
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

function post_form(){

	const viewattaches = document.getElementById('attachfiles');
	const request = getXmlHttp();
	const formData = new FormData(uploadattachform);
	request.open('POST','/forum/uploadattachfile');
	request.send(formData);
	
	//событие срабатывает после получения ответа от сервера
	request.addEventListener('load',function(){
		const requestContent  = request.responseText;
		const requestStatus = request.status;
		viewattaches.innerHTML = request.responseText;
		//console.log(requestContent);
	})
}