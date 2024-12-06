document.querySelector("form").addEventListener("submit", function (event) {
  event.preventDefault(); // Prevent the form from submitting (frontend only)
  alert(
    "Inquiry has been submitted (this is just a demo, no backend submission)."
  );
});

// JavaScript for responsive navigation menu
const burger = document.querySelector(".burger");
const navLinks = document.querySelector("nav ul");

burger.addEventListener("click", () => {
  navLinks.classList.toggle("active");
});
