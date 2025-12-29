var EpisodeService = {
    getByAnimeId: function(animeId, callback) {
        RestClient.get("episodes/anime/" + animeId, function(data) {
            if (callback) callback(data);
        });
    },


    add: function(entity, callback) {
        RestClient.post("episodes/add", entity, function(response) {
            toastr.success("Episode added successfully!");
            if (callback) callback(response);
        });
    },

    delete: function(id, animeId) {
        if (confirm("Are you sure you want to delete this episode?")) {
            RestClient.delete("episodes/delete/" + id, function() {
                toastr.success("Episode deleted.");
                if (window.location.hash.includes("edit-anime")) {
                    AnimeService.loadEditForm(animeId);
                }
            });
        }
    },
    getById: function(id, callback) {
        RestClient.get("episode/get/" + id, function(data) {
            if (callback) callback(data);
        });
    },

    loadWatchingSidebar: function(animeId, currentEpisodeId) {
        this.getByAnimeId(animeId, function(episodes) {
            let html = "";
            episodes.forEach(ep => {
                const activeClass = (ep.id == currentEpisodeId) ? "active" : "";
                html += `<a href="#anime-watching?id=${ep.id}" class="${activeClass}">Ep ${ep.episode_number}</a>`;
            });
            $("#watching-episode-nav").html(html);
        });
    }
};