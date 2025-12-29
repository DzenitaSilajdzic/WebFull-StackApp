var AuthService = {
    init: function () {
        const token = localStorage.getItem("user_token");
        if (token) {
            AuthService.generateMenuItems();
        } else {
            AuthService.generateMenuItems(); 
        }
    },

    login: function (entity) {
        RestClient.post("auth/login", entity, function (response) {
            localStorage.setItem("user_token", response.token); 
            toastr.success("Login successful!");
            window.location.hash = "#home";
            AuthService.generateMenuItems();
        }, function(error) {
            toastr.error("Invalid email or password");
        });
    },

    register: function (entity) {
        RestClient.post("auth/register", entity, function (response) {
            toastr.success("Registration successful! Please login.");
            window.location.hash = "#login";
        }, function(error) {
            toastr.error(error.responseJSON?.message || "Registration failed");
        });
    },

    logout: function () {
        localStorage.removeItem("user_token");
        toastr.info("Logged out successfully");
        window.location.hash = "#login";
        AuthService.generateMenuItems();
    },

    generateMenuItems: function() {
        const token = localStorage.getItem("user_token");
        const user = Utils.parseJwt(token); 
        
        let navHtml = `<li><a href="#home">Home</a></li>
                       <li><a href="#categories">Categories</a></li>`;
        if (user && user.role === 'admin') {
            navHtml += `<li><a href="#add-anime" style="color: #f39c12;">Admin Panel</a></li>`;
        }

        if (user) {
            navHtml += `<li><a href="javascript:void(0)" onclick="AuthService.logout()">Logout (${user.username})</a></li>`;
        } else {
            navHtml += `<li><a href="#login">Login</a></li>`;
        }
        $(".header__menu ul").html(navHtml);
    }
};