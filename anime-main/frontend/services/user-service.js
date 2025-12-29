var UserService = {
    getAll: function() {
        RestClient.get("users", function(data) {
            let html = "";
            data.forEach(user => {
                html += `
                    <tr>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td><span class="badge ${user.role === 'admin' ? 'bg-warning' : 'bg-info'}">${user.role}</span></td>
                        <td>
                            <button onclick="UserService.openEditModal(${user.id})" class="btn btn-sm btn-primary">Edit</button>
                            <button onclick="UserService.delete(${user.id})" class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>`;
            });
            $("#users-table-body").html(html);
        });
    },


    loadMyProfile: function() {
        const token = localStorage.getItem("user_token");
        const currentUser = Utils.parseJwt(token);
        
        RestClient.get("user/get/" + currentUser.id, function(user) {
            $("#profile-username").val(user.username);
            $("#profile-name").val(user.name);
            $("#profile-email").val(user.email);
            $("#profile-img-preview").attr("src", user.profile_img || "assets/img/anime/review-1.jpg");
        });
    },

    update: function(entity) {
        RestClient.request("user/update/" + entity.id, "PATCH", entity, function(response) {
            toastr.success("Profile updated successfully!");
        });
    },

    delete: function(id) {
        if (confirm("Are you sure you want to delete this user?")) {
            RestClient.delete("user/delete/" + id, function() {
                toastr.success("User removed.");
                UserService.getAll(); 
            });
        }
    }
};