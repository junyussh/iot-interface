const URL = "http://192.168.0.181:8080/api";
var Data;

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
      get("/user?token=" + localStorage.getItem("token")).then((res) => {
        if (res.id) {
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
const TableRows = (number, name, sensor_name, field, value, time, mac) => {
  return `
  <tr>
  <td>${number}</td>
  <td>${name}</td>
  <td>${sensor_name}</td>
  <td>${field}</td>
  <td>${value}</td>
  <td>${time}</td>
  <td><button data-name=${name} data-mac=${mac} onclick="seeinfo(this)" class="ts icon button"><i class="icon unhide"></i></button></td>
  </tr>
  `
}
const modal = (name, mac) => {
  return `
  <div class="header">
    裝置資訊
  </div>
  <div class="content">
  <form class="ts horizontal form">
  <div class="field">
      <label>裝置名稱</label>
      <input name="name" type="text" value='${name}'>
  </div>
  <div class="field">
      <label>MAC 位址</label>
      <input name="mac" type="text" value='${mac}'>
  </div>
</form>
  </div>
  <div class="actions">
    <button class="ts ok button">
        關閉
    </button>
  </div>`
}
const info = (username, point, frequency) => {
  return `
  <div class="ts three cards">
  <div class="ts card">
      <div class="content">
          <div class="ts left aligned statistic">
              <div class="value">${username}</div>
              <div class="label">使用者名稱</div>
          </div>
      </div>
      <div class="symbol">
          <i class="icon user"></i>
      </div>
  </div>
  <div class="ts card">
      <div class="content">
          <div class="ts left aligned statistic">
              <div class="value">${point}</div>
              <div class="label">監控點</div>
          </div>
      </div>
      <div class="symbol">
          <i class="icon microchip"></i>
      </div>
  </div>
  <div class="ts card">
      <div class="content">
          <div class="ts left aligned statistic">
              <div class="value">${frequency}</div>
              <div class="label">更新頻率(分鐘)</div>
          </div>
      </div>
      <div class="symbol">
          <i class="icon time"></i>
      </div>
  </div>
  </div>
  `
}
function getUserInfo(token) {
  get("/user?token=" + localStorage.getItem("token")).then((data) => {

  })
}
function seeinfo(e) {
  document.querySelector("#seeinfo").removeAttribute("data-modal-initialized")
  var name = e.getAttribute("data-name");
  var mac = e.getAttribute("data-mac");
  document.querySelector("#seeinfo").innerHTML = modal(name, mac)
  ts("#seeinfo").modal("show")
}
function loadData() {
  var content = document.querySelector("#content");
  content.querySelector(".dimmer").classList.add("active");
  get("/data?token=" + localStorage.getItem("token")).then((data) => {
    Data = data;
    var fragment = "";
    var number = 1;
    if (Object.getOwnPropertyNames(data).length != 0) {
      data.map((item) => {
        if (item.data == 0) {
          fragment += TableRows(number, item.name, "NaN", "NaN", "NaN", "NaN", item.mac)
          number++;
        } else {
          item.data.forEach((e) => {
            var time = new Date(e.time).toLocaleString()
            fragment += TableRows(number, item.name, e.name, e.field, e.value, time, item.mac)
            number++;
          })
        }
      })
    } else {

    }

    number--;
    get("/user?token=" + localStorage.getItem("token")).then((user) => {
      document.querySelector("#info").innerHTML = info(user.username, number, "10")
    })
    document.querySelector("#timestamp").innerHTML = new Date().toLocaleString();
    content.querySelector("tbody").innerHTML = fragment;
    content.querySelector(".dimmer").classList.remove("active");
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
  post(data, "/login").then((res) => {
    let message = document.querySelector("#message");
    if (res.error) {
      message.innerHTML = ErrorMessageBox("錯誤的使用者名稱或密碼！");
    } else {
      e.classList.add("loading", "disabled");
      message.innerHTML = PostiveMessageBox("登入成功", "正在為您跳轉……");
      SaveSession(res.token);
      window.location = "./dashboard.html";
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
  post(data, "/user").then((res) => {
    let message = document.querySelector("#message");
    if (res.error) {
      message.innerHTML = ErrorMessageBox("使用者名稱已存在");
    } else {
      message.innerHTML = PostiveMessageBox("恭喜！", "您已註冊成功，請進行<a href='./'>登入</a>");
    }
  });
}

function post(data, url) {
  return fetch(URL + url, {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      'Accept': 'application/json',
      "Content-Type": "application/json"
    }
  })
    .then(response => response.json())
}
function get(url) {
  return fetch(URL + url).then(response => response.json());
}
document.querySelector("form").addEventListener("submit", function (event) {
  event.preventDefault();
});
