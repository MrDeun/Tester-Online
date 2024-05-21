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
function deleteTableRow(button){
    var row = button.parentNode.parentNode;
    var table = row.parentNode;
    table.removeChild(row);
}
function addGroupRow(table_id, input_id){
    var input = document.getElementById(input_id);
    console.log(input.value);
    var input_name = document.getElementById(input.value).textContent;
    console.log(input_name);
    if(input.value != ""){
        var table = document.getElementById(table_id);
        var row = table.insertRow();
        var id = row.insertCell();
        var name = row.insertCell();
        var deleteRow = row.insertCell();
        id.innerHTML = input.value;
        name.innerHTML = "<input type='hidden' name='groups[]' id='group_" + row.sectionRowIndex + "' value='" + input.value + "'>" + input_name;
        deleteRow.innerHTML = "<button type='button' class='w3-button w3-red' onclick='deleteTableRow(this)'>Usuń</button>";
    }

};
function openQuestionToggle(close_id, open_id, el_id){
    var checkbox = document.getElementById(el_id);
    var closeQuestion = document.getElementById(close_id);
    var openQuestion = document.getElementById(open_id);
    if(checkbox.checked == true){
        closeQuestion.classList.replace("w3-show", "w3-hide");
    }
    else{
        closeQuestion.classList.replace("w3-hide", "w3-show");
        
    }
};
function addAnswerRow(table_id, input_id){
    var input = document.getElementById(input_id);
    if(input.value != ""){
        var table = document.getElementById(table_id);
        var row = table.insertRow();
        if(row.sectionRowIndex <= 10){
            var id = row.insertCell();
            var text = row.insertCell();
            var correct = row.insertCell();
            var deleteRow = row.insertCell();
            id.innerHTML = "";
            text.innerHTML = input.value;
            correct.innerHTML = "<td><input type='checkbox' class='w3-check' name='answers_correct[]' value='" + row.sectionRowIndex + "'></td>";
            deleteRow.innerHTML = "<button type='button' class='w3-button w3-red' onclick='deleteTableRow(this)'>Usuń</button>";
        }
    }

};
