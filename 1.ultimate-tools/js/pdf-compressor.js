document.addEventListener("DOMContentLoaded", function () {
  const fileInput = document.getElementById("pdfInput");
  const compressBtn = document.getElementById("compressBtn");
  const output = document.getElementById("output");

  compressBtn.addEventListener("click", function () {
    const file = fileInput.files[0];
    if (!file || file.type !== "application/pdf") {
      output.textContent = "Please select a valid PDF file.";
      return;
    }

    output.textContent = "Compressing (simulated)...";

    setTimeout(() => {
      output.textContent = `Compressed PDF: ${file.name}`;
    }, 2000); // simulate compression
  });
});
