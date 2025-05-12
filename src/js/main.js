function compressImage() {
    fetch("http://localhost:3000/compress-image")
      .then(res => res.text())
      .then(data => {
        document.getElementById("output").innerText = data;
      });
  }
  
  function generateQR() {
    fetch("http://localhost:3000/generate-qr")
      .then(res => res.text())
      .then(data => {
        document.getElementById("output").innerText = data;
      });
  }
  