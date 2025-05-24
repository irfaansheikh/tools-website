// const express = require("express");
// const cors = require("cors");
// const compressImage = require("../src/tools/image-compressor");
// const generateQR = require("../src/tools/qr-generator");

// const app = express();
// app.use(cors());

// app.get("/compress-image", compressImage);
// app.get("/generate-qr", generateQR);

// app.use(express.static('public'));

// app.listen(3000, () => console.log("Server running on http://localhost:3000"));

const express = require('express');
const multer = require('multer');
const sharp = require('sharp');
const path = require('path');
const fs = require('fs');


const app = express();
const PORT = 3000;

// Set up storage for uploaded images
const storage = multer.diskStorage({
  destination: 'uploads/',
  filename: (req, file, cb) => {
    cb(null, 'original_' + Date.now() + path.extname(file.originalname));
  },
});

const upload = multer({ storage });

// Serve static files from the public directory
app.use(express.static('public'));
app.use('/compressed', express.static(path.join(__dirname, '../compressed')));


// Route to handle image upload and compression
app.post('/compress-image', upload.single('image'), async (req, res) => {
  try {
    const inputPath = req.file.path;
    const outputFilename = 'compressed_' + Date.now() + '.jpg';
    const outputPath = path.join('compressed', outputFilename);

    // Compress and resize the image
    await sharp(inputPath)
      .resize({ width: 800 }) // Resize to width of 800px
      .jpeg({ quality: 80 }) // Compress to 80% quality
      .toFile(outputPath);

    // Delete the original uploaded file
    fs.unlinkSync(inputPath);

    // Redirect to the download route
    res.redirect(`/download/${outputFilename}`);
  } catch (error) {
    console.error('Error during image compression:', error);
    res.status(500).send('Compression failed.');
  }
});

app.get('/download/:filename', (req, res) => {
  const filename = req.params.filename;
  const filePath = path.join(__dirname, '../compressed', filename);

  // Check if the file exists
  fs.access(filePath, fs.constants.F_OK, (err) => {
    if (err) {
      console.error('File not found:', err);
      return res.status(404).send('File not found');
    }

    // Set headers to prompt download
    res.setHeader('Content-Disposition', `attachment; filename="${filename}"`);
    res.setHeader('Content-Type', 'image/jpeg'); // Adjust MIME type if necessary

    // Create a read stream and pipe it to the response
    const fileStream = fs.createReadStream(filePath);
    fileStream.pipe(res);

    fileStream.on('end', () => {
      console.log('Download completed');
    });

    fileStream.on('error', (err) => {
      console.error('Error during download:', err);
      res.status(500).send('Error during download');
    });
  });
});


app.get('/download/:filename', (req, res) => {
  const filename = req.params.filename;
  const filePath = path.join(__dirname, '../compressed', filename);
  res.download(filePath, filename, (err) => {
    if (err) {
      console.error('Error downloading file:', err);
      res.status(500).send('Error downloading file.');
    }
  });
});


app.listen(PORT, () => {
  console.log(`Server is running on http://localhost:${PORT}`);
});

const QRCode = require('qrcode');

// Route to generate QR code
app.get('/generate-qr', async (req, res) => {
  try {
    const url = req.query.url || 'https://example.com'; // Default URL if none provided
    const qrImage = await QRCode.toDataURL(url);

    // Send the QR code image as an HTML response
    res.send(`
      <h2>QR Code for: ${url}</h2>
      <img src="${qrImage}" alt="QR Code" />
    `);
  } catch (error) {
    console.error('Error generating QR code:', error);
    res.status(500).send('QR Code generation failed.');
  }
});
