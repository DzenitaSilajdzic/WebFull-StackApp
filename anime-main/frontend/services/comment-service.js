var CommentService = {
    getByAnimeId: function(animeId) {
        RestClient.get("comments/anime/" + animeId, function(data) {
            const token = localStorage.getItem("user_token");
            const decodedToken = token ? Utils.parseJwt(token) : null;
            
            let currentUserRole = null;

            if (decodedToken) {
                if (decodedToken.user && decodedToken.user.role) {
                    currentUserRole = decodedToken.user.role;
                } else if (decodedToken.role) {
                    currentUserRole = decodedToken.role;
                }
            }
            
            let html = "";
            data.forEach(comment => {
                let deleteBtn = "";
                if (currentUserRole === 'admin') {
                    deleteBtn = `
                        <button onclick="CommentService.delete(${comment.id}, ${animeId})" 
                                class="btn btn-sm btn-danger float-right" 
                                style="margin-left:10px;">
                            <i class="fa fa-trash"></i> Delete
                        </button>`;
                }

                let imgSrc = comment.profile_img ? comment.profile_img : 'assets/img/anime/review-1.jpg';

                html += `
                    <div class="anime__review__item">
                        <div class="anime__review__item__pic">
                            <img src="${imgSrc}" alt="" onerror="this.onerror=null;this.src='assets/img/anime/review-1.jpg';">
                        </div>
                        <div class="anime__review__item__text">
                            <h6>${comment.username} - <span>${comment.date_posted || 'Just now'}</span></h6>
                            <p>${comment.text}</p>
                            ${deleteBtn}
                        </div>
                    </div>`;
            });

            if (data.length === 0) {
                html = '<p class="text-white">No reviews yet. Be the first to comment!</p>';
            }

            $("#comments-container").html(html);
            $("#anime-comment-count").text(data.length);
        });
    },

    add: function(entity) {
        const token = localStorage.getItem("user_token");
        
        if (!token) {
            toastr.error("You must be logged in to post a comment.");
            return;
        }

        const decoded = Utils.parseJwt(token);

        if (decoded) {
            if (decoded.user && decoded.user.id) {
                entity.user_id = decoded.user.id;
            } else if (decoded.id) {
                entity.user_id = decoded.id;
            }
        }

        if (!entity.user_id) {
            toastr.error("Could not verify User ID. Please Logout and Login again.");
            return;
        }

        RestClient.post("comments/add", entity, function(response) {
            toastr.success("Comment posted!");
            $("#comment-text").val(""); 
            CommentService.getByAnimeId(entity.anime_id); 
        }, function(error) {
            console.error("Comment Error:", error);
            const errMsg = error.responseJSON?.error || error.responseJSON?.message || "Failed to post comment.";
            toastr.error(errMsg);
        });
    },

  delete: function(id, animeId) {
        if (confirm("Are you sure you want to delete this comment?")) {
            RestClient.delete("comments/" + id, function(response) {
                toastr.success("Comment deleted successfully.");
                CommentService.getByAnimeId(animeId);
            }, function(error) {
                 const msg = error.responseJSON?.message || "Error deleting comment.";
                 toastr.error(msg);
            });
        }
    }
};