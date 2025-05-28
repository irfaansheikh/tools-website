/* infinite-scroll.js placeholder */
// let page = 1;
// const blogGrid = document.getElementById("blogGrid");

// function loadMoreBlogs() {
//   fetch(`/blog/page-${page}.html`)
//     .then(res => res.text())
//     .then(html => {
//       const div = document.createElement("div");
//       div.innerHTML = html;
//       [...div.children].forEach(el => blogGrid.appendChild(el));
//       page++;
//     });
// }

// window.addEventListener("scroll", () => {
//   if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
//     loadMoreBlogs();
//   }
// });
// loadMoreBlogs();
document.addEventListener("DOMContentLoaded", () => {
  const blogContainer = document.getElementById("blog-posts");
  let currentPage = 1;
  const maxPages = 1000;
  const postsPerLoad = 12;

  function loadPosts() {
    for (let i = 0; i < postsPerLoad && currentPage <= maxPages; i++) {
      const postId = currentPage.toString().padStart(3, "0");
      const post = document.createElement("div");
      post.className = "blog-post";
      post.innerHTML = `
        <h3><a href="blog/blog-${postId}.html">Blog Post ${postId}</a></h3>
        <p>This is a brief description for blog post #${postId}. Click to read more.</p>
      `;
      blogContainer.appendChild(post);
      currentPage++;
    }
  }

  window.addEventListener("scroll", () => {
    const nearBottom = window.innerHeight + window.scrollY >= document.body.offsetHeight - 300;
    if (nearBottom) loadPosts();
  });

  loadPosts(); // Initial load
});
