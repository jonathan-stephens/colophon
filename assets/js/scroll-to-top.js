// Get the button element using its class name
const scrollToTopButton = document.querySelector(".scroll-to-top");

// Define the scroll threshold (in pixels)
const scrollThreshold = 300;

// Add a scroll event listener to the window
window.addEventListener("scroll", () => {
  // Check if the user has scrolled past the threshold
  if (window.scrollY > scrollThreshold) {
    // If they have, add the 'show' class to make the button visible
    scrollToTopButton.classList.add("show");
  } else {
    // Otherwise, remove the 'show' class to hide the button
    scrollToTopButton.classList.remove("show");
  }
});

// Add a click event listener to the button
scrollToTopButton.addEventListener("click", () => {
// Use the modern, smooth `scrollTo` method for a good user experience
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
});
