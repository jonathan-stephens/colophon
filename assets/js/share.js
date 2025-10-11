// assets/js/share.js
// Handles bookmark saving, metadata fetching, and Android Share Target logic

window.addEventListener("DOMContentLoaded", () => {
  console.log("✅ share.js loaded");

  // Update test div to show JS is working
  const testDiv = document.getElementById("js-test");
  if (testDiv) {
    testDiv.textContent = "✅ JavaScript loaded and working!";
    testDiv.style.background = "#d4edda";
    testDiv.style.color = "#155724";
  }

  // --- Element references ---
  const websiteInput = document.getElementById("website");
  const tldInput = document.getElementById("tld");
  const authorInput = document.getElementById("author");
  const tagsInput = document.getElementById("tags");
  const titleInput = document.getElementById("page-title");
  const textInput = document.getElementById("text");
  const form = document.getElementById("bookmark-form");
  const fetchBtn = document.getElementById("fetch-metadata-btn");
  const quickSaveBtn = document.getElementById("quick-save-btn");
  const messageEl = document.getElementById("message");

  // Debug: Log all elements
  console.log("Element check:", {
    websiteInput: !!websiteInput,
    tldInput: !!tldInput,
    authorInput: !!authorInput,
    tagsInput: !!tagsInput,
    titleInput: !!titleInput,
    textInput: !!textInput,
    form: !!form,
    fetchBtn: !!fetchBtn,
    quickSaveBtn: !!quickSaveBtn,
    messageEl: !!messageEl
  });

  // --- CHECK URL PARAMETERS FIRST ---
  // This handles the case where Service Worker redirected POST to GET
  const urlParams = new URLSearchParams(window.location.search);
  const urlFromParam = urlParams.get('url');
  const titleFromParam = urlParams.get('title');
  const textFromParam = urlParams.get('text');

  console.log("🔍 URL Parameters detected:", {
    url: urlFromParam,
    title: titleFromParam,
    text: textFromParam,
    fullSearch: window.location.search
  });

  // If we have URL parameters, populate the form IMMEDIATELY
  if (urlFromParam && websiteInput && !websiteInput.value) {
    console.log("📝 Populating from URL parameters");
    websiteInput.value = urlFromParam;

    if (titleFromParam && titleInput && !titleInput.value) {
      titleInput.value = titleFromParam;
    }

    if (textFromParam && textInput && !textInput.value) {
      textInput.value = textFromParam;
    }

    // Extract domain immediately
    const domain = extractDomain(urlFromParam);
    if (domain && tldInput) {
      tldInput.value = domain;
      console.log("Domain extracted from URL param:", domain);
    }
  }

  // --- Utility: Show message ---
  function showMessage(text, type = "info") {
    if (!messageEl) {
      console.error("Message element not found");
      return;
    }
    messageEl.textContent = text;
    messageEl.className = "message " + type;
    messageEl.style.display = "block";
    setTimeout(() => (messageEl.style.display = "none"), 5000);
  }

  // --- Extract domain from URL ---
  function extractDomain(url) {
    try {
      const urlObj = new URL(url);
      return urlObj.hostname.replace(/^www\./, "");
    } catch (e) {
      console.error("Error extracting domain:", e);
      return "";
    }
  }

  // --- Auto-extract domain on blur ---
  if (websiteInput) {
    websiteInput.addEventListener("blur", () => {
      const url = websiteInput.value.trim();
      if (!url) return;
      const domain = extractDomain(url);
      if (domain && tldInput) {
        tldInput.value = domain;
        console.log("Domain extracted:", domain);
      }
    });
  }

  // --- Fetch metadata from backend ---
  async function fetchMetadata(url) {
    if (!url) return;
    showMessage("Fetching metadata...", "info");

    try {
      const response = await fetch("/api/bookmarks/fetch-metadata", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ url })
      });

      const result = await response.json();
      console.log("Metadata response:", result);

      if (result.status === "success" && result.data) {
        const data = result.data;
        if (data.author && authorInput && !authorInput.value) authorInput.value = data.author;
        if (data.tags && tagsInput && !tagsInput.value) tagsInput.value = data.tags;
        if (data.title && titleInput && !titleInput.value) titleInput.value = data.title;

        showMessage("Metadata fetched successfully!", "success");
      } else {
        showMessage("Could not fetch metadata", "info");
      }
    } catch (err) {
      console.error("Metadata fetch error:", err);
      showMessage("Error fetching metadata: " + err.message, "error");
    }
  }

  // --- Fetch metadata button ---
  if (fetchBtn) {
    fetchBtn.addEventListener("click", () => {
      console.log("Fetch metadata button clicked");
      const url = websiteInput ? websiteInput.value.trim() : "";
      if (url) {
        fetchMetadata(url);
      } else {
        showMessage("Please enter a URL first", "error");
      }
    });
  }

  // --- Auto-fetch when URL is present ---
  // Use a slightly longer delay to ensure all form population is complete
  setTimeout(() => {
    if (websiteInput && websiteInput.value) {
      console.log("🌐 URL detected in form:", websiteInput.value);
      console.log("Source: " + (urlFromParam ? "URL parameter" : "Server-side render"));

      // Only auto-fetch if we have a URL but missing other data
      const needsMetadata = !authorInput.value || !titleInput.value || !tagsInput.value;
      if (needsMetadata) {
        console.log("🔄 Auto-fetching metadata...");
        fetchMetadata(websiteInput.value);
      } else {
        console.log("ℹ️ Form already populated, skipping auto-fetch");
      }
    } else {
      console.log("ℹ️ No URL found in form.");
    }
  }, 500); // Increased delay to 500ms

  // --- Authentication ---
  async function getApiAuth() {
    const userEmail = document.body.dataset.userEmail || null;

    if (userEmail) {
      // Logged-in user path
      let password = localStorage.getItem("kirby_api_password");
      if (!password) {
        password = prompt("Enter your Kirby password for API access:\n(This is your panel login password)");
        if (password) {
          localStorage.setItem("kirby_api_password", password);
        } else {
          return null;
        }
      }
      console.log("Using stored auth for:", userEmail);
      return { email: userEmail, password };
    }

    // Guest path
    let email = localStorage.getItem("kirby_api_email");
    let password = localStorage.getItem("kirby_api_password");

    if (!email || !password) {
      const newEmail = prompt("Enter your Kirby email:\n(Your panel login email)");
      if (!newEmail) return null;

      const newPassword = prompt("Enter your Kirby password:\n(Your panel login password)");
      if (!newPassword) return null;

      localStorage.setItem("kirby_api_email", newEmail);
      localStorage.setItem("kirby_api_password", newPassword);
      console.log("Stored new credentials for:", newEmail);
      return { email: newEmail, password: newPassword };
    }

    console.log("Using stored credentials for:", email);
    return { email, password };
  }

  // --- Regular Save (full form) ---
  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      console.log("Form submitted");

      const auth = await getApiAuth();
      if (!auth || !auth.password) {
        showMessage("Authentication required", "error");
        return;
      }

      const formData = {
        website: websiteInput ? websiteInput.value : "",
        title: titleInput ? titleInput.value : "",
        tld: tldInput ? tldInput.value : "",
        author: authorInput ? authorInput.value : "",
        tags: tagsInput ? tagsInput.value : "",
        text: textInput ? textInput.value : ""
      };

      console.log("Submitting bookmark:", formData);

      try {
        const response = await fetch("/api/bookmarks/add", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: "Basic " + btoa(auth.email + ":" + auth.password)
          },
          body: JSON.stringify(formData)
        });

        const result = await response.json();
        console.log("Save response:", result);

        if (result.status === "success") {
          showMessage("Bookmark saved successfully!", "success");
          form.reset();
        } else {
          if (result.message && result.message.includes("authentication")) {
            localStorage.removeItem("kirby_api_password");
            showMessage("Authentication failed. Please try again.", "error");
          } else {
            showMessage(result.message || "Error saving bookmark", "error");
          }
        }
      } catch (err) {
        console.error("Network error:", err);
        showMessage("Network error: " + err.message, "error");
      }
    });
  }

  // --- Quick Save ---
  if (quickSaveBtn) {
    quickSaveBtn.addEventListener("click", async () => {
      console.log("Quick save button clicked");

      const url = websiteInput ? websiteInput.value.trim() : "";
      const title = titleInput ? titleInput.value.trim() : "";

      if (!url) {
        showMessage("URL is required", "error");
        return;
      }

      console.log("Quick saving:", { url, title });

      try {
        const response = await fetch("/api/bookmarks/quick-add", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          credentials: "same-origin", // Include session cookies
          body: JSON.stringify({ url, title, text: "" })
        });

        const result = await response.json();
        console.log("Quick save response:", result);

        if (result.status === "success") {
          showMessage("Quickly saved!", "success");
          form.reset();
        } else {
          showMessage(result.message || "Error saving bookmark", "error");
        }
      } catch (err) {
        console.error("Network error:", err);
        showMessage("Network error: " + err.message, "error");
      }
    });
  }

  console.log("🎉 All event listeners attached successfully");
});
