document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector(".sidebar");
  const toggleBtn = document.createElement("button");
  toggleBtn.textContent = "☰";
  toggleBtn.classList.add("toggle-btn");
  document.body.insertBefore(toggleBtn, document.body.firstChild);

  toggleBtn.addEventListener("click", () => {
    sidebar.style.display =
      sidebar.style.display === "block" ? "none" : "block";
  });
});
