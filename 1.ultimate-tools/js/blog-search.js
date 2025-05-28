// console.log('Blog search script loaded');

// document.getElementById('blogSearchInput').addEventListener('keyup', function () {
//   const term = this.value.toLowerCase();
//   document.querySelectorAll('#blogGrid article').forEach(card => {
//     card.style.display = card.textContent.toLowerCase().includes(term) ? '' : 'none';
//   });
// });


// document.addEventListener("DOMContentLoaded", function () {
//   const searchInput = document.getElementById("blog-search");
//   const blogPosts = document.querySelectorAll(".blog-post");

//   searchInput.addEventListener("keyup", function () {
//     const query = this.value.toLowerCase();
//     blogPosts.forEach(post => {
//       const text = post.textContent.toLowerCase();
//       post.style.display = text.includes(query) ? "block" : "none";
//     });
//   });
// });
document.getElementById("searchBar").addEventListener("input", function () {
  const query = this.value.toLowerCase();
  document.querySelectorAll(".blog-post").forEach(post => {
    const title = post.querySelector("h3").innerText.toLowerCase();
    const content = post.querySelector("p").innerText.toLowerCase();
    post.style.display = (title.includes(query) || content.includes(query)) ? "" : "none";
  });
});
