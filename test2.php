<!DOCTYPE html>
<html>
	<head>
	<link rel="icon" href="Blåslaget.png" type="image/png" />
	<link rel="stylesheet" type "text/css" href="styling2.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<title>Strecklista</title>
    </head>
	<body>
	<div id="full-container" class="grid-container">
		<div id="navbarrow" class="grid-item">
			<ul id="navbar">
				<li class="dropdown">
					<div class="dropbtn">Admin</div>
					<div id="admin" class="dropdown-content"></div>
				</li>
				<li class="dropdown">
					<div class="dropbtn">Ångra streck</div>
					<div id="latestStreck" class="dropdown-content">
					</div>
				</li>
				<li class="dropdown">
					<div class="dropbtn">Sortera efter</div>
					<div class="dropdown-content">
						<span onclick=sortTable(0)>Namn</span>
						<span onclick=sortTable(2)>Sektion</span>
						<span onclick=sortTable(1)>Streck</span>
					</div>
				</li>
				<li>
					<div class="dropbtn">Räkna sedan:<input id="count_since" type="text" value="2018-12-16" onkeyup="parseDate(this.value)"></div>
				</li>
			</ul>
		</div>
		<div id="headerrow" class="grid-item">
			<h1>Blåslagets Strecklista Online</h1>
		</div>
		<div id="tableitem" class="grid-item">
			<div id="main">
				<table id="main_table"></table>
			</div>
		</div>
		</div>
	</div>
	<script>
		var currentSort = 2;
		var usersCounted = 0;
		var usersToCount = 0;
		var currChars = 10;
		var oldChars = 10;
		var ui = [];
		var users = [];
		var latestStreck = [];


		function logoutAdmin() {
			document.cookie = "admin=false";
			drawAdmin();
		}

		function add_streck() {
			var price = document.getElementById("buttonAmount").value;
			var name = document.getElementById("buttonName").value;
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					fetchUI();
				}
			};
			var req = "add_streck.php?n=" + name + "&p=" + price;
			xhttp.open("POST", req, true);
			xhttp.send();
		}

		function add_user() {
			var section = document.getElementById("userSection").value;
			var name = document.getElementById("userName").value;
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					fetchUI();
				}
			};
			var req = "add_user.php?n=" + name + "&s=" + section;
			xhttp.open("POST", req, true);
			xhttp.send();
		}


		function remove_button(name) {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					fetchUI();
				}
			};

			var req = "remove_button.php?n=" + name;
			xhttp.open("POST", req, true);
			xhttp.send();
		}

		function remove_user(name) {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					fetchUI();
				}
			};

			var req = "remove_user.php?n=" + name;
			xhttp.open("POST", req, true);
			xhttp.send();
		}

		function drawAdmin() {
			var cookie = decodeURIComponent(document.cookie);
			var html = "<span onclick=admin()>Logga in</span>";
			if (cookie.localeCompare("admin=true") == 0) {
				var logout = '<span onclick="logoutAdmin()">Logga ut</span>';
				var exportbutton = '<span onclick="exportSaldos()">Exportera skuldlista</span>';
				var add_button = '<span class="dropdown-2"><div clas="dropbtn-2">Lägg till knapp</div><div class="dropdown-content-2"><input class="listInput" id="buttonName" value=Titel><input class="listInput" id="buttonAmount" value=Kostnad><span onclick="add_streck()">Lägg till</span></div></span>';
				var remove_options = "";
				for (i = 0; i < ui.length; i++) {
					remove_options += '<span onclick=remove_button("' + ui[i][0] + '")>' + ui[i][0] + '</span>';
				}
				var remove_button = '<span class="dropdown-2"><div clas="dropbtn-2">Ta bort knapp</div><div class="dropdown-content-2">' + remove_options + '</div></span>';
				var add_user = '<span class="dropdown-2"><div clas="dropbtn-2">Lägg till användare</div><div class="dropdown-content-2"><input class="listInput" id="userName" value=Titel><input class="listInput" id="userSection" value=Sektion><span onclick="add_user()">Lägg till</span></div></span>';
				var remove_user_options = "";
				for (i = 0; i < users.length; i++) {
					remove_user_options += '<span onclick=remove_user("' + users[i][0] + '")>' + users[i][0] + '</span>';
				}
				var remove_user = '<span class="dropdown-2"><div clas="dropbtn-2">Ta bort användare</div><div class="dropdown-content-2">' + remove_user_options + '</div></span>';
				var buttons = exportbutton + add_button + remove_button + add_user + remove_user + logout;
				html = buttons;
			}
			document.getElementById("admin").innerHTML = html;
		}

		function exportSaldos() {
			window.location.href = "export.php";
		}

		function admin() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var pwdInput = prompt("Please enter the password");
					var pwd = this.responseText.substring(0, this.responseText.length-1);
					if ((pwdInput.localeCompare(pwd)) == 0) {
						document.cookie = "admin=true";
						drawAdmin();
					}
				}
			};
			xhttp.open("GET", "pwd", true);
			xhttp.send();
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



		function parseDate(input) {
			var dateReg = /^\d{4}([./-])\d{2}\1\d{2}$/
			currChars = input.length;
			if (currChars != oldChars && input.match(dateReg) != null) {
				var table = document.getElementById("main_table");
				var rows = table.rows;
				usersCounted = 0;
				for (i = 1; i < rows.length; i++) {
					var name = rows[i].getElementsByTagName("TD")[0].innerHTML;
					fetch_streck_logs(name, "", get_n_of_streck, true);
				}
			}
			oldChars = currChars;
		}
		function fetchUI() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					ui = this.responseText.split("\n");
					for (i = 0; i < ui.length; i++) {
						ui[i] = ui[i].split(",");
					}
					ui.pop();
					fetchUsers();
				}
			};
			xhttp.open("POST", "ui", true);
			xhttp.send();
		}
		function fetchUsers() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var tablehead = "<thead><tr><th onclick='sortTable(0)'>Namn</th><th onclick='sortTable(1)'>Streck</th>";
					for (i = 0; i < ui.length; i++) {
						tablehead += "<th></th>";
					}
					tablehead += "</tablehead>";
					document.getElementById("main_table").innerHTML = "<table id='main_table'>" + tablehead + "<tbody id=main_table_body>";
					var txt = this.responseText;
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
						var row = "<tr class= " + section + " id = " + name + "></tr>";
						document.getElementById("main_table_body").innerHTML += row;
						fetch_streck_logs(name, section, false)
					}
				}
			};
			xhttp.open("POST", "users2", true);
			xhttp.send();
		}
		function fetch_streck_logs(name, section, update) {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var logfile = this.responseText;
					logfile = logfile.split("\n");
					for (i = 0; i < logfile.length; i++) {
						logfile[i] = logfile[i].split(",");
					}
					get_n_of_streck(name, section, logfile, update)
				}
			};
			path = "usrs/" + name;
			xhttp.open("POST", path, true);
			xhttp.send();
		}
		function get_n_of_streck(name, section, log, update) {
			counter = 0;
			var time = document.getElementById("count_since").value;
			time = new Date(time);
			for (j = 0; j < log.length; j++) {
				streckdate = new Date(log[j][0]);
				if (0 > log[j][1] && time <= streckdate) {
					counter += 1;
				}
			}
			if (!update) {
				var row = "<td>" + name + "</td>";
				var streck = "<td>" + counter + "</td>";
				var buttons = "";
				if (counter < 200000) {
					for (i = 0; i < ui.length; i++) {
						buttons += "<td><button class='streckbutton' onclick=strecka('" + name + "','" + section + "','" +  ui[i][1] +"')>" + ui[i][0] + "</button></td>";
					}
				}
				document.getElementById(name).innerHTML = row + streck + buttons;
				usersCounted += 1;
				if (usersCounted = usersToCount) {
					sortTable(currentSort);
					drawAdmin();
				}
			}
			else {
				document.getElementById(name).getElementsByTagName("TD")[1].innerHTML = counter;
				usersCounted += 1;
				if (usersCounted = usersToCount) {
					sortTable(currentSort);
				}
			}

		}

		function strecka(name, section, amount) {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					latestStreck.push([name, section]);
					console.log(latestStreck);
					if (latestStreck.length > 5) {
						latestStreck.shift();
					}
					updateLatestStreck();
					fetch_streck_logs(name, section, get_n_of_streck);
				}
			};

			var req = "write.php?u=" + name + "&a=" + amount;
			xhttp.open("POST", req, true);
			xhttp.send();
		}

		function undoStreck(name, section) {
			//var name = latestStreck[0][0];
			//var section = latestStreck[0][1];
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					latestStreck.pop();
					updateLatestStreck();
					fetch_streck_logs(name, section, get_n_of_streck);
				}
			};
			var req = "delete.php?u=" + name;
			xhttp.open("POST", req, true);
			xhttp.send();
		}



		function sortTable(n) {
			var table, rows, switching, i, x, y, shouldSwitch;
			table = document.getElementById("main_table");
			switching = true;
			rows = table.rows;
			for (i = 1; i < rows.length; i++) {
				if (rows[i].className == "sektionLabel") {
					table.deleteRow(i);
					i--;
				}
			}
			while (switching) {
				switching = false;
				rows = table.rows;
				for (i = 1; i < (rows.length - 1); i++) {
					shouldSwitch = false;
					x = rows[i].getElementsByTagName("TD")[n];
					y = rows[i + 1].getElementsByTagName("TD")[n];
						if (n == 1) {
							if(Number(x.innerHTML) < Number(y.innerHTML)) {
								shouldSwitch = true;
								break;
							}
						}
						else if (n == 2) {
							if(rows[i].className > rows[i+1].className) {
								shouldSwitch = true;
								break;
							}
						}
						else {
							if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
								shouldSwitch = true;
								break;
							}
						}
				}
				if (shouldSwitch) {
					rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
					switching = true;
				}
			}
			var title = "";
			for (i = 1; i < rows.length; i++) {
				if (n == 2 && rows[i].className != title) {
					title = rows[i].className;
					var row = table.insertRow(i);
					row.className = "sektionLabel";
					row.innerHTML = "<td class=sektionLabel colspan=" + Number(2 + ui.length) + ">" + title + "</td>";
				}
			}
			currentSort = n;
		} 
		fetchUI();
	</script>
    </body>
</html>
