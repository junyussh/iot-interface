const URL = "http://35.236.160.136/api";

const SaveSession = (token) => {
  localStorage["token"] = token;
  localStorage["time"] = new Date().toISOString();
}
const CleanSession = () => {
  localStorage.clear();
}
function isLogin() {
  return new Promise((resolve) => {
    if (localStorage.getItem("token")) {
      get(URL + "/user?token=" + localStorage.getItem("token")).then((res) => {
        if (res._id) {
          console.log("true");
          resolve(true);
        } else {
          CleanSession();
          resolve(false);
        }
      })
    } else {
      resolve(false);
    }
  })
}
function getValue() {
  var form = document.querySelector("form");
  var data = {};
  var item = form.querySelectorAll(
    "input[type='text'], input[type='password']"
  );
  item.forEach(e => {
    data[e.name] = e.value;
  });
  return data;
}

function login(e) {
  let data = getValue();
  post(data, "http://35.236.160.136/api/login").then((res) => {
    let message = document.querySelector("#message");
    if (res.error) {
      message.innerHTML = ErrorMessageBox("錯誤的使用者名稱或密碼！");
    } else {
      e.classList.add("loading", "disabled");
      message.innerHTML = PostiveMessageBox("登入成功", "正在為您跳轉……");
      console.log(res.token);
      SaveSession(res.token);
      isLogin();
    }
  });
}

const ErrorMessageBox = (message) => {
  return `
  <div class="ts secondary inverted negative message">
  <div class="header">錯誤</div>
  <p>${message}</p>
  </div>
  `
}

const PostiveMessageBox = (title, message) => {
  return `
  <div class="ts secondary inverted positive message">
  <div class="header">${title}</div>
  <p>${message}</p>
  </div>
  `
}

function register() {
  let data = getValue();
  post(data, "http://35.236.160.136/api/user").then((res) => {
    let message = document.querySelector("#message");
    if (res.error) {
      message.innerHTML = ErrorMessageBox("使用者名稱已存在");
    } else {
      message.innerHTML = PostiveMessageBox("恭喜！", "您已註冊成功，請進行<a href='./'>登入</a>");
    }
  });
}

function post(data, url) {
  return fetch(url, {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      'Accept': 'application/json',
      "Content-Type": "application/json"
    },
    mode: 'cors'
  })
    .then(response => response.json())
}
function get(url) {
  return fetch(url).then(response => response.json());
}
document.querySelector("form").addEventListener("submit", function (event) {
  event.preventDefault();
});