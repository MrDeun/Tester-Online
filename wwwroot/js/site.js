"use strict";
let root = document.documentElement;
function mainSizeChange() {
    let main_height = document.querySelector("main").offsetHeight;
    root.style.setProperty("--main-height", main_height + "px");
};

function togglePassword(ElementId) {
    var passwordField = document.getElementById(ElementId);
    if (passwordField.type === "password") {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
};

function ChangePasswordForm(id, login){
    var div = document.querySelector(".ChangePasswordForm");
    div.classList.replace("w3-hide", "w3-show");
    var PC_id = document.querySelector("input[name='PC_id']");
    PC_id.value = id;
    var PC_login = document.querySelector("input[name='PC_login']");
    PC_login.value = login;
    
};