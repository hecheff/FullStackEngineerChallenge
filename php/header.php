<div class="header">
    <div class="content_wrapper" style="padding-bottom:0;">
        <div class="title"><?php echo TITLE; ?></div>
        <div class="menu">
            <div class="menu_left">
                <a href="/"><div class="menu_item">
                    Top Page
                </div></a>
            </div>
            <div class="menu_right">
                <?php if (!isset($_SESSION['username']) || empty($_SESSION['username']) || !isset($_SESSION['password']) || empty($_SESSION['password'])) : ?>
                    <div class="sub_title">Login</div>
                    <form action="/php/login.php" method="post">
                        Username: <input id="username" name="username" type="text" maxlength="32" style="width:100px;" required>
                        Password: <input id="password" name="password" type="password" maxlength="16" style="width:100px;" required>
                        <input type="submit" value="Login">
                    </form>
                <?php else: ?>
                    Welcome, <?php echo $_SESSION['username']; ?> (<?php echo $GLOBALS['user_details']['first_name']." ".$GLOBALS['user_details']['last_name']; ?>). 
                    <button onclick="window.location.href='/php/logout.php';">Logout</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>