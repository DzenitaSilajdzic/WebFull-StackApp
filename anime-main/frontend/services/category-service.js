var CategoryService = {
    getAll: function(callback) {
        RestClient.get("utilities/categories", function(data) {
            if (callback) callback(data);
        });
    }
};