$(document).ready(function() {
    var app = $.spapp({
        defaultView: "#login",
        templateDir: ""
    });

    app.route({
        view: "home",
        load: "views/home.html",
        onReady: function() {
        AnimeService.getAll();
    }
    });

    app.route({
        view: 'anime-details',
        load: 'views/anime-details.html',
        onReady: function() {
            var id = Utils.getUrlParam('id');
            if (typeof AnimeService !== 'undefined' && id) {
                AnimeService.getById(id);
            }
        }
    });

    app.route({
        view: 'anime-watching',
        load: 'views/anime-watching.html',
        onReady: function() {
            var id = Utils.getUrlParam('id'); 
            if (typeof EpisodeService !== 'undefined' && id) {
                 EpisodeService.getById(id); 
            }
        }
    });
    
    app.route({ 
        view: 'categories', 
        load: 'views/categories.html',
        onReady: function() {
            if (typeof AnimeService !== 'undefined') {
                AnimeService.getAll("#category-anime-list");
            }
            
            if (typeof CategoryService !== 'undefined') {
                CategoryService.getAll(function(data){
                });
            }
        }
    });

    app.route({ view: 'login', load: 'views/login.html' });
    app.route({ view: 'signup', load: 'views/signup.html' });

    const token = localStorage.getItem("user_token");
    if (token && window.location.hash === "#login") {
      window.location.hash = "#home";
    }

    app.run();
});