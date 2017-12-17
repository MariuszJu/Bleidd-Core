var ajax = function(url, type, data, before, success, complete, error) {
    if (!type) {
        type = 'POST';
    }
    if (!data) {
        data = {};
    }

    $.ajax({
        url: url,
        type: type,
        data: data,

        beforeSend: function() {
            typeof before == 'function' && before();
        },
        success: function(response) {
            typeof success == 'function' && success(response);
        },
        complete: function() {
            typeof complete == 'function' && complete();
        },
        error: function() {
            typeof error == 'function' && error();
        },

        statusCode: {
            401: function(response) {
                console.log('You are not allowed to perform this action');
            }
        }
    });
};
var get = function(url, data, before, success, complete, error) {
    ajax(url, 'GET', data, before, success, complete, error);
};
var post = function(url, data, before, success, complete, error) {
    ajax(url, 'POST', data, before, success, complete, error);
};