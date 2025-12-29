var StudioService = {
    getAll: function(callback) {
        RestClient.get("studios", function(data) {
            if (callback) callback(data);
        });
    }
};