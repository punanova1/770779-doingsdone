<div class="modal" <?= $login ? "" : "hidden " ?>id="user_login">
  <button class="modal__close" type="button" name="button">Закрыть</button>

  <h2 class="modal__heading">Вход на сайт</h2>

  <form class="form" action="" method="post">
    <div class="form__row">
        <label class="form__label" for="email">E-mail <sup>*</sup></label>
        <input class="form__input <?= $loginErrors["emptyEmail"] || $loginErrors["emailNotFound"] ? "form__input--error" : "" ?>" type="text" name="email" id="email" value="<?=$userEmail?>" placeholder="Введите e-mail">
        <?= $loginErrors["emptyEmail"] ? "<p class=\"form__message\">Введите e-mail</p>" : "" ?>
        <?= $loginErrors["emailNotFound"] ? "<p class=\"form__message\">Пользователь с таким email не найден</p>" : "" ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="password">Пароль <sup>*</sup></label>
      <input class="form__input<?= $loginErrors["emptyPassword"] || $loginErrors["incorrectPassword"] ? "form__input--error" : "" ?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">
      <?= $loginErrors["emptyPassword"] ? "<p class=\"form__message\">Введите пароль</p>" : "" ?>
      <?= $loginErrors["incorrectPassword"] ? "<p class=\"form__message\">Неверный пароль</p>" : "" ?>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="login" value="Войти">
    </div>
  </form>
</div>
