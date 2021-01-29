//<script type="text/javascript">				
	var nicedit;
		
	function addNicedit() {
	nicedit = new nicEditor({fullPanel : true,  iconsPath : '/Nicedit/nicEditorIcons.gif'}).panelInstance('newseditor');
	}
	function removeNicedit() {
	nicedit.removeInstance('newseditor');
	}
	
	
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
		request.open('POST','/controlpanel/uploadattachfile');
		request.send(formData);

		//событие срабатывает после получения ответа от сервера
		request.addEventListener('load',function(){
			const requestContent  = request.responseText;
			viewattaches.innerHTML = request.responseText;
		})
	}
	
	function reload_attaches(url) {

      	const viewattaches = document.getElementById('attachfiles');		
      	var req = getXmlHttp()
		req.open('GET', url, true); 
		
		//событие при возврате ответа от сервера
		req.onreadystatechange = function() {
		  if (req.readyState == 4) {
			 if(req.status == 200) {
				viewattaches.innerHTML = req.responseText;
			 }
		  }
		};
		
		req.send(null); 		
	}								
//</script>