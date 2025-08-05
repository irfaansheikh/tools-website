<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ultimate Tools - Text to QR Code Generator</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #F5FAFA; color: #2D3748; }
    header { background-color: #007A7A; color: #F5FAFA; padding: 10px; text-align: center; }
    nav a { color: #F5FAFA; text-decoration: none; margin: 0 10px; }
    .container { max-width: 800px; margin: 20px auto; }
    .tool-section { background: #FFFFFF; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    input[type="text"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #718096; border-radius: 4px; }
    button { padding: 10px 20px; margin: 5px; background-color: #007A7A; color: #F5FAFA; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background-color: #FF6F61; }
    #qrImage { margin-top: 20px; max-width: 100%; display: none; }
    #downloadBtn { display: none; margin-left: 10px; }
    #resetBtn { display: inline-block; }
    #errorMsg { color: #FF6F61; margin-top: 10px; display: none; }
    #debugInfo { color: #718096; margin-top: 10px; }
    footer { background-color: #007A7A; color: #F5FAFA; text-align: center; padding: 10px; margin-top: 20px; }
  </style>
</head>
<body>
  <header>
    <h1>Ultimate Tools</h1>
    <nav>
      <a href="index.html">Home</a>
      <a href="tools.html">All Tools</a>
    </nav>
  </header>
  <div class="container">
    <div class="tool-section">
      <h2>Text to QR Code Generator</h2>
      <p>Enter any text, URL, or message below to generate a QR code you can scan or download.</p>
      <form action="generate-text-qr.php" method="post">
        <input type="text" id="qrInput" name="qrtext" placeholder="Enter text, URL, or message">
        <button type="submit">Generate QR</button>
        <button type="button" id="resetBtn" onclick="resetForm()">Reset</button>
      </form>
      <?php
      if (isset($_GET['img'])) {
          $imagePath = htmlspecialchars($_GET['img']);
          echo '<img id="qrImage" src="' . $imagePath . '" alt="Generated QR Code" onload="this.style.display=\'block\';" onerror="document.getElementById(\'errorMsg\').style.display=\'block\';">';
          echo '<a id="downloadBtn" href="' . $imagePath . '" download>Download QR Code</a>'; // Show if img is set
          echo '<button type="button" id="resetBtn" onclick="resetForm()">Reset</button>'; // Ensure reset is always available
          echo '<div id="errorMsg">Error loading QR code. Please check the path or try again.</div>';
          echo '<div id="debugInfo">Image Path: ' . $imagePath . '</div>'; // Debug info
      }
      ?>
      <div id="instructions">
        <h3>How to Use This Tool</h3>
        <ul>
          <li>Enter any text, URL, or message in the input box.</li>
          <li>Click “Generate QR” to create and view the QR code.</li>
          <li>Click “Download QR Code” to save the image.</li>
        </ul>
      </div>
    </div>
  </div>
  <footer>
    <p>© 2025 Ultimate Tools. All rights reserved.</p>
  </footer>
  <script>
    function resetForm() {
      document.getElementById('qrInput').value = '';
      document.getElementById('qrImage').style.display = 'none';
      document.getElementById('downloadBtn').style.display = 'none';
      document.getElementById('errorMsg').style.display = 'none';
      document.getElementById('debugInfo').style.display = 'none';
    }
  </script>
</body>
</html>