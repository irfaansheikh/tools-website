const sharp = require("sharp");
const fs = require("fs");

function compressImage(req, res) {
  const inputPath = "./sample.jpg"; // Replace with uploaded file in real app
  const outputPath = "./compressed.jpg";

  sharp(inputPath)
    .jpeg({ quality: 60 })
    .toFile(outputPath)
    .then(() => res.send("Image compressed successfully!"))
    .catch(err => res.status(500).send("Compression failed."));
}

module.exports = compressImage;
