const sidebar = document.getElementById("sidebar");
const hamburger = document.getElementById("hamburger");

hamburger.addEventListener("click", () => {
  sidebar.classList.toggle("active");
});

document.addEventListener("click", (e) => {
  if (!sidebar.contains(e.target) && !hamburger.contains(e.target)) {
    sidebar.classList.remove("active");
  }
});

const menuLinks = document.querySelectorAll(".sidebar-menu li a[data-target]");
const sections = document.querySelectorAll(".main-content .content");

menuLinks.forEach((link) => {
  link.addEventListener("click", (e) => {
    e.preventDefault();

    menuLinks.forEach((l) => l.classList.remove("active"));
    link.classList.add("active");

    const target = link.getAttribute("data-target");

    sections.forEach((section) => {
      if (section.id === target) section.style.display = "block";
      else section.style.display = "none";
    });
  });
});

const btnAdd = document.getElementById("btnAdd");
const btnViewList = document.getElementById("btnViewList");

function showSection(targetId) {
  menuLinks.forEach((l) => l.classList.remove("active"));
  const matching = Array.from(menuLinks).find(
    (l) => l.getAttribute("data-target") === targetId
  );
  if (matching) matching.classList.add("active");

  sections.forEach((section) => {
    section.style.display = section.id === targetId ? "block" : "none";
  });
}

if (btnAdd)
  btnAdd.addEventListener("click", (e) => {
    e.preventDefault();
    showSection("add");
  });
if (btnViewList)
  btnViewList.addEventListener("click", (e) => {
    e.preventDefault();
    showSection("list");
  });

document.addEventListener("DOMContentLoaded", () => {
  try {
    const params = new URLSearchParams(window.location.search);
    if (
      params.has("added") ||
      params.has("error") ||
      params.get("view") === "add"
    ) {
      showSection("add");
    }

    if (params.has("added")) {
      const addSection = document.getElementById("add");
      if (addSection) {
        const msg = document.createElement("div");
        msg.className = "form-message success";
        msg.textContent = "Senior added successfully.";
        const form = addSection.querySelector("form") || addSection;
        addSection.insertBefore(msg, form);
        setTimeout(() => {
          msg.remove();
          history.replaceState(null, "", window.location.pathname);
        }, 4000);
      }
    }

    if (params.has("error")) {
      const addSection = document.getElementById("add");
      if (addSection) {
        const msg = document.createElement("div");
        msg.className = "form-message error";
        const err = params.get("error");
        msg.textContent = err ? decodeURIComponent(err) : "An error occurred.";
        const form = addSection.querySelector("form") || addSection;
        addSection.insertBefore(msg, form);
        setTimeout(() => {
          msg.remove();
          history.replaceState(null, "", window.location.pathname);
        }, 6000);
      }
    }
  } catch (e) {}
  const seniorForm = document.getElementById("seniorForm");
  const confirmModal = document.getElementById("confirmModal");
  const confirmSubmit = document.getElementById("confirmSubmit");
  const cancelSubmit = document.getElementById("cancelSubmit");

  if (seniorForm && confirmModal && confirmSubmit && cancelSubmit) {
    seniorForm.addEventListener("submit", (ev) => {
      ev.preventDefault();
      confirmModal.style.display = "flex";
    });

    confirmSubmit.addEventListener("click", (ev) => {
      ev.preventDefault();
      confirmModal.style.display = "none";
      seniorForm.submit();
    });

    cancelSubmit.addEventListener("click", (ev) => {
      ev.preventDefault();
      confirmModal.style.display = "none";
    });

    confirmModal.addEventListener("click", (e) => {
      if (e.target === confirmModal) confirmModal.style.display = "none";
    });
  }
});
