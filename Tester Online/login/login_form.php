<style>
.form-container {
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
}
.form-group input[type="text"],
.form-group input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.show-password {
    margin-left: 5px;
    cursor: pointer;
}
</style>
<div class="form-container">
    <h2>Logowanie</h2>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="username">Login: 
                <?php if(isset($_POST['error']) && $_POST['error'] == 1){
                    echo('<span style="color:red;">Brak użytkownika</span>');
                }?>
            </label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Hasło:
                <?php if(isset($_POST['error']) && $_POST['error'] == 2){
                    echo('<span style="color:red;">Błędne hasło</span>');
                }?>
            </label>
            <input type="password" id="password" name="password" required><br>
            <span class="show-password w3-button" onclick="togglePassword('password')">Pokaż hasło</span>
        </div>
        <button type="submit">Zaloguj</button>
    </form>
</div>