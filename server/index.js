const express = require("express");
const cors = require("cors");
const compressImage = require("../src/tools/image-compressor");
const generateQR = require("../src/tools/qr-generator");

const app = express();
app.use(cors());

app.get("/compress-image", compressImage);
app.get("/generate-qr", generateQR);

app.listen(3000, () => console.log("Server running on http://localhost:3000"));
