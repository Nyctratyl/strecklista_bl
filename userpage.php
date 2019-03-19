<!DOCTYPE html>
<?php $user = $_REQUEST["u"]; ?>
<html>
	<head>
	<link rel="icon" href="Blåslaget.png" type="image/png" />
	<link rel="stylesheet" type "text/css" href="styling25.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<title>Strecklista</title>
    </head>
	<body>
	<div id="full-container" class="grid-container">
		<div id="navbarrow" class="grid-item">
			<ul id="navbar">
				<li class="dropdown">
					<div class="dropbtn" onclick=back()>Tillbaka</div>
				</li>
				<li>
					<div class="dropbtn">Räkna sedan:<input id="count_since" type="text" value="2018-12-16" onkeyup="parseDate(this.value)"></div>
				</li>
			</ul>
		</div>
		<div id="headerrow" class="grid-item">
			<h1><?php echo $user ?></h1>
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



		function back() {
			window.location.href = "test.php";
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
				fetch_streck_logs();
			}
			oldChars = currChars;
		}
		function fetch_streck_logs() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var tablehead = "<thead><tr><th onclick='sortTable(0)'>Tid</th><th onclick='sortTable(1)'>Belopp</th>";
					tablehead += "</tablehead>";
					document.getElementById("main_table").innerHTML = "<table id='main_table'>" + tablehead + "<tbody id=main_table_body>";
					var logfile = this.responseText;
					logfile = logfile.split("\n");
					logfile.pop();
					var time = document.getElementById("count_since").value;
					time = new Date(time);
					for (i = logfile.length-1; i >= 0; i--) {
						logfile[i] = logfile[i].split(",");
						streckdate = new Date(logfile[i][0]);
						if (time <= streckdate) {
							document.getElementById("main_table_body").innerHTML += "<tr><td>" + logfile[i][0] + "</td><td>" + logfile[i][1] + "</td></tr>";
						}
					}
					document.getElementById("main_table").innerHTML += "</tbody>";
					get_n_of_streck(logfile)
				}
			};
			var name = <?php echo json_encode($user); ?> 
			path = "usrs/" + name;
			xhttp.open("POST", path, true);
			xhttp.send();
		}
		function get_n_of_streck(log) {
			counter = 0;
			var time = document.getElementById("count_since").value;
			time = new Date(time);
			for (j = 0; j < log.length; j++) {
				streckdate = new Date(log[j][0]);
				if (0 > log[j][1] && time <= streckdate) {
					counter += 1;
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
						if (n == 0) {
							if(Date.parse(x.innerHTML) < Date.parse(y.innerHTML)) {
								shouldSwitch = true;
								break;
							}
						}
						else if (n == 1) {
							if(Number(x.innerHTML) > Number(y.innerHTML)) {
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
		fetch_streck_logs();
	</script>
    </body>
</html>
