var Utils = {
  parseJwt: function (token) {
    if (!token) return null;
    var base64Url = token.split(".")[1];
    var base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");
    var jsonPayload = decodeURIComponent(
      atob(base64)
        .split("")
        .map(function (c) {
          return "%" + ("00" + c.charCodeAt(0).toString(16)).slice(-2);
        })
        .join("")
    );
    return JSON.parse(jsonPayload);
  },

  
  get_form_data: function (form) {
    let data = {};
    $.each($(form).serializeArray(), function () {
      data[this.name] = this.value;
    });
    return data;
  }, 

  getUrlParam: function(name) {
        var results = new RegExp('[?&]' + name + '=([^&#]*)').exec(window.location.href);
        return results ? decodeURIComponent(results[1]) : null;
    }
};