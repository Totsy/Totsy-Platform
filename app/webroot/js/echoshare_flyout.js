(function () {
				var a, b, c, d; b = document.createElement("script"); b.type = "text/javascript"; b.async = true;
				var e = new Date(); var f = e.getFullYear()+""+e.getMonth()+""+e.getDate()+""+e.getHours();
				b.src = (document.location.protocol === "https:" ? "https:"  : "http:") + "//api.theechosystem.com/core/resource/getjs?antiCache="+f;
				a = document.getElementsByTagName("script")[0]; a.parentNode.insertBefore(b, a);
			} ())