		<script>
			// JavaScript Document "../js/navigation-scripts.js"

			$("#menu-toggle").click(function(e) {
				e.preventDefault();
				$("#wrapper").toggleClass("toggled");
			});

			window.setInterval("zeitanzeige()",1000);

			function zeitanzeige()
			{
				d = new Date ();
				h = (d.getHours () < 10 ? '0' + d.getHours () : d.getHours ());
				m = (d.getMinutes () < 10 ? '0' + d.getMinutes () : d.getMinutes ());
				s = (d.getSeconds () < 10 ? '0' + d.getSeconds () : d.getSeconds ());
				if (getCookie('lang')=="de") {
					var monate = new Array ("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
				} else {
					var monate = new Array ("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
				}
					/*var monate = new Array ("Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez");*/
			
				document.getElementById("anzeige_uhrzeit").innerHTML = h + ':' + m + <?php if ($show_seconds == 1) { echo "':' + s + "; } ?>' Uhr';
			
				document.getElementById("anzeige_datum").innerHTML = d.getDate () + '. ' + monate[d.getMonth ()] + ' ' + d.getFullYear ();
			}

			function setLang(tmp) {
				setCookie("lang", tmp, null, (new Date().getTime() + 3600*24),"/");
				window.location.href = $(location).attr('href');
				window.reload;
			}

			function setCookie(name, wert, domain, expires, path, secure){
			   var cook = name + "=" + unescape(wert);
			   cook += (domain) ? "; domain=" + domain : "";
			   cook += (expires) ? "; expires=" + expires : "";
			   cook += (path) ? "; path=" + path : "";

			   cook += (secure) ? "; secure" : "";
			   document.cookie = cook;
			}

			function getCookie(cname) {
				var name = cname + "=";
				var ca = document.cookie.split(';');
				for(var i=0; i<ca.length; i++) {
					var c = ca[i];
					while (c.charAt(0)==' ') c = c.substring(1);
					if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
				}
				return "";
			}

			function htmlEntities(str) {
				return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
			}

		</script>