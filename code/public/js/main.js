function update() {
  setInterval(function () {
    $.ajax({
      method: 'POST',
      url: "/user/indexRefresh/"
    }).done(function (data) {
      let users = $.parseJSON(response);
      if(users.length != 0){
        for(var k in users){
          let row = "<tr>";
          row += "<td>" + users[k].id + "</td>";
          maxId = users[k].id;
          row += "<td>" + users[k].username + "</td>";
          row += "<td>" + users[k].userlastname + "</td>";
          row += "<td>" + users[k].userbirthday + "</td>";
          row += "</tr>";
          $('.content-template tbody').append(row);
        }
      }
    });
  }, 10000);

}