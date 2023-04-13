<?php
/**
 * @var object $USER
 */
?>
<div class="columns is-mobile">
    <div class="column is-4 is-offset-11">
        <button class="js-modal-trigger button is-primary" data-target="modal-js-example">
            Создать группу
        </button>
    </div>
</div>

<a class="group-card" href="">
    <figure class="image is-64x64">
        <img class="GroupPhoto" src="upload/medialibrary/61d/e3qzx1q2eooofim1tkwrp3itfgimg131.png">
    </figure>
    <div class="GroupName">Lorem ipsum dolor sit amet</div>
</a>


<form name="Create Team" action="" method="post">
    <input name="adminId" type="hidden" value="<?= $USER->getID() ?>">
    <div class="modal" id="modal-js-example" >
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Создание группы</p>
                <button class="delete" type="reset" aria-label="close"></button>
            </header>

            <section class="modal-card-body">
                <div class="field">
                    <label class="label">Название</label>
                    <div class="control">
                        <input name="title" class="input is-primary mb-4 is-large" type="text" placeholder="Название группы">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Описание</label>
                    <div class="control">
                        <input name="description" class="input is-primary mb-4 " type="text" placeholder="Описание">
                    </div>
                </div>
                <label class="checkbox">
                    <input name="isPrivate" type="checkbox">
                    Публичная группа
                </label>


            </section>
            <footer class="modal-card-foot">
                <button class="button is-success" type="submit">Создать</button>
                <button class="button" type="reset" >Отмена</button>
            </footer>
        </div>
    </div>
</form>