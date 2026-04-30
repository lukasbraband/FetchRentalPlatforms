<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body {
        font-family: Arial, sans-serif;
        padding: 15px;
        font-size: 18px;
    }

    form {
        max-width: 500px;
    }

    select, input, button {
        width: 100%;
        font-size: 20px;     /* 👉 größere Schrift */
        padding: 12px;       /* 👉 größere Klickfläche */
        margin-bottom: 15px; /* 👉 Abstand zwischen Feldern */
        box-sizing: border-box;
    }

    button {
        background: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 22px;
        padding: 14px;
    }

    button:active {
        transform: scale(0.98);
    }
</style>
</head>
<form method="post">
    Plattform:
    <select name="plattform">
        <option value="getaround">Getaround</option>
        <option value="snappcar">SnappCar</option>
        <option value="paulcamper">PaulCamper</option>
    </select>
    <br><br>

    Auto:
    <select name="auto">
        <option value="Nissan">Nissan</option>
        <option value="Dacia">Dacia</option>
        <option value="Renault">Renault9Sitzer</option>
        <option value="Camper">Camper</option>
    </select>
    <br><br>

    Startdatum:
    <input type="date" name="start_date">
    Startzeit:
    <input type="time" name="start_time">
    <br><br>

    Enddatum:
    <input type="date" name="end_date">
    Endzeit:
    <input type="time" name="end_time">
    <br><br>

    <button type="submit">Generieren</button>
</form>

<?php
function roundToHalfHour(string $time): string
{
    list($hour, $minute) = explode(":", $time);

    $hour = (int)$hour;
    $minute = (int)$minute;

    if ($minute <= 15) {
        $minute = 0;
    } elseif ($minute <= 45) {
        $minute = 30;
    } else {
        $minute = 0;
        $hour++;
    }

    // 24h-Überlauf abfangen
    if ($hour === 24) {
        $hour = 0;
    }

    // sauber formatieren (HH:MM)
    return str_pad($hour, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minute, 2, "0", STR_PAD_LEFT);
}

// -------------

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $plattform = $_POST["plattform"];
    $auto = $_POST["auto"];
    $start_date = $_POST["start_date"];
    $start_time = $_POST["start_time"];
    $end_date = $_POST["end_date"];
    $end_time = $_POST["end_time"];

    // URL-Encoding (wichtig!)
    $start_time_enc = urlencode($start_time);
    $end_time_enc = urlencode($end_time);
    $auto_enc = urlencode($auto);

    # for snappcar
    $start_time = roundToHalfHour($start_time);
    $end_time = roundToHalfHour($end_time);
    [$start_hour, $start_minute] = explode(":", $start_time);
    [$end_hour, $end_minute] = explode(":", $end_time);


    if ($plattform === "getaround") {
	if ($auto ==="Nissan") {
		$url = "https://getaround.com/search?"
		    . "address=Storkower+Strasse+112%2C+10407+Berlin%2C+Germany"
		    . "&address_source=woosmap"
		    . "&start_date=$start_date"
		    . "&start_time=$start_time_enc"
		    . "&end_date=$end_date"
		    . "&end_time=$end_time_enc"
		    . "&country_scope=DE"
		    . "&latitude=52.53572"
		    . "&longitude=13.44999"
		    . "&seats_min=8"
		    . "&brands%5B%5D=$auto_enc";
		$url_snap = "https://www.snappcar.de/query?"
			. "loc=Storkower%20Stra%C3%9Fe%20112,%2010407%20Berlin,%20Deutschland&lat=52.53572&lng=13.44999&min-lat=52.517234495703434&min-lng=13.411838258969283&max-lat=52.5541977250563&max-lng=13.488141741024549&rental-"
			. "start=$start_date"
			. "T$start_hour:$start_minute&rental-end=$end_date"
			. "T$end_hour:$end_minute"
			. "&brand=Renault&seats-min=9&sort=recommended&offset=0&order=desc&zoom=14&year-min=2002&year-max=2004";

		$url_pc = "https://www.paulcamper.de/rv/81460?startDate=$start_date&endDate=$end_date&hireType=4&excessReductionId=205";
	} else if ($auto ==="Dacia") {
		$url = "https://getaround.com/search?"
		    . "address=Storkower+Strasse+112%2C+10407+Berlin%2C+Germany"
		    . "&address_source=woosmap"
		    . "&start_date=$start_date"
		    . "&start_time=$start_time_enc"
		    . "&end_date=$end_date"
		    . "&end_time=$end_time_enc"
		    . "&country_scope=DE"
		    . "&latitude=52.53572"
		    . "&longitude=13.44999"
		    . "&seats_min=5"
		    . "&brands%5B%5D=$auto_enc";
		$url_snap = "https://www.snappcar.de/query?"
			. "loc=Storkower%20Stra%C3%9Fe%20112,%2010407%20Berlin,%20Deutschland&lat=52.53572&lng=13.44999&min-lat=52.517234495703434&min-lng=13.411838258969283&max-lat=52.5541977250563&max-lng=13.488141741024549&rental-"
			. "start=$start_date"
			. "T$start_hour:$start_minute&rental-end=$end_date"
			. "T$end_hour:$end_minute"
			. "&brand=Dacia&model=Lodgy&sort=recommended&offset=0&order=desc&zoom=14&year-min=2011&year-max=2013";

	} else {
		$url = "";
		echo "Auto=$auto ist für plattform=$plattform nicht gueltig.<br>";
	}
        echo "<b>Als Text:</b><br>";
        echo "$url<br>";
        echo "<br>$url_snap<br>";
        echo "<br>$url_pc<br>";
        echo "<br><b>Als Link:</b><br>";
        echo "<a href='$url' target='_blank'>$url</a><br><br>";
        echo "<a href='$url_snap' target='_blank'>$url_snap</a><br><br>";
        echo "<a href='$url_pc' target='_blank'>$url_pc</a><br>";
    }

    if ($plattform === "snappcar") {
	if ($auto ==="Nissan" || $auto === "Renault9Sitzer") {
		$url_snap = "https://www.snappcar.de/query?"
			. "loc=Storkower%20Stra%C3%9Fe%20112,%2010407%20Berlin,%20Deutschland&lat=52.53572&lng=13.44999&min-lat=52.517234495703434&min-lng=13.411838258969283&max-lat=52.5541977250563&max-lng=13.488141741024549&rental-"
			. "start=$start_date"
			. "T$start_hour:$start_minute&rental-end=$end_date"
			. "T$end_hour:$end_minute"
			. "&brand=Renault&seats-min=9&sort=recommended&offset=0&order=desc&zoom=14&year-min=2002&year-max=2004";

	}
	if ($auto ==="Dacia") {
		$url_snap = "https://www.snappcar.de/query?"
			. "loc=Storkower%20Stra%C3%9Fe%20112,%2010407%20Berlin,%20Deutschland&lat=52.53572&lng=13.44999&min-lat=52.517234495703434&min-lng=13.411838258969283&max-lat=52.5541977250563&max-lng=13.488141741024549&rental-"
			. "start=$start_date"
			. "T$start_hour:$start_minute&rental-end=$end_date"
			. "T$end_hour:$end_minute"
			. "&brand=Dacia&model=Lodgy&sort=recommended&offset=0&order=desc&zoom=14&year-min=2011&year-max=2013";

	}
    }
    if ($plattform === "PaulCamper") {
	//$url_pc = "abc";
        echo "<b>Als Text:</b><br>";
//        echo "$url_pc<br>";
//        echo "<br><b>Als Link:</b><br>";
//        echo "<a href='$url_pc' target='_blank'>$url_pc</a><br>";
//
    }


    // Hier kannst du später Snappcar & PaulCamper ergänzen
}
?>
