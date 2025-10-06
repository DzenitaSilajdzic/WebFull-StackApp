var app = $.spapp({
  defaultView: "log",
  templateDir: "./views/"
});

// Define all routes
app.route({
  view: 'home',
  load: 'home.html',
  onReady: function() {
    setBgImages();
  }
});

app.route({
  view: 'categories',
  load: 'categories.html',
  onReady: function() {
    setBgImages();
  }
});

app.route({
  view: 'watch',
  load: 'anime-watching.html',
  onReady: function() {
    setBgImages();
  }
});

app.route({
  view: 'details',
  load: 'anime-details.html',
  onReady: function() {
    setBgImages();
  }
});

app.route({
  view: 'sign',
  load: 'signup.html',
  onReady: function() {
    setBgImages();
  }
});

app.route({
  view: 'log',
  load: 'login.html',
  onReady: function() {
    setBgImages();
  }
});

// Run the app
app.run();

// Add global function for background images
function setBgImages() {
  $('.set-bg').each(function() {
    var bg = $(this).data('setbg');
    $(this).css('background-image', 'url(' + bg + ')');
  });
}