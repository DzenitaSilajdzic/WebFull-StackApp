window.apiCall = function(url, method, data) {
    return new Promise((resolve, reject) => {
        RestClient.request(url, method, data, function(response) {
            resolve(response);
        }, function(error) {
            reject(error);
        });
    });
};

window.currentAnimeId = null; 

function updateAuthUI() {
    if (typeof authModel === 'undefined') return;
    
    const user = authModel.getUser();
    const isLoggedIn = authModel.isLoggedIn();
    
    if (isLoggedIn) {
        $('#unauth-links').hide();
        $('#username-display').text(user.username || user.email); 
        $('#auth-profile').show();

        if (authModel.isAdmin()) { 
            $('#admin-link').show();
        } else {
            $('#admin-link').hide();
        }

    } else {
        $('#unauth-links').show();
        $('#auth-profile').hide();
        $('#admin-link').hide();
    }
}

window.loadHomePageData = function() {
    const container = $('#product-list-container'); 
    container.empty().html('<div class="col-lg-12 text-center"><p>Loading anime list...</p></div>'); 
    
    window.apiCall('anime/listing/0/8/NULL/NULL', 'GET')
        .then(data => {
            container.empty(); 
            
            if (data.length === 0) {
                 container.html('<div class="col-lg-12 text-center"><p>No anime found.</p></div>');
                 return;
            }

            data.forEach(anime => {
                const html = `
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="product__item">
                            <div class="product__item__pic set-bg" data-setbg="${anime.image_url}" style="background-image: url('${anime.image_url}');">
                                <div class="ep">${anime.type}</div>
                                <div class="comment"><i class="fa fa-comments"></i> 0</div>
                                <div class="view"><i class="fa fa-eye"></i> ${anime.views || 0}</div>
                            </div>
                            <div class="product__item__text">
                                <h5><a href="#anime-details?id=${anime.id}">${anime.title}</a></h5>
                            </div>
                        </div>
                    </div>
                `;
                container.append(html);
            });
            
            $('.set-bg').each(function() {
                var bg = $(this).data('setbg');
                if (bg && bg !== 'undefined' && bg !== 'null') {
                    $(this).css('background-image', 'url(' + bg + ')');
                } else {
                    $(this).css('background-image', 'url("assets/img/hero/hero-1.jpg")'); 
                }
            });
        })
        .catch(error => {
            console.error("Home page data failed to load:", error);
            container.html('<div class="col-lg-12"><p class="text-danger">Failed to load anime: ' + error.message + '</p></div>');
        });
};


window.loadAnimeDetails = function(animeId) {
    window.currentAnimeId = animeId; 
    const container = $('#anime-details-container'); 
    container.html('<div class="text-center"><p>Loading anime details...</p></div>'); 
    
    window.apiCall('anime/details/' + animeId, 'GET')
        .then(anime => {
            container.empty();
            
            const genres = anime.genres ? anime.genres.split(',').map(g => g.trim()) : [];
            const studios = anime.studios ? anime.studios.split(',').map(s => s.trim()) : [];

            const adminButtonsHtml = authModel.isAdmin() ? `
                <div class="admin-buttons mt-3 mb-3">
                    <a href="#edit-anime?id=${animeId}" id="edit-anime-button" class="site-btn">
                        <i class="fa fa-edit"></i> Edit Anime
                    </a>
                    <button id="delete-anime-button" class="site-btn btn-danger">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </div>
            ` : '';

            const html = `
                <div class="row">
                    <div class="col-lg-3">
                        <div class="anime__details__pic set-bg" 
                             data-setbg="${anime.image_url}" 
                             style="background-image: url('${anime.image_url}');">
                            <div class="ep">${anime.total_episodes || '?'} / ${anime.type}</div>
                            <div class="view"><i class="fa fa-eye"></i> ${anime.views || 0}</div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="anime__details__text">
                            <div class="anime__details__title">
                                <h3>${anime.title}</h3>
                            </div>
                            <p>${anime.description}</p>
                            ${adminButtonsHtml} <div class="anime__details__widget">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Type:</span> ${anime.type}</li>
                                            <li><span>Studios:</span> ${studios.join(', ') || 'N/A'}</li>
                                            <li><span>Date aired:</span> ${anime.release_date}</li>
                                            <li><span>Status:</span> ${anime.status}</li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Genres:</span> ${genres.join(', ') || 'N/A'}</li>
                                            <li><span>Duration:</span> ${anime.duration} min</li>
                                            <li><span>Views:</span> ${anime.views || 0}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="anime__details__btn">
                                <a href="#anime-watching?anime_id=${anime.id}&episode_id=1" class="watch-btn"><i class="fa fa-play"></i> WATCH NOW</a>
                                <a href="#" class="follow-btn"><i class="fa fa-heart-o"></i> Follow</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.html(html);

            $('.set-bg').each(function() {
                var bg = $(this).data('setbg');
                if (bg && bg !== 'undefined' && bg !== 'null') {
                    $(this).css('background-image', 'url(' + bg + ')');
                } else {
                    $(this).css('background-image', 'url("assets/img/hero/hero-1.jpg")'); 
                }
            });
            
            window.loadEpisodeList(animeId);
            window.loadComments(animeId); 
            
            $('#delete-anime-button').off('click').on('click', window.handleDeleteAnime);

        })
        .catch(error => {
            console.error("Anime details failed to load:", error);
            container.html('<div class="col-lg-12 text-center"><p class="text-danger">Could not load anime details: ' + error.message + '</p></div>');
        });
};

window.loadComments = function(animeId) {
    const $container = $('#comments-container');
    $container.empty().html('<div class="text-center"><p>Fetching comments...</p></div>');
    
    const currentUser = authModel.getUser();

    window.apiCall('comments/anime/' + animeId, 'GET')
        .then(comments => {
            $container.empty();

            if (comments.length === 0) {
                $container.html('<p class="text-center">No comments posted yet. Be the first!</p>');
                return;
            }

            comments.forEach(comment => {
                const date = new Date(comment.date_posted).toLocaleString();
                const user = comment.username || 'Anonymous';
                
                const isAuthorOrAdmin = currentUser && 
                                        (comment.user_id == currentUser.id || currentUser.role === 'admin');
                
                const deleteButton = isAuthorOrAdmin ? 
                    `<button class="delete-comment-btn btn-link btn-sm text-danger float-right" data-comment-id="${comment.id}">Delete</button>` 
                    : '';

                const html = `
                    <div class="anime__review__item">
                        <div class="anime__review__item__pic">
                            <img src="assets/img/sidebar/comment-1.jpg" alt="User Avatar">
                        </div>
                        <div class="anime__review__item__text">
                            <h6>${user} <span>${date}</span></h6>
                            <p>${comment.comment_text}</p>
                            ${deleteButton}
                        </div>
                    </div>
                `;
                $container.append(html);
            });

            $container.off('click', '.delete-comment-btn').on('click', '.delete-comment-btn', window.handleDeleteComment);
        })
        .catch(error => {
            console.error("Failed to load comments:", error);
            $container.html('<p class="text-danger text-center">Failed to load comments. Error: ' + error.message + '</p>');
        });
};


window.handleCommentSubmission = function(e) {
    e.preventDefault();
    
    if (!authModel.isLoggedIn()) {
        $('#comment-status').html('<p class="text-danger">You must be logged in to post a comment.</p>');
        return;
    }

    if (!window.currentAnimeId) {
        $('#comment-status').html('<p class="text-danger">Error: Anime ID not found.</p>');
        return;
    }

    const $form = $(this);
    const $status = $('#comment-status');
    const commentText = $form.find('#comment-text').val();

    if (commentText.trim() === '') {
        $status.html('<p class="text-danger">Comment text cannot be empty.</p>');
        return;
    }

    const commentData = {
        anime_id: window.currentAnimeId,
        comment_text: commentText
    };

    $status.html('<p class="text-info">Posting comment...</p>');

    window.apiCall('comments/add', 'POST', commentData)
        .then(response => {
            $status.html('<p class="text-success">Comment posted successfully!</p>');
            $form[0].reset(); 
            window.loadComments(window.currentAnimeId);
        })
        .catch(error => {
            console.error("Comment submission failed:", error);
            $status.html('<p class="text-danger">Failed to post comment: ' + (error.message || 'An error occurred.') + '</p>');
        });
};


window.handleDeleteComment = function(e) {
    e.preventDefault();
    const commentId = $(e.currentTarget).data('comment-id');
    
    if (!authModel.isLoggedIn()) {
        alert("Authorization failed. Please log in.");
        return;
    }
    
    if (confirm("Are you sure you want to delete this comment?")) {
        window.apiCall('comments/' + commentId, 'DELETE', null)
            .then(() => {
                if(window.currentAnimeId) {
                    window.loadComments(window.currentAnimeId); 
                }
            })
            .catch(error => {
                alert("Failed to delete comment: " + (error.message || 'Check network or backend setup.'));
            });
    }
};


window.loadEpisodeList = function(animeId) {
    const $container = $('#episode-list-container');
    $container.empty().html('<div class="col-lg-12 text-center"><p>Loading episodes...</p></div>'); 
    
    const isAdmin = authModel.isAdmin();

    window.apiCall('episode/anime/' + animeId, 'GET')
        .then(episodes => {
            $container.empty();
            
            if (isAdmin) {
                const addButton = `<a href="#add-episode?anime_id=${animeId}" class="site-btn float-right mb-3">Add New Episode</a>`;
                $container.before(addButton); 
            } else {
                $container.siblings('a.site-btn.float-right').remove();
            }

            if (episodes.length === 0) {
                 $container.append('<div class="col-lg-12 text-center"><p>No episodes available yet.</p></div>');
                 return;
            }

            const ul = $('<ul class="anime__details__episodes__list"></ul>');
            
            episodes.forEach(episode => {
                const adminEpisodeButtons = isAdmin ? `
                    <span class="ml-2 float-right episode-admin-controls">
                        <a href="#edit-episode?id=${episode.id}" class="btn-link btn-sm text-warning">Edit</a> | 
                        <button class="btn-link btn-sm text-danger delete-episode-btn" data-episode-id="${episode.id}">Delete</button>
                    </span>
                ` : '';

                const listItem = `
                    <li>
                        <a href="#anime-watching?episode_id=${episode.id}">
                            Episode ${episode.episode_number} <span>${episode.title}</span>
                            <i class="fa fa-play"></i> ${episode.duration} min
                        </a>
                        ${adminEpisodeButtons}
                    </li>
                `;
                ul.append(listItem);
            });

            $container.append(ul);
            
            $container.off('click', '.delete-episode-btn').on('click', '.delete-episode-btn', window.handleDeleteEpisode);
        })
        .catch(error => {
            console.error("Episode list failed to load:", error);
            $container.html('<div class="col-lg-12 text-center"><p class="text-danger">Failed to load episodes: ' + error.message + '</p></div>');
        });
};


window.handleDeleteEpisode = function(e) {
    e.preventDefault();
    const episodeId = $(e.currentTarget).data('episode-id');
    
    if (!authModel.isAdmin()) {
        alert("Authorization failed. Admin privileges required.");
        return;
    }

    if (confirm("Are you sure you want to delete this episode?")) {
        window.apiCall('episode/delete/' + episodeId, 'POST', { id: episodeId }) 
            .then(() => {
                window.loadEpisodeList(window.currentAnimeId); 
            })
            .catch(error => {
                alert("Failed to delete episode: " + (error.message || 'Check network or backend setup.'));
            });
    }
};

$(document).ready(function() {
    updateAuthUI(); 

    $('#logout-button').on('click', function(e) {
        e.preventDefault();
        authModel.logout();
        updateAuthUI();
        window.location.hash = '#home'; 
    });

    $(window).on('hashchange', function() {
        updateAuthUI();
    });
});

$(window).on('load', function () {
    $(".loader").fadeOut();
    $("#preloder").delay(200).fadeOut("slow");
    $('.filter__controls li').on('click', function () {
        $('.filter__controls li').removeClass('active');
        $(this).addClass('active');
    });
    
    if ($('.product__sidebar__view .filter__controls').length > 0) {
        var containerEl = document.querySelector('.product__sidebar__view .filter__controls');
        var mixer = mixitup(containerEl);
    }
});