document.addEventListener("DOMContentLoaded", () => {
  const bar = document.getElementById("snb-notice-bar");
  const dismiss = document.getElementById("snb-dismiss");

  if (!bar) return;

  const version = bar.dataset.version || "1";
  const position = bar.dataset.position || "top";
  const dismissed = localStorage.getItem("snb_dismissed_version");

  if (dismissed === version) {
    bar.remove();
    if (position === "top") {
      document.body.style.paddingTop = "0";
      const header = document.querySelector("header.site-header, .main-header");
      if (header) header.style.top = "0";
    }
    return;
  }

  // Push header down if the bar is on top
  setTimeout(() => {
    const barHeight = bar.offsetHeight;
    if (position === "top" && barHeight > 0) {
      document.body.style.paddingTop = `${barHeight}px`;
      const header = document.querySelector("header.site-header, .main-header");
      if (header && getComputedStyle(header).position === "fixed") {
        header.style.top = `${barHeight}px`;
        header.style.zIndex = "10000";
      }
    }
  }, 100);

  if (dismiss) {
    dismiss.addEventListener("click", () => {
      localStorage.setItem("snb_dismissed_version", version);
      bar.remove();
      if (position === "top") {
        document.body.style.paddingTop = "0";
        const header = document.querySelector(
          "header.site-header, .main-header"
        );
        if (header) header.style.top = "0";
      }
    });
  }
});
