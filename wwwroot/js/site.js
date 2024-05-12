﻿"use strict";
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

function FilterTable() {
    var input, filter, table, tr, td, i;
    input = document.getElementById("filterInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("filterTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[0];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }