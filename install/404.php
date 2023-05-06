<?
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("404");
?>

<div class="is-flex is-justify-content-center is-align-items-center" style=" height: 100vh;">
	<div class="has-text-centered">
		<h1 class="is-size-1 has-text-weight-bold has-text-primary">404</h1>
		<p class="is-size-5 has-text-weight-medium"> <span class="has-text-danger">Opps!</span> Page not found.</p>
		<p class="is-size-6 mb-2">
			The page you’re looking for doesn’t exist.
		</p>
		<a href="/" class="button is-primary">На главную</a>
	</div>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>





