window.addEventListener("DOMContentLoaded", () => {
  console.log("✅ share.js loaded");

  // Get all form elements
  const websiteInput = document.getElementById("website");
  const tldInput = document.getElementById("tld");
  const authorInput = document.getElementById("author");
  const tagsInput = document.getElementById("tags");
  const titleInput = document.getElementById("page-title");
  const textInput = document.getElementById("text");
  const form = document.getElementById("bookmark-form");
  const fetchMetadataBtn = document.getElementById("fetch-metadata-btn");
  const quickSaveBtn = document.getElementById("quick-save-btn");
  const messageDiv = document.getElementById("message");

  console.log("Element check:", {
    websiteInput: !!websiteInput,
    tldInput: !!tldInput,
    authorInput: !!authorInput,
    tagsInput: !!tagsInput,
    titleInput: !!titleInput,
    textInput: !!textInput,
    form: !!form,
    fetchBtn: !!fetchMetadataBtn,
    quickSaveBtn: !!quickSaveBtn,
    messageDiv: !!messageDiv
  });

  // Test indicator
  const jsTest = document.getElementById("js-test");
  if (jsTest) {
    jsTest.textContent = "✅ JavaScript loaded and working!";
    jsTest.style.background = "#d4edda";
    jsTest.style.color = "#155724";
  }

  // Show message to user
  function showMessage(text, type = "info") {
    if (!messageDiv) {
      console.error("Message element not found");
      return;
    }
    messageDiv.textContent = text;
    messageDiv.className = "message " + type;
    messageDiv.style.display = "block";

    setTimeout(() => {
      messageDiv.style.display = "none";
    }, 5000);
  }

  // Extract domain from URL
  function extractDomain(url) {
    try {
      const hostname = new URL(url).hostname;
      return hostname.replace(/^www\./, "");
    } catch (err) {
      console.error("Error extracting domain:", err);
      return "";
    }
  }

  // Fetch metadata from URL
  async function fetchMetadata(url) {
    if (!url) return;

    showMessage("Fetching metadata...", "info");
    console.log("📡 Fetching metadata for:", url);

    try {
      const response = await fetch("/api/bookmarks/fetch-metadata", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ url: url })
      });

      const result = await response.json();
      console.log("Metadata response:", result);

      if (result.status === "success" && result.data) {
        const data = result.data;
        let updated = [];

        // Update author (only if empty)
        if (data.author && authorInput && !authorInput.value) {
          authorInput.value = data.author;
          updated.push("author");
          console.log("✅ Author set:", data.author);
        }

        // Update tags (only if empty)
        if (data.tags && tagsInput && !tagsInput.value) {
          tagsInput.value = data.tags;
          updated.push("tags");
          console.log("✅ Tags set:", data.tags);
        }

        // Update title (only if empty)
        if (data.title && titleInput && !titleInput.value) {
          titleInput.value = data.title;
          updated.push("title");
          console.log("✅ Title set:", data.title);
        }

        if (updated.length > 0) {
          showMessage(
            `Metadata fetched! Updated: ${updated.join(", ")}`,
            "success"
          );
        } else {
          showMessage("Metadata fetched (no empty fields to fill)", "info");
        }
      } else {
        showMessage("Could not fetch metadata", "info");
        console.log("Metadata fetch returned no data");
      }
    } catch (err) {
      console.error("Metadata fetch error:", err);
      showMessage("Error fetching metadata: " + err.message, "error");
    }
  }

  // Auto-extract domain when URL changes
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

  // Manual fetch metadata button
  if (fetchMetadataBtn) {
    fetchMetadataBtn.addEventListener("click", () => {
      console.log("Fetch metadata button clicked");
      const url = websiteInput ? websiteInput.value.trim() : "";
      if (url) {
        fetchMetadata(url);
      } else {
        showMessage("Please enter a URL first", "error");
      }
    });
  }

  // Auto-fetch metadata on page load if URL is prefilled
  setTimeout(() => {
    if (websiteInput && websiteInput.value) {
      console.log("🌐 Prefilled URL detected:", websiteInput.value);

      // Trigger blur to extract domain
      websiteInput.dispatchEvent(new Event("blur"));

      // Auto-fetch metadata
      console.log("🔄 Auto-fetching metadata for shared URL...");
      fetchMetadata(websiteInput.value);
    } else {
      console.log("ℹ️ No prefilled URL found.");
    }
  }, 300);

  // Get authentication credentials
  async function getAuthCredentials() {
    // Check if user email is in page (for logged-in users)
    const userEmail = document.body.dataset.userEmail || null;

    if (userEmail) {
      let password = localStorage.getItem("kirby_api_password");

      if (!password) {
        password = prompt(
          "Enter your Kirby password for API access:\n(This is your panel login password)"
        );
        if (!password) return null;
        localStorage.setItem("kirby_api_password", password);
      }

      console.log("Using stored auth for:", userEmail);
      return { email: userEmail, password: password };
    }

    // Try stored credentials
    let email = localStorage.getItem("kirby_api_email");
    let password = localStorage.getItem("kirby_api_password");

    if (!email || !password) {
      email = prompt("Enter your Kirby email:\n(Your panel login email)");
      if (!email) return null;

      password = prompt("Enter your Kirby password:\n(Your panel login password)");
      if (!password) return null;

      localStorage.setItem("kirby_api_email", email);
      localStorage.setItem("kirby_api_password", password);
      console.log("Stored new credentials for:", email);

      return { email: email, password: password };
    }

    console.log("Using stored credentials for:", email);
    return { email: email, password: password };
  }

  // Form submission handler
  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      console.log("Form submitted");

      const auth = await getAuthCredentials();
      if (!auth || !auth.password) {
        showMessage("Authentication required", "error");
        return;
      }

      const bookmarkData = {
        website: websiteInput ? websiteInput.value : "",
        title: titleInput ? titleInput.value : "",
        tld: tldInput ? tldInput.value : "",
        author: authorInput ? authorInput.value : "",
        tags: tagsInput ? tagsInput.value : "",
        text: textInput ? textInput.value : ""
      };

      console.log("Submitting bookmark:", bookmarkData);

      try {
        const response = await fetch("/api/bookmarks/add", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": "Basic " + btoa(auth.email + ":" + auth.password)
          },
          body: JSON.stringify(bookmarkData)
        });

        const result = await response.json();
        console.log("Save response:", result);

        if (result.status === "success") {
          showMessage("Bookmark saved successfully!", "success");
          form.reset();
        } else if (result.message && result.message.includes("authentication")) {
          localStorage.removeItem("kirby_api_password");
          showMessage("Authentication failed. Please try again.", "error");
        } else {
          showMessage(result.message || "Error saving bookmark", "error");
        }
      } catch (err) {
        console.error("Network error:", err);
        showMessage("Network error: " + err.message, "error");
      }
    });
  }

  // Quick save button handler
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
          credentials: "same-origin",
          body: JSON.stringify({
            url: url,
            title: title,
            text: ""
          })
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
