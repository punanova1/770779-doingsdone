<h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.html" method="post">
        <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/" class="tasks-switch__item <?= !isset($_GET["today"]) && !isset($_GET["tomorrow"]) && !isset($_GET["failed"]) ? "tasks-switch__item--active" : "" ?>">Все задачи</a>
            <a href="index.php?today" class="tasks-switch__item <?= isset($_GET["today"]) ? "tasks-switch__item--active" : "" ?>">Повестка дня</a>
            <a href="index.php?tomorrow" class="tasks-switch__item <?= isset($_GET["tomorrow"]) ? "tasks-switch__item--active" : "" ?>">Завтра</a>
            <a href="index.php?failed" class="tasks-switch__item <?= isset($_GET["failed"]) ? "tasks-switch__item--active" : "" ?>">Просроченные</a>
        </nav>
    <form>
        <label class="checkbox">
        <input
            class="checkbox__input visually-hidden show_completed"
            type="checkbox"
            onchange="location.href = 'index.php?show_completed=' + (this.checked ? '1' : '0')"
            name="show_complete"
            <?= $show_complete_tasks == 1 ? "checked" : "" ?>>
            <span class= "checkbox__text" > Показывать выполненные </span>
        </label>
    </form>
    </div>

    <table class="tasks">
        <?php if ($show_complete_tasks == 1): ?>
            <?php foreach ($tasks as $key => $val): ?>
                <tr class="tasks__item task <?php if ($val['end_date'] !== NULL): ?>task--completed <?php elseif (deadline($val['deadline']) <= 24 and deadline($val['deadline']) != ""): ?>task--important<?php endif ?>">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input
                            class="checkbox__input visually-hidden task__checkbox"
                            type="checkbox" value="<?= $val["id"]?>" 
                            <?= $val['end_date'] == !NULL ? "checked" : "" ?>>
                            <span class="checkbox__text">
                                <?=htmlspecialchars($val['task']);?>
                            </span>
                        </label>
                    </td>
                    <td class="task__file">
                        <?php if(!empty($val['file_path'])): ?>
                            <a class="download-link" href="<?=$val['file_path']?>"><?=htmlspecialchars($val['file_name'])?></a>
                        <?php endif ?>
                    </td>
                    <td class="task__date">
                        <?=$val['deadline'];?>
                    </td>
                </tr>
            <?php endforeach; ?>
         <?php else: ?>
            <?php foreach ($tasks as $key => $val):?>
                <?php if ($val['end_date'] == NULL): ?>
                <tr class="tasks__item task <?php if ($val['end_date'] !== NULL): ?>task--completed <?php elseif (deadline($val['deadline']) <= 24 and deadline($val['deadline']) != ""): ?>task--important<?php endif ?>">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                        <input
                            class="checkbox__input visually-hidden task__checkbox"
                            type="checkbox" value="1"
                            <?= $val['end_date'] == !NULL ? "checked" : "" ?>>
                            <span class="checkbox__text">
                                <?= htmlspecialchars($val['task']);?>
                            </span>
                        </label>
                    </td>
                    <td class="task__file">
                        <?php if(!empty($val['file_path'])): ?>
                            <a class="download-link" href="<?=$val['file_path']?>"><?=htmlspecialchars($val['file_name'])?></a>
                        <?php endif ?>
                    </td>
                    <td class="task__date">
                        <?=$val['deadline'];?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php endforeach;?>
        <?php endif ?>
    </table>