var AnimeService = {
    getAll: function(containerId = "#anime-list") {
        RestClient.get("anime", function(data) {
            console.log("Data received:", data);
            var html = "";
            
            var animeList = data.data ? data.data : data; 

            if (!animeList || animeList.length === 0) {
                $(containerId).html('<p class="text-white">No anime found.</p>');
                return;
            }

            for (var i = 0; i < animeList.length; i++) {
                html += `
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="product__item">
                        <div class="product__item__pic set-bg" data-setbg="${animeList[i].image_url}" style="background-image: url('${animeList[i].image_url}');">
                            <div class="ep">18 / 18</div>
                            <div class="comment"><i class="fa fa-comments"></i> 11</div>
                            <div class="view"><i class="fa fa-eye"></i> 9141</div>
                        </div>
                        <div class="product__item__text">
                            <ul>
                                <li>Active</li>
                                <li>Movie</li>
                            </ul>
                            <h5><a href="#anime-details?id=${animeList[i].id}">${animeList[i].title}</a></h5>
                        </div>
                    </div>
                </div>`;
            }
            $(containerId).html(html);
        }, function(error) {
            console.error("Error fetching anime:", error);
            toastr.error("Failed to load anime data.");
        });
    },

    getById: function(id) {
        RestClient.get("anime/" + id, function(anime) {
            $(".breadcrumb__links span").text(anime.title);
            $(".anime__details__title h3").text(anime.title);
            $(".anime__details__title span").text(anime.original_name || "Original Title");

            $(".anime__details__pic").css("background-image", `url(${anime.image_url})`);
            $(".anime__details__text p").text(anime.description);

            $("#anime-type").text(anime.type || "TV Series");
            $("#anime-studios").text(anime.studios || "Unknown");
            $("#anime-date").text(anime.release_date || "N/A");
            $("#anime-status").text(anime.status);
            $("#anime-genre").text(anime.genres || "Action, Adventure");
            
            $("#anime-score").text("8.5 / 10");
            $("#anime-views").text("15,432 Views");

            $(".anime__details__btn .watch-btn").attr("href", "#anime-watching?id=" + anime.id);
        
            const token = localStorage.getItem("user_token");
            const user = token ? Utils.parseJwt(token) : null;
            
            let managementButtons = "";
            if (user && user.role === 'admin') {
                managementButtons = `
                    <a href="#edit-anime?id=${anime.id}" class="site-btn btn-warning" style="background: #f39c12; margin-right: 10px;">Edit Anime</a>
                    <button onclick="AnimeService.delete(${anime.id})" class="site-btn btn-danger" style="background: #e74c3c; border: none;">Delete Anime</button>
                `;
            }
            
            $("#anime-management-container").html(managementButtons);
            
            StudioService.getAll(function(studios) {
                let html = '<option value="">Select Production Studio</option>';
                studios.forEach(studio => {
                    let selected = (anime.studio_id && studio.id == anime.studio_id) ? "selected" : ""; 
                    html += `<option value="${studio.id}" ${selected}>${studio.name}</option>`;
                });
                $("#edit-anime-studio").html(html);
            });

            EpisodeService.getByAnimeId(id, function(episodes) {
                let html = "";
                episodes.forEach(ep => {
                    html += `<a href="#anime-watching?id=${ep.id}">Ep ${ep.episode_number}: ${ep.title}</a>`;
                });
                
                if (episodes.length === 0) {
                    html = '<p class="text-white">No episodes available yet.</p>';
                    $(".anime__details__btn .watch-btn").attr("href", "#").click(function(e){
                         e.preventDefault();
                         toastr.info("No episodes available for this anime yet.");
                    });
                } else {
                    $(".anime__details__btn .watch-btn")
                        .attr("href", "#anime-watching?id=" + episodes[0].id)
                        .off("click");
                }
                
                $("#episode-list-container").html(html);
            });
            CommentService.getByAnimeId(id);
        });
    },

    add: function(entity) {
        if(entity.episode_video && !entity.episode_video_url){
            entity.episode_video_url = entity.episode_video;
        }

        RestClient.post("admin/anime/add", entity, function(response) {
            toastr.success("Anime successfully added to database!");
            window.location.hash = "#home";
        }, function(error) {
            toastr.error(error.responseJSON?.message || "Failed to add anime. Ensure you are logged in as Admin.");
        });
    },

    loadEditForm: function(id) {
        RestClient.get("anime/" + id, function(data) {
            $("#edit-anime-id").val(data.id);
            $("#edit-title").val(data.title);
            $("#edit-type").val(data.type);
            $("#edit-date").val(data.release_date);
            $("#edit-status").val(data.status);
            $("#edit-details").val(data.description);
            
            CategoryService.getAll(function(allCategories) {
                let html = "";
                let selectedIds = data.category_ids || []; 
                
                allCategories.forEach(cat => {
                    let checked = selectedIds.includes(cat.id) ? "checked" : "";
                    html += `
                        <div class="col-lg-4 col-md-6">
                            <input type="checkbox" class="edit-category-id" value="${cat.id}" ${checked}>
                            <span class="text-white ml-2">${cat.name}</span>
                        </div>`;
                });
                $("#edit-categories-list").html(html);
            });
        });
    },

    update: function(animeData, episodes) {
        RestClient.request("admin/anime/update/" + animeData.id, "PATCH", animeData, function(response) {
            if (episodes.length > 0) {
                episodes.forEach(ep => {
                    ep.anime_id = animeData.id;
                    RestClient.post("episodes/add", ep); 
                });
            }
            toastr.success("Anime and episodes updated!");
            window.location.hash = "#home";
        });
    },

    delete: function(id) {
        if (confirm("Are you sure you want to delete this anime? This action cannot be undone.")) {
            RestClient.delete("anime/" + id, function(response) {
                toastr.success("Anime deleted successfully.");
                window.location.hash = "#home"; 
            }, function(error) {
                toastr.error("Error deleting anime. Make sure you have admin privileges.");
            });
        }
    }
};