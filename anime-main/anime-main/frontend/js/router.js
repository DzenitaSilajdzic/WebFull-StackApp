var app = $.spapp({
    defaultView  : "todo",
    templateDir  : "./views/"
  });


app.route({ view: 'home', load: 'home.html' });
app.route({ view: 'todo', load: 'todo.html' });

app.run();

//This JS is used for managing checkboxes. 
document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.task-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const task = this.closest('.task');
            if (this.checked) {
                task.classList.add('completed');
            } else {
                task.classList.remove('completed');
            }
        });
    });
});

