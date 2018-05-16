<!--Содержимое тега main-->

 <h2 class="content__main-heading">Список задач</h2>

                <form class="search-form" action="index.html" method="post">
                    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

                    <input class="search-form__submit" type="submit" name="" value="Искать">
                </form>

                <div class="tasks-controls">
                    <nav class="tasks-switch">
                        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
                        <a href="/" class="tasks-switch__item">Повестка дня</a>
                        <a href="/" class="tasks-switch__item">Завтра</a>
                        <a href="/" class="tasks-switch__item">Просроченные</a>
                    </nav>

					<label class="checkbox">
							<!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
							
							<?php if ($show_complete_tasks == 1): ?>
								<input class="checkbox__input visually-hidden show_completed" type="checkbox" checked>
								<span class="checkbox__text"> Показывать выполненные</span>
							<?php endif; ?>
					</label>
				</div>

                <table class="tasks">
					<?php foreach ($tasks as $key => $val): ?>
							<tr class="tasks__item task <?php if ($val['complete'] == true): ?>task--completed <?php elseif (deadline($val['end_date']) <= 24 and deadline($val['end_date']) != ""): ?>task--important<?php endif ?>">
								<td class="task__select">
									<label class="checkbox task__checkbox">
										<input class="checkbox__input visually-hidden task__checkbox" type="checkbox" <?php if ($val['complete'] == true):?> checked <?php endif ?>>
										<span class="checkbox__text"><?=htmlspecialchars($val['task']);?></span>
									</label>
								</td>
								<td class="task__date">
									<?=$val['end_date'];?>
								</td>
								<td  class="task__date">
									<?=$val['category'];?>
								</td>
							</tr>	
					<?php endforeach; ?>
                </table>