document.getElementById('add-anime-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        const form = event.target;
        const formData = new FormData(form);
        const data = {};
        
        // Convert FormData to a regular object, handling arrays for multi-selects
        for (let [key, value] of formData.entries()) {
            if (key.endsWith('[]')) {
                // Handle multi-select arrays
                const baseKey = key.slice(0, -2);
                if (!data[baseKey]) {
                    data[baseKey] = [];
                }
                data[baseKey].push(parseInt(value));
            } else {
                data[key] = value;
            }
        }
        
        // Convert episodes to integer
        data.episodes = parseInt(data.episodes);
        
        // The arrays collected by FormData for multi-select are tricky.
        // We need to re-handle them correctly for JSON format.
        // For simplicity in this front-end mockup:
        
        // Get selected category IDs
        const categorySelect = document.getElementById('category_ids');
        const selectedCategories = Array.from(categorySelect.options)
            .filter(option => option.selected)
            .map(option => parseInt(option.value));
        data.category_ids = selectedCategories;
        
        // Get selected studio IDs
        const studioSelect = document.getElementById('studio_ids');
        const selectedStudios = Array.from(studioSelect.options)
            .filter(option => option.selected)
            .map(option => parseInt(option.value));
        data.studio_ids = selectedStudios;

        // Clean up keys that might have been partially processed (e.g., 'category_ids' might be an empty string if nothing was selected)
        delete data['category_ids[]'];
        delete data['studio_ids[]'];

        console.log('Form data to submit:', data);

        // --- Actual API Submission Logic (Placeholder) ---
        // In a real application, you would make a fetch/XHR call here:
        /*
        fetch('/rest/anime/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // Add Authorization header here if needed
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(result => {
            console.log('Success:', result);
            alert('Anime added successfully!'); // Replace with a proper modal/message box
            form.reset();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to add anime: ' + error.message); // Replace with a proper modal/message box
        });
        */
        
        // Placeholder success message (replace with proper UI notification)
        alert('Anime data ready for submission (check console for payload)');
        form.reset();
    });

    