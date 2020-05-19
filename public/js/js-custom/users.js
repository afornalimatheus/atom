var users = {

    init: function () {
        $('#send-user').on('click', users.saveUser);
        $('#search-user').on('click', users.searchUser);
    },

    saveUser: function (e) {

        e.preventDefault();
        
        var name   = $('#name').val();
        var email  = $('#email').val();
        var active = $('#active').val();

        $.ajax({
            type: 'POST',
            url: '/new-user',
            data: {
                name: name,
                email: email,
                active: active
            },
            dataType: 'json',
            success: function (data) {

                if (data) {
                    $('#name').val('');
                    $('#email').val('');
                    $('#active').val('');
                    
                    alert('USUÁRIO CADASTRADO!');
                } else {
                    alert('ERRO AO CADASTRAR!');
                    return false;
                }
            }
        })
    },

    searchUser: function (e) {

        e.preventDefault();
        
        var user = $('#user').val();

        $.ajax({
            type: 'POST',
            url: '/search-user',
            data: {
                user: user,
            },
            dataType: 'json',
            success: function (data) {

                if (data) {
                    $("#table-users > tbody").empty();
                    
                    var user = data.users;

                    for (var i = 0; i < user.length; i++) {
                        $("#table-users > tbody").append(
                            "<tr>" +
                            "    <td>" + user[i].id   + "</td>" +
                            "    <td>" + user[i].name   + "</td>" +
                            "    <td>" + user[i].email  + "</td>" +
                            "    <td>" + user[i].active + "</td>" +
                            "    <td>" + user[i].last_login + "</td>" +
                            "</tr>"
                        );
                    }

                    return true;
                } else {
                    alert('ERRO AO LISTAR!');
                    return false;
                }
            }
        })
    }
}

$(document).ready(function () {
    users.init();
});