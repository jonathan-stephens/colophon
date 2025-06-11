// Add this to your site's JavaScript file or in a script tag
document.addEventListener('DOMContentLoaded', function() {
  // Find all elements with the 'copy-to-clipboard' class
  const copyElements = document.querySelectorAll('.copy-to-clipboard');

  copyElements.forEach(element => {
    // Create a copy button
    const copyButton = document.createElement('button');
    copyButton.className = 'copy-btn';
    copyButton.innerHTML = 'Copy';

    // Add the button after the element
    element.parentNode.insertBefore(copyButton, element.nextSibling);

    // Add click event to the button
    copyButton.addEventListener('click', function() {
      let textToCopy;

      // Check for data attributes to determine what to copy
      if (element.dataset.copyUrl) {
        // Copy URL (useful for sharing links)
        textToCopy = window.location.origin + element.dataset.copyUrl;
      } else if (element.dataset.copyAttribute) {
        // Copy content from a specific attribute
        textToCopy = element.getAttribute(element.dataset.copyAttribute);
      } else {
        // Default: copy the element's text content
        textToCopy = element.innerText || element.textContent;
      }

      // Use the Clipboard API to copy the text
      navigator.clipboard.writeText(textToCopy)
        .then(() => {
          // Provide visual feedback
          copyButton.innerHTML = 'Copied!';
          setTimeout(() => {
            copyButton.innerHTML = 'Copy';
          }, 2000);
        })
        .catch(err => {
          console.error('Failed to copy text: ', err);
          copyButton.innerHTML = 'Failed!';
          setTimeout(() => {
            copyButton.innerHTML = 'Copy';
          }, 2000);
        });
    });
  });
});
