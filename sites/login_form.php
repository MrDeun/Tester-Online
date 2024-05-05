<div class="form-container">
    <h2>Logowanie</h2>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="username">Login: 
                <?php if(isset($_POST['error']) && $_POST['error'] == 1){
                    echo('<span style="color:red;">Brak użytkownika</span>');
                }?>
            </label><br>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Hasło:
                <?php if(isset($_POST['error']) && $_POST['error'] == 2){
                    echo('<span style="color:red;">Błędne hasło</span>');
                }?>
            </label><br>
            <input type="password" id="password" name="password" required><br>
            <span class="show-password w3-button" onclick="togglePassword('password')">Pokaż hasło</span>
        </div><br><br>
        <button type="submit">Zaloguj</button>
    </form>
</div>