// assets/js/share.js
// Handles bookmark saving, metadata fetching, and Android Share Target logic

window.addEventListener("DOMContentLoaded", () => {
  console.log("✅ share.js loaded");

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

  // --- Utility: Show message ---
  function showMessage(text, type = "info") {
    if (!messageEl) return;
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
    } catch {
      return "";
    }
  }

  // --- Auto-extract domain on blur ---
  websiteInput?.addEventListener("blur", () => {
    const url = websiteInput.value.trim();
    if (!url) return;
    const domain = extractDomain(url);
    if (domain) {
      tldInput.value = domain;
    }
  });

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

      if (result.status === "success" && result.data) {
        const data = result.data;
        if (data.author && !authorInput.value) authorInput.value = data.author;
        if (data.tags && !tagsInput.value) tagsInput.value = data.tags;
        if (data.title && !titleInput.value) titleInput.value = data.title;

        showMessage("Metadata fetched successfully!", "success");
      } else {
        showMessage("Could not fetch metadata", "info");
      }
    } catch (err) {
      showMessage("Error fetching metadata: " + err.message, "error");
    }
  }

  // --- Fetch metadata button ---
  fetchBtn?.addEventListener("click", () => {
    const url = websiteInput.value.trim();
    if (url) fetchMetadata(url);
  });

  // --- Auto-fetch when prefilled (e.g. from Android Share) ---
  setTimeout(() => {
    if (websiteInput?.value) {
      console.log("🌐 Prefilled URL detected:", websiteInput.value);
      websiteInput.dispatchEvent(new Event("blur"));
      fetchMetadata(websiteInput.value);
    } else {
      console.log("ℹ️ No prefilled URL found.");
    }
  }, 300); // allow Android to finish autofilling

  // --- Authentication ---
  async function getApiAuth() {
    const userEmail = document.body.dataset.userEmail || null;

    if (userEmail) {
      // Logged-in user path
      let password = localStorage.getItem("kirby_api_password");
      if (!password) {
        password = prompt("Enter your Kirby password for API access:");
        if (password) localStorage.setItem("kirby_api_password", password);
        else return null;
      }
      return { email: userEmail, password };
    }

    // Guest path
    let email = localStorage.getItem("kirby_api_email");
    let password = localStorage.getItem("kirby_api_password");

    if (!email || !password) {
      const newEmail = prompt("Enter your Kirby email:");
      const newPassword = prompt("Enter your Kirby password:");
      if (newEmail && newPassword) {
        localStorage.setItem("kirby_api_email", newEmail);
        localStorage.setItem("kirby_api_password", newPassword);
        return { email: newEmail, password: newPassword };
      }
      return null;
    }

    return { email, password };
  }

  // --- Regular Save (full form) ---
  form?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const auth = await getApiAuth();
    if (!auth?.password) {
      showMessage("Authentication required", "error");
      return;
    }

    const formData = {
      website: websiteInput.value,
      title: titleInput.value,
      tld: tldInput.value,
      author: authorInput.value,
      tags: tagsInput.value,
      text: textInput.value
    };

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

      if (result.status === "success") {
        showMessage("Bookmark saved successfully!", "success");
        form.reset();
      } else {
        if (result.message?.includes("authentication")) {
          localStorage.removeItem("kirby_api_password");
          showMessage("Authentication failed. Please try again.", "error");
        } else {
          showMessage(result.message || "Error saving bookmark", "error");
        }
      }
    } catch (err) {
      showMessage("Network error: " + err.message, "error");
    }
  });

  // --- Quick Save ---
  quickSaveBtn?.addEventListener("click", async () => {
    const auth = await getApiAuth();
    if (!auth?.password) {
      showMessage("Authentication required", "error");
      return;
    }

    const url = websiteInput.value.trim();
    const title = titleInput.value.trim();

    if (!url) {
      showMessage("URL is required", "error");
      return;
    }

    try {
      const response = await fetch("/api/bookmarks/quick-add", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: "Basic " + btoa(auth.email + ":" + auth.password)
        },
        body: JSON.stringify({ url, title, text: "" })
      });

      const result = await response.json();

      if (result.status === "success") {
        showMessage("Quickly saved!", "success");
        form.reset();
      } else {
        if (result.message?.includes("authentication")) {
          localStorage.removeItem("kirby_api_password");
          showMessage("Authentication failed. Please try again.", "error");
        } else {
          showMessage(result.message || "Error saving bookmark", "error");
        }
      }
    } catch (err) {
      showMessage("Network error: " + err.message, "error");
    }
  });
});
