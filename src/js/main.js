function compressImage() {
  fetch('http://localhost:3000/compress-image')
    .then(response => response.text())
    .then(data => {
      document.getElementById('output').innerText = data;
    });
}

function generateQR() {
  fetch('http://localhost:3000/generate-qr')
    .then(response => response.text())
    .then(data => {
      document.getElementById('output').innerHTML = data;
    });
}
