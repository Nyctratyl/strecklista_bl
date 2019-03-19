<!DOCTYPE html>
<html>
	<head>
		<link rel="icon" href="Blåslaget.png" type="image/png" />
		<link rel="stylesheet" type "text/css" href="styling25.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<title>Strecklista</title>
	</head>

	<div class = mobileheader>Strecklistan i mobilen!</div>

	<select id = chooseuser onchange = fetchUI()><option value="None">Välj användare...</option></select>
	
	<div id = profileinfo></div>

	<div id = uiButtons></div>

	<script>
		var ui = [];
		var users = [];
		var latestStreck = [];
		var oldChars = 10;
		var currChars = 10;

		function parseDate(input) {
			var dateReg = /^\d{4}([./-])\d{2}\1\d{2}$/
			currChars = input.length;
			if (currChars != oldChars && input.match(dateReg) != null) {
				fetch_streck_log();
			}
			oldChars = currChars;
		}

		function updateLatestStreck() {
			var undobutton = '<div id="undo"><button class="undobutton" onclick=undoStreck()>Ångra senaste streck</button></div>';
			var str = "";
			for (i = latestStreck.length - 1; i >= 0; i--) {
				var name = latestStreck[i][0];
				var section = latestStreck[i][1];
				str += "<span onclick=undoStreck('" + name + "','" + section + "')>" + latestStreck[i][0] + "</span>";
			}
			document.getElementById("latestStreck").innerHTML = str;
			console.log(latestStreck);
		}

		function fetchUI() {
			user = document.getElementById("chooseuser").value;
			document.getElementById("uiButtons").innerHTML = "";
			document.getElementById("profileinfo").innerHTML = "";

			if (user != "None") {
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {

						//Draw profile info - needs streck log, so call on that and let it do the
						//drawing too.
						fetch_streck_log();
						

						//Draw streck buttons
						ui = this.responseText.split("\n");
						for (i = 0; i < ui.length - 1; i++) {
							ui[i] = ui[i].split(",");
							document.getElementById("uiButtons").innerHTML += "<button class=mobilebutton onclick=strecka('" + ui[i][1] + "')>" + ui[i][0] + "</button>";
						}

						document.getElementById("uiButtons").innerHTML += "<div id = bottombuttons></div>";

						//Draw bottom buttons, dvs, ångra streck resp. gå till datorsida
						var undoButton = "<button class=mobilebutton onclick=undoStreck()>Ångra senaste</button>";
						var desktopSiteButton = "<button class=mobilebutton onclick=goToDesktop()>Gå till datorhemsidan</button>";

						document.getElementById("bottombuttons").innerHTML = undoButton + desktopSiteButton;
					}
				};
				xhttp.open("POST", "ui", true);
				xhttp.send();
			}
		}

		function goToDesktop() {
			window.location.href = "test.php?s=true";
		}

		function fetchUsers() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var txt = this.responseText;
					console.log(txt);
					txt = txt.split("\n");
					txt.pop();
					usersCounted = 0;
					usersToCount = txt.length;
					users = [];
					for (l = 0; l < txt.length; l++) {
						txt[l] = txt[l].split(",");
						var name = txt[l][0];
						var section = txt[l][1].substring(1, txt[l][1].length);
						users.push([name, section]);
						var option = "<option value='" + name + "'>" + name + "</option>";
						document.getElementById("chooseuser").innerHTML += option;
					}
				}
			};
			xhttp.open("POST", "users2", true);
			xhttp.send();
		}
		function fetch_streck_log() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var logfile = this.responseText;
					logfile = logfile.split("\n");
					for (i = 0; i < logfile.length; i++) {
						logfile[i] = logfile[i].split(",");
					}
					draw_n_of_streck(logfile)
				}
			};
			path = "usrs/" + user;
			xhttp.open("POST", path, true);
			xhttp.send();
		}
		function draw_n_of_streck(log) {
			counter = 0;
			if (document.getElementById("mobile_count_since") == null) {
				var time = "2018-12-16";
			}
			else {
				var time = document.getElementById("mobile_count_since").value;
			}
			time = new Date(time);
			var saldo = 0;
			for (j = 0; j < log.length -1; j++) {
				streckdate = new Date(log[j][0]);
				saldo += Number(log[j][1]);
				if (0 > log[j][1] && time <= streckdate) {
					counter += 1;
				}
			}
			var welcome  = "<div id=infoheader>Välkommen " + user + "!</div>";
			var streckCounter = "Antal streck: " + counter + "<br>";
			var timeInput = "Räkna streck sedan:<input id = mobile_count_since onkeyup = parseDate(this.value) type=text value='" + time.toISOString().substring(0,10) + "'><br>";
			var saldoCounter = "Saldo: " + saldo + "<br>";
			document.getElementById("profileinfo").innerHTML = welcome + streckCounter + timeInput + saldoCounter;
		}

		function profile(name) {
			window.location.href = "userpage.php?u=" + name;
		}

		function strecka(amount) {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					//latestStreck.push([name, section]);
					//console.log(latestStreck);
					//if (latestStreck.length > 5) {
					//	latestStreck.shift();
					//}
					//updateLatestStreck();
					//fetch_streck_logs(name, section, get_n_of_streck);
				}
			};

			var req = "write.php?u=" + user + "&a=" + amount;
			xhttp.open("POST", req, true);
			xhttp.send();
		}

		function undoStreck() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					latestStreck.pop();
					updateLatestStreck();
					fetch_streck_logs();
				}
			};
			var req = "delete.php?u=" + user;
			xhttp.open("POST", req, true);
			xhttp.send();
		}

		fetchUsers();
	</script>
    </body>
</html>
