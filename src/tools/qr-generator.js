const QRCode = require("qrcode");

function generateQR(req, res) {
  const text = "https://yourdomain.com";
  QRCode.toDataURL(text, (err, url) => {
    if (err) return res.status(500).send("QR generation failed.");
    res.send(`<img src='${url}' alt='QR Code' />`);
  });
}

module.exports = generateQR;
