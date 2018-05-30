<div class="modal" <?= $addProjectErrors["errors"] ? "" : "hidden"?> id="project_add">
  <button class="modal__close" type="button" name="button">Закрыть</button>

  <h2 class="modal__heading">Добавление проекта</h2>

  <form class="form" action="index.php" method="post">
    <div class="form__row">
      <label class="form__label" for="project_name">Название <sup>*</sup></label>

      <input class="form__input" type="text" name="name" id="project_name" value="<?= strip_tags($postedTitle) ?>" placeholder="Введите название проекта">
      <p class="form__message"><?= $addProjectErrors["projectExists"] ? "Такой проект уже существует" : ""?></p>
      <p class="form__message"><?= $addProjectErrors["emptyTitle"] ? "Заполните это поле" : ""?></p>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="project_add" value="Добавить">
    </div>
  </form>
</div>
