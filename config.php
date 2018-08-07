<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Config</title>
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/tocas-ui/2.3.3/tocas.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tocas-ui/2.3.3/tocas.js"></script>
</head>
<style>
  .hidden {
    display: none;
  }

  .center {
    text-align: center;
  }
</style>

<body>
  <div style="align-items: center;" class="ts narror container grid">
    <div class="four wide column">
      <a class="left" href="http://www.embedded-box.com.tw">
        <img class="ts image" src="logo.jpg" alt="" srcset="">
      </a>
    </div>
    <div class="eight wide column">
      <h2 class="ts center aligned header">
        CGATE Cloud Setting
      </h2>
    </div>
    <div class="four wide column"></div>
  </div>
  <div class="ts divider"></div>
  <div class="ts narror container grid">
    <div class="four wide column"></div>

    <div class="eight wide column">
      <form class="ts center horizontal form">
        <div class="field">
          <div class="ts toggle checkbox">
            <input id="active" onclick="Check(this)" type="checkbox">
            <label for="active">啟用</label>
          </div>
        </div>
        <div class="hidden config">
          <div class="field">
            <label>裝置名稱</label>
            <input name="name" value="<?php echo exec(" python getName.py "); ?>" type="text">
          </div>
          <div class="field">
            <label>MAC 位址</label>
            <input name="mac" type="text" disabled value=<?php echo exec("python getmac.py") ?>>
          </div>
          <div class="field">
            <label>使用者名稱</label>
            <input name="username" type="text">
          </div>
          <div class="field">
            <label>更新頻率(分)</label>
            <select>
              <option>10</option>
              <option>20</option>
              <option>30</option>
            </select>
          </div>
          <button onclick="save()" class="ts primary button">儲存</button>
        </div>
      </form>
    </div>
    <div class="four wide column"></div>
    <div class="ts modals dimmer">
      <dialog id="authorize" class="ts closable tiny modal">
        <div class="header">
          輸入使用者密碼
        </div>
        <div class="content">
          <form class="ts form">
            <div class="field">
              <label>請輸入您的密碼以授權更改</label>
              <input name="password" id="password" type="password">
            </div>
          </form>
        </div>
        <div class="actions">
          <button class="ts deny button">
            取消
          </button>
          <button class="ts positive button">
            確定
          </button>
        </div>
      </dialog>
    </div>
    <div class="ts modals dimmer">
      <dialog id="cancelActive" class="ts tiny modal">
        <div class="header">
          授權
        </div>
        <div class="content">
          <div class="ts secondary warning message">
            <div class="header"><i class="icon caution sign warning"></i> 注意</div>
            <p>此動作將會刪除這台裝置存放在雲端上的所有資料，請謹慎操作。</p>
          </div>
          <form class="ts form">
            <div class="field">
              <label>使用者名稱</label>
              <input name="username" type="text">
            </div>
            <div class="field">
              <label>密碼</label>
              <input name="password" type="password">
            </div>
          </form>
        </div>
        <div class="actions">
          <button class="ts deny button">
            取消
          </button>
          <button class="ts positive button">
            確定
          </button>
        </div>
      </dialog>
    </div>
    <div class="ts modals dimmer">
      <dialog id="error" class="ts closable tiny modal">

      </dialog>
    </div>
    <div class="ts modals dimmer">
      <dialog id="success" class="ts closable tiny modal">

      </dialog>
    </div>
  </div>
  <script>
    var active;
    const MAC = "<?php echo exec("python getmac.py") ?>";
    const Name = "<?php echo exec("python getName.py"); ?>";
    const URL = "http://192.168.0.181:8080/api";
    function ErrorModal(message) {
      var modal = `
      <div class="header">
          <i class="icon negative remove"></i> 失敗
        </div>
        <div class="content">
          <p>${message}</p>
        </div>
        <div class="actions">
          <button class="ts ok basic button">
            確定
          </button>
        </div>
      `
      document.querySelector("#error").innerHTML = modal;
      document.querySelector("#error").removeAttribute("data-modal-initialized");
    }
    function SuccessModal(message) {
      var modal = `
      <div class="header">
          <i class="icon positive checkmark"></i>成功
        </div>
        <div class="content">
          <p>${message}</p>
        </div>
        <div class="actions">
          <button class="ts ok basic button">
            確定
          </button>
        </div>
      `
      document.querySelector("#success").innerHTML = modal;
      document.querySelector("#success").removeAttribute("data-modal-initialized");
    }
    function Check(e) {
      if (active) {
        ts("#cancelActive").modal({
          onDeny: function () {
            e.checked = true;
            isChecked(e);
          },
          onApprove: function () {
            var payload = getValue("#cancelActive");
            post(payload, "/login").then((res) => {
              if (res.error) {
                e.checked = true;
                ErrorModal("使用者名稱或密碼錯誤。")
                ts("#error").modal("show");
                isChecked(e);
              } else {
                data = { "mac": MAC };
                fetch(URL + "/device?token=" + res.token, {
                  method: "DELETE",
                  body: JSON.stringify(data),
                  headers: {
                    'Accept': 'application/json',
                    "Content-Type": "application/json"
                  }
                })
                  .then(response => response.json())
                  .then((callback) => {
                    if (callback.error) {
                      e.checked = true;
                      ErrorModal(callback.message);
                      ts("#error").modal("show");
                      isChecked(e);
                    } else {
                      SuccessModal("裝置已取消啟用。")
                      ts("#success").modal("show");
                      active = false;
                      e.checked = false;
                    }
                  })
              }
            })
          }
        }).modal("show");
      }
      isChecked(e);
    }
    function isChecked(e) {
      if (e.checked) {
        document.querySelector("form > .config").classList.remove("hidden");
      } else {
        document.querySelector("form > .config").classList.add("hidden");
      }
    }
    function getValue(query) {
      var form = document.querySelector(query);
      var data = {};
      var item = form.querySelectorAll(
        "input[type='text'], input[type='password']"
      );
      item.forEach(e => {
        data[e.name] = e.value;
      });
      return data;
    }
    function activeDevice(payload, token) {
      console.log("active")
      post(payload, "/device?token=" + token).then((res) => {
        if (res.error) {
          ErrorModal("裝置已存在。");
          ts("#error").modal("show");
        } else {
          modifylocal(payload);
          SuccessModal("啟用成功！");
          ts("#success").modal("show");
          active = true;
        }
      })
    }
    function modifyDevice(payload, token) {
      console.log("modify")
      put(payload, "/device?token=" + token).then((res) => {
        if (res.error) {
          ErrorModal(res.message);
          ts("#error").modal("show");
        } else {
          modifylocal(payload);
          SuccessModal("修改成功！");
          ts("#success").modal("show");
        }
      })
    }
    function modifylocal(payload) {
      return fetch("./connect.php", {
        method: "POST",
        body: JSON.stringify(payload),
        headers: {
          'Accept': 'application/json',
          "Content-Type": "application/json"
        }
      })
    }
    function save() {
      var payload = getValue(".config");
      ts('#authorize').modal({
        onApprove: function () {
          var password = getValue("#authorize");
          payload = Object.assign(password, payload);
          document.querySelector("#password").value = ""
          post(payload, "/login").then((res) => {
            if (res.error) {
              ErrorModal("使用者名稱或密碼錯誤。")
              ts("#error").modal("show");
            } else {
              if (active) {
                modifyDevice(payload, res.token);
              } else {
                activeDevice(payload, res.token);
              }
            }
          })
        }
      }).modal("show");
    }
    function put(data, url) {
      return fetch(URL + url, {
        method: "PUT",
        body: JSON.stringify(data),
        headers: {
          'Accept': 'application/json',
          "Content-Type": "application/json"
        }
      })
        .then(response => response.json())
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
    document.querySelectorAll("form").forEach(function (e) {
      e.addEventListener("submit", function (event) {
        event.preventDefault();
      })
    });
    function load() {
      get("/device?mac=" + MAC).then((res) => {
        let checkbox = document.querySelector("#active");
        if (res.message == "exist") {
          active = true;
          checkbox.checked = "true";
        } else {
          active = false;
        }
        isChecked(checkbox);
      })
    }
    window.onload = load;
  </script>
</body>

</html>