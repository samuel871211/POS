class trade {
  #temp = [];
  #name = [];
  #amountArray = [];
  #price = [];
  #s_price = [];
  #discount = [];
  #amount = false;
  #member = false;
  #retrieve_target = false;
  #item_target = false;
  #sockets = io.connect();
  #socket = io();
  #data = "";
  newTrade(arg) {
    this.#name = [];
    this.#amountArray = [];
    this.#price = [];
    this.#s_price = [];
    this.#discount = [];
    this.#amount = false;
    this.#member = false;
    this.#item_target = false; //項目加、減、刪除
    this.#data = "";
    $("#xy div")[0].style["visibility"] = "hidden";
    $("#member div")[0].innerHTML = "";
    $("#item_table")[0].innerHTML = "<tr>\
                      <th width='10%'>項目</th>\
                      <th width='30%'>商品名稱</th>\
                      <th width='10%'>數量</th>\
                      <th width='10%'>原價</th>\
                      <th width='10%'>特價</th>\
                      <th width='30%'>優惠促銷</th>\
                    </tr>"
    $("#output_area")[0].innerHTML = "交易已" + arg;
  }
  addAmount() {
    if (isNaN(parseInt($("#output_area")[0].innerHTML)) == false) {
      this.#amount = parseInt($("#output_area")[0].innerHTML);
      $("#output_area")[0].innerHTML += "數量";
      this.#data = "";
    } else { $("#output_area")[0].innerHTML = "操作錯誤" };
  }
  add(arg) {
    if ($("#transparent")[0].style["display"] == "") { return };
    if ((arg == "←" || arg == "Backspace")) {
      if (this.#data != "") { this.#data = this.#data.slice(0, this.#data.length - 1) };
      $("#output_area")[0].innerHTML = this.#data;
    } else if (arg == "↲" || arg == "Enter") {
      if (this.#data.length == 5 || this.#data.length == 13) {
        this.#sockets.emit('FindItemByBarcode', this.#data);
        $("#output_area")[0].innerHTML = this.#data;
        this.#data = "";
        this.#socket.once('FindItemByBarcode_Result' + usernameHash, function(result) {
          if (result == "Invalid barcode") {
            $("#auto")[0].play();
            $("#output_area")[0].innerHTML = "查無商品資料";
          } else if (result == "DB Reconnecting") {
            $("#auto")[0].play();
            $("#output_area")[0].innerHTML = "資料庫重新連線中";
          } else { trades.#checkDuplicate(JSON.parse(result)) };
        });
      } else if (this.#data.length == 10 || this.#data.length == 8) {
        this.#sockets.emit('FindMemberByPhoneNumber', this.#data);
        $("#output_area")[0].innerHTML = this.#data;
        this.#data = "";
        this.#socket.once('FindMemberByPhoneNumber_Result' + usernameHash, function(result) {
          if (result == "Invalid phoneNumber") {
            $("#auto")[0].play();
            $("#output_area")[0].innerHTML = "查無會員資料";
          } else if (result == "DB Reconnecting") {
            $("#auto")[0].play();
            $("#output_area")[0].innerHTML = "資料庫重新連線中";
          } else { trades.#isMember(JSON.parse(result)) };
        });
      } else if (this.#data.length != 0) {
        $("#auto")[0].play();
        $("#output_area")[0].innerHTML = "查無相關資料";
        this.#data = "";
        this.#amount = false;
      };
    } else if (arg == "d") {
      this.#data = "";
      this.#amount = false;
      $("#output_area")[0].innerHTML = "清除輸入";
    } else {
      if (check.includes(arg) && this.#data.length < 13) {
        this.#data += arg;
        $("#output_area")[0].innerHTML = this.#data;
      };
    };
  }
  #checkDuplicate(result) {
    let index = this.#name.indexOf(result.name);
    if (index != -1) {
      if (this.#amount) {
        this.#amountArray[index] += this.#amount;
      } else { this.#amountArray[index] += 1 };
      this.#updateRow(index, result.discount);
    } else { this.#newRow(result) };
  }
  #updateRow(index, discount) {
    let tr = $("#item_table")[0].rows[index + 1];
    tr.cells[2].innerHTML = this.#amountArray[index];
    tr.cells[3].innerHTML = this.#price[index] * this.#amountArray[index];
    this.#calculateDiscount(index, discount);
    $("#x")[0].innerHTML = this.#amountArray.reduce((a, b) => a + b);
    $("#y")[0].innerHTML = this.#s_price.reduce((a, b) => a + b);
  }
  #calculateDiscount(index, discount) {
    let special_price = "";
    let constrain = discount != "" && (discount.constrain == "None" || (discount.constrain == "Member" && this.#member));

    if (constrain && discount.description.indexOf("系列") == -1) {
      if (discount.dis_rate == "單件8折") {
        special_price = Math.floor(this.#amountArray[index] * this.#price[index] * 0.8);
      } else if (discount.dis_rate == "單件99元") {
        special_price = this.#amountArray[index] * 99;
      };
      if (this.#member && special_price != "") { special_price = Math.floor(special_price * 0.9) } else if (this.#member && special_price == "") { special_price = Math.floor(this.#amountArray[index] * this.#price[index] * 0.9) };
      $("#item_table")[0].rows[index + 1].cells[4].innerHTML = special_price;
      this.#s_price[index] = special_price;
    } else if (constrain && discount.description.indexOf("系列") != -1) {
      var [indexArr, priceArr] = this.#sortAndFlatten(discount);
      if (discount.dis_rate == "第2件68折") {
        for (let i = priceArr.length - 2; i >= 0; i -= 2) { priceArr[i] = Math.floor(priceArr[i] * 0.68) };
      } else if (discount.dis_rate == "2件85折") {
        for (let i = indexArr.length % 2; i < indexArr.length; i++) { priceArr[i] = Math.floor(priceArr[i] * 0.85) };
      } else if (discount.dis_rate == "會員買3送1") {
        for (let i = priceArr.length - 4; i >= 0; i -= 4) { priceArr[i] = 0 };
      };
      let total = 0;
      let index = indexArr[0];
      for (let i = 0; i < indexArr.length; i++) {
        if (indexArr[i] == index) { total += priceArr[i] } else {
          let origin = $("#item_table")[0].rows[index + 1].cells[3].innerHTML;
          let special = $("#item_table")[0].rows[index + 1].cells[4];
          this.#s_price[index] = total;
          if (origin != String(total)) { special.innerHTML = total } else { special.innerHTML = "" };
          index = indexArr[i];
          total = priceArr[i];
        };
        let origin = $("#item_table")[0].rows[index + 1].cells[3].innerHTML;
        let special = $("#item_table")[0].rows[index + 1].cells[4];
        this.#s_price[index] = total;
        if (origin != String(total)) { special.innerHTML = total } else { special.innerHTML = "" };
      };
    } else if (!constrain && this.#member) {
      special_price = Math.floor(this.#amountArray[index] * this.#price[index] * 0.9);
      $("#item_table")[0].rows[index + 1].cells[4].innerHTML = special_price;
      this.#s_price[index] = special_price;
    } else if (!constrain && !this.#member) { this.#s_price[index] = this.#amountArray[index] * this.#price[index] };

    for (let i = 0; i < this.#discount.length; i++) {
      if (this.#discount[i] != "" && this.#discount[i].dis_rate == "會員滿百加購價29元" && this.#member) {
        if (this.#s_price.reduce((a, b) => a + b) >= 100 + 45) { special_price = Math.floor((this.#amountArray[i] - 1) * this.#price[i] * 0.9) + 29 } else { special_price = Math.floor(this.#amountArray[i] * this.#price[i] * 0.9) };
        $("#item_table")[0].rows[i + 1].cells[4].innerHTML = special_price;
        this.#s_price[i] = special_price;
      } else if (this.#discount[i] != "" && this.#discount[i].dis_rate == "滿299送") {
        if (this.#s_price.reduce((a, b) => a + b) >= 299 + 40) { special_price = (this.#amountArray[i] - 1) * this.#price[i] } else { special_price = this.#amountArray[i] * this.#price[i] };
        if (this.#member) { special_price = Math.floor(special_price * 0.9) };
        this.#s_price[i] = special_price;
        if (special_price == this.#amountArray[i] * this.#price[i]) { special_price = "" };
        $("#item_table")[0].rows[i + 1].cells[4].innerHTML = special_price;
      };
    };
  }
  #sortAndFlatten(discount) {
    let sortedArray_2d = [];
    for (let i = 0; i < this.#discount.length; i++) {
      if (this.#discount[i].description == discount.description) {
        if (sortedArray_2d.length == 0) { sortedArray_2d.push([i, this.#amountArray[i], this.#price[i]]) } else {
          let det = true;
          for (let j = 0; j < sortedArray_2d.length; j++) {
            if (this.#price[i] < sortedArray_2d[j][2]) {
              sortedArray_2d.splice(j, 0, [i, this.#amountArray[i], this.#price[i]]);
              det = false;
              break;
            };
          };
          if (det) { sortedArray_2d.push([i, this.#amountArray[i], this.#price[i]]) };
        };
      };
    };

    let indexArr = [];
    let priceArr = [];
    for (let i = 0; i < sortedArray_2d.length; i++) {
      for (let j = 0; j < sortedArray_2d[i][1]; j++) {
        indexArr.push(sortedArray_2d[i][0]);
        if (this.#member) { priceArr.push(Math.floor(sortedArray_2d[i][2] * 0.9)) } else { priceArr.push(sortedArray_2d[i][2]) };
      };
    };
    return [indexArr, priceArr];
  }
  #newRow(result) {
    let tableobj = $("#item_table")[0];
    let tr = tableobj.insertRow(tableobj.rows.length);
    tr.setAttribute("onclick", "trades.selectItem(" + (tableobj.rows.length - 1) + ")");

    let index = tr.insertCell(0);
    index.innerHTML = tableobj.rows.length - 1;

    let name = tr.insertCell(1);
    name.innerHTML = result.name;
    this.#name.push(result.name);

    let amount = tr.insertCell(2);
    if (this.#amount) { amount.innerHTML = this.#amount } else { amount.innerHTML = 1 };
    this.#amountArray.push(parseInt(amount.innerHTML));

    let original_price = tr.insertCell(3);
    original_price.innerHTML = result.price * parseInt(amount.innerHTML);
    this.#price.push(result.price);

    let special_price = tr.insertCell(4);
    special_price.className = "special_price";
    let discount = tr.insertCell(5);
    if (result.discount == "") { discount = "" } else { discount.innerHTML = result.discount.description + result.discount.dis_rate };
    this.#discount.push(result.discount);
    this.#calculateDiscount(tableobj.rows.length - 2, result.discount);

    this.#amount = false;
    if ($("#xy div")[0].style["visibility"] == "hidden") { $("#xy div")[0].style["visibility"] = "" };
    $("#x")[0].innerHTML = this.#amountArray.reduce((a, b) => a + b);
    $("#y")[0].innerHTML = this.#s_price.reduce((a, b) => a + b);
    $("#left_bottom_area")[0].scrollTop = $("#left_bottom_area")[0].scrollHeight;
  }
  #isMember(result) {
    $("#member div")[0].innerHTML = result.name + "<br>" + result.phonenumber;
    this.#member = $("#member div")[0].innerHTML;
    for (let i = 0; i < this.#name.length; i++) {
      this.#updateRow(i, this.#discount[i]);
    };
  }
  dump() {
    if ($("#item_table")[0].rows.length == 1) {
      $("#output_area")[0].innerHTML = "操作錯誤";
      return;
    };
    this.#temp.push({
      "name": this.#name,
      "price": this.#price,
      "s_price": this.#s_price,
      "discount": this.#discount,
      "amountArray": this.#amountArray,
      "member": this.#member,
      "time": String(new Date).split(" ")[4].slice(0, 5)
    });
    this.newTrade("保留");
  }
  retrieve() {
    let table = $("#retrieve_table")[0];
    table.innerHTML = "<tr>\
                <th width='15%'>會員</th>\
                <th width='10%'>總數量</th>\
                <th width='10%'>總價格</th>\
                <th width='10%'>保留時間</th>\
                <th width='55%'>商品列表</th>\
              </tr>";
    this.#temp.forEach(function(temp, index) {
      let tr = table.insertRow(table.rows.length);
      tr.setAttribute("onclick", "trades.retrieveTarget(" + index + ")");

      if (temp.member) { tr.insertCell(0).innerHTML = temp.member } else { tr.insertCell(0).innerHTML = "" };
      tr.insertCell(1).innerHTML = temp.amountArray.reduce((a, b) => a + b);
      tr.insertCell(2).innerHTML = temp.s_price.reduce((a, b) => a + b);
      tr.insertCell(3).innerHTML = temp.time;
      tr.insertCell(4).innerHTML = temp.name;
    })
    $("#grayBackground").fadeToggle(300);
    $("#retrieve").fadeToggle(300);
  }
  retrieve_cancel() {
    $("#grayBackground").fadeToggle(300);
    $("#retrieve").fadeToggle(300);
  }
  retrieve_confirm() {
    if (this.#retrieve_target === false) { return };
    this.newTrade("回復");
    this.#member = this.#temp[this.#retrieve_target].member;
    if (this.#member) { $("#member div")[0].innerHTML = this.#member };

    for (let i = 0; i < this.#temp[this.#retrieve_target].name.length; i++) {
      this.#amount = this.#temp[this.#retrieve_target].amountArray[i];
      this.#newRow({
        "name": this.#temp[this.#retrieve_target].name[i],
        "price": this.#temp[this.#retrieve_target].price[i],
        "discount": this.#temp[this.#retrieve_target].discount[i]
      });
    };
    this.#temp.splice(this.#retrieve_target, 1);
    this.#retrieve_target = false;
    this.retrieve_cancel();
  }
  retrieveTarget(arg) {
    $("#retrieve_table")[0].rows[this.#retrieve_target + 1].style["background-color"] = "";
    this.#retrieve_target = arg;
    $("#retrieve_table")[0].rows[arg + 1].style["background-color"] = "#17bdff";
  }
  cash() {
    if (this.#data == "" || this.#data[0] == "0") { $("#output_area")[0].innerHTML = "請輸入金額" } else if ($("#item_table")[0].rows.length == 1) {
      $("#output_area")[0].innerHTML = "無交易項";
      this.#data = "";
    } else if (parseInt(this.#data) >= this.#s_price.reduce((a, b) => a + b)) {
      $("#output_area")[0].innerHTML = "找零" + (parseInt(this.#data) - this.#s_price.reduce((a, b) => a + b)) + "元";
      $("#transparent")[0].style["display"] = "";
      this.#sockets.emit("newTrade", this.#name, this.#amountArray, this.#s_price.reduce((a, b) => a + b), this.#member, "cash");
      this.#newTrade_Result();
    } else if (parseInt(this.#data) < this.#s_price.reduce((a, b) => a + b)) {
      $("#output_area")[0].innerHTML = "現金不足";
      this.#data = "";
    };
  }
  card(arg) {
    if (arg != 'credit' && arg != 'easycard') { return };
    if ($("#item_table")[0].rows.length == 1) { $("#output_area")[0].innerHTML = "無交易項" } else {
      if (arg == "credit") { $("#output_area")[0].innerHTML = "請感應信用卡" } else { $("#output_area")[0].innerHTML = "請感應悠遊卡" };
      $("#transparent")[0].style["display"] = "";
      setTimeout(function() {
        if (Math.random() > 0.03) {
          trades.#sockets.emit("newTrade", trades.#name, trades.#amountArray, trades.#s_price.reduce((a, b) => a + b), trades.#member, arg);
          trades.#newTrade_Result();
        } else {
          $("#output_area")[0].innerHTML = "感應失敗";
          $("#auto")[0].play();
          $("#transparent")[0].style["display"] = "none";
        };
      }, 3000)
    };
  }
  selectItem(arg) {
    $("#grayBackground").fadeToggle(300);
    $("#selectItemWindow").fadeToggle(300);
    $("#item_table")[0].rows[arg].style["background-color"] = "#17bdff";
    this.#item_target = arg;
  }
  closeWindows() {
    $("#grayBackground").fadeToggle(300);
    $("#selectItemWindow").fadeOut(300);
    $("#retrieve").fadeOut(300);
    if (this.#item_target) {
      $("#item_table")[0].rows[this.#item_target].style["background-color"] = "";
      $("#item_table")[0].rows[this.#item_target].cells[2].innerHTML = this.#amountArray[this.#item_target - 1];
      if ($("#item_table")[0].rows[this.#item_target].style["display"] == "none") { $("#item_table")[0].rows[this.#item_target].style["display"] = "" };
      this.#item_target = false;
    };
  }
  amountMinusOne() {
    if (parseInt($("#item_table")[0].rows[this.#item_target].cells[2].innerHTML) > 1) {
      $("#item_table")[0].rows[this.#item_target].cells[2].innerHTML = parseInt($("#item_table")[0].rows[this.#item_target].cells[2].innerHTML) - 1;
    };
  }
  amountAddOne() {
    $("#item_table")[0].rows[this.#item_target].cells[2].innerHTML = parseInt($("#item_table")[0].rows[this.#item_target].cells[2].innerHTML) + 1;
  }
  deleteItem() {
    $("#item_table")[0].rows[this.#item_target].style["display"] = "none";
  }
  confirmItemWindow() {
    if ($("#item_table")[0].rows[this.#item_target].style["display"] == "none") {
      $("#item_table")[0].deleteRow(this.#item_target);
      this.#name.splice(this.#item_target - 1, 1);
      this.#amountArray.splice(this.#item_target - 1, 1);
      this.#price.splice(this.#item_target - 1, 1);
      this.#s_price.splice(this.#item_target - 1, 1);
      this.#discount.splice(this.#item_target - 1, 1);

      let IsEmptyTable = true;
      for (let i = 1; i < $("#item_table")[0].rows.length; i++) {
        $("#item_table")[0].rows[i].setAttribute("onclick", "trades.selectItem(" + i + ")");
        $("#item_table")[0].rows[i].cells[0].innerHTML = i;
        this.#updateRow(i - 1, this.#discount[i - 1]);
        IsEmptyTable = false;
      };
      if (IsEmptyTable) { this.newTrade("重置") };
    } else {
      if (parseInt($("#item_table")[0].rows[this.#item_target].cells[2].innerHTML) != this.#amountArray[this.#item_target - 1]) {
        this.#amountArray[this.#item_target - 1] = parseInt($("#item_table")[0].rows[this.#item_target].cells[2].innerHTML);
        this.#updateRow(this.#item_target - 1, this.#discount[this.#item_target - 1]);
      };
      $("#item_table")[0].rows[this.#item_target].style["background-color"] = "";
    };
    this.#item_target = false;
    this.closeWindows();
  }
  #newTrade_Result() {
    this.#socket.once('newTrade_Result' + usernameHash, function(result) {
      if (result == "ok") {
        setTimeout(function() {
          $("#output_area")[0].innerHTML = "交易完成";
          setTimeout(function() {
            $("#transparent")[0].style["display"] = "none";
            trades.newTrade("刷新");
          }, 5000)
        }, 5000);
      } else if (result == "Invalid newTrade") {
        setTimeout(function() {
          $("#auto")[0].play();
          $("#output_area")[0].innerHTML = "交易失敗";
          $("#transparent")[0].style["display"] = "none";
        }, 5000);
      } else if (result == "DB Reconnecting") {
        $("#auto")[0].play();
        $("#output_area")[0].innerHTML = "資料庫重新連線中";
      }
    });
  }
}

const trades = new trade();
const check = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
const usernameHash = $('meta[name=usernameHash]').attr('content');

$(document).keydown(function(event) { trades.add(event.key) })