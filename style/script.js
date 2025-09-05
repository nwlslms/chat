window.onload = () => {
  loadMessages();
  updateActiveUsers();
  pingActive();
  fetchUserStatus();
};

const input = document.getElementById("messageInput");
const fileInput = document.getElementById("fileInput");
const activeList = document.getElementById("activeList");
const userStatus = document.getElementById("userStatus");
const myUsername = input?.dataset.username || "anon";

let lastMessageTimestamp = "";

function loadMessages() {
  fetch("load.php")
    .then(res => res.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, "text/html");
      const messages = doc.querySelectorAll(".message");
      const chatWindow = document.getElementById("chatWindow");

      if (!chatWindow || messages.length === 0) return;

      const latest = messages[messages.length - 1];
      const latestTime = latest.querySelector("strong")?.textContent?.split(" - ")[0] || "";

      if (latestTime !== lastMessageTimestamp) {
        lastMessageTimestamp = latestTime;
        chatWindow.innerHTML = html;
        chatWindow.scrollTop = chatWindow.scrollHeight;
      }
    });
}

function pingActive() {
  fetch("active.php");
}

function updateActiveUsers() {
  fetch("active_list.php")
    .then(res => res.text())
    .then(list => {
      if (activeList) {
        activeList.textContent = list || "Tidak ada";
      }
    });
}

function fetchUserStatus() {
  fetch("user_status.php")
    .then(res => res.text())
    .then(html => {
      const onlineUsers = userStatus.querySelector(".online-users");
      if (onlineUsers) {
        onlineUsers.innerHTML = html;
      }
    });
}

setInterval(loadMessages, 2000);
setInterval(updateActiveUsers, 5000);
setInterval(pingActive, 10000);
setInterval(fetchUserStatus, 5000);

if (fileInput) {
  fileInput.addEventListener("change", () => {
    const form = fileInput.closest("form");
    const replyInput = document.getElementById("replyToInput");
    const replyFileInput = document.getElementById("replyToInputFile");

    if (form && fileInput.files.length > 0) {
      if (replyInput && replyFileInput) {
        replyFileInput.value = replyInput.value;
      }
      form.submit();
    }
  });
}

function setReply(id, text) {
  const replyInput = document.getElementById("replyToInput");
  const replyFileInput = document.getElementById("replyToInputFile");
  const messageInput = document.getElementById("messageInput");
  const messagePreview = document.getElementById("replyPreview");

  if (replyInput) replyInput.value = id;
  if (replyFileInput) replyFileInput.value = id;

  if (messageInput) {
    const previewText = text.length > 50 ? text.slice(0, 50) + "..." : text;
    messageInput.placeholder = `Balas: "${previewText}"`;
  }

  if (messagePreview) {
    messagePreview.innerHTML = `<blockquote>${text}</blockquote><button onclick="cancelReply()">Batal</button>`;
    messagePreview.style.display = "block";
  }
}

function cancelReply() {
  const replyInput = document.getElementById("replyToInput");
  const replyFileInput = document.getElementById("replyToInputFile");
  const messageInput = document.getElementById("messageInput");
  const messagePreview = document.getElementById("replyPreview");

  if (replyInput) replyInput.value = "";
  if (replyFileInput) replyFileInput.value = "";
  if (messageInput) messageInput.placeholder = "...";
  if (messagePreview) {
    messagePreview.innerHTML = "";
    messagePreview.style.display = "none";
  }
}