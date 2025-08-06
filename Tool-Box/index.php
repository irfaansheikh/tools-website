<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ultimate Tools Converter</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f7fa; color: #333; }
    .container { max-width: 900px; margin: 0 auto; }
    .tab { overflow: hidden; border-bottom: 1px solid #ddd; }
    .tab button { background-color: #f1f1f1; border: none; outline: none; cursor: pointer; padding: 14px 16px; transition: 0.3s; font-size: 16px; }
    .tab button:hover { background-color: #ddd; }
    .tab button.active { background-color: #007A7A; color: white; }
    .tab-content { display: none; padding: 20px; background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
    .tab-content.active { display: block; }
    .converter { margin: 20px 0; }
    .converter label { display: block; margin: 10px 0 5px; }
    .converter input { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
    .converter select { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
    .converter button { padding: 10px 20px; background-color: #007A7A; color: white; border: none; border-radius: 4px; cursor: pointer; }
    .converter button:hover { background-color: #FF6F61; }
    .result { margin-top: 10px; font-weight: bold; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Ultimate Tools Converter</h1>
    <div class="tab">
      <button class="tablinks" onclick="openTab(event, 'Temperature')">Temperature</button>
      <button class="tablinks" onclick="openTab(event, 'Time')">Time</button>
      <button class="tablinks" onclick="openTab(event, 'Length')">Length</button>
      <button class="tablinks" onclick="openTab(event, 'Area')">Area</button>
      <button class="tablinks" onclick="openTab(event, 'Volume')">Volume</button>
      <button class="tablinks" onclick="openTab(event, 'Weight')">Weight</button>
    </div>

    <div id="Temperature" class="tab-content">
      <div class="converter">
        <label for="tempInput">Value:</label>
        <input type="number" id="tempInput" step="0.1">
        <label for="tempFrom">From:</label>
        <select id="tempFrom">
          <option value="celsius">Celsius (°C)</option>
          <option value="fahrenheit">Fahrenheit (°F)</option>
        </select>
        <label for="tempTo">To:</label>
        <select id="tempTo">
          <option value="celsius">Celsius (°C)</option>
          <option value="fahrenheit">Fahrenheit (°F)</option>
        </select>
        <button onclick="convert('Temperature')">Convert</button>
        <div id="tempResult" class="result"></div>
      </div>
    </div>

    <div id="Time" class="tab-content">
      <div class="converter">
        <label for="timeInput">Value:</label>
        <input type="number" id="timeInput" step="0.1">
        <label for="timeFrom">From:</label>
        <select id="timeFrom">
          <option value="seconds">Seconds (s)</option>
          <option value="minutes">Minutes (min)</option>
          <option value="hours">Hours (h)</option>
        </select>
        <label for="timeTo">To:</label>
        <select id="timeTo">
          <option value="seconds">Seconds (s)</option>
          <option value="minutes">Minutes (min)</option>
          <option value="hours">Hours (h)</option>
        </select>
        <button onclick="convert('Time')">Convert</button>
        <div id="timeResult" class="result"></div>
      </div>
    </div>

    <div id="Length" class="tab-content">
      <div class="converter">
        <label for="lengthInput">Value:</label>
        <input type="number" id="lengthInput" step="0.1">
        <label for="lengthFrom">From:</label>
        <select id="lengthFrom">
          <option value="meters">Meters (m)</option>
          <option value="feet">Feet (ft)</option>
        </select>
        <label for="lengthTo">To:</label>
        <select id="lengthTo">
          <option value="meters">Meters (m)</option>
          <option value="feet">Feet (ft)</option>
        </select>
        <button onclick="convert('Length')">Convert</button>
        <div id="lengthResult" class="result"></div>
      </div>
    </div>

    <div id="Area" class="tab-content">
      <div class="converter">
        <label for="areaInput">Value:</label>
        <input type="number" id="areaInput" step="0.1">
        <label for="areaFrom">From:</label>
        <select id="areaFrom">
          <option value="squaremeters">Square Meters (m²)</option>
          <option value="squarefeet">Square Feet (ft²)</option>
        </select>
        <label for="areaTo">To:</label>
        <select id="areaTo">
          <option value="squaremeters">Square Meters (m²)</option>
          <option value="squarefeet">Square Feet (ft²)</option>
        </select>
        <button onclick="convert('Area')">Convert</button>
        <div id="areaResult" class="result"></div>
      </div>
    </div>

    <div id="Volume" class="tab-content">
      <div class="converter">
        <label for="volumeInput">Value:</label>
        <input type="number" id="volumeInput" step="0.1">
        <label for="volumeFrom">From:</label>
        <select id="volumeFrom">
          <option value="liters">Liters (L)</option>
          <option value="gallons">Gallons (gal)</option>
        </select>
        <label for="volumeTo">To:</label>
        <select id="volumeTo">
          <option value="liters">Liters (L)</option>
          <option value="gallons">Gallons (gal)</option>
        </select>
        <button onclick="convert('Volume')">Convert</button>
        <div id="volumeResult" class="result"></div>
      </div>
    </div>

    <div id="Weight" class="tab-content">
      <div class="converter">
        <label for="weightInput">Value:</label>
        <input type="number" id="weightInput" step="0.1">
        <label for="weightFrom">From:</label>
        <select id="weightFrom">
          <option value="kilograms">Kilograms (kg)</option>
          <option value="pounds">Pounds (lb)</option>
        </select>
        <label for="weightTo">To:</label>
        <select id="weightTo">
          <option value="kilograms">Kilograms (kg)</option>
          <option value="pounds">Pounds (lb)</option>
        </select>
        <button onclick="convert('Weight')">Convert</button>
        <div id="weightResult" class="result"></div>
      </div>
    </div>
  </div>

  <script>
    // Open default tab
    document.getElementsByClassName("tablinks")[0].click();

    function openTab(evt, tabName) {
      var i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tab-content");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
      }
      tablinks = document.getElementsByClassName("tablinks");
      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
      }
      document.getElementById(tabName).style.display = "block";
      evt.currentTarget.className += " active";
    }

    function convert(type) {
      let input = parseFloat(document.getElementById(`${type.toLowerCase()}Input`).value);
      let from = document.getElementById(`${type.toLowerCase()}From`).value;
      let to = document.getElementById(`${type.toLowerCase()}To`).value;
      let resultElement = document.getElementById(`${type.toLowerCase()}Result`);

      if (isNaN(input)) {
        resultElement.textContent = "Please enter a valid number.";
        return;
      }

      let result = input;
      switch (type) {
        case 'Temperature':
          if (from === 'celsius' && to === 'fahrenheit') result = (input * 9/5) + 32;
          else if (from === 'fahrenheit' && to === 'celsius') result = (input - 32) * 5/9;
          break;
        case 'Time':
          const timeFactors = { seconds: 1, minutes: 60, hours: 3600 };
          result = input * timeFactors[from] / timeFactors[to];
          break;
        case 'Length':
          if (from === 'meters' && to === 'feet') result = input * 3.28084;
          else if (from === 'feet' && to === 'meters') result = input / 3.28084;
          break;
        case 'Area':
          if (from === 'squaremeters' && to === 'squarefeet') result = input * 10.7639;
          else if (from === 'squarefeet' && to === 'squaremeters') result = input / 10.7639;
          break;
        case 'Volume':
          if (from === 'liters' && to === 'gallons') result = input * 0.264172;
          else if (from === 'gallons' && to === 'liters') result = input / 0.264172;
          break;
        case 'Weight':
          if (from === 'kilograms' && to === 'pounds') result = input * 2.20462;
          else if (from === 'pounds' && to === 'kilograms') result = input / 2.20462;
          break;
      }
      resultElement.textContent = `Result: ${result.toFixed(4)} ${to === from ? from : to}`;
    }
  </script>
</body>
</html>