import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import $ from 'jquery';

$(function () {
    $("#login").val(localStorage.getItem('login'));

    function updateToken(callback) {
        let login = localStorage.getItem('login');

        $.post("/api/v1/login", {login: login}, function(data, status){
            if ('success' == status) {
                localStorage.setItem('token', data.token);
                callback();
            }
        });
    };

    $("#signin").click(function () {
        let login = $("#login").val();

        if ('' == login) {
            return;
        }

        localStorage.setItem('login', login);
        updateToken(function() {updateTable();});
    });

    $("#signout").click(function () {
        $("#login").val('');

        localStorage.setItem('login', '');
        localStorage.setItem('token', '');
        updateTable();
    });

    let page = 1;
    function updateTable() {
        $.get({
            url: "/api/v1/document?page=" + page, 
            type: 'GET', 
            beforeSend: function (xhr) {
                let token = localStorage.getItem('token');

                if (token) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                }
            },
            success: function (data) { 
                let $body = $("#tableBody");

                $body.empty();
                for (let i in data.document) {
                    let document = data.document[i];

                    $body.append('<tr>\
                    <th scope="row"><a href="#" data-action="openDocument" data-bs-toggle="modal" data-bs-target="#documentModal">' + document.id + '</a></th>\
                    <td><pre>' + JSON.stringify(document.payload, null, 2) + '</pre></td>\
                    <td>' + document.createAt + '</td>\
                    <td>' + document.modifyAt + '</td>\
                    <td>' + document.status + '</td>\
                    </tr>');
                }

                let $pagiantion = $("#pagination");
                let pages = Math.floor(data.pagination.total / data.pagination.perPage);
                if (data.pagination.total % data.pagination.perPage != 0) {
                    pages++;
                }

                $pagiantion.empty();
                $pagiantion.append('<li class="page-item ' + (page < 2 ? 'disabled' : '') + '"><a class="page-link bg-dark link-light" href="#" data-action="paginationPrev">Previous</a></li>')
                for (let i = Math.max(pages - 3, 1); i <= Math.min(pages + 3, pages); i++) {
                    $pagiantion.append('<li class="page-item ' + (page == i ? 'active' : '') + '"><a class="page-link bg-dark link-light" href="#" data-action="pagination">' + i + '</a></li>')
                }
                $pagiantion.append('<li class="page-item ' + (page >= pages ? 'disabled' : '') + '"><a class="page-link bg-dark link-light" href="#" data-action="paginationNext">Next</a></li>')
            },
            error: function(data) {
                if (401 == data.status) {
                    updateToken(function() {updateTable();});
                }
            }
        });
    }
    updateTable();

    $("#tableBody").on("click", "[data-action=openDocument]", function() {
        loadModal($(this).text());
        
        return false;
    });

    function loadModal(id) {
        $.get({
            url: "/api/v1/document/" + id, 
            type: 'GET', 
            beforeSend: function (xhr) {
                let token = localStorage.getItem('token');

                if (token) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                }
            },
            success: function (data) { 
                $("#documentModalTitle").text(data.document.id);
                $("#documentModalBody").html('<pre>' + JSON.stringify(data.document.payload, null, 2) + '</pre>');
            },
            error: function(data) {
                if (401 == data.status) {
                    updateToken(function() {loadModal(id);});
                }
            }
        });
    };

    $("#pagination").on("click", "[data-action=paginationNext]", function() {
        page++;
        updateTable();

        return false;
    });
    $("#pagination").on("click", "[data-action=paginationPrev]", function() {
        page--;
        updateTable();

        return false;
    });
    $("#pagination").on("click", "[data-action=pagination]", function() {
        page = parseInt($(this).text());
        updateTable();

        return false;
    });
});