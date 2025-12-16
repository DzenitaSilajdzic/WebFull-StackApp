const API_BASE_URL = "http://localhost/WebFull-StackApp/anime-main/backend/";


const Auth = {
    isLoggedIn: function() {
        return !!localStorage.getItem('jwt_token');
    },
    setToken: function(token, user) {
        localStorage.setItem('jwt_token', token);
        localStorage.setItem('user_data', JSON.stringify(user));
    },
    getToken: function() {
        return localStorage.getItem('jwt_token');
    },
    getUser: function() {
        const userData = localStorage.getItem('user_data');
        const parsedData = userData ? JSON.parse(userData) : null;
        return parsedData ? parsedData.user : null;
    },
    isAdmin: function() {
        const user = this.getUser();
        return user && user.role === 'admin'; 
    },
    logout: function() {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('user_data');
    }
};


function apiCall(endpoint, method = 'GET', data = null) {
    const url = API_BASE_URL + endpoint;
    const headers = {
        'Content-Type': 'application/json',
    };

    if (Auth.isLoggedIn()) {
        headers['Authorization'] = 'Bearer ' + Auth.getToken();
    }

    const config = {
        method: method,
        headers: headers,
        body: data ? JSON.stringify(data) : null,
    };

    return fetch(url, config)
        .then(response => {
            if (response.status === 401 || response.status === 403) {
                Auth.logout();
                window.location.hash = '#login'; 
                throw new Error('Session expired or unauthorized access.');
            }
            
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.error || errorData.message || 'API Request Failed');
                }).catch(() => {
                    throw new Error(`Server error: ${response.status} ${response.statusText}`);
                });
            }
            
            return response.json();
        })
        .catch(error => {
            throw error; 
        });
}


window.Auth = Auth;
window.apiCall = apiCall;


$(document).ready(function() {
    var app = $.spapp({
        pageNotFound: 'error_404',
        defaultView: 'home',
        templateDir: './views/'
    });

    app.route({ view: 'home', onCreate: window.loadHomePageData });
    app.route({ view: 'login' });
    app.route({ view: 'signup' });
    app.route({ 
        view: 'anime-details', 
        onCreate: function(evt) {
            const animeId = evt.params.id;
            if (animeId) { window.loadAnimeDetails(animeId); } 
        }
    });
    app.route({ 
        view: 'anime-watching', 
        onCreate: function(evt) {
            const episodeId = evt.params.episode_id;
            if (episodeId) { window.loadWatchingView(episodeId); } 
        }
    });

    app.route({
        view: 'add-anime',
        onBefore: function() {
            if (!window.Auth.isAdmin()) {
                alert("Access Denied. Admin privileges required.");
                window.location.hash = '#home';
                return false; 
            }
            return true; 
        },
        onCreate: window.loadAddAnimeForm 
    });

    app.route({
        view: 'edit-anime',
        onBefore: function(evt) {
            if (!window.Auth.isAdmin()) {
                alert("Access Denied. Admin privileges required.");
                window.location.hash = '#home';
                return false; 
            }
            return true; 
        },
        onCreate: function(evt) {
            const animeId = evt.params.id;
            if (animeId) {
                window.loadEditAnimeForm(animeId); 
            } else {
                $('#edit-anime-status').html('<p class="text-danger">Error: Anime ID is missing for editing.</p>');
            }
        }
    });


    app.run();
    window.updateAuthUI(); 
});