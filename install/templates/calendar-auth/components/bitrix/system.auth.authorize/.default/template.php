<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>


<section class="container">
    <!--<div class="bx-auth">-->
    <? if ($arResult["AUTH_SERVICES"]): ?>
        <div class="bx-auth-title"><? echo GetMessage("AUTH_TITLE") ?></div>
    <? endif ?>
    <!--	<div class="bx-auth-note">--><? //=GetMessage("AUTH_PLEASE_AUTH"); ?><!--</div>-->

    <!--	<section class="container">-->
    <div class="columns is-multiline">
        <div class="column is-8 is-offset-2 register">
            <div class="columns">
                <div class="column left">
                    <h1 class="title is-1"><?= GetMessage("AUTH_MODULE_NAME") ?></h1>
                    <h2 class="subtitle colored is-4"><?= GetMessage("AUTH_MODULE_MIN_DESC")?></h2>
                    <p> <?= GetMessage("AUTH_MODULE_FULL_DESC")?> </p>
                </div>
                <div class="column right has-text-centered">
                    <h1 class="title is-4"><?= GetMessage("AUTH_MODULE_CONNECT_TO_US")?></h1>
                    <p class="description"><?= GetMessage("AUTH_MODULE_AUTH_DESC")?></p>

                    <form name="form_auth" method="post" target="_top" action="/auth">

                        <input type="hidden" name="AUTH_FORM" value="Y"/>
                        <input type="hidden" name="TYPE" value="AUTH"/>
                        <? if ($arResult["BACKURL"] <> ''): ?>
                            <input type="hidden" name="backurl" value="/auth"/>
                        <? endif ?>
                        <? foreach ($arResult["POST"] as $key => $value): ?>
                            <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
                        <? endforeach ?>

                        <div class="field">
                            <div class="control has-icons-left">
                                <input class="input bx-auth-input form-control" type="text" placeholder="Логин"
                                       name="USER_LOGIN" value="<?= $arResult["LAST_LOGIN"] ?>">
                                <span class="icon is-small is-left">
					<i class="fas fa-user"></i>
				</span>
                            </div>
                        </div>

                        <div class="field">
                            <p class="control has-icons-left">
                                <input class="input bx-auth-input form-control" type="password" placeholder="Пароль"
                                       name="USER_PASSWORD" autocomplete="off">
                                <span class="icon is-small is-left">
					<i class="fas fa-lock"></i>
				</span>
                            </p>
                        </div>

                        <? if ($arResult["SECURE_AUTH"]): ?>
                            <span class="bx-auth-secure" id="bx_auth_secure"
                                  title="<? echo GetMessage("AUTH_SECURE_NOTE") ?>" style="display:none">
					<div class="bx-auth-secure-icon"></div>
				</span>
                            <noscript>
				<span class="bx-auth-secure" title="<? echo GetMessage("AUTH_NONSECURE_NOTE") ?>">
					<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
				</span>
                            </noscript>
                            <script type="text/javascript">
                                document.getElementById('bx_auth_secure').style.display = 'inline-block';
                            </script>
                        <? endif ?>
                        <div style="margin-bottom: 15px;">
                        <?
                        ShowMessage($arParams["~AUTH_RESULT"]);
                        ShowMessage($arResult['ERROR_MESSAGE']);
                        ?>
                        </div>
                        <div class="authorize-submit-cell">
                            <input type="submit" class="button is-block is-primary is-fullwidth btn btn-primary"
                                   name="Login" value="<?= GetMessage("AUTH_AUTHORIZE") ?>"/>
                        </div>


                        <? //if ($arParams["NOT_SHOW_LINKS"] != "Y"):?>
                        <!--		<noindex>-->
                        <!--			<p>-->
                        <!--				<a href="-->
                        <? //=$arResult["AUTH_FORGOT_PASSWORD_URL"]?><!--" rel="nofollow">-->
                        <? //=GetMessage("AUTH_FORGOT_PASSWORD_2")?><!--</a>-->
                        <!--			</p>-->
                        <!--		</noindex>-->
                        <? //endif?>

                        <? if ($arParams["NOT_SHOW_LINKS"] != "Y" && $arResult["NEW_USER_REGISTRATION"] == "Y" && $arParams["AUTHORIZE_REGISTRATION"] != "Y"): ?>
                            <noindex>
                                <br/>
                                <small><em>Еще нет аккаунта? </em><a href="<?= $arResult["AUTH_REGISTER_URL"] ?>"
                                                                     rel="nofollow"><?= GetMessage("AUTH_REGISTER") ?></a></small>
                                <!--			<p>-->
                                <!--				<a href="-->
                                <? //=$arResult["AUTH_REGISTER_URL"]?><!--" rel="nofollow">-->
                                <? //=GetMessage("AUTH_REGISTER")?><!--</a><br />-->
                                <? //=GetMessage("AUTH_FIRST_ONE")?>
                                <!--			</p>-->
                            </noindex>
                        <? endif ?>

                    </form>
                </div>
                <!--	</form>-->
            </div>
        </div>
    </div>
    <div class="column is-8 is-offset-2">
        <br>
        <div class="level-right">
            <small class="level-item" style="color: var(--textLight)">
                &copy; Hobby Calendar. All Rights Reserved.
            </small>
        </div>
    </div>

</section>

<script type="text/javascript">
    <?if ($arResult["LAST_LOGIN"] <> ''):?>
    try {
        document.form_auth.USER_PASSWORD.focus();
    } catch (e) {
    }
    <?else:?>
    try {
        document.form_auth.USER_LOGIN.focus();
    } catch (e) {
    }
    <?endif?>
</script>

<? if ($arResult["AUTH_SERVICES"]): ?>
    <?
    $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
        array(
            "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
            "CURRENT_SERVICE" => $arResult["CURRENT_SERVICE"],
            "AUTH_URL" => $arResult["AUTH_URL"],
            "POST" => $arResult["POST"],
            "SHOW_TITLES" => $arResult["FOR_INTRANET"] ? 'N' : 'Y',
            "FOR_SPLIT" => $arResult["FOR_INTRANET"] ? 'Y' : 'N',
            "AUTH_LINE" => $arResult["FOR_INTRANET"] ? 'N' : 'Y',
        ),
        $component,
        array("HIDE_ICONS" => "Y")
    );
    ?>
<? endif ?>
